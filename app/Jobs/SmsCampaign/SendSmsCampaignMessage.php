<?php

namespace App\Jobs\SmsCampaign;

use Exception;
use Throwable;
use App\Models\Message;
use App\Models\Project;
use App\Enums\MessageType;
use App\Models\Subscriber;
use App\Models\SmsCampaign;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SendSmsCampaignMessage implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $message;
    public $subscriber;
    public $smsCampaign;

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
    public function __construct(Project $project, Subscriber $subscriber, Message $message, SmsCampaign $smsCampaign)
    {
        $this->onQueue('sms');

        // Strip relationships from ALL models to prevent massive serialized queue payloads
        $this->project = $project->withoutRelations();
        $this->message = $message->withoutRelations();
        $this->smsCampaign = $smsCampaign->withoutRelations();
        $this->subscriber = $subscriber->withoutRelations();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        $campaignId = (isset($this->smsCampaign) && $this->smsCampaign) ? $this->smsCampaign->id : '0';
        $subscriberId = (isset($this->subscriber) && $this->subscriber) ? $this->subscriber->id : '0';

        return $campaignId . '-' . $subscriberId;
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

            // Set the message type
            $messageType = MessageType::Content;

            /**
             * Send the SMS
             * @var SubscriberMessage $subscriberMessage
             */
            $subscriberMessage = SmsService::sendSms(
                $this->project,
                $this->subscriber,
                $this->message,
                $messageType
            );

            // Update the sms campaign schedule
            $this->updateSmsCampaignSubscriber($subscriberMessage);

            /**
             * CRITICAL FIX:
             * Returning 'false' does NOT trigger Laravel's retry mechanism. It assumes success and deletes the job.
             * To utilize $tries = 3 and $retryAfter = 3600, we MUST throw an exception when it fails.
             */
            if (!$subscriberMessage->is_successful) {
                throw new Exception('SMS Campaign Message sending failed or was rejected by the provider.');
            }

            // Explicitly free memory for the daemon worker before it picks up the next job
            unset($this->project, $this->subscriber, $this->message, $this->smsCampaign, $subscriberMessage);

        } catch (Throwable $th) {

            Log::error('SendSmsCampaignMessage Job Failed: ' . $th->getMessage());

            // Re-throw the exception so the queue worker knows it crashed and schedules the retry
            throw $th;

        }
    }

    /**
     * Update the sms campaign schedule record.
     *
     * @param SubscriberMessage $subscriberMessage
     * @return void
     */
    private function updateSmsCampaignSubscriber($subscriberMessage)
    {
        // Set the smsSentAt datetime
        $smsSentAt = $subscriberMessage->created_at;

        // Determine success state
        $isSuccessful = $subscriberMessage->is_successful;

        /**
         * Select ONLY the columns we actually need to calculate the updates.
         * Grabbing the entire row into memory for thousands of jobs creates memory bloat.
         */
        $existingSchedule = DB::table('sms_campaign_schedules')
            ->select('attempts', 'total_successful_attempts', 'total_failed_attempts')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'sms_campaign_id' => $this->smsCampaign->id
            ])->first();

        // Calculate the next sms campaign message date
        $nextMessageDate = $this->smsCampaign->nextSmsCampaignMessageDate();

        // If the matching sms campaign schedule exists
        if ($existingSchedule) {

            $attempts = $existingSchedule->attempts + 1;

            if ($isSuccessful) {
                $totalSuccessfulAttempts = $existingSchedule->total_successful_attempts + 1;
                $totalFailedAttempts = $existingSchedule->total_failed_attempts;
            } else {
                $totalSuccessfulAttempts = $existingSchedule->total_successful_attempts;
                $totalFailedAttempts = $existingSchedule->total_failed_attempts + 1;
            }

            // Update the matching sms campaign schedule
            DB::table('sms_campaign_schedules')->where([
                'subscriber_id' => $this->subscriber->id,
                'sms_campaign_id' => $this->smsCampaign->id
            ])->update([
                'attempts'                  => $attempts,
                'total_successful_attempts' => $totalSuccessfulAttempts,
                'total_failed_attempts'     => $totalFailedAttempts,
                'next_message_date'         => $nextMessageDate,
                'updated_at'                => $smsSentAt,
            ]);

        // If the matching sms campaign schedule does not exist
        } else {

            // Create the sms campaign schedule record
            DB::table('sms_campaign_schedules')->insert([
                'project_id'                => $this->message->project_id,
                'sms_campaign_id'           => $this->smsCampaign->id,
                'subscriber_id'             => $this->subscriber->id,
                'attempts'                  => 1,
                'total_successful_attempts' => $isSuccessful ? 1 : 0,
                'total_failed_attempts'     => $isSuccessful ? 0 : 1,
                'next_message_date'         => $nextMessageDate,
                'created_at'                => $smsSentAt,
                'updated_at'                => $smsSentAt,
            ]);

        }
    }
}
