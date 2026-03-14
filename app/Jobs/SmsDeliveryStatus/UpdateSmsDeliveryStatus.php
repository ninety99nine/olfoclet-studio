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
        $this->onQueue('low');

        // CRITICAL: Strip relationships from BOTH models to prevent massive serialized queue payloads
        $this->project = $project->withoutRelations();
        $this->subscriberMessage = $subscriberMessage->withoutRelations();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        if (isset($this->subscriberMessage) && $this->subscriberMessage !== null) {
            return (string) $this->subscriberMessage->id;
        }

        // Fallback for legacy/corrupt payloads so the unique lock can be released without error
        return 'legacy-'.spl_object_id($this);
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
        if (! isset($this->project, $this->subscriberMessage) || $this->project === null || $this->subscriberMessage === null) {
            Log::warning('UpdateSmsDeliveryStatus: skipping job with missing project or subscriberMessage (legacy or corrupt payload)');

            return;
        }

        try {
            /**
             * Update the delivery status via the service
             * * @var SubscriberMessage $updatedSubscriberMessage
             */
            $updatedSubscriberMessage = SmsService::updateSmsDeliveryStatus($this->project, $this->subscriberMessage);

            if (!$updatedSubscriberMessage->delivery_status_update_is_successful) {
                // Known API issue: retry silently with release(); no throw, so nothing is logged.
                if ($this->attempts() < $this->tries) {
                    $this->release($this->retryAfter);

                    return;
                }
                // Max attempts reached; SubscriberMessage already has failure state for dashboard.
                return;
            }

            unset($this->project, $this->subscriberMessage, $updatedSubscriberMessage);

        } catch (\Throwable $th) {
            // Unexpected errors (e.g. DB, code) are still thrown so they are reported and retried.
            throw $th;
        }
    }
}
