<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\SmsCampaignService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StartSmsCampaigns implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('Event Handle: SmsCampaignService::startSmsCampaigns()');

        //  Start the sms campaigns
        SmsCampaignService::startSmsCampaigns();
    }
}
