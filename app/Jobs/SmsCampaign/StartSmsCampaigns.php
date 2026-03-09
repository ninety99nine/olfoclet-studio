<?php

namespace App\Jobs\SmsCampaign;

use Throwable;
use App\Models\Project;
use App\Services\QueueBackpressure;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Contracts\Queue\ShouldBeUnique; // Removed as it wasn't implemented

class StartSmsCampaigns implements ShouldQueue
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
            Log::warning('StartSmsCampaigns: skipped (queue backpressure)', [
                'pending' => QueueBackpressure::getPendingCount(),
            ]);
            return;
        }

        Log::info('StartSmsCampaigns: job started');

        try {
            /**
             * Use chunk() (not chunkById) because chunkById can abort when the query
             * uses whereHas. canSendMessages() in with prevents pulling inactive campaigns.
             */
            Project::canSendMessages()
                ->whereHas('smsCampaigns', function ($query) {
                    $query->canSendMessages();
                })
                ->with(['smsCampaigns' => function($query) {
                    // CRITICAL FIX: Actually scope the eager loaded models!
                    return $query->canSendMessages()->withCount('smsCampaignBatchJobs');
                }])
                ->orderBy('id')
                ->chunk(100, function ($projects) {

                    // Foreach project in the current chunk of 100
                    foreach ($projects as $project) {

                        // If this project has the sms credentials then continue
                        if ($project->hasSmsCredentials()) {

                            // Foreach active sms campaign
                            foreach ($project->smsCampaigns as $smsCampaign) {

                                // Safely handle potential null counts
                                $batchJobsCount = $smsCampaign->sms_campaign_batch_jobs_count ?? 0;

                                /**
                                 * Strip the heavy relationships before sending to Redis/Database.
                                 * If you do not do this, the Project object serializes ALL of its
                                 * eager-loaded smsCampaigns into the payload of every single child job.
                                 */
                                StartSmsCampaign::dispatch(
                                    $project->withoutRelations(),
                                    $smsCampaign->withoutRelations(),
                                    $batchJobsCount
                                );

                            }

                        }
                    }

                });

        } catch (Throwable $th) {

            Log::error('StartSmsCampaigns Job Failed', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
                // Removed getTraceAsString() to prevent massive log file bloat during a crash
            ]);

            // Re-throw so the queue knows the Master Job failed
            throw $th;
        }

        Log::info('StartSmsCampaigns: job completed');
    }
}
