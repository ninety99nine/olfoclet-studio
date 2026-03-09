<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\BillingReport;
use App\Repositories\BillingReportRepository;
use App\Repositories\BillingTransactionRepository;
use App\Http\Requests\BillingReports\ListBillingReportsRequest;
use App\Http\Requests\Transactions\ListTransactionsRequest;
use App\Exports\BillingReportTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BillingReportController extends Controller
{
    protected $project;
    protected $billingReport;
    protected $billingReportRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->billingReport = request()->route('billing_report')
            ? BillingReport::findOrFail(request()->route('billing_report'))->load('project')
            : null;

        $this->billingReportRepository = new BillingReportRepository($this->project, $this->billingReport);
    }

    /**
     * Build filters array from validated request (for JSON list).
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildFilters(array $validated): array
    {
        return [
            'search' => $validated['search'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? null,
            'sort' => $validated['sort'] ?? null,
        ];
    }

    public function showBillingReports(Request $request)
    {
        // JSON request (e.g. from Axios): validate and return paginated list
        if ($request->expectsJson()) {
            $validated = $request->validate((new ListBillingReportsRequest())->rules());
            $filters = $this->buildFilters($validated);
            $billingReports = $this->billingReportRepository->getProjectBillingReportsWithFilters($filters, [], []);

            return response()->json([
                'billingReportsPayload' => $billingReports,
            ]);
        }

        // Inertia: render shell only; frontend fetches list via Axios
        return Inertia::render('BillingReports/List/Main', [
            'projectPayload' => $this->project,
        ]);
    }

    /**
     * Show a single billing report with detail view.
     */
    public function showBillingReport()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        return Inertia::render('BillingReports/Show/Main', [
            'projectPayload' => $this->project,
            'billingReportPayload' => $this->billingReport,
        ]);
    }

    /**
     * Return paginated transactions for the billing report period (JSON).
     */
    public function transactionsForReport(Request $request)
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $validated = $request->validate((new ListTransactionsRequest())->rules());
        $dateFrom = Carbon::createFromDate(
            (int) $this->billingReport->year,
            (int) $this->billingReport->month,
            1
        )->startOfMonth()->toDateString();
        $dateTo = Carbon::createFromDate(
            (int) $this->billingReport->year,
            (int) $this->billingReport->month,
            1
        )->endOfMonth()->toDateString();

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'per_page' => $validated['per_page'] ?? 15,
            'sort' => $validated['sort'] ?? 'created_at:desc',
        ];
        if (! empty($validated['msisdn'] ?? null)) {
            $filters['msisdn'] = $validated['msisdn'];
        }
        if (isset($validated['status'])) {
            $filters['status'] = $validated['status'];
        }
        if (isset($validated['created_using_auto_billing'])) {
            $filters['created_using_auto_billing'] = $validated['created_using_auto_billing'];
        }
        if (! empty($validated['pricing_plan_id'] ?? null)) {
            $filters['pricing_plan_id'] = $validated['pricing_plan_id'];
        }

        $repo = new BillingTransactionRepository($this->project, null);
        $transactions = $repo->getProjectTransactions(
            $filters,
            ['subscriber:id,msisdn,project_id', 'subscription', 'pricingPlan'],
            []
        );

        return response()->json([
            'transactionsPayload' => $transactions,
        ]);
    }

    /**
     * Download transactions for the billing report period as CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadTransactionsCsv()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $date = Carbon::createFromDate(
            (int) $this->billingReport->year,
            (int) $this->billingReport->month,
            1
        );

        return Excel::download(
            new BillingReportTransactionsExport($this->project, $date),
            'billing-report-' . $this->billingReport->name . '-transactions.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Download transactions for the billing report period as Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTransactionsExcel()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $date = Carbon::createFromDate(
            (int) $this->billingReport->year,
            (int) $this->billingReport->month,
            1
        );

        return Excel::download(
            new BillingReportTransactionsExport($this->project, $date),
            'billing-report-' . $this->billingReport->name . '-transactions.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Download the billing report as a detailed PDF (metrics, cost breakdown, analytics, charts).
     * Uses Browsershot (Puppeteer/Chromium). If PDF generation fails, redirects back with an error.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function downloadDetailedPdf()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $report = $this->billingReport;
        $project = $this->project;
        $periodStart = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->startOfMonth()->toDateString();
        $periodEnd = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->endOfMonth()->toDateString();

        try {
            $overview = $this->getOverviewForPeriod($periodStart, $periodEnd);
            $transactionsChartData = $this->getTransactionsOverTimeForPeriod($periodStart, $periodEnd);
            $revenueChartData = $this->getRevenueOverTimeForPeriod($periodStart, $periodEnd);

            $transactionsChartImage = $this->buildQuickChartImage($transactionsChartData, 'transactions');
            $revenueChartImage = $this->buildQuickChartImage($revenueChartData, 'revenue');

            $periodLabel = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->format('F Y');

            $chromiumPath = config('services.browsershot.chromium_path');
            $pdf = Pdf::view('pdfs.billing-report-detailed', [
                'project' => $project,
                'billingReport' => $report,
                'periodLabel' => $periodLabel,
                'overview' => $overview,
                'transactionsChartImage' => $transactionsChartImage,
                'revenueChartImage' => $revenueChartImage,
            ])->name('billing-report-' . preg_replace('/\s+/', '-', $report->name) . '-detailed.pdf');

            if ($chromiumPath) {
                $pdf->withBrowsershot(function (\Spatie\Browsershot\Browsershot $b) use ($chromiumPath) {
                    $b->setChromePath($chromiumPath)->noSandbox();
                });
            }

            return $pdf->download();
        } catch (ProcessFailedException $e) {
            return $this->showReportPrintView();
        } catch (\Throwable $e) {
            return $this->showReportPrintView();
        }
    }

    /**
     * Show the billing report as HTML for printing / Save as PDF (fallback when Chromium is unavailable).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showReportPrintView()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $report = $this->billingReport;
        $project = $this->project;
        $periodStart = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->startOfMonth()->toDateString();
        $periodEnd = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->endOfMonth()->toDateString();

        $overview = $this->getOverviewForPeriod($periodStart, $periodEnd);
        $transactionsChartData = $this->getTransactionsOverTimeForPeriod($periodStart, $periodEnd);
        $revenueChartData = $this->getRevenueOverTimeForPeriod($periodStart, $periodEnd);
        $transactionsChartImage = $this->buildQuickChartImage($transactionsChartData, 'transactions');
        $revenueChartImage = $this->buildQuickChartImage($revenueChartData, 'revenue');
        $periodLabel = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->format('F Y');

        return view('pdfs.billing-report-detailed', [
            'project' => $project,
            'billingReport' => $report,
            'periodLabel' => $periodLabel,
            'overview' => $overview,
            'transactionsChartImage' => $transactionsChartImage,
            'revenueChartImage' => $revenueChartImage,
            'showPrintBanner' => true,
        ]);
    }

    /**
     * Download the billing report as an invoice PDF for the MNO (revenue, cost breakdown, share split).
     * Uses Browsershot (Puppeteer/Chromium). If PDF generation fails, redirects back with an error.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function downloadInvoicePdf()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $report = $this->billingReport;
        $project = $this->project;
        $periodLabel = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->format('F Y');

        try {
            $chromiumPath = config('services.browsershot.chromium_path');
            $pdf = Pdf::view('pdfs.billing-report-invoice', [
                'project' => $project,
                'billingReport' => $report,
                'periodLabel' => $periodLabel,
            ])->name('billing-report-' . preg_replace('/\s+/', '-', $report->name) . '-invoice.pdf');

            if ($chromiumPath) {
                $pdf->withBrowsershot(function (\Spatie\Browsershot\Browsershot $b) use ($chromiumPath) {
                    $b->setChromePath($chromiumPath)->noSandbox();
                });
            }

            return $pdf->download();
        } catch (ProcessFailedException $e) {
            return $this->showInvoicePrintView();
        } catch (\Throwable $e) {
            return $this->showInvoicePrintView();
        }
    }

    /**
     * Show the invoice as HTML for printing / Save as PDF (fallback when Chromium is unavailable).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showInvoicePrintView()
    {
        if ($this->billingReport === null) {
            abort(404, 'Billing report not found.');
        }

        $report = $this->billingReport;
        $project = $this->project;
        $periodLabel = Carbon::createFromDate((int) $report->year, (int) $report->month, 1)->format('F Y');

        return view('pdfs.billing-report-invoice', [
            'project' => $project,
            'billingReport' => $report,
            'periodLabel' => $periodLabel,
            'showPrintBanner' => true,
        ]);
    }

    /**
     * Get overview stats (transactions, revenue) for the given date range.
     *
     * @return array{total_transactions: int, successful_transactions: int, total_revenue: float, transaction_success_rate: float|null}
     */
    private function getOverviewForPeriod(string $periodStart, string $periodEnd): array
    {
        $base = $this->project->billingTransactions()->whereDate('created_at', '>=', $periodStart)->whereDate('created_at', '<=', $periodEnd);
        $totalTransactions = (int) (clone $base)->count();
        $successfulTransactions = (int) (clone $base)->successful()->count();
        $totalRevenue = (float) (clone $base)->successful()->sum('amount');
        $transactionSuccessRate = $totalTransactions > 0 ? round(100 * $successfulTransactions / $totalTransactions, 1) : null;

        return [
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'total_revenue' => $totalRevenue,
            'transaction_success_rate' => $transactionSuccessRate,
        ];
    }

    /**
     * Get all dates between start and end (inclusive).
     *
     * @return array<int, string> List of Y-m-d date strings
     */
    private function allDatesInRange(string $periodStart, string $periodEnd): array
    {
        $start = Carbon::parse($periodStart);
        $end = Carbon::parse($periodEnd);
        $dates = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dates[] = $d->toDateString();
        }

        return $dates;
    }

    /**
     * Get transactions over time (daily) for the given date range.
     * Includes every day in the range; days with no transactions show 0.
     *
     * @return array{labels: array, values: array, values_successful: array}
     */
    private function getTransactionsOverTimeForPeriod(string $periodStart, string $periodEnd): array
    {
        $dates = $this->allDatesInRange($periodStart, $periodEnd);
        $base = $this->project->billingTransactions()
            ->whereDate('created_at', '>=', $periodStart)
            ->whereDate('created_at', '<=', $periodEnd)
            ->selectRaw('DATE(created_at) as date');
        $allByDay = (clone $base)->selectRaw('COUNT(*) as count')->groupBy('date')->orderBy('date')->get()->keyBy(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'));
        $successByDay = (clone $base)->successful()->selectRaw('COUNT(*) as count')->groupBy('date')->orderBy('date')->get()->keyBy(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'));

        $labels = array_map(fn ($d) => Carbon::parse($d)->format('M j'), $dates);
        $values = array_map(fn ($d) => (int) ($allByDay->get($d)?->count ?? 0), $dates);
        $valuesSuccessful = array_map(fn ($d) => (int) ($successByDay->get($d)?->count ?? 0), $dates);

        return ['labels' => $labels, 'values' => $values, 'values_successful' => $valuesSuccessful];
    }

    /**
     * Get revenue over time (daily sum) for the given date range.
     * Includes every day in the range; days with no revenue show 0.
     *
     * @return array{labels: array, values: array}
     */
    private function getRevenueOverTimeForPeriod(string $periodStart, string $periodEnd): array
    {
        $dates = $this->allDatesInRange($periodStart, $periodEnd);
        $rows = $this->project->billingTransactions()->successful()
            ->whereDate('created_at', '>=', $periodStart)
            ->whereDate('created_at', '<=', $periodEnd)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'));
        $labels = array_map(fn ($d) => Carbon::parse($d)->format('M j'), $dates);
        $values = array_map(fn ($d) => round((float) ($rows->get($d)?->total ?? 0), 2), $dates);

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Build a base64 data URL for a chart image from QuickChart.
     *
     * @param  array  $data  Chart data (labels, values, etc.)
     * @param  string  $type  'transactions' or 'revenue'
     * @return string|null  data:image/png;base64,... or null if no data
     */
    private function buildQuickChartImage(array $data, string $type): ?string
    {
        $labels = $data['labels'] ?? [];
        if (empty($labels)) {
            return null;
        }

        if ($type === 'transactions') {
            $config = [
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        ['label' => 'All', 'data' => $data['values'] ?? [], 'borderColor' => '#94a3b8', 'backgroundColor' => 'rgba(148,163,184,0.1)', 'fill' => true, 'tension' => 0.2],
                        ['label' => 'Successful', 'data' => $data['values_successful'] ?? [], 'borderColor' => '#10b981', 'backgroundColor' => 'rgba(16,185,129,0.1)', 'fill' => true, 'tension' => 0.2],
                    ],
                ],
                'options' => ['plugins' => ['legend' => ['position' => 'bottom']], 'scales' => ['y' => ['beginAtZero' => true]]],
            ];
        } else {
            $config = [
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        ['label' => 'Revenue', 'data' => $data['values'] ?? [], 'borderColor' => '#059669', 'backgroundColor' => 'rgba(5,150,105,0.15)', 'fill' => true, 'tension' => 0.2],
                    ],
                ],
                'options' => ['plugins' => ['legend' => ['display' => false]], 'scales' => ['y' => ['beginAtZero' => true]]],
            ];
        }

        $url = 'https://quickchart.io/chart?c=' . urlencode(json_encode($config)) . '&w=600&h=280&f=png';
        try {
            $response = Http::timeout(15)->get($url);
            if ($response->successful() && $response->body()) {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Throwable $e) {
            // Return null so the PDF still renders without the chart
        }

        return null;
    }
}
