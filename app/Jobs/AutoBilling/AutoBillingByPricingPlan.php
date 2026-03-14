<?php

namespace App\Jobs\AutoBilling;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\PricingPlan;
use Illuminate\Support\Str;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\AutoBilling\AutoBillSubscriber;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutoBillingByPricingPlan implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $project;
    protected $pricingPlan;
    protected $autoBillingJobBatchesCount;

    public function uniqueId()
    {
        return $this->pricingPlan->id;
    }

    public function __construct(Project $project, PricingPlan $pricingPlan, int $autoBillingJobBatchesCount)
    {
        $this->onQueue('billing');
        $this->pricingPlan = $pricingPlan;
        $this->project = $project->withoutRelations();
        $this->autoBillingJobBatchesCount = $autoBillingJobBatchesCount;
    }

    public function handle()
    {
        try {
            Log::info("AutoBillingByPricingPlan Started for Plan: {$this->pricingPlan->id}");

            if (!$this->project->hasBillingCredentials()) {
                return;
            }

            $subscribersQuery = $this->project->subscribers()
                ->select('subscribers.id', 'subscribers.msisdn')
                ->whereHas('autoBillingSchedules', function (Builder $query) {
                    $query->where('auto_billing_enabled', '1')
                        ->where('is_processing', false)
                        ->where('next_attempt_date', '<=', Carbon::now())
                        ->where('pricing_plan_id', $this->pricingPlan->id);
                })
                ->orderBy('subscribers.id');

            $pricingPlan = $this->pricingPlan;
            $project = $this->project;
            $batchCount = $this->autoBillingJobBatchesCount;
            $batchIndex = 0;

            $subscribersQuery->chunk(1000, function ($chunked_subscribers) use ($project, $pricingPlan, &$batchIndex, $batchCount) {

                $subscriberIds = $chunked_subscribers->pluck('id')->toArray();
                $token = Str::uuid()->toString(); // Unique token for this specific claim batch

                // ATOMIC CLAIM: Mark as processing and assign the token
                $affected = DB::table('auto_billing_schedules')
                    ->where('pricing_plan_id', $pricingPlan->id)
                    ->whereIn('subscriber_id', $subscriberIds)
                    ->where('is_processing', false)
                    ->update([
                        'is_processing' => true,
                        'processing_token' => $token,
                        'updated_at' => Carbon::now()
                    ]);

                if ($affected === 0) {
                    return;
                }

                $chunkJobs = [];
                foreach ($chunked_subscribers as $subscriber) {
                    $chunkJobs[] = new AutoBillSubscriber($project, $subscriber, $pricingPlan, $token);
                }

                $sprintName = 'Sprint #' . ($batchCount + $batchIndex + 1);

                $batch = Bus::batch($chunkJobs)
                    ->name($sprintName)
                    ->allowFailures()
                    ->dispatch();

                DB::table('auto_billing_job_batches')->insert([
                    'pricing_plan_id' => $pricingPlan->id,
                    'job_batch_id'    => $batch->id,
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now()
                ]);

                $batchIndex++;
            });

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
