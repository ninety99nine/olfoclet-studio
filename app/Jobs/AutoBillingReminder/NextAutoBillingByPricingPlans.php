<?php

namespace App\Jobs\AutoBillingReminder;

use Throwable;
use App\Models\Project;
use App\Services\QueueBackpressure;
use Illuminate\Bus\Queueable;
use App\Models\PricingPlan;
use App\Models\AutoBillingReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Pivots\PricingPlanAutoBillingReminder;
use App\Jobs\AutoBillingReminder\NextAutoBillingByPricingPlan;

class NextAutoBillingByPricingPlans implements ShouldQueue
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
            return;
        }

        Log::info('NextAutoBillingByPricingPlans: job started');

        try {
            /**
             * 1. REPLACED PHP SORTING WITH SQL JOIN & ORDER BY
             * Instead of fetching everything and sorting in PHP RAM, we join the
             * auto_billing_reminders table and let the database sort it instantly.
             */
            $query = PricingPlanAutoBillingReminder::select('pricing_plan_auto_billing_reminders.*')
                ->join('auto_billing_reminders', 'auto_billing_reminders.id', '=', 'pricing_plan_auto_billing_reminders.auto_billing_reminder_id')
                ->whereHas('project', function($query) {
                    return $query->canAutoBill();
                })
                ->whereHas('pricingPlan', function($query) {
                    return $query->active()->nonFolder()->canAutoBill();
                })
                ->with(['project', 'autoBillingReminder', 'pricingPlan' => function($query) {
                    $query->withCount('autoBillingReminderJobBatches');
                }])
                ->orderBy('auto_billing_reminders.hours', 'desc');

            /**
             * 2. REPLACED ->get() with ->chunk(100)
             * We use chunk() here because we are enforcing a strict custom ORDER BY
             * from a joined table, which chunkById() cannot easily do. Since this is
             * a settings/mapping table, it is small enough that OFFSET chunking is perfectly safe.
             */
            $query->chunk(100, function ($pricingPlanAutoBillingReminders) {

                // Foreach chunked reminder setting
                foreach ($pricingPlanAutoBillingReminders as $pricingPlanAutoBillingReminder) {

                    $pricingPlan = $pricingPlanAutoBillingReminder->pricingPlan;

                    // Only process if there is a reminder message set
                    if (!empty($pricingPlan->next_auto_billing_reminder_sms_message)) {

                        $project = $pricingPlanAutoBillingReminder->project;
                        $autoBillingReminder = $pricingPlanAutoBillingReminder->autoBillingReminder;
                        $batchesCount = $pricingPlan->auto_billing_reminder_job_batches_count ?? 0;

                        /**
                         * 3. CRITICAL FIX: withoutRelations()
                         * Strip the heavy eager-loaded relationships BEFORE dispatching
                         * to keep the Redis/Database queue payload tiny.
                         */
                        NextAutoBillingByPricingPlan::dispatch(
                            $project->withoutRelations(),
                            $pricingPlan->withoutRelations(),
                            $autoBillingReminder->withoutRelations(),
                            $batchesCount
                        );

                    }
                }

                // 4. Free up memory for the daemon worker before pulling the next 100 rows
                unset($pricingPlanAutoBillingReminders);

            });

        } catch (Throwable $th) {
            throw $th;
        }

        Log::info('NextAutoBillingByPricingPlans: job completed');
    }
}
