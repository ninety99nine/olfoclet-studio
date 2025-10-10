<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\PricingPlan;
use App\Repositories\SubscriptionRepository;
use App\Repositories\PricingPlanRepository;
use App\Http\Requests\PricingPlans\CreatePricingPlanRequest;
use App\Http\Requests\PricingPlans\UpdatePricingPlanRequest;
use App\Models\AutoBillingReminder;

class PricingPlanController extends Controller
{
    protected $project;
    protected $pricingPlan;
    protected $subscriptionRepository;
    protected $pricingPlanRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->pricingPlan = request()->route('pricing_plan') ? PricingPlan::findOrFail(request()->route('pricing_plan')) : null;

        $this->subscriptionRepository = new SubscriptionRepository($this->project, null);
        $this->pricingPlanRepository = new PricingPlanRepository($this->project, $this->pricingPlan);
    }

    public function showPricingPlans()
    {
        //  Get the auto billing reminders
        $autoBillingReminders = AutoBillingReminder::all();

        //  Get the total subscriptions
        $totalSubscriptions = $this->subscriptionRepository->countProjectSubscriptions();

        // Fetch the pricing plans using the repository with the required relationships and pagination
        $pricingPlansPayload = $this->pricingPlanRepository->getProjectPricingPlans(['autoBillingReminders'], ['subscriptions']);

        // Render the subscriptions view
        return Inertia::render('PricingPlans/List/Main', [
            'totalSubscriptions' => $totalSubscriptions,
            'autoBillingReminders' => $autoBillingReminders,
            'pricingPlansPayload' => $pricingPlansPayload
        ]);
    }

    public function showPricingPlan()
    {
        //  Get the auto billing reminders
        $autoBillingReminders = AutoBillingReminder::all();

        //  Get the total subscriptions
        $totalSubscriptions = $this->subscriptionRepository->countProjectSubscriptions();

        $breadcrumbs = $this->pricingPlanRepository->getProjectPricingPlanBreadcrumbNavigation();
        $pricingPlansPayload = $this->pricingPlanRepository->getProjectPricingPlanChildren(['autoBillingReminders'], ['subscriptions']);

        return Inertia::render('PricingPlans/List/Main', [
            'breadcrumbs' => $breadcrumbs,
            'totalSubscriptions' => $totalSubscriptions,
            'autoBillingReminders' => $autoBillingReminders,
            'parentPricingPlan' => $this->pricingPlan,
            'pricingPlansPayload' => $pricingPlansPayload
        ]);
    }

    public function createPricingPlan(CreatePricingPlanRequest $request)
    {
        //  Get the pricing plan name
        $name = $request->input('name');

        //  Get the pricing plan description
        $description = $request->input('description');

        //  Get the pricing plan active
        $active = $request->input('active');

        //  Get the pricing plan is folder
        $isFolder = $request->input('is_folder');

        //  Get the pricing plan price
        $price = $request->input('price') ?? null;

        //  Get the pricing plan trial days
        $trialDays = $request->input('trial_days');

        //  Get the pricing plan billing type
        $billingType = $request->input('billing_type') ?? null;

        //  Get the pricing plan duration
        $duration = $request->input('duration') ?? null;

        //  Get the pricing plan frequency
        $frequency = $request->input('frequency') ?? null;

        //  Get the pricing plan tags
        $tags = $request->input('tags') ?? null;

        //  Set parent topic id
        $parentId = $request->input('parent_id') ?? null;

        //  Get the pricing plan can auto bill status
        $canAutoBill = $request->input('can_auto_bill');

        //  Get the pricing plan maximum auto billing attempts
        $maxAutoBillingAttempts = $request->input('max_auto_billing_attempts');

        //  Get the pricing plan auto billing reminder ids
        $autoBillingReminderIds = $request->input('auto_billing_reminder_ids');

        //  Get the pricing plan insufficient funds message
        $insufficientFundsMessage = $request->input('insufficient_funds_message');

        //  Get the trial started sms message
        $trialStartedSmsMessage = $request->input('trial_started_sms_message') ?? null;

        //  Get the successful payment sms message
        $successfulPaymentSmsMessage = $request->input('successful_payment_sms_message') ?? null;

        //  Get the pricing plan successful auto billing payment sms message
        $successfulAutoBillingPaymentSmsMessage = $request->input('successful_auto_billing_payment_sms_message') ?? null;

        //  Get the pricing plan next auto bill reminder sms message
        $nextAutoBillingReminderSmsMessage = $request->input('next_auto_billing_reminder_sms_message') ?? null;

        //  Get the pricing plan auto billing disabled sms message
        $autoBillingDisabledSmsMessage = $request->input('auto_billing_disabled_sms_message') ?? null;

        //  Get the pricing plan auto billing product ID
        $billingProductId = $request->input('billing_product_id');

        //  Get the pricing plan auto billing purchase category code
        $billingPurchaseCategoryCode = $request->input('billing_purchase_category_code');

        // Create a new pricing plan using the repository
        $this->pricingPlanRepository->createProjectPricingPlan($name, $description, $active, $isFolder, $price, $trialDays, $billingType, $duration, $frequency, $tags, $canAutoBill, $billingProductId, $billingPurchaseCategoryCode, $maxAutoBillingAttempts, $insufficientFundsMessage, $trialStartedSmsMessage, $successfulPaymentSmsMessage, $successfulAutoBillingPaymentSmsMessage, $nextAutoBillingReminderSmsMessage, $autoBillingDisabledSmsMessage, $autoBillingReminderIds, $parentId);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updatePricingPlan(UpdatePricingPlanRequest $request)
    {
        //  Get the pricing plan name
        $name = $request->input('name');

        //  Get the pricing plan description
        $description = $request->input('description') ?? null;

        //  Get the pricing plan active
        $active = $request->input('active');

        //  Get the pricing plan is folder
        $isFolder = $request->input('is_folder');

        //  Get the pricing plan price
        $price = $request->input('price') ?? null;

        //  Get the pricing plan trial days
        $trialDays = $request->input('trial_days');

        //  Get the pricing plan billing type
        $billingType = $request->input('billing_type') ?? null;

        //  Get the pricing plan duration
        $duration = $request->input('duration') ?? null;

        //  Get the pricing plan frequency
        $frequency = $request->input('frequency') ?? null;

        //  Get the pricing plan tags
        $tags = $request->input('tags') ?? null;

        //  Get the pricing plan can auto bill status
        $canAutoBill = $request->input('can_auto_bill');

        //  Get the pricing plan maximum auto billing attempts
        $maxAutoBillingAttempts = $request->input('max_auto_billing_attempts');

        //  Get the pricing plan auto billing reminder ids
        $autoBillingReminderIds = $request->input('auto_billing_reminder_ids');

        //  Get the pricing plan insufficient funds message
        $insufficientFundsMessage = $request->input('insufficient_funds_message');

        //  Get the trial started sms message
        $trialStartedSmsMessage = $request->input('trial_started_sms_message') ?? null;

        //  Get the successful payment sms message
        $successfulPaymentSmsMessage = $request->input('successful_payment_sms_message') ?? null;

        //  Get the pricing plan successful auto billing payment sms message
        $successfulAutoBillingPaymentSmsMessage = $request->input('successful_auto_billing_payment_sms_message');

        //  Get the pricing plan next auto bill reminder sms message
        $nextAutoBillingReminderSmsMessage = $request->input('next_auto_billing_reminder_sms_message');

        //  Get the pricing plan auto billing disabled sms message
        $autoBillingDisabledSmsMessage = $request->input('auto_billing_disabled_sms_message');

        //  Get the pricing plan auto billing product ID
        $billingProductId = $request->input('billing_product_id');

        //  Get the pricing plan auto billing purchase category code
        $billingPurchaseCategoryCode = $request->input('billing_purchase_category_code');

        // Update existing pricing plan using the repository
        $this->pricingPlanRepository->updateProjectPricingPlan($name, $description, $active, $isFolder, $price, $trialDays, $billingType, $duration, $frequency, $tags, $canAutoBill, $billingProductId, $billingPurchaseCategoryCode, $maxAutoBillingAttempts, $insufficientFundsMessage, $trialStartedSmsMessage, $successfulPaymentSmsMessage, $successfulAutoBillingPaymentSmsMessage, $nextAutoBillingReminderSmsMessage, $autoBillingDisabledSmsMessage, $autoBillingReminderIds);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deletePricingPlan()
    {
        // Delete the pricing plan using the repository
        $this->pricingPlanRepository->deleteProjectPricingPlan();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
