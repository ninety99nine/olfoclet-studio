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
             *  Query the subscribers that are soon ready for billing.
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
                 *  @var int $hours
                 */
                $hours = $this->autoBillingReminder->hours;

                /**
                 *  Must have an auto billing schedule where the next_attempt_date
                 *  is set to be exactly x hours from now or less.
                 */
                $query->where('auto_billing_enabled', '1')
                      ->where('next_attempt_date', '<=', now()->subHours($hours))
                      ->where('subscription_plan_id', $this->subscriptionPlan->id);

                    //  Must not have been reminded before
                    if($hours == 1) {
                        $query->where('reminded_one_hour_before', '0');
                    }else if($hours == 6) {
                        $query->where('reminded_six_hours_before', '0');
                    }else if($hours == 12) {
                        $query->where('reminded_twelve_hours_before', '0');
                    }else if($hours == 24) {
                        $query->where('reminded_twenty_four_hours_before', '0');
                    }else if($hours == 48) {
                        $query->where('reminded_forty_eight_hours_before', '0');
                    }else if($hours == 72) {
                        $query->where('reminded_seventy_two_hours_before', '0');
                    }

            })->select('subscribers.id', 'subscribers.msisdn');

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

            Log::error('NextAutoBillingBySubscriptionPlan Job Failed: '. $th->getMessage());

            return false;

        }
    }

}
