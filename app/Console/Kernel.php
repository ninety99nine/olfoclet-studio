<?php

namespace App\Console;

use App\Jobs\SmsCampaign\StartSmsCampaigns;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\BillingReport\StartCreatingBillingReports;
use App\Jobs\AutoBilling\AutoBillingByPricingPlans;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SmsDeliveryStatus\StartSmsDeliveryStatusUpdate;
use App\Jobs\AutoBillingReminder\NextAutoBillingByPricingPlans;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\HandleServerErrors::class
    ];

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
         *  If the job queue appears to dispatch the jobs, but no jobs are being
         *  saved on the database for processing then do the following:
         *
         *  Make sure you have set the "QUEUE_CONNECTION=database" in the .env file.
         *  Remember to clear the cache after changes to the .env file. Consider
         *  running the following commands to reset:
         *
         *  ✅ LOCAL DEVELOPMENT:
         *
         *  stop running the php artisan queue:work
         *  sudo php artisan config:cache
         *  sudo php artisan config:clear
         *  sudo php artisan cache:clear
         *  start running the php artisan queue:work
         *
         *  ✅ PRODUCTION (Supervisor setup):
         *
         *  sudo supervisorctl stop all
         *  sudo php artisan config:cache
         *  sudo php artisan config:clear
         *  sudo php artisan cache:clear
         *  sudo supervisorctl reread
         *  sudo supervisorctl start all
         */

        //  If we can create Billing Reports
        if(config('app.CAN_CREATE_BILLING_REPORTS')) {

            //  Add this job to the queue for processing
            $schedule->job(new StartCreatingBillingReports)->name('StartCreatingBillingReports')->hourly()->between('00:00', '06:00')->withoutOverlapping();

        }

        //  If we can run Auto Billing
        if(config('app.CAN_RUN_AUTO_BILLING')) {

            //  Add this job to the queue for processing
            $schedule->job(new AutoBillingByPricingPlans)->name('AutoBillingByPricingPlansJob')->everyMinute()->withoutOverlapping();

            //  Add this job to the queue for processing
            $schedule->job(new NextAutoBillingByPricingPlans)->name('NextAutoBillingByPricingPlansJob')->everyMinute()->withoutOverlapping();

        }

        //  If we can run SMS campaigns
        if(config('app.CAN_RUN_SMS_CAMPAIGNS')) {

            //  Add this job to the queue for processing
            $schedule->job(new StartSmsCampaigns)->name('StartSmsCampaignsJob')->everyMinute()->withoutOverlapping();

        }

        if(config('app.CAN_RUN_AUTO_BILLING') || config('app.CAN_RUN_SMS_CAMPAIGNS')) {

            //  Add this job to the queue for processing
            $schedule->job(new StartSmsDeliveryStatusUpdate)->name('StartSmsDeliveryStatusUpdateJob')->everyMinute()->withoutOverlapping();

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
