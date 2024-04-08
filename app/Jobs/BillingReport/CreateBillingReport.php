<?php

namespace App\Jobs\BillingReport;

use Carbon\Carbon;
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
     *  Report date
     *
     *  @var Carbon
     */
    protected $date;

    /**
     *  Project instance.
     *
     *  @var \App\Models\Project
     */
    protected $project;

    /**
     *  Billing transactions count
     *
     *  @var int
     */
    protected $billingTransactionsCount;

    /**
     *  The unique ID of the job.
     *
     *  Sometimes, you may want to ensure that only one instance of a specific job is on
     *  the queue at any point in time. You may do so by implementing the ShouldBeUnique
     *  interface on your job class. So the current job will not be dispatched if another
     *  instance of the job is already on the queue and has not finished processing.
     *
     *  Refer: https://laravel.com/docs/8.x/queues#unique-jobs
     *
     *  @return string
     */
    public function uniqueId()
    {
        return $this->project->id;
    }

    /**
     * Create a new job instance.
     *
     * @param App\Models\Project $project
     * @param int $billingTransactionsCount
     *
     * @return void
     */
    public function __construct(Project $project, Carbon $date, int $billingTransactionsCount)
    {
        $this->date = $date;
        $this->project = $project;

        /**
         *  It appears that the eager loaded withCount('billingTransactions') is not accessible using
         *  $project->billing_transactions_count within the handle() method.
         *  Therefore we will set this as its own parameter.
         */
        $this->billingTransactionsCount = $billingTransactionsCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            $gross_revenue = $this->project->billingTransactions()->where('is_successful', '1')
                                     ->whereMonth('created_at', $this->date->month)
                                     ->whereYear('created_at', $this->date->year)
                                     ->sum('amount');

            $cost_percentage = collect($this->project->costs)->sum('percentage') / 100;

            $costs = $gross_revenue * $cost_percentage;

            $cost_breakdown = collect($this->project->costs)->mapWithKeys(function($cost, $key) use ($gross_revenue) {

                return [

                    /**
                     *  This will run as follows:
                     *
                     *  USAF => 2000 * 1/100
                     *  BOCRA => 2000 * 4/100
                     *  VAT (14%) => 2000 * 14/100
                     *  Dealer Commission (Airtime) => 2000 * 13.5/100
                     */
                    $cost['name'] => $gross_revenue * $cost['percentage'] / 100

                ];

            })->all();

            $sharable_revenue = $gross_revenue - $costs;

            $name = BillingReport::getNameFromDate($this->date);

            $our_share = $sharable_revenue * $this->project->our_share_percentage / 100;

            $their_share = $sharable_revenue * $this->project->their_share_percentage / 100;


            $billingReport = BillingReport::create([
                'name' => $name,
                'costs' => $costs,
                'our_share' => $our_share,
                'year' => $this->date->year,
                'month' => $this->date->month,
                'their_share' => $their_share,
                'gross_revenue' => $gross_revenue,
                'project_id' => $this->project->id,
                'cost_breakdown' => $cost_breakdown,
                'sharable_revenue' => $sharable_revenue,
                'total_transactions' => $this->billingTransactionsCount
            ]);

            $overviewPdfPath = $this->project->id.'/pdf_files/'.$this->date->shortMonthName.'-'.$this->date->year.'-Overview.pdf';

            //  Create the monthly billing report pdf file
            Pdf::view('pdfs/monthly-billing-report-overview', [
                'project' => $this->project,
                'billingReport' => $billingReport,
            ])->disk('public_uploads')
              ->save($overviewPdfPath);

            $successfulTransactionsCsvPath = $this->project->id.'/csv_files/'.$this->date->shortMonthName.'-'.$this->date->year.'-Transactions.csv';

            //  Create the monthly billing report transactions xml file
            (new BillingReportTransactionsExport($this->project, $this->date))->store($successfulTransactionsCsvPath, 'public_uploads');

            $billingReport->update([
                'overview_pdf_path' => $overviewPdfPath,
                'successful_transactions_csv_path' => $successfulTransactionsCsvPath
            ]);

            foreach($this->project->billing_report_email_addresses as $emailAddress) {

                //  Send Monthly Billing Report Email
                Mail::to($emailAddress)->queue(new MonthlyBillingReport($this->project, $billingReport));

            }

        } catch (\Throwable $th) {

            Log::error('CreateBillingReport Job Failed: '. $th->getMessage());

            return false;

        }
    }

}
