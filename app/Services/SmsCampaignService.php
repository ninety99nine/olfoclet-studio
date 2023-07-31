<?php

namespace App\Services;

use App\Models\Project;
use App\Jobs\StartSmsCampaign;
use Illuminate\Database\Eloquent\Collection;

class SmsCampaignService
{
    /**
     *  Start the project campaigns of each specified project
     *
     *  @return Collection<Project>
     */
    public static function startSmsCampaigns()
    {
        //  Get the project that can run campaigns
        $projects = self::getProjectsForSmsCampaigns();

        // Foreach project
        foreach ($projects as $project) {

            // If this project has the sms credentials then continue
            if ($project->hasSmsCredentials()) {

                /**
                 * Foreach campaign
                 * @var Campaign $campaign
                 */
                foreach ($project->campaigns as $campaign) {

                    //  Add this SMS campaign to the queue for processing
                    StartSmsCampaign::dispatch($project, $campaign, $campaign->campaign_batch_jobs_count);

                }

            }
        }
    }

    /**
     *  Return the projects that can send messages with their
     *  associated campaigns that can send messages
     *
     *  @return Collection<Project>
     */
    public static function getProjectsForSmsCampaigns()
    {
        //  Make sure that the projects can send messages
        return Project::canSendMessages()->whereHas('campaigns', function ($query) {

            //  Make sure that the campaigns can send messages
            $query->canSendMessages();

        })->with(['campaigns' => function($query) {

            //  Get campaigns that can send messages with their total batch jobs
            return $query->withCount('campaignBatchJobs');

        }])->get();
    }
}
