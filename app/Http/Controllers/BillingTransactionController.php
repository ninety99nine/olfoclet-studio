<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\BillingTransaction;
use App\Repositories\BillingTransactionRepository;
use App\Repositories\PricingPlanRepository;
use App\Http\Requests\Transactions\ListTransactionsRequest;

class BillingTransactionController extends Controller
{
    protected $project;
    protected $billingTransaction;
    protected $billingTransactionRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->billingTransaction = request()->route('billing_transaction')
            ? BillingTransaction::findOrFail(request()->route('billing_transaction'))->load(['subscriber', 'subscription', 'pricingPlan'])
            : null;

        $this->billingTransactionRepository = new BillingTransactionRepository($this->project, $this->billingTransaction);
    }

    /**
     * Build filters array from validated request (for JSON and list).
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildFilters(array $validated): array
    {
        return [
            'msisdn' => $validated['msisdn'] ?? null,
            'status' => $validated['status'] ?? null,
            'created_using_auto_billing' => $validated['created_using_auto_billing'] ?? null,
            'pricing_plan_id' => $validated['pricing_plan_id'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? null,
            'sort' => $validated['sort'] ?? null,
        ];
    }

    public function showTransactions(Request $request)
    {
        // JSON request (e.g. from Axios): validate and return paginated list
        if ($request->expectsJson()) {
            $validated = $request->validate((new ListTransactionsRequest())->rules());
            $filters = $this->buildFilters($validated);
            $transactions = $this->billingTransactionRepository->getProjectTransactions(
                $filters,
                ['subscriber:id,msisdn,project_id', 'subscription', 'pricingPlan'],
                []
            );

            return response()->json([
                'transactionsPayload' => $transactions,
            ]);
        }

        // Inertia: render shell only; frontend fetches list via Axios
        $pricingPlanRepository = new PricingPlanRepository($this->project, null);
        $pricingPlans = $pricingPlanRepository->queryProjectPricingPlans()->get();

        return Inertia::render('Transactions/List/Main', [
            'pricingPlans' => $pricingPlans,
        ]);
    }

    public function showTransaction()
    {
        if ($this->billingTransaction === null) {
            abort(404, 'Transaction not found.');
        }

        return Inertia::render('Transactions/Show/Main', [
            'transaction' => $this->billingTransaction,
        ]);
    }
}
