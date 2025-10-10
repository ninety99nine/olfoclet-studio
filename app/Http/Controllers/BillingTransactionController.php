<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\BillingTransaction;
use App\Repositories\BillingTransactionRepository;

class BillingTransactionController extends Controller
{
    protected $project;
    protected $billingTransaction;
    protected $billingTransactionRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->billingTransaction = request()->route('billing_transaction') ? BillingTransaction::findOrFail(request()->route('billing_transaction'))->load(['subscriber', 'pricingPlan']) : null;

        $this->billingTransactionRepository = new BillingTransactionRepository($this->project, $this->billingTransaction);
    }

    public function showBillingTransactions()
    {
        // Get the billing transactions using the repository with the required relationships and pagination
        $billingTransactions = $this->billingTransactionRepository->getProjectBillingTransactions(null,
            ['subscriber', 'subscription', 'pricingPlan'], []
        );

        // Render the billing transaction view
        return Inertia::render('BillingTransactions/List/Main', [
            'billingTransactionsPayload' => $billingTransactions
        ]);
    }

    public function showBillingTransaction()
    {
        return Inertia::render('BillingTransaction/List/Main', [
            'billingTransaction' => $this->billingTransaction
        ]);
    }
}
