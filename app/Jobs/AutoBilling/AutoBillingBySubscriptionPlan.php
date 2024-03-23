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

            Log::info('AutoBillingBySubscriptionPlan');

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
                 *  We need to limit the qualifying subscribers based on their inactive non-cancelled
                 *  subscription end date. We must search for subscribers who's subscription end date
                 *  is within a reasonable expiration range e.g We cannot auto bill subscribers who's
                 *  subscription ended 1 year ago or 1 month ago. That is too far off to be qualified
                 *  for auto billing.
                 *
                 *  We can set a reasonable maximum subscription end datetime before the subscription
                 *  is considered too old e.g 3 days.
                 *
                 *  Remember that the first billing attempt is expected to occur as soon as the
                 *  subscription ends provided that we do not have any downtime or delays while
                 *  processing other jobs. The second will occur 24 hours later after the
                 *  first, and the third will occur 24 hours later after the second.
                 *
                 *  Each subscription plan can have 1, 2 or 3 maximum auto billing attempts. The following
                 *  is a timeline of how the auto billing occurs based on the maximum auto billing
                 *  attempts set on the subscription plan (max_auto_billing_attempts).
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
                 *
                 *  In theory, the first attempt date is the same as the subscription end date,
                 *  the second attempt date is the 24 hours after the first attempt date,
                 *  the third attempt date is the 48 hours after the second attempt date.
                 *  This is how the theoretical timeline might look like:
                 *
                 *  Subscription end|             |            |
                 *                  |             |            |
                 *                  |< 24 hours > |< 24 hours >|
                 *                  |             |            |
                 *         Attempt 1|    Attempt 2|   Attempt 3|
                 *
                 *  In pratice, we might not be able to always run the first attempt exactly as
                 *  soon as the subscription ends for various reasons e.g system downtime,
                 *  maintenance, delays due to other jobs being processed etc. This is
                 *  also true for the second and third attempt. For simplicity, we
                 *  will only take the first attempt delays into consideration.
                 *  This is how the practical timeline might look like:
                 *
                 *
                 *  Subscription end|                   |             |            |
                 *                  |    < x hours >    |             |            |
                 *                  | unexpected delays |< 24 hours > |< 24 hours >|
                 *                  |                   |             |            |
                 *                  |          Attempt 1|    Attempt 2|   Attempt 3|
                 *
                 *  We know that in the best case scenerio (our theorectical scenerio) we need
                 *  48 hours to be able to execute for 3 attempts. Because we know we might
                 *  have unexpected delays before the first, second or third attempt, we
                 *  can factor 24 hours dedicated to accommodate those delays. This means
                 *  that the maximum possible number of delays we can have must add up
                 *  to 24 hours altogether.
                 *
                 *  $endedAtleastDaysAgo =  48 hours (expected execution time)
                 *                        + 24 hours (accommodation for delays)
                 *                        ----------
                 *                          72 hours (3 days)
                 *                        ----------
                 *
                 *  @var int $endedAtleastDaysAgo
                 */
                $endedAtleastDaysAgo = 3;

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
                 *  Each subscription plan can have 1, 2 or 3 maximum auto billing attempts.
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
                 *
                 *  In theory, the first attempt date is the same as the subscription end date,
                 *  the second attempt date is the 24 hours after the first attempt date,
                 *  the third attempt date is the 48 hours after the second attempt date.
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

            Log::info('$subscribers->count() before: '.$subscribers->count());

            //  If we have subscribers to auto bill
            if( $subscribers->count() > 0 ) {

                $jobs = [];

                //  Only query 1000 subscribers at a time (This helps us save memory)
                $subscribers->chunk(1000, function ($chunked_subscribers) use (&$jobs) {

                    //  Foreach subscriber we retrieved from the query
                    foreach ($chunked_subscribers as $subscriber) {

                        Log::info('$subscriber msisdn: '.$subscriber->msisdn);

                        Log::info('hasBillingCredentials(): '.$this->project->hasBillingCredentials());

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

            Log::info($th);

            // Send error report here
            //Log::channel('slack')->error('AutoBillingBySubscriptionPlan Job Failed: '. $th->getMessage());

        }
    }

}
