<?php

namespace App\Jobs\AutoBilling;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use App\Models\subscriptionPlan;
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

class AutoBillingBySubscriptionPlan implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     *  Project instance.
     *
     *  @var \App\Models\Project
     */
    protected $project;

    /**
     *  Subscription plan
     *
     *  @var \App\Models\SubscriptionPlan
     */
    protected $subscriptionPlan;

    /**
     *  Auto billing job batches count
     *
     *  @var int
     */
    protected $autoBillingJobBatchesCount;

    /**
     *  The unique ID of the job.
     *
     *  Sometimes, you may want to ensure that only one instance of a specific job is on
     *  the queue at any point in time. You may do so by implementing the ShouldBeUnique
     *  interface on your job class. So the current job will not be dispatched if another
     *  instance of the job is already on the queue and has not finished processing.
     *
     *  Refer: https://laravel.com/docs/8.x/queues#unique-jobs
     *
     *  @return string
     */
    public function uniqueId()
    {
        return $this->subscriptionPlan->id;
    }

    /**
     * Create a new job instance.
     *
     * @param App\Models\Project $project
     * @param App\Models\SubscriptionPlan $subscriptionPlan
     * @param int $autoBillingJobBatchesCount
     *
     * @return void
     */
    public function __construct(Project $project, SubscriptionPlan $subscriptionPlan, int $autoBillingJobBatchesCount)
    {
        Log::info('AutoBillingBySubscriptionPlan: __construct()');
        $this->subscriptionPlan = $subscriptionPlan;
        $this->project = $project->withoutRelations();

        /**
         *  It appears that the eager loaded withCount('autoBillingJobBatches')
         *  is not accessible using $subscriptionPlan->auto_billing_job_batches_count
         *  within the handle() method. Therefore we will set this as its own parameter.
         */
        $this->autoBillingJobBatchesCount = $autoBillingJobBatchesCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            Log::info('AutoBillingBySubscriptionPlan: handle()');

            /**
             *  Query the subscribers that are ready for billing.
             *
             *  Limit the loaded subscriber to the subscriber id and msisdn to consume less memory.
             *
             *  The final query output is as follows:
             *
             *  [
             *      {
             *          "id": 1,
             *          "msisdn": "26772000001"
             *      },
             *      ...
             *  ]
             */
            $subscribers = $this->project->subscribers()->whereHas('autoBillingSchedules', function (Builder $query) {

                /**
                 *  We need to limit the subscribers based on the auto billing schedules next_attempt_date.
                 *  The next_attempt_date helps us to determine if acceptable date and time to qualify the
                 *  subscriber for auto billing e.g
                 *
                 *  1) We do not want to auto bill too soon, such as auto billing long before the specified
                 *     next_attempt_date. We cannot afford to accidentally auto bill subscribers who's
                 *     subscription only ends 1 month or 1 week from now. That is too soon for the
                 *     subscriber to be qualified for auto billing.
                 *
                 *
                 *  2) We do not want to auto bill too late, such as auto billing long after the specified
                 *     next_attempt_date. We cannot afford to accidentally auto bill subscribers who's
                 *     subscription ended 1 year  ago or 1 month ago. That is too far off for the
                 *     subscriber to be qualified for auto billing.
                 *
                 *  We must search for subscribers who's next_attempt_date is within a reasonable range.
                 *  We can autobill on the following conditions:
                 *
                 *  1) If the next_attempt_date has been reached, that is, auto bill on the same
                 *     date and time or sometime after.
                 *
                 *  2) If the next_attempt_date is not sometime after that is more than 48 hours
                 *    from the desired datetime.
                 *
                 *  Remember that the first billing attempt is expected to occur as soon as the
                 *  subscription ends provided that we do not have any downtime or delays while
                 *  processing other jobs. The second attempt will occur 24 hours later after
                 *  the first, and the third attempt will occur 24 hours later after the
                 *  second attempt.
                 *
                 *  Each subscription plan can have from 1 up to 10 maximum auto billing attempts.
                 *  The following is a timeline of how the auto billing occurs based on the maximum
                 *  auto billing attempts set on the subscription plan (max_auto_billing_attempts).
                 *
                 *  ---------------------------------------------------------------------------------
                 *  Theoretical Timeline for (1) Attempt:
                 *
                 *  1) Subscription ends                    2024-01-01 08:00:00
                 *  2) Attempt #1 (occurs immediately)      2024-01-01 08:00:00
                 *  ---------------------------------------------------------------------------------
                 *  Theoretical Timeline for (2) Attempts:
                 *
                 *  1) Subscription ends                    2024-01-01 08:00:00
                 *  2) Attempt #1 (occurs immediately)      2024-01-01 08:00:00
                 *  3) Attempt #2 (24 hours later)          2024-01-02 08:00:00
                 *  ---------------------------------------------------------------------------------
                 *  Theoretical Timeline for (3) Attempts:
                 *
                 *  1) Subscription ends                    2024-01-01 08:00:00
                 *  2) Attempt #1 (occurs immediately)      2024-01-01 08:00:00
                 *  3) Attempt #2 (24 hours later)          2024-01-02 08:00:00
                 *  4) Attempt #3 (24 hours later)          2024-01-03 08:00:00
                 *  ---------------------------------------------------------------------------------
                 *  This approach continues until the maximum auto billing attempts on the
                 *  subscription plan (max_auto_billing_attempts) are exhausted.
                 *  ---------------------------------------------------------------------------------
                 *
                 *  In theory, the first attempt date is the same as the subscription end date,
                 *  the second attempt date is the 24 hours after the first attempt date,
                 *  the third attempt date is the 24 hours after the second attempt date.
                 *  This is how the theoretical timeline might look like:
                 *
                 *  Subscription end|             |            |
                 *                  |             |            |
                 *                  |< 24 hours > |< 24 hours >|
                 *                  |             |            |
                 *         Attempt 1|    Attempt 2|   Attempt 3|
                 *
                 *  In practice, we might not always be able to run any of these attempts
                 *  exactly on their specified next_attempt_date due to various reasons e.g
                 *  system downtime, scheduled maintenance or delays due to other jobs
                 *  being processed etc. For simplicity, we will only take the first
                 *  attempt delays into consideration for demonstration. This is how
                 *  the practical timeline might look like:
                 *
                 *  Subscription end|                   |             |            |
                 *                  |    < x hours >    |             |            |
                 *                  | unexpected delays |< 24 hours > |< 24 hours >|
                 *                  |                   |             |            |
                 *                  |          Attempt 1|    Attempt 2|   Attempt 3|
                 *
                 *  To accommodate these possible delays, we can qualify auto billing
                 *  schedules with a next_attempt_date which is not older than
                 *  48 hours (2 days).
                 */
                $query->where('auto_billing_enabled', '1')
                      ->where('next_attempt_date', '<=', Carbon::now())
                      ->where('next_attempt_date', '>=', Carbon::now()->subDays(2))
                      ->where('subscription_plan_id', $this->subscriptionPlan->id);

             })->select('subscribers.id', 'subscribers.msisdn');

             Log::info('$subscribers->count(): '.$subscribers->count());

            //  If we have subscribers to auto bill
            if( $subscribers->count() > 0 ) {

                $jobs = [];

                //  Only query 1000 subscribers at a time (This helps us save memory)
                $subscribers->chunk(1000, function ($chunked_subscribers) use (&$jobs) {

                    //  Foreach subscriber we retrieved from the query
                    foreach ($chunked_subscribers as $subscriber) {

                        //  If this project has the billing credentials then continue
                        if( $this->project->hasBillingCredentials() ) {

                            //  Create a job to bill the subscriber
                            $jobs[] = new AutoBillSubscriber($this->project, $subscriber, $this->subscriptionPlan);

                        }

                    }

                });

                //  If we have jobs to process
                if( count($jobs) > 0 ) {

                    /**
                     *  We cannot reference "$this->subscriptionPlan" within the Bus::batch() closures.
                     *  Therefore we must create an subscriptionPlan variable that we can pass as a
                     *  parameter of the various closures.
                     */
                    $subscriptionPlan = $this->subscriptionPlan;

                    //  Set the sprint name
                    $sprintName = 'Sprint #' . ($this->autoBillingJobBatchesCount + 1);

                    //  Create the batch to send
                    $batch = Bus::batch($jobs
                        )->then(function (Batch $batch) use ($subscriptionPlan) {

                        })->catch(function (Batch $batch, Throwable $e) use ($subscriptionPlan) {

                        })->finally(function (Batch $batch) use ($subscriptionPlan) {

                        })->name($sprintName)->allowFailures()->dispatch();

                    //  Create a new auto billing job batch record
                    DB::table('auto_billing_job_batches')->insert([
                        'subscription_plan_id' => $subscriptionPlan->id,
                        'job_batch_id' => $batch->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                }

            }

        } catch (\Throwable $th) {

            Log::error('AutoBillingBySubscriptionPlan Job Failed: '. $th->getMessage());

        }
    }

}
