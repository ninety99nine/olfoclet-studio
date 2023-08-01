<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\SmsCampaignService;
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

            Log::channel('slack')->info('StartSmsCampaigns');

            //  Start the sms campaigns
            SmsCampaignService::startSmsCampaigns();

        } catch (\Throwable $th) {

            // Send error report here
            Log::channel('slack')->error('StartSmsCampaigns Job Failed: '. $th->getMessage());

            // The job failed
            return false;

        }
    }
}
