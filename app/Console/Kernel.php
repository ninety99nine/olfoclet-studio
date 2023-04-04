<?php

namespace App\Console;

use App\Models\Project;
use App\Models\Campaign;
use App\Jobs\StartSmsCampaign;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         *  IMPORTANT NOTE:
         *  ---------------
         *
         *  If the job queue appears to dispatch the jobs, but no
         *  jobs are being saved on the databse for processing
         *  then do the following:
         *
         *  (1) Stop running the queue service (i.e stop running the php artisan queue:work)
         *  (2) Run: php artisan config:clear && php artisan cache:clear
         *  (3) Run: php artisan queue:work
         *  (4) Run: php artisan schedule:work on local server
         *      but use cron jobs on production server
         *
         *  Make sure you have set the "QUEUE_CONNECTION=database" in the .env file
         *  Remember to clear the cache after changes to the .env file. Consider
         *  running the following commands to reset:
         *
         *  (1) php artisan config:cache
         *  (2) php artisan config:clear
         *  (3) php artisan cache:clear
         */
        try {

            $schedule->call(function () {

                info('schedule->call');

                //  Get projects that can send messages and have campaigns
                $projects = Project::where('projects.can_send_messages', '1')->has('campaignsThatCanSendMessages')->with(['campaignsThatCanSendMessages' => function($query) {

                    //  Get campaigns that can send messagse with their total batch jobs
                    return $query->withCount('campaignBatchJobs');

                }])->get();

                info('Total projects: '. count($projects));

                //  Foreach project
                foreach($projects as $project) {

                    info('hasSmsCredentials:'. ($project->hasSmsCredentials() ? 'Yes' : 'No'));

                    //  If this project has the sms credentials then continue
                    if( $project->hasSmsCredentials() ) {

                        /**
                         *  Foreach campaign
                         *  @var Campaign $campaign
                         */
                        foreach($project->campaigns as $campaign) {

                            info('Campaign ID:'. $campaign->id);
                            info('Campaign Name:'. $campaign->name);

                            StartSmsCampaign::dispatch($project, $campaign)->onConnection('database');

                        }

                    }

                }

            //  Start sending from 06:00 to 18:00
            })->name('Send campaign messages')->everySecond()/* ->between('6:00', '18:00') */->withoutOverlapping();

        } catch (\Exception $e) {

            //  Log::error('ERROR sending campaign: '.$e->getMessage());

        }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
