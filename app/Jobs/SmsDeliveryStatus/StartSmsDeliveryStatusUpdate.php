<?php

namespace App\Jobs\SmsDeliveryStatus;

use Throwable;
use App\Services\QueueBackpressure;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartSmsDeliveryStatusUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * Queue is set in constructor to avoid PHP 8.2+ conflict with Queueable trait's $queue property.
     */
    public function __construct()
    {
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! QueueBackpressure::canDispatch()) {
            return;
        }

        Log::info('StartSmsDeliveryStatusUpdate: job started');

        try {

            /**
             * Build the query for subscriber messages that are waiting.
             * * Note: oldest() has been removed because chunkById() inherently orders
             * by the ID column ascending, which achieves the exact same chronological
             * result but with vastly superior database performance.
             */
            $query = SubscriberMessage::messageWaiting()
                ->with(['project' => function($query) {
                    return $query->select('id', 'settings');
                }])
                ->select('id', 'project_id');

            /**
             * 1. Replaced chunk() with chunkById() to prevent massive OFFSET queries that
             * freeze the database when dealing with millions of subscriber messages.
             */
            $query->chunkById(1000, function ($chunkedSubscriberMessages) {

                // Foreach chunked subscriber messages
                foreach ($chunkedSubscriberMessages as $subscriberMessage) {

                    // Ensure the project exists and has credentials
                    if ($subscriberMessage->project && $subscriberMessage->project->hasSmsCredentials()) {

                        /**
                         * 2. CRITICAL FIX: withoutRelations()
                         * Since you eager-loaded 'project' onto the $subscriberMessage, passing the
                         * raw $subscriberMessage into the child job will serialize the Project
                         * model inside it. Passing the $project explicitly means it gets serialized TWICE.
                         * * Stripping relationships keeps the Redis/DB queue payload tiny.
                         */
                        UpdateSmsDeliveryStatus::dispatch(
                            $subscriberMessage->project->withoutRelations(),
                            $subscriberMessage->withoutRelations()
                        );

                    }

                }

                // 3. Explicitly free memory for the daemon worker before fetching the next 1000 records
                unset($chunkedSubscriberMessages);

            }, 'id');

        } catch (Throwable $th) {

            Log::error('StartSmsDeliveryStatusUpdate Job Failed', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
                // Removed getTraceAsString() to prevent log file bloat
            ]);

            // Re-throw so the queue worker registers the failure
            throw $th;
        }

        Log::info('StartSmsDeliveryStatusUpdate: job completed');
    }
}
