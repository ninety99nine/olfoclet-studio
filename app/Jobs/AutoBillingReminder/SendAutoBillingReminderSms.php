<?php

namespace App\Jobs\AutoBillingReminder;

use App\Models\Project;
use App\Enums\MessageType;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\DB;
use App\Models\AutoBillingReminder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SendAutoBillingReminderSms implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $subscriber;
    public $pricingPlan;
    public $autoBillingReminder;

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
    public function __construct(Project $project, Subscriber $subscriber, PricingPlan $pricingPlan, AutoBillingReminder $autoBillingReminder)
    {
        $this->onQueue('sms');

        // CRITICAL: Strip relationships from ALL models to prevent massive serialized queue payloads
        $this->project = $project->withoutRelations();
        $this->subscriber = $subscriber->withoutRelations();
        $this->pricingPlan = $pricingPlan->withoutRelations();
        $this->autoBillingReminder = $autoBillingReminder->withoutRelations();
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

            // Early exit to keep code clean and prevent unnecessary processing
            if (empty($this->pricingPlan->next_auto_billing_reminder_sms_message)) {
                return;
            }

            // Set the message type
            $messageType = MessageType::AutoBillingReminder;

            /**
             * @var Subscription $subscriptionWithFurthestEndAt
             */
            $subscriptionWithFurthestEndAt = $this->subscriber->subscriptionWithFurthestEndAt()
                ->where('pricing_plan_id', $this->pricingPlan->id)
                ->first();

            /**
             * Set the next auto billing reminder sms message
             * @var string $messageContent
             */
            $messageContent = $this->pricingPlan->craftNextAutoBillingReminderSmsMessage($subscriptionWithFurthestEndAt);

            /**
             * Send the SMS via the service
             * @var SubscriberMessage $subscriberMessage
             */
            $subscriberMessage = SmsService::sendSms($this->project, $this->subscriber, $messageContent, $messageType);

            // If the SMS was successfully sent
            if ($subscriberMessage->is_successful) {

                $hours = $this->autoBillingReminder->hours;
                $data = [];

                if ($hours == 1) {
                    $data = ['reminded_one_hour_before_at' => $subscriberMessage->created_at];
                } else if ($hours == 6) {
                    $data = ['reminded_six_hours_before_at' => $subscriberMessage->created_at];
                } else if ($hours == 12) {
                    $data = ['reminded_twelve_hours_before_at' => $subscriberMessage->created_at];
                } else if ($hours == 24) {
                    $data = ['reminded_twenty_four_hours_before_at' => $subscriberMessage->created_at];
                } else if ($hours == 48) {
                    $data = ['reminded_forty_eight_hours_before_at' => $subscriberMessage->created_at];
                } else if ($hours == 72) {
                    $data = ['reminded_seventy_two_hours_before_at' => $subscriberMessage->created_at];
                }

                // Only update if $data was actually populated
                if (!empty($data)) {
                    DB::table('auto_billing_schedules')
                        ->where('subscriber_id', $this->subscriber->id)
                        ->where('pricing_plan_id', $this->pricingPlan->id)
                        ->update($data);
                }

            } else {
                if ($this->attempts() < $this->tries) {
                    $this->release($this->retryAfter);

                    return;
                }

                return;
            }

            unset($this->project, $this->subscriber, $this->pricingPlan, $this->autoBillingReminder, $subscriberMessage, $subscriptionWithFurthestEndAt);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
