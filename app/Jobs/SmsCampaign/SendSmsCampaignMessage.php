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
    public $token;

    public $tries = 3;
    public $retryAfter = 3600;

    public function __construct(Project $project, Subscriber $subscriber, Message $message, SmsCampaign $smsCampaign, string $token)
    {
        $this->onQueue('sms');

        $this->project = $project->withoutRelations();
        $this->message = $message->withoutRelations();
        $this->smsCampaign = $smsCampaign->withoutRelations();
        $this->subscriber = $subscriber->withoutRelations();
        $this->token = $token;
    }

    public function uniqueId()
    {
        $campaignId = (isset($this->smsCampaign) && $this->smsCampaign) ? $this->smsCampaign->id : '0';
        $subscriberId = (isset($this->subscriber) && $this->subscriber) ? $this->subscriber->id : '0';

        return $campaignId . '-' . $subscriberId;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle()
    {
        // 1. LOCK VALIDATION
        $currentLock = DB::table('sms_campaign_schedules')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'sms_campaign_id' => $this->smsCampaign->id
            ])->select('processing_token', 'is_processing')->first();

        if (!$currentLock || $currentLock->processing_token !== $this->token) {
            Log::warning("Duplicate SMS Job Prevented: Subscriber {$this->subscriber->id} lock mismatch.");
            return;
        }

        $subscriberMessage = null;

        try {

            Log::info('SendSmsCampaignMessage Job Started');

            $subscriberMessage = SmsService::sendSms(
                $this->project,
                $this->subscriber,
                $this->message,
                MessageType::Content
            );

            // ALWAYS UPDATE: Push schedule forward even if SMS failed to prevent 1-minute loops
            $this->updateSmsCampaignSubscriber($subscriberMessage);

            if (!$subscriberMessage->is_successful) {
                throw new Exception('SMS provider rejected the message.');
            }

        } catch (Throwable $th) {
            Log::error('SendSmsCampaignMessage Job Failed: ' . $th->getMessage());

            // EMERGENCY BUMP: If the service crashed before creating a message object,
            // ensure the next_message_date is still updated to avoid spamming the user.
            if (!$subscriberMessage) {
                $this->emergencyUpdateSchedule();
            }

            throw $th;
        } finally {
            // 2. ATOMIC RELEASE
            DB::table('sms_campaign_schedules')
                ->where([
                    'subscriber_id' => $this->subscriber->id,
                    'sms_campaign_id' => $this->smsCampaign->id,
                    'processing_token' => $this->token
                ])->update([
                    'is_processing' => false,
                    'processing_token' => null,
                    'updated_at' => now()
                ]);

            unset($this->project, $this->subscriber, $this->message, $this->smsCampaign, $subscriberMessage);
        }
    }

    private function emergencyUpdateSchedule()
    {
        DB::table('sms_campaign_schedules')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'sms_campaign_id' => $this->smsCampaign->id
            ])->update([
                'next_message_date' => $this->smsCampaign->nextSmsCampaignMessageDate(),
                'updated_at' => now()
            ]);
    }

    private function updateSmsCampaignSubscriber($subscriberMessage)
    {
        Log::info('UpdateSmsCampaignSubscriber Started');

        $smsSentAt = $subscriberMessage->created_at;
        $isSuccessful = $subscriberMessage->is_successful;

        $existingSchedule = DB::table('sms_campaign_schedules')
            ->select('attempts', 'total_successful_attempts', 'total_failed_attempts')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'sms_campaign_id' => $this->smsCampaign->id
            ])->first();

        $nextMessageDate = $this->smsCampaign->nextSmsCampaignMessageDate();

        if ($existingSchedule) {

            $attempts = $existingSchedule->attempts + 1;

            if ($isSuccessful) {
                $totalSuccessfulAttempts = $existingSchedule->total_successful_attempts + 1;
                $totalFailedAttempts = $existingSchedule->total_failed_attempts;
            } else {
                $totalSuccessfulAttempts = $existingSchedule->total_successful_attempts;
                $totalFailedAttempts = $existingSchedule->total_failed_attempts + 1;
            }

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

        } else {

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
