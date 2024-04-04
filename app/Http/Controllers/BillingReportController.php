<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\BillingReport;
use App\Repositories\BillingReportRepository;

class BillingReportController extends Controller
{
    protected $project;
    protected $billingReport;
    protected $billingReportRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->billingReport = request()->route('billing_report') ? BillingReport::findOrFail(request()->route('billing_report'))->load(['subscriber', 'subscriptionPlan']) : null;

        $this->billingReportRepository = new BillingReportRepository($this->project, $this->billingReport);
    }

    public function showBillingReports()
    {
        // Get the billing reports using the repository with the required relationships and pagination
        $billingReports = $this->billingReportRepository->getProjectBillingReports();

        // Render the billing transaction view
        return Inertia::render('BillingReports/List/Main', [
            'billingReportsPayload' => $billingReports
        ]);
    }

    public function showBillingReport()
    {
        return Inertia::render('BillingReport/List/Main', [
            'billingReport' => $this->billingReport
        ]);
    }
}
