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

class AutoBillingByPricingPlans implements ShouldQueue
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

            //  Get projects that can auto bill with their pricing plans that can also auto bill
            $projects = Project::canAutoBill()->with(['pricingPlans' => function($query) {

                /**
                 *  Must be active non folder pricing plans that can auto bill.
                 *  Also count the total auto billing job batches.
                 */
                return $query->active()->nonFolder()->canAutoBill()->withCount('autoBillingJobBatches');

            }])->get();

            // Foreach project
            foreach ($projects as $project) {

                // Foreach pricing plan
                foreach($project->pricingPlans as $pricingPlan) {

                    /**
                     *  @var int $autoBillingJobBatchesCount
                     */
                    $autoBillingJobBatchesCount = $pricingPlan->auto_billing_job_batches_count;

                    //  Add this job to the queue for processing
                    AutoBillingByPricingPlan::dispatch($project, $pricingPlan, $autoBillingJobBatchesCount);

                }

            }

        } catch (\Throwable $th) {

            Log::error('AutoBillingByPricingPlans Job Failed: '. $th->getMessage());

        }
    }
}
