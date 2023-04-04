<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Message;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SendSubscriptionSms implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscriber;
    public $campaign;
    public $username;
    public $password;
    public $message;
    public $sender;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber, Message $message, Campaign $campaign, $sender, $username, $password)
    {
        info('Dispatch: SendSubscriptionSms - construct()');

        $this->subscriber = $subscriber->withoutRelations();
        $this->campaign = $campaign;
        $this->username = $username;
        $this->password = $password;
        $this->message = $message;
        $this->sender = $sender;

        info($this->subscriber);
        info($this->campaign);
        info($this->username);
        info($this->password);
        info($this->message);
        info($this->sender);
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
        info('Dispatch: SendSubscriptionSms - handle()');

        //  Send the daily message to the subscriber
        try {

            //  Sleep for 5 seconds
            sleep(5);

            //  Get the recipient mobile number
            $recipient = $this->subscriber->msisdn;

            //  Get the sms message
            $message = $this->message->content;

            //  Get connection configuration information
            $ip_address = config('app.sms_config.ip_address');
            $timeout = config('app.sms_config.timeout');
            $port = config('app.sms_config.port');

            if(App::environment('production')) {
                info('PRODUCTION !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

                (new \App\Services\SmsBuilder($this->sender, $ip_address, $port, $this->username, $this->password, $timeout))
                ->setRecipient($recipient, \smpp\SMPP::TON_INTERNATIONAL)
                ->sendMessage($message);

                info('!!!!!!!!!!!!!!!!!!!!!!!! SMS SENT TO: ' . $recipient . ' - ' . $message);

            }else{
                info('NOT PRODUCTION !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

            }

            //  Find a matching subscriber message
            $matchingSubscriberMessage = DB::table('subscriber_messages')->where([
                'subscriber_id' => $this->subscriber->id,
                'message_id' => $this->message->id
            ]);

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

        } catch (\Throwable $th) {

            throw($th);

        } catch (\Exception $e) {

            throw($e);

        }
    }
}
