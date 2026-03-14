<?php

namespace App\Jobs\BillingReport;

use Carbon\Carbon;
use Throwable;
use App\Models\Project;
use App\Models\BillingReport;
use Illuminate\Bus\Queueable;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Mail\MonthlyBillingReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Exports\BillingReportTransactionsExport;

class CreateBillingReport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Report date
     *
     * @var Carbon
     */
    protected $date;

    /**
     * Project instance.
     *
     * @var \App\Models\Project
     */
    protected $project;

    /**
     * Billing transactions count
     *
     * @var int
     */
    protected $billingTransactionsCount;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 600; // 10 minutes (File generation might take a while)

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->project->id;
    }

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Project $project
     * @param Carbon $date
     * @param int $billingTransactionsCount
     *
     * @return void
     */
    public function __construct(Project $project, Carbon $date, int $billingTransactionsCount)
    {
        $this->onQueue('low');
        $this->date = $date;

        // CRITICAL: Prevent serializing the entire loaded project graph into the queue payload
        $this->project = $project->withoutRelations();

        $this->billingTransactionsCount = $billingTransactionsCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("CreateBillingReport() handle started for Project ID: " . $this->project->id);

            // Execute as an aggregate query directly on the database (Highly memory efficient)
            $gross_revenue = (float) $this->project->billingTransactions()
                ->where('is_successful', '1')
                ->whereMonth('created_at', $this->date->month)
                ->whereYear('created_at', $this->date->year)
                ->sum('amount');

            $cost_percentage = collect($this->project->costs)->sum('percentage') / 100;
            $costs = $gross_revenue * $cost_percentage;

            $cost_breakdown = collect($this->project->costs)->mapWithKeys(function ($cost) use ($gross_revenue) {
                return [
                    $cost['name'] => $gross_revenue * $cost['percentage'] / 100
                ];
            })->all();

            $sharable_revenue = $gross_revenue - $costs;
            $name = BillingReport::getNameFromDate($this->date);
            $our_share = $sharable_revenue * $this->project->our_share_percentage / 100;
            $their_share = $sharable_revenue * $this->project->their_share_percentage / 100;

            $billingReport = BillingReport::create([
                'name'               => $name,
                'costs'              => $costs,
                'our_share'          => $our_share,
                'year'               => $this->date->year,
                'month'              => $this->date->month,
                'their_share'        => $their_share,
                'gross_revenue'      => $gross_revenue,
                'project_id'         => $this->project->id,
                'cost_breakdown'     => $cost_breakdown,
                'sharable_revenue'   => $sharable_revenue,
                'total_transactions' => $this->billingTransactionsCount
            ]);

            Log::info("Created database record for report ID: " . $billingReport->id);

            $overviewPdfPath = $this->project->id . '/pdf_files/' . $this->date->shortMonthName . '-' . $this->date->year . '-Overview.pdf';

            // Create the monthly billing report pdf file
            Pdf::view('pdfs/monthly-billing-report-overview', [
                'project'       => $this->project,
                'billingReport' => $billingReport,
            ])->disk('public_uploads')
              ->save($overviewPdfPath);

            Log::info("Created PDF: " . $overviewPdfPath);

            $successfulTransactionsCsvPath = $this->project->id . '/csv_files/' . $this->date->shortMonthName . '-' . $this->date->year . '-Transactions.csv';

            // Create the monthly billing report transactions csv file
            (new BillingReportTransactionsExport($this->project, $this->date))
                ->store($successfulTransactionsCsvPath, 'public_uploads');

            Log::info("Created CSV: " . $successfulTransactionsCsvPath);

            $billingReport->update([
                'overview_pdf_path'                => $overviewPdfPath,
                'successful_transactions_csv_path' => $successfulTransactionsCsvPath
            ]);

            // Dispatch emails
            $emailAddresses = $this->project->billing_report_email_addresses ?? [];
            foreach ($emailAddresses as $emailAddress) {
                Mail::to($emailAddress)->queue(new MonthlyBillingReport($this->project, $billingReport));
                Log::info("Queued email to: " . $emailAddress);
            }

            // Explicitly free memory for the daemon worker (Crucial after heavy PDF/CSV operations)
            unset($this->project, $billingReport);

        } catch (Throwable $th) {
            throw $th;
        }
    }
}
