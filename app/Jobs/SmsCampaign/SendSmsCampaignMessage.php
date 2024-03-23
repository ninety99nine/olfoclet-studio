<?php

namespace App\Jobs\SmsCampaign;

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
        $this->project = $project;
        $this->message = $message;
        $this->smsCampaign = $smsCampaign;
        $this->subscriber = $subscriber->withoutRelations();
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
        return $this->smsCampaign->id.'-'.$this->subscriber->id;
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            //  Set the message type
            $messageType = MessageType::Content;

            /**
             *  @var SubscriberMessage $subscriberMessage The SubscriberMessage instance
             */
            $subscriberMessage = SmsService::sendSms($this->project, $this->subscriber, $this->message, $messageType);

            /**
             *  @var bool $isSuccessful Whether the sms was sent successfully
             */
            $isSuccessful = $subscriberMessage->is_successful;

            if($isSuccessful) {

                // Update the sms campaign subscriber
                $this->updateSmsCampaignSubscriber($subscriberMessage);

            }else{

                Log::channel('slack')->error('Message: "'. $this->message->content.'" failed to send to '.$this->subscriber->msisdn);

            }

            /**
             *  Return True or False as an indication for whether the SMS sent successfully or not.
             *  If we return True then this event will be removed from the queue, otherwise if we
             *  return False then this event will be added again to the queue so that we can retry
             *  this event 3 times every 1 hour before being rejected entirely.
             */
            return $isSuccessful;

        } catch (\Throwable $th) {

            Log::info('Error: '. $th->getMessage());

            // Send error report here
            //  Log::channel('slack')->error('SendSmsCampaignMessage Job Failed: '. $th->getMessage());

            return false;

        }
    }

    /**
     * Update the sms campaign subscriber record.
     *
     *  @var SubscriberMessage $subscriberMessage
     *
     * @return void
     */
    private function updateSmsCampaignSubscriber($subscriberMessage)
    {
        //  Set the smsSentAt datetime
        $smsSentAt = $subscriberMessage->created_at;

        //  Find a matching sms campaign subscriber
        $matchingSmsCampaignSubscriber = DB::table('sms_campaign_subscriber')->where([
            'subscriber_id' => $this->subscriber->id,
            'sms_campaign_id' => $this->smsCampaign->id
        ]);

        //  Calculate the next sms campaign message date
        $nextSmsCampaignMessageDate = $this->smsCampaign->nextSmsCampaignMessageDate();

        //  If the matching sms campaign subscriber exists
        if( $instance = $matchingSmsCampaignSubscriber->first() ) {

            $sendSmsCount = ((int) $instance->sent_sms_count) + 1;

            //  Update the matching sms campaign subscriber
            $matchingSmsCampaignSubscriber->update([
                'next_message_date' => $nextSmsCampaignMessageDate,
                'sent_sms_count' => $sendSmsCount,
                'updated_at' => $smsSentAt
            ]);

        //  If the matching sms campaign subscriber does not exist
        }else{

            //  Create the sms campaign subscriber record
            DB::table('sms_campaign_subscriber')->insert([
                'next_message_date' => $nextSmsCampaignMessageDate,
                'sms_campaign_id' => $this->smsCampaign->id,
                'project_id' => $this->message->project_id,
                'subscriber_id' => $this->subscriber->id,
                'created_at' => $smsSentAt,
                'updated_at' => $smsSentAt,
                'sent_sms_count' => 1
            ]);

        }
    }
}
