<?php

namespace App\Jobs\AutoBilling;

use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use App\Models\SubscriptionPlan;
use App\Services\BillingService;
use App\Models\BillingTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\CreatedUsingAutoBilling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\SubscriptionRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class AutoBillSubscriber implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $subscriber;
    public $subscriptionPlan;
    public $billingAttemptAt;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 3600;  //  3600 seconds = 1 hour

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project, Subscriber $subscriber, SubscriptionPlan $subscriptionPlan)
    {
        $this->project = $project;
        $this->subscriber = $subscriber;
        $this->subscriptionPlan = $subscriptionPlan;
    }

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
        return $this->subscriptionPlan->id.'-'.$this->subscriber->id;
    }

    /**
     *  Get the middleware the job should pass through.
     *
     *  As you may have noticed in the previous examples, batched jobs should typically determine
     *  if their corresponding batch has been cancelled before continuing execution. However, for
     *  convenience, you may assign the SkipIfBatchCancelled middleware to the job instead. As
     *  its name indicates, this middleware will instruct Laravel to not process the job if
     *  its corresponding batch has been cancelled:
     *
     *  Reference: https://laravel.com/docs/10.x/queues#cancelling-batches
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     *  Execute the job.
     *
     *  @return void
     */
    public function handle()
    {
        try {

            /**
             *  Bill the subscriber using artime.
             *
             *  @var BillingTransaction $billingTransaction
             */
            $billingTransaction = BillingService::billUsingAirtime($this->project, $this->subscriptionPlan, $this->subscriber, CreatedUsingAutoBilling::YES);

            //  Set the billing attempt datetime
            $this->billingAttemptAt = $billingTransaction->created_at;

            /**
             *  @var bool $isSuccessful Whether the subscriber was billed successfully
             */
            $isSuccessful = $billingTransaction->is_successful;

            //  If the subscriber was billed successfully
            if($isSuccessful) {

                //  Create a new subscription using the same subscription plan
                $subscription = (new SubscriptionRepository($this->project))->createProjectSubscription($this->subscriber, $this->subscriptionPlan, CreatedUsingAutoBilling::YES, $billingTransaction);

            }else{

                // Update the billing schedule
                $this->updateBillingScheduleOnUnsuccessfulAttempt();

            }

            /**
             *  If we return True then this event will be removed from the queue, otherwise if we
             *  return False then this event will be added again to the queue so that we can retry
             *  this event 3 times every 24 hours before being rejected entirely.
             */

        } catch (\Throwable $th) {

            Log::error('AutoBillSubscriber Job Failed: '. $th->getMessage());

            return false;

        }
    }

    /**
     *  Update the billing schedule on a unsuccessful attempt
     *
     *  @return void
     */
    private function updateBillingScheduleOnUnsuccessfulAttempt()
    {
        //  Query the existing auto billing schedule
        $existingAutoBillingSchedule = DB::table('auto_billing_schedules')->where([
            'subscriber_id' => $this->subscriber->id,
            'subscription_plan_id' => $this->subscriptionPlan->id
        ])->first();

        $attempts = ((int) $existingAutoBillingSchedule->attempts) + 1;

        /**
         *  @var $autoBillingEnabled Whether the auto billing is enabled for future attempts
         */
        $autoBillingEnabled = $attempts < $this->subscriptionPlan->max_auto_billing_attempts;
        $totalFailedAttempts = $existingAutoBillingSchedule->total_failed_attempts + 1;
        $nextAttemptDate = now()->addDay();

        //  If auto billing on this auto billing schedule is disabled
        if($autoBillingEnabled == false) {
            $attempts = 0;
            $nextAttemptDate = null;
        }

        //  Update the existing auto billing schedule
        DB::table('auto_billing_schedules')->where([
            'subscriber_id' => $this->subscriber->id,
            'subscription_plan_id' => $this->subscriptionPlan->id
        ])->update([
            'attempts' => $attempts,
            'updated_at' => $this->billingAttemptAt,
            'next_attempt_date' => $nextAttemptDate,
            'auto_billing_enabled' => $autoBillingEnabled,
            'total_failed_attempts' => $totalFailedAttempts,
        ]);

        //  If auto billing has been disabled
        if(!$autoBillingEnabled) {

            SendAutoBillingDisabledSms::dispatch($this->project, $this->subscriber, $this->subscriptionPlan);

        }
    }
}
