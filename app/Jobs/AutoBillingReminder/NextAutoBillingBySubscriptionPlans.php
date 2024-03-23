<?php

namespace App\Jobs\AutoBillingReminder;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\SubscriptionPlan;
use App\Models\AutoBillingReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\Pivots\SubscriptionPlanAutoBillingReminder;
use App\Jobs\AutoBillingReminder\NextAutoBillingBySubscriptionPlan;

class NextAutoBillingBySubscriptionPlans implements ShouldQueue
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

            $subscriptionPlanAutoBillingReminders = SubscriptionPlanAutoBillingReminder::whereHas('project', function($query) {

                /**
                 *  Must have a project that can auto bill.
                 */
                return $query->canAutoBill();

            })->whereHas('subscriptionPlan', function($query) {

                /**
                 *  Must have an active, non-folder subscription plan that can also auto bill.
                 */
                return $query->active()->nonFolder()->canAutoBill();

            })->with(['project', 'autoBillingReminder', 'subscriptionPlan' => function($query) {

                $query->withCount('autoBillingReminderJobBatches');

            }])->get();

            /**
             *  Order the subscriptionPlanAutoBillingReminders by the autoBillingReminder hours
             *  so that those with more hours appear at the top of the stack while those with
             *  fewer hours appear at the bottom of the stack.
             */
            $subscriptionPlanAutoBillingReminders = $subscriptionPlanAutoBillingReminders->sortByDesc(function ($subscriptionPlanAutoBillingReminder) {
                return $subscriptionPlanAutoBillingReminder->autoBillingReminder->hours;
            })->all();

            // Foreach project
            foreach ($subscriptionPlanAutoBillingReminders as $subscriptionPlanAutoBillingReminder) {

                /**
                 *  @var Project $project
                 */
                $project = $subscriptionPlanAutoBillingReminder->project;

                /**
                 *  @var SubscriptionPlan $subscriptionPlan
                 */
                $subscriptionPlan = $subscriptionPlanAutoBillingReminder->subscriptionPlan;

                /**
                 *  @var AutoBillingReminder $autoBillingReminder
                 */
                $autoBillingReminder = $subscriptionPlanAutoBillingReminder->autoBillingReminder;

                /**
                 *  @var int $autoBillingReminderJobBatchesCount
                 */
                $autoBillingReminderJobBatchesCount = $subscriptionPlan->auto_billing_reminder_job_batches_count;

                //  Add this job to the queue for processing
                NextAutoBillingBySubscriptionPlan::dispatch($project, $subscriptionPlan, $autoBillingReminder, $autoBillingReminderJobBatchesCount);

            }

        } catch (\Throwable $th) {

            Log::info('Error: '. $th->getMessage());

            // Send error report here
            //  Log::channel('slack')->error('NextAutoBillingBySubscriptionPlans Job Failed: '. $th->getMessage());

        }
    }
}
