<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Http\Requests\SubscriptionPlans\CreateSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlans\UpdateSubscriptionPlanRequest;
use App\Models\AutoBillingReminder;

class SubscriptionPlanController extends Controller
{
    protected $project;
    protected $subscriptionPlan;
    protected $subscriptionRepository;
    protected $subscriptionPlanRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->subscriptionPlan = request()->route('subscription_plan') ? SubscriptionPlan::findOrFail(request()->route('subscription_plan')) : null;

        $this->subscriptionRepository = new SubscriptionRepository($this->project, null);
        $this->subscriptionPlanRepository = new SubscriptionPlanRepository($this->project, $this->subscriptionPlan);
    }

    public function showSubscriptionPlans()
    {
        //  Get the auto billing reminders
        $autoBillingReminders = AutoBillingReminder::all();

        //  Get the total subscriptions
        $totalSubscriptions = $this->subscriptionRepository->countProjectSubscriptions();

        // Fetch the subscription plans using the repository with the required relationships and pagination
        $subscriptionPlansPayload = $this->subscriptionPlanRepository->getProjectSubscriptionPlans(['autoBillingReminders'], ['subscriptions']);

        // Render the subscriptions view
        return Inertia::render('SubscriptionPlans/List/Main', [
            'totalSubscriptions' => $totalSubscriptions,
            'autoBillingReminders' => $autoBillingReminders,
            'subscriptionPlansPayload' => $subscriptionPlansPayload
        ]);
    }

    public function showSubscriptionPlan()
    {
        //  Get the auto billing reminders
        $autoBillingReminders = AutoBillingReminder::all();

        //  Get the total subscriptions
        $totalSubscriptions = $this->subscriptionRepository->countProjectSubscriptions();

        $breadcrumbs = $this->subscriptionPlanRepository->getProjectSubscriptionPlanBreadcrumbNavigation();
        $subscriptionPlansPayload = $this->subscriptionPlanRepository->getProjectSubscriptionPlanChildren(['autoBillingReminders'], ['subscriptions']);

        return Inertia::render('SubscriptionPlans/List/Main', [
            'breadcrumbs' => $breadcrumbs,
            'totalSubscriptions' => $totalSubscriptions,
            'autoBillingReminders' => $autoBillingReminders,
            'parentSubscriptionPlan' => $this->subscriptionPlan,
            'subscriptionPlansPayload' => $subscriptionPlansPayload
        ]);
    }

    public function createSubscriptionPlan(CreateSubscriptionPlanRequest $request)
    {
        //  Get the subscription plan name
        $name = $request->input('name');

        //  Get the subscription plan description
        $description = $request->input('description');

        //  Get the subscription plan active
        $active = $request->input('active');

        //  Get the subscription plan is folder
        $isFolder = $request->input('is_folder');

        //  Get the subscription plan price
        $price = $request->input('price') ?? null;

        //  Get the subscription plan duration
        $duration = $request->input('duration') ?? null;

        //  Get the subscription plan frequency
        $frequency = $request->input('frequency') ?? null;

        //  Get the subscription plan tags
        $tags = $request->input('tags') ?? null;

        //  Set parent topic id
        $parentId = $request->input('parent_id') ?? null;

        //  Get the subscription plan can auto bill status
        $canAutoBill = $request->input('can_auto_bill');

        //  Get the subscription plan maximum auto billing attempts
        $maxAutoBillingAttempts = $request->input('max_auto_billing_attempts');

        //  Get the subscription plan auto billing reminder ids
        $autoBillingReminderIds = $request->input('auto_billing_reminder_ids');

        //  Get the subscription plan insufficient funds message
        $insufficientFundsMessage = $request->input('insufficient_funds_message');

        //  Get the subscription plan successful payment sms message
        $successfulPaymentSmsMessage = $request->input('successful_payment_sms_message');

        //  Get the subscription plan next auto bill reminder sms message
        $nextAutoBillingReminderSmsMessage = $request->input('next_auto_billing_reminder_sms_message');

        // Create a new subscription plan using the repository
        $this->subscriptionPlanRepository->createProjectSubscriptionPlan($name, $description, $active, $isFolder, $price, $duration, $frequency, $tags, $canAutoBill, $maxAutoBillingAttempts, $insufficientFundsMessage, $successfulPaymentSmsMessage, $nextAutoBillingReminderSmsMessage, $autoBillingReminderIds, $parentId);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscriptionPlan(UpdateSubscriptionPlanRequest $request)
    {
        //  Get the subscription plan name
        $name = $request->input('name');

        //  Get the subscription plan description
        $description = $request->input('description') ?? null;

        //  Get the subscription plan active
        $active = $request->input('active');

        //  Get the subscription plan is folder
        $isFolder = $request->input('is_folder');

        //  Get the subscription plan price
        $price = $request->input('price') ?? null;

        //  Get the subscription plan duration
        $duration = $request->input('duration') ?? null;

        //  Get the subscription plan frequency
        $frequency = $request->input('frequency') ?? null;

        //  Get the subscription plan tags
        $tags = $request->input('tags') ?? null;

        //  Get the subscription plan can auto bill status
        $canAutoBill = $request->input('can_auto_bill');

        //  Get the subscription plan maximum auto billing attempts
        $maxAutoBillingAttempts = $request->input('max_auto_billing_attempts');

        //  Get the subscription plan auto billing reminder ids
        $autoBillingReminderIds = $request->input('auto_billing_reminder_ids');

        //  Get the subscription plan insufficient funds message
        $insufficientFundsMessage = $request->input('insufficient_funds_message');

        //  Get the subscription plan successful payment sms message
        $successfulPaymentSmsMessage = $request->input('successful_payment_sms_message');

        //  Get the subscription plan next auto bill reminder sms message
        $nextAutoBillingReminderSmsMessage = $request->input('next_auto_billing_reminder_sms_message');

        // Update existing subscription plan using the repository
        $this->subscriptionPlanRepository->updateProjectSubscriptionPlan($name, $description, $active, $isFolder, $price, $duration, $frequency, $tags, $canAutoBill, $maxAutoBillingAttempts, $insufficientFundsMessage, $successfulPaymentSmsMessage, $nextAutoBillingReminderSmsMessage, $autoBillingReminderIds);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteSubscriptionPlan()
    {
        // Delete the subscription plan using the repository
        $this->subscriptionPlanRepository->deleteProjectSubscriptionPlan();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
