<?php

namespace App\Jobs\AutoBillingReminder;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use App\Models\AutoBillingReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\AutoBilling\AutoBillSubscriber;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Jobs\AutoBillingReminder\SendAutoBillingReminderSms;

class NextAutoBillingBySubscriptionPlan implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     *  Project instance.
     *
     *  @var \App\Models\Project
     */
    protected $project;

    /**
     *  Subscription plan instance.
     *
     *  @var \App\Models\SubscriptionPlan
     */
    protected $subscriptionPlan;

    /**
     *  Auto Billing Reminder instance.
     *
     *  @var \App\Models\AutoBillingReminder
     */
    protected $autoBillingReminder;

    /**
     *  Auto billing reminder job batches count
     *
     *  @var int
     */
    protected $autoBillingReminderJobBatchesCount;

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
        return $this->autoBillingReminder->id;
    }

    /**
     * Create a new job instance.
     *
     * @param App\Models\Project $project
     * @param App\Models\AutoBillingReminder $autoBillingReminder
     * @param int $autoBillingReminderJobBatchesCount
     *
     * @return void
     */
    public function __construct(Project $project, SubscriptionPlan $subscriptionPlan, AutoBillingReminder $autoBillingReminder, int $autoBillingReminderJobBatchesCount)
    {
        $this->project = $project;
        $this->subscriptionPlan = $subscriptionPlan;
        $this->autoBillingReminder = $autoBillingReminder->withoutRelations();

        /**
         *  It appears that the eager loaded withCount('autoBillingReminderJobBatches') is not accessible using
         *  $autoBillingReminder->auto_billing_reminder_job_batches_count within the handle() method.
         *  Therefore we will set this as its own parameter.
         */
        $this->autoBillingReminderJobBatchesCount = $autoBillingReminderJobBatchesCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{


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

                $query->where('subscription_plan_id', $this->subscriptionPlan->id)
                      ->where('next_attempt_date', '<=', Carbon::now())
                      ->where('auto_billing_enabled', '1');

             })->select('subscribers.id', 'subscribers.msisdn');

            Log::info('$subscribers->count() before: '.$subscribers->count());

            /**
             *  Limit the subscribers based on the inactive non-cancelled subscription with the furthest
             *  end_at datetime, where the subscription matches the given subscription plan id and has
             *  ended at most 3 days ago.
             */
            $subscribers = $subscribers->whereHas('subscriptionWithFurthestEndAt', function (Builder $query) {

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

                $query->inactive()->notCancelled()
                      ->where('subscription_plan_id', $this->subscriptionPlan->id)
                      ->where('end_at', '>=', Carbon::now()->subDays($endedAtleastDaysAgo));
            });






















            /**
             *  @var int $endsInNumberOfHours
             */
            $endsInNumberOfHours = $this->autoBillingReminder->hours;

            /**
             *  Query the subscribers that are ready for billing.
             *
             *  Limit the subscribers based on the active non-cancelled subscription with the furthest
             *  end_at datetime, where the subscription matches the given subscription plan id and has
             *  ended at most 3 days ago.
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
            $subscribers = $this->project->subscribers()
                ->whereHas('subscriptions', function (Builder $query) use ($endsInNumberOfHours) {

                    /**
                     *  Must be any active non cancelled subscription
                     */
                    $query->active()->notCancelled()
                            ->where('subscriptions.subscription_plan_id', $this->subscriptionPlan->id)

                            /**
                             *  Must be any subscription that will end in x number of hours or less
                             *  but must be greater than the current date and time.
                             */
                            ->where('end_at', '<=', now()->addHours($endsInNumberOfHours))
                            ->where('end_at', '>', now());

                })->select('subscribers.id', 'subscribers.msisdn');

            //  Query only subscribers who have not already been notified
            $subscribers->whereDoesntHave('autoBillingSchedules', function (Builder $query) {

                $hours = $this->autoBillingReminder->hours;

                $query->where('subscription_plan_id', $this->subscriptionPlan->id);

                if($hours == 1) {
                    $query->where('reminded_one_hour_before', '1');
                }else if($hours == 6) {
                    $query->where('reminded_six_hours_before', '1');
                }else if($hours == 12) {
                    $query->where('reminded_twelve_hours_before', '1');
                }else if($hours == 24) {
                    $query->where('reminded_twenty_four_hours_before', '1');
                }else if($hours == 48) {
                    $query->where('reminded_forty_eight_hours_before', '1');
                }else if($hours == 72) {
                    $query->where('reminded_seventy_two_hours_before', '1');
                }

            });



            //  If we have subscribers to remind
            if( $subscribers->count() > 0 ) {

                $jobs = [];

                //  Only query 1000 subscribers at a time (This helps us save memory)
                $subscribers->chunk(1000, function ($chunked_subscribers) use (&$jobs) {

                    //  Foreach subscriber we retrieved from the query
                    foreach ($chunked_subscribers as $subscriber) {

                        //  If this project has the sms credentials then continue
                        if( $this->project->hasSmsCredentials() ) {

                            //  Create a job to send the auto billing reminder SMS to the subscriber
                            $jobs[] = new SendAutoBillingReminderSms($this->project, $subscriber, $this->subscriptionPlan, $this->autoBillingReminder);

                        }

                    }

                });

                //  If we have jobs to process
                if( count($jobs) > 0 ) {

                    /**
                     *  We cannot reference "$this->autoBillingReminder" within the Bus::batch() closures.
                     *  Therefore we must create an autoBillingReminder variable that we can pass as a
                     *  parameter of the various closures.
                     */
                    $autoBillingReminder = $this->autoBillingReminder;

                    //  Set the sprint name
                    $sprintName = 'Sprint #' . ($this->autoBillingReminderJobBatchesCount + 1);

                    //  Create the batch to send
                    $batch = Bus::batch($jobs
                        )->then(function (Batch $batch) use ($autoBillingReminder) {

                        })->catch(function (Batch $batch, Throwable $e) use ($autoBillingReminder) {

                        })->finally(function (Batch $batch) use ($autoBillingReminder) {

                        })->name($sprintName)->allowFailures()->dispatch();

                    //  Create a new auto billing reminder job batch record
                    DB::table('auto_billing_reminder_job_batches')->insert([
                        'auto_billing_reminder_id' => $autoBillingReminder->id,
                        'subscription_plan_id' => $this->subscriptionPlan->id,
                        'job_batch_id' => $batch->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                }

            }

        } catch (\Throwable $th) {

            Log::info('Error: '. $th->getMessage());

            return false;

            // Send error report here
            //  Log::channel('slack')->error('NextAutoBillingBySubscriptionPlan Job Failed: '. $th->getMessage());

        }
    }

}
