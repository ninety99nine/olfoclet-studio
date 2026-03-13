<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Jobs\SmsCampaign\StartSmsCampaigns;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\AutoBilling\AutoBillingByPricingPlans;
use App\Jobs\BillingReport\StartCreatingBillingReports;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SmsDeliveryStatus\StartSmsDeliveryStatusUpdate;
use App\Jobs\AutoBillingReminder\NextAutoBillingByPricingPlans;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\HandleServerErrors::class,
        Commands\TestSmsDeliveryStatus::class,
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
         * IMPORTANT NOTE:
         * ---------------
         *
         * If the job queue appears to dispatch the jobs, but no jobs are being
         * saved on the database for processing then do the following:
         *
         * Make sure you have set the "QUEUE_CONNECTION=database" in the .env file.
         * Remember to clear the cache after changes to the .env file. Consider
         * running the following commands to reset:
         *
         * ✅ LOCAL DEVELOPMENT:
         *
         * stop running the php artisan queue:work
         * sudo php artisan config:cache
         * sudo php artisan config:clear
         * sudo php artisan cache:clear
         * start running the php artisan queue:work
         *
         * ✅ PRODUCTION (Supervisor setup):
         *
         * sudo supervisorctl stop all
         * sudo php artisan config:cache
         * sudo php artisan config:clear
         * sudo php artisan cache:clear
         * sudo supervisorctl reread
         * sudo supervisorctl start all
         */

        // ------------------------------------------------------------------
        // PREVENT DATABASE BLOAT FROM GROWING TO HUNDREDS OF MEGABYTES
        // ------------------------------------------------------------------

        // 1. Added --unfinished flag. Without this, failed/stuck batches stay in the DB forever.
        $schedule->command('queue:prune-batches --hours=24 --unfinished=72')->daily();

        $schedule->command('queue:prune-failed --hours=72')->daily();

        // 2. Custom Pruning for your specific Job Batch tracking tables
        $schedule->call(function () {
            $cutoff = Carbon::now()->subDays(3); // Keep 3 days of history for debugging

            DB::table('auto_billing_job_batches')->where('created_at', '<', $cutoff)->delete();
            DB::table('auto_billing_reminder_job_batches')->where('created_at', '<', $cutoff)->delete();
            DB::table('sms_campaign_job_batches')->where('created_at', '<', $cutoff)->delete();

        })->daily()->name('PruneCustomJobBatches')->withoutOverlapping();

        // Clear stale Auto Billing locks (4-hour window)
        $schedule->command('billing:clear-stale-locks')
            ->name('ClearStaleBillingLocks')
            ->withoutOverlapping()
            ->hourly();

        // Clear stale SMS Campaign locks (4-hour window)
        $schedule->command('sms:clear-stale-locks')
            ->name('ClearStaleSmsLocks')
            ->withoutOverlapping()
            ->hourly();

        //  If we can create Billing Reports
        if(config('app.CAN_CREATE_BILLING_REPORTS')) {
            $schedule->job(new StartCreatingBillingReports)
                ->name('StartCreatingBillingReports')
                ->hourly()
                ->between('00:00', '06:00')
                ->withoutOverlapping();
        }

        //  If we can run Auto Billing
        if(config('app.CAN_RUN_AUTO_BILLING')) {
            $schedule->job(new AutoBillingByPricingPlans)
                ->name('AutoBillingByPricingPlansJob')
                ->everyMinute()
                ->withoutOverlapping();

            $schedule->job(new NextAutoBillingByPricingPlans)
                ->name('NextAutoBillingByPricingPlansJob')
                ->everyMinute()
                ->withoutOverlapping();
        }

        //  If we can run SMS campaigns
        if(config('app.CAN_RUN_SMS_CAMPAIGNS')) {
            $schedule->job(new StartSmsCampaigns)
                ->name('StartSmsCampaignsJob')
                ->everyMinute()
                ->withoutOverlapping();
        }

        if(config('app.CAN_RUN_AUTO_BILLING') || config('app.CAN_RUN_SMS_CAMPAIGNS')) {
            $schedule->job(new StartSmsDeliveryStatusUpdate)
                ->name('StartSmsDeliveryStatusUpdateJob')
                ->everyMinute()
                ->withoutOverlapping();
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
