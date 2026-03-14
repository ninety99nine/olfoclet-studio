<?php

namespace App\Jobs\AutoBillingReminder;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\DB;
use App\Models\AutoBillingReminder;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Jobs\AutoBillingReminder\SendAutoBillingReminderSms;

class NextAutoBillingByPricingPlan implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Project instance.
     *
     * @var \App\Models\Project
     */
    protected $project;

    /**
     * Pricing plan instance.
     *
     * @var \App\Models\PricingPlan
     */
    protected $pricingPlan;

    /**
     * Auto Billing Reminder instance.
     *
     * @var \App\Models\AutoBillingReminder
     */
    protected $autoBillingReminder;

    /**
     * Auto billing reminder job batches count
     *
     * @var int
     */
    protected $autoBillingReminderJobBatchesCount;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->autoBillingReminder->id;
    }

    /**
     * Create a new job instance.
     *
     * @param Project $project
     * @param PricingPlan $pricingPlan
     * @param AutoBillingReminder $autoBillingReminder
     * @param int $autoBillingReminderJobBatchesCount
     *
     * @return void
     */
    public function __construct(Project $project, PricingPlan $pricingPlan, AutoBillingReminder $autoBillingReminder, int $autoBillingReminderJobBatchesCount)
    {
        $this->onQueue('high');

        // CRITICAL: Strip relationships from ALL models to keep the queue payload tiny
        $this->project = $project->withoutRelations();
        $this->pricingPlan = $pricingPlan->withoutRelations();
        $this->autoBillingReminder = $autoBillingReminder->withoutRelations();

        $this->autoBillingReminderJobBatchesCount = $autoBillingReminderJobBatchesCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            /**
             * EARLY EXITS:
             * 1. If there's no reminder message, exit.
             * 2. Check SMS credentials ONCE at the top of the job. Checking this inside
             * the chunk loop wastes CPU cycles and database queries.
             */
            if (empty($this->pricingPlan->next_auto_billing_reminder_sms_message) || !$this->project->hasSmsCredentials()) {
                return;
            }

            // Build the query
            $subscribersQuery = $this->project->subscribers()
                ->select('subscribers.id', 'subscribers.msisdn')
                ->whereHas('autoBillingSchedules', function (Builder $query) {

                    $hours = $this->autoBillingReminder->hours;

                    $query->where('auto_billing_enabled', '1')
                        ->where('next_attempt_date', '>', Carbon::now())
                        ->where('next_attempt_date', '<=', Carbon::now()->addHours($hours))
                        ->where('pricing_plan_id', $this->pricingPlan->id);

                    if ($hours == 1) {
                        $query->whereNull('reminded_one_hour_before_at');
                    } else if ($hours == 6) {
                        $query->whereNull('reminded_six_hours_before_at');
                    } else if ($hours == 12) {
                        $query->whereNull('reminded_twelve_hours_before_at');
                    } else if ($hours == 24) {
                        $query->whereNull('reminded_twenty_four_hours_before_at');
                    } else if ($hours == 48) {
                        $query->whereNull('reminded_forty_eight_hours_before_at');
                    } else if ($hours == 72) {
                        $query->whereNull('reminded_seventy_two_hours_before_at');
                    }

                })
                ->orderBy('subscribers.id');

            /**
             * Extracting variables to pass to the closure to prevent memory leaks
             * from binding the entire parent job instance.
             */
            $project = $this->project;
            $pricingPlan = $this->pricingPlan;
            $autoBillingReminder = $this->autoBillingReminder;
            $batchCount = $this->autoBillingReminderJobBatchesCount;
            $batchIndex = 0;

            /**
             * Use chunk() (not chunkById) because chunkById can abort with "column does not exist
             * or was modified during chunking" when the query uses whereHas (subquery).
             */
            $subscribersQuery->chunk(1000, function ($chunked_subscribers) use ($project, $pricingPlan, $autoBillingReminder, &$batchIndex, $batchCount) {

                $chunkJobs = [];

                foreach ($chunked_subscribers as $subscriber) {
                    $chunkJobs[] = new SendAutoBillingReminderSms($project, $subscriber, $pricingPlan, $autoBillingReminder);
                }

                // If no jobs were generated, skip batch dispatch
                if (empty($chunkJobs)) {
                    return;
                }

                $sprintName = 'Sprint #' . ($batchCount + $batchIndex + 1);

                // Dispatch the batch directly inside the chunk loop
                $batch = Bus::batch($chunkJobs)
                    ->name($sprintName)
                    ->allowFailures()
                    ->dispatch();

                // Log the batch to your database
                DB::table('auto_billing_reminder_job_batches')->insert([
                    'auto_billing_reminder_id' => $autoBillingReminder->id,
                    'pricing_plan_id'          => $pricingPlan->id,
                    'job_batch_id'             => $batch->id,
                    'created_at'               => Carbon::now(),
                    'updated_at'               => Carbon::now()
                ]);

                $batchIndex++;

                // Explicitly free memory for the garbage collector
                unset($chunkJobs);

            });

        } catch (Throwable $th) {
            throw $th;
        }
    }
}
