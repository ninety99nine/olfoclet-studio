<?php

namespace App\Jobs\AutoBilling;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutoBillingBySubscriptionPlans implements ShouldQueue
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

            Log::info('AutoBillingBySubscriptionPlans');

            //  Get projects that can auto bill with their subscription plans that can also auto bill
            $projects = Project::canAutoBill()->with(['subscriptionPlans' => function($query) {

                /**
                 *  Must be active non folder subscription plans that can auto bill.
                 *  Also count the total auto billing job batches.
                 */
                return $query->active()->nonFolder()->canAutoBill()->withCount('autoBillingJobBatches');

            }])->get();

            // Foreach project
            foreach ($projects as $project) {

                Log::info('project: '.$project->name);

                // Foreach subscription plan
                foreach($project->subscriptionPlans as $subscriptionPlan) {

                    Log::info('subscriptionPlan: '.$project->subscriptionPlan);

                    /**
                     *  @var int $autoBillingJobBatchesCount
                     */
                    $autoBillingJobBatchesCount = $subscriptionPlan->auto_billing_job_batches_count;

                    //  Add this job to the queue for processing
                    AutoBillingBySubscriptionPlan::dispatch($project, $subscriptionPlan, $autoBillingJobBatchesCount);

                }

            }

        } catch (\Throwable $th) {

            Log::error('AutoBillingBySubscriptionPlans Job Failed: '. $th->getMessage());

        }
    }
}
