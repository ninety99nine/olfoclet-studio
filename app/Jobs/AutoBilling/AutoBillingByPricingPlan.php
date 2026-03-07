<?php

namespace App\Jobs\AutoBilling;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\PricingPlan;
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

    /**
     * Project instance.
     *
     * @var \App\Models\Project
     */
    protected $project;

    /**
     * Pricing plan
     *
     * @var \App\Models\PricingPlan
     */
    protected $pricingPlan;

    /**
     * Auto billing job batches count
     *
     * @var int
     */
    protected $autoBillingJobBatchesCount;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->pricingPlan->id;
    }

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Project $project
     * @param \App\Models\PricingPlan $pricingPlan
     * @param int $autoBillingJobBatchesCount
     *
     * @return void
     */
    public function __construct(Project $project, PricingPlan $pricingPlan, int $autoBillingJobBatchesCount)
    {
        $this->onQueue('billing');
        $this->pricingPlan = $pricingPlan;
        $this->project = $project->withoutRelations();
        $this->autoBillingJobBatchesCount = $autoBillingJobBatchesCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // If this project does not have billing credentials, exit early
            if (!$this->project->hasBillingCredentials()) {
                return;
            }

            // Build the query
            $subscribersQuery = $this->project->subscribers()
                ->select('subscribers.id', 'subscribers.msisdn')
                ->whereHas('autoBillingSchedules', function (Builder $query) {
                    $query->where('auto_billing_enabled', '1')
                        ->where('next_attempt_date', '<=', Carbon::now())
                        ->where('pricing_plan_id', $this->pricingPlan->id);
                });

            /**
             * Extract properties to local variables to prevent binding the entire
             * job instance ($this) into the closure, which prevents memory leaks.
             */
            $pricingPlan = $this->pricingPlan;
            $project = $this->project;
            $batchCount = $this->autoBillingJobBatchesCount;
            $batchIndex = 0;

            /**
             * Use chunkById for performance on large tables. It uses indexed WHERE clauses
             * rather than heavy OFFSET queries.
             */
            $subscribersQuery->chunkById(1000, function ($chunked_subscribers) use ($project, $pricingPlan, &$batchIndex, $batchCount) {

                $chunkJobs = [];

                // Create the individual subscriber billing jobs
                foreach ($chunked_subscribers as $subscriber) {
                    $chunkJobs[] = new AutoBillSubscriber($project, $subscriber, $pricingPlan);
                }

                // If no jobs were created, skip batch creation
                if (empty($chunkJobs)) {
                    return;
                }

                $sprintName = 'Sprint #' . ($batchCount + $batchIndex + 1);

                // Create and dispatch the batch for this chunk
                $batch = Bus::batch($chunkJobs)
                    ->name($sprintName)
                    ->allowFailures()
                    ->dispatch();

                // Record the batch in the database
                DB::table('auto_billing_job_batches')->insert([
                    'pricing_plan_id' => $pricingPlan->id,
                    'job_batch_id'    => $batch->id,
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now()
                ]);

                $batchIndex++;

                // Explicitly free the memory used by this chunk's jobs
                unset($chunkJobs);

            }, 'subscribers.id');

        } catch (\Throwable $th) {

            Log::error('AutoBillingByPricingPlan Job Failed: ' . $th->getMessage());

            // Re-throw the exception so the Laravel Queue Worker knows the job failed
            throw $th;
        }
    }
}
