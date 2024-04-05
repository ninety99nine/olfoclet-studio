<?php

namespace App\Jobs\SmsDeliveryStatus;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StartSmsDeliveryStatusUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            //  Get projects that can send messages
            $subscriberMessages = SubscriberMessage::messageWaiting()->with(['project' => function($query) {

                //  Get sms campaigns that can send messages with their total batch jobs
                return $query->select('id', 'settings');

            }])->select('id', 'project_id')->oldest();

            //  Only query 1000 subscriber messages at a time (This helps us save memory)
            $subscriberMessages->chunk(1000, function ($chunkedSubscriberMessages) {

                //  Foreach chunked subscriber messages
                foreach($chunkedSubscriberMessages as $subscriberMessage) {

                    if( $subscriberMessage->project->hasSmsCredentials() ) {

                        //  Create a job to update the sms delivery status
                        UpdateSmsDeliveryStatus::dispatch($subscriberMessage->project, $subscriberMessage);

                    }

                }

            });

        } catch (\Throwable $th) {

            Log::error('StartSmsDeliveryStatusUpdate Job Failed: '. $th->getMessage());

        }
    }
}
