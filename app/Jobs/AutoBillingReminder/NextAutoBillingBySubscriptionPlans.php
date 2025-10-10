<?php

namespace App\Jobs\AutoBillingReminder;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\PricingPlan;
use App\Models\AutoBillingReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\Pivots\PricingPlanAutoBillingReminder;
use App\Jobs\AutoBillingReminder\NextAutoBillingByPricingPlan;

class NextAutoBillingByPricingPlans implements ShouldQueue
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

            $pricingPlanAutoBillingReminders = PricingPlanAutoBillingReminder::whereHas('project', function($query) {

                /**
                 *  Must have a project that can auto bill.
                 */
                return $query->canAutoBill();

            })->whereHas('pricingPlan', function($query) {

                /**
                 *  Must have an active, non-folder pricing plan that can also auto bill.
                 */
                return $query->active()->nonFolder()->canAutoBill();

            })->with(['project', 'autoBillingReminder', 'pricingPlan' => function($query) {

                $query->withCount('autoBillingReminderJobBatches');

            }])->get();

            /**
             *  Order the pricingPlanAutoBillingReminders by the autoBillingReminder hours
             *  so that those with more hours appear at the top of the stack while those with
             *  fewer hours appear at the bottom of the stack.
             */
            $pricingPlanAutoBillingReminders = $pricingPlanAutoBillingReminders->sortByDesc(function ($pricingPlanAutoBillingReminder) {
                return $pricingPlanAutoBillingReminder->autoBillingReminder->hours;
            })->all();

            // Foreach project
            foreach ($pricingPlanAutoBillingReminders as $pricingPlanAutoBillingReminder) {

                /**
                 *  @var PricingPlan $pricingPlan
                 */
                $pricingPlan = $pricingPlanAutoBillingReminder->pricingPlan;

                if(!empty($pricingPlan->next_auto_billing_reminder_sms_message)) {

                    /**
                     *  @var Project $project
                     */
                    $project = $pricingPlanAutoBillingReminder->project;

                    /**
                     *  @var AutoBillingReminder $autoBillingReminder
                     */
                    $autoBillingReminder = $pricingPlanAutoBillingReminder->autoBillingReminder;

                    /**
                     *  @var int $autoBillingReminderJobBatchesCount
                     */
                    $autoBillingReminderJobBatchesCount = $pricingPlan->auto_billing_reminder_job_batches_count;

                    //  Add this job to the queue for processing
                    NextAutoBillingByPricingPlan::dispatch($project, $pricingPlan, $autoBillingReminder, $autoBillingReminderJobBatchesCount);

                }

            }

        } catch (\Throwable $th) {

            Log::error('NextAutoBillingByPricingPlans Job Failed: '. $th->getMessage());

        }
    }
}
