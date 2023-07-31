<?php

namespace App\Console;

use App\Jobs\StartSmsCampaigns;
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
