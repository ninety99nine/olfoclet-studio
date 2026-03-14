<?php

namespace App\Jobs\BillingReport;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use App\Services\QueueBackpressure;
use Illuminate\Bus\Queueable;
use App\Models\BillingReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartCreatingBillingReports implements ShouldQueue
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

        Log::info('StartCreatingBillingReports: job started');

        try {

            $date = Carbon::now()->subMonth();
            $name = BillingReport::getNameFromDate($date);

            /**
             * Build the query for projects that need a billing report.
             */
            $projectsQuery = Project::canCreateBillingReports()
                ->has('billingTransactions')
                ->whereDoesntHave('billingReports', function($query) use ($name) {
                    return $query->where('name', $name);
                })
                ->withCount(['billingTransactions' => function (Builder $query) use ($date) {
                    $query->where('is_successful', '1')
                          ->whereYear('created_at', $date->year)
                          ->whereMonth('created_at', $date->month);
                }])
                ->orderBy('id');

            /**
             * Use chunk() (not chunkById) because chunkById can abort when the query
             * uses whereDoesntHave/withCount (subqueries). Chunk keeps memory flat.
             */
            $projectsQuery->chunk(100, function ($projects) use ($date) {

                foreach ($projects as $project) {

                    // Safely grab the count, defaulting to 0 if null
                    $billingTransactionsCount = $project->billing_transactions_count ?? 0;

                    /**
                     * 2. CRITICAL FIX: withoutRelations()
                     * Stripping the heavy relationships from the Project model BEFORE
                     * dispatching it ensures your queue payload (stored in Redis or DB)
                     * remains tiny and fast to process.
                     */
                    CreateBillingReport::dispatch(
                        $project->withoutRelations(),
                        $date,
                        $billingTransactionsCount
                    );

                }

            });

        } catch (Throwable $th) {
            throw $th;
        }

        Log::info('StartCreatingBillingReports: job completed');
    }
}
