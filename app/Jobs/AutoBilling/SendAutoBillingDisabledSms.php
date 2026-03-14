<?php

namespace App\Jobs\AutoBilling;

use App\Models\Project;
use App\Enums\MessageType;
use App\Models\Subscriber;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use App\Models\PricingPlan;
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
    public $pricingPlan;

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
    public function __construct(Project $project, Subscriber $subscriber, PricingPlan $pricingPlan)
    {
        $this->onQueue('sms');

        // Strip relationships to prevent heavy payloads in the queue
        $this->project = $project->withoutRelations();
        $this->subscriber = $subscriber->withoutRelations();
        $this->pricingPlan = $pricingPlan->withoutRelations();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->pricingPlan->id . '-' . $this->subscriber->id;
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Early exit if there is no message to send
            if (empty($this->pricingPlan->auto_billing_disabled_sms_message)) {
                return;
            }

            // Set the message type and craft the content
            $messageType = MessageType::AutoBillingDisabled;
            $messageContent = $this->pricingPlan->craftAutoBillingDisabledSmsMessage();

            /**
             * Send the SMS via the service
             *
             * @var SubscriberMessage $subscriberMessage
             */
            $subscriberMessage = SmsService::sendSms(
                $this->project,
                $this->subscriber,
                $messageContent,
                $messageType
            );

            if (!$subscriberMessage->is_successful) {
                if ($this->attempts() < $this->tries) {
                    $this->release($this->retryAfter);

                    return;
                }

                return;
            }

            unset($this->project, $this->subscriber, $this->pricingPlan, $subscriberMessage);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
