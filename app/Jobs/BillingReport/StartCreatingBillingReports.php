<?php

namespace App\Jobs\BillingReport;

use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\BillingReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StartCreatingBillingReports implements ShouldQueue
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

            $date = Carbon::now()->subMonth();
            $name = BillingReport::getNameFromDate($date);

            $projects = Project::canCreateBillingReports()->has('billingTransactions')->whereDoesntHave('billingReports', function($query) use ($name) {

                return $query->where('name', $name);

            })->withCount(['billingTransactions' => function (Builder $query) use ($date) {
                $query->where('is_successful', '1')
                      ->whereYear('created_at', $date->year)
                      ->whereMonth('created_at', $date->month);
            }])->get();

            Log::info("projects:".count($projects));

            /**
             *  @var Project $project
             */
            foreach ($projects as $project) {

                /**
                 *  @var int $billingTransactionsCount
                 */
                $billingTransactionsCount = $project->billing_transactions_count;

                //  Add this job to the queue for processing
                CreateBillingReport::dispatch($project, $date, $billingTransactionsCount);

            }

        } catch (\Throwable $th) {

            Log::error('StartCreatingBillingReports Job Failed: '. $th->getMessage());

        }
    }
}
