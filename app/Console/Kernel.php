<?php

namespace App\Console;

use App\Jobs\StartSmsCampaigns;
use Illuminate\Support\Facades\Log;
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
        //  If we can run the SMS campaigns
        if(config('app.CAN_RUN_SMS_CAMPAIGNS')) {

            Log::channel('slack')->info('Schedule run');

            /**
             *  IMPORTANT NOTE:
             *  ---------------
             *
             *  If the job queue appears to dispatch the jobs, but no
             *  jobs are being saved on the database for processing
             *  then do the following:
             *
             *  (1) Stop running the queue service (i.e stop running the php artisan queue:work)
             *      On production run: "sudo supervisorctl stop all" to stop the supervisor queue service
             *  (2) Run: php artisan config:clear && php artisan cache:clear
             *  (3) Run: php artisan queue:work
             *  (4) Run: php artisan schedule:work on local server
             *      but use cron jobs on production server
             *  (5) Start running the queue service (i.e start running the php artisan queue:work)
             *      On production run: "sudo supervisorctl start all" to start the supervisor queue service
             *
             *  Make sure you have set the "QUEUE_CONNECTION=database" in the .env file
             *  Remember to clear the cache after changes to the .env file. Consider
             *  running the following commands to reset:
             *
             *  (1) php artisan config:cache
             *  (2) php artisan config:clear
             *  (3) php artisan cache:clear
             */

            //  Add this job to the queue for processing
            $schedule->job(new StartSmsCampaigns)->name('StartSmsCampaignsJob')->everyMinute()->withoutOverlapping();

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
