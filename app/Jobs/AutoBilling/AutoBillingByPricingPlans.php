<?php

namespace App\Jobs\AutoBilling;

use Throwable;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutoBillingByPricingPlans implements ShouldQueue
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
        Log::info('AutoBillingByPricingPlans: job started');

        try {
            /**
             * Use chunkById instead of get().
             * This processes 100 projects at a time, keeping RAM usage flat and predictable
             * no matter if you have 10 projects or 100,000 projects.
             */
            Project::canAutoBill()
                ->with(['pricingPlans' => function($query) {
                    return $query->active()->nonFolder()->canAutoBill()->withCount('autoBillingJobBatches');
                }])
                ->chunkById(100, function ($projects) {

                    // Foreach project in this specific chunk
                    foreach ($projects as $project) {

                        // Foreach pricing plan in the project
                        foreach ($project->pricingPlans as $pricingPlan) {

                            $autoBillingJobBatchesCount = $pricingPlan->auto_billing_job_batches_count ?? 0;

                            /**
                             * Crucial Optimization: withoutRelations()
                             * By stripping the relations BEFORE dispatching, we prevent the Queue
                             * from serializing massive, heavy objects. This keeps your Redis/Database
                             * queue payload extremely small and fast to process.
                             */
                            AutoBillingByPricingPlan::dispatch(
                                $project->withoutRelations(),
                                $pricingPlan->withoutRelations(),
                                $autoBillingJobBatchesCount
                            );

                        }
                    }

                });

        } catch (\Throwable $th) {

            Log::error('AutoBillingByPricingPlans Job Failed', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                // Removed getTraceAsString() as it can heavily bloat your log files on queue errors
            ]);

            throw $th;
        }

        Log::info('AutoBillingByPricingPlans: job completed');
    }
}
