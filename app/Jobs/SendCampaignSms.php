<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Message;
use App\Models\Campaign;
use App\Models\Subscriber;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SendCampaignSms implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;
    public $campaign;
    public $subscriber;
    public $senderName;
    public $senderNumber;
    public $clientCredentials;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 24;

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
    public function __construct(Subscriber $subscriber, Message $message, Campaign $campaign, $senderName, $senderNumber, $clientCredentials)
    {
        $this->message = $message;
        $this->campaign = $campaign;
        $this->senderName = $senderName;
        $this->senderNumber = $senderNumber;
        $this->clientCredentials = $clientCredentials;
        $this->subscriber = $subscriber->withoutRelations();
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

            Log::channel('slack')->info('Event Handle SendCampaignSms()');

            //  Return true if the SMS sent and false if the SMS failed to send
            $status = SmsService::sendSms($this->message, $this->subscriber, $this->senderName, $this->senderNumber, $this->clientCredentials);

            Log::channel('slack')->info('Sms sent status'.$status);

            if($status) {

                // Update the subscriber message
                $this->updateSubscriberMessage();

                // Update the campaign subscriber
                $this->updateCampaignSubscriber();

            }

            /**
             *  Return True or False as an indication for whether the SMS sent successfully or not.
             *  If we return True then this event will be removed from the queue, otherwise if we
             *  return False then this event will be added again to the queue so that we can retry
             *  this event 24 times every 1 hour before being rejected entirely.
             */
            return $status;

        } catch (\Throwable $th) {

            // Send error report here
            Log::channel('slack')->error('SendCampaignSms Job Failed: '. $th->getMessage());

            // The job failed
            return false;

        }
    }

    /**
     * Update the subscriber message record.
     *
     * @return void
     */
    private function updateSubscriberMessage()
    {
        //  Find a matching subscriber message
        $matchingSubscriberMessage = DB::table('subscriber_messages')->where([
            'subscriber_id' => $this->subscriber->id,
            'message_id' => $this->message->id
        ])->latest();

        //  If the matching subscriber message exists
        if( $instance = $matchingSubscriberMessage->first() ) {

            $sendSmsCount = ((int) $instance->sent_sms_count) + 1;

            //  Update the matching subscriber message
            $matchingSubscriberMessage->update([
                'sent_sms_count' => $sendSmsCount,
                'updated_at' => Carbon::now()
            ]);

        //  If the matching subscriber message does not exist
        }else{

            //  Create the subscriber message record
            DB::table('subscriber_messages')->insert([
                'project_id' => $this->message->project_id,
                'subscriber_id' => $this->subscriber->id,
                'message_id' => $this->message->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'sent_sms_count' => 1
            ]);

        }
    }

    /**
     * Update the campaign subscriber record.
     *
     * @return void
     */
    private function updateCampaignSubscriber()
    {
        //  Find a matching campaign subscriber
        $matchingCampaignSubscriber = DB::table('campaign_subscriber')->where([
            'subscriber_id' => $this->subscriber->id,
            'campaign_id' => $this->campaign->id
        ]);

        //  If the matching campaign subscriber exists
        if( $instance = $matchingCampaignSubscriber->first() ) {

            $sendSmsCount = ((int) $instance->sent_sms_count) + 1;

            //  Update the matching campaign subscriber
            $matchingCampaignSubscriber->update([
                'next_message_date' => $this->campaign->nextCampaignSmsMessageDate(),
                'sent_sms_count' => $sendSmsCount,
                'updated_at' => Carbon::now()
            ]);

        //  If the matching campaign subscriber does not exist
        }else{

            //  Create the campaign subscriber record
            DB::table('campaign_subscriber')->insert([
                'next_message_date' => $this->campaign->nextCampaignSmsMessageDate(),
                'project_id' => $this->message->project_id,
                'subscriber_id' => $this->subscriber->id,
                'campaign_id' => $this->campaign->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'sent_sms_count' => 1
            ]);

        }
    }
}
