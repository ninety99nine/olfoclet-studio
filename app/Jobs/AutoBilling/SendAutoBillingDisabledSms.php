<?php

namespace App\Jobs\AutoBilling;

use App\Models\Project;
use App\Enums\MessageType;
use App\Models\Subscriber;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SendAutoBillingDisabledSms implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $subscriber;
    public $subscriptionPlan;

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
    public $retryAfter = 3600; // 3600 seconds = 1 hour

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

            //  Set the message type
            $messageType = MessageType::AutoBillingDisabled;

            /**
             *  Set the auto billing disabled sms message
             *
             *  @var string $messageContent
             */
            $messageContent = $this->subscriptionPlan->craftAutoBillingDisabledSmsMessage();

            /**
             *  @var SubscriberMessage $subscriberMessage The SubscriberMessage instance
             */
            $subscriberMessage = SmsService::sendSms($this->project, $this->subscriber, $messageContent, $messageType);

            /**
             *  @var bool $isSuccessful Whether the sms was sent successfully
             */
            $isSuccessful = $subscriberMessage->is_successful;

            /**
             *  Return True or False as an indication for whether the SMS sent successfully or not.
             *  If we return True then this event will be removed from the queue, otherwise if we
             *  return False then this event will be added again to the queue so that we can retry
             *  this event 3 times every 1 hour before being rejected entirely.
             */
            return $isSuccessful;

        } catch (\Throwable $th) {

            Log::error('SendAutoBillingDisabledSms Job Failed: '. $th->getMessage());

            return false;

        }
    }
}
