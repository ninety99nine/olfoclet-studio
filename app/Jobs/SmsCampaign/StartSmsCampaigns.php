<?php

namespace App\Jobs\SmsCampaign;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StartSmsCampaigns implements ShouldQueue
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
            $projects = Project::canSendMessages()->whereHas('smsCampaigns', function ($query) {

                //  Make sure that the sms campaigns can send messages
                $query->canSendMessages();

            })->with(['smsCampaigns' => function($query) {

                //  Get sms campaigns that can send messages with their total batch jobs
                return $query->withCount('smsCampaignBatchJobs');

            }])->get();

            // Foreach project
            foreach ($projects as $project) {

                // If this project has the sms credentials then continue
                if ($project->hasSmsCredentials()) {

                    /**
                     * Foreach sms campaign
                     * @var SmsCampaign $smsCampaign
                     */
                    foreach ($project->smsCampaigns as $smsCampaign) {

                        //  Add this sms campaign to the queue for processing
                        StartSmsCampaign::dispatch($project, $smsCampaign, $smsCampaign->sms_campaign_batch_jobs_count);

                    }

                }
            }

        } catch (\Throwable $th) {

            Log::info('Error: '. $th->getMessage());

            // Send error report here
            //  Log::channel('slack')->error('StartSmsCampaigns Job Failed: '. $th->getMessage());

        }
    }
}
