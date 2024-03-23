<?php

namespace App\Jobs\SmsDeliveryStatus;

use App\Models\Project;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class UpdateSmsDeliveryStatus implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $subscriberMessage;

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
     * @param Project $project
     * @param SubscriberMessage $subscriberMessage
     * @return void
     */
    public function __construct(Project $project, SubscriberMessage $subscriberMessage)
    {
        $this->project = $project;
        $this->subscriberMessage = $subscriberMessage->withoutRelations();
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
        return $this->subscriberMessage->id;
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

            /**
             *  @var SubscriberMessage $subscriberMessage
             */
            $subscriberMessage = SmsService::updateSmsDeliveryStatus($this->project, $this->subscriberMessage);

            /**
             *  Return True or False as an indication for whether the SMS delivery status update was successful or not.
             *  If we return True then this event will be removed from the queue, otherwise if we return False then
             *  this event will be added again to the queue so that we can retry this event 3 times every 1 hour
             *  before being rejected entirely.
             */
            return $subscriberMessage->delivery_status_update_is_successful;

        } catch (\Throwable $th) {

            Log::info('Error: '. $th->getMessage());

            //  Send error report here
            //  Log::channel('slack')->error('UpdateSmsDeliveryStatus Job Failed: '. $th->getMessage());

            return false;

        }
    }
}
