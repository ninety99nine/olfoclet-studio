<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Models\SubscriptionPlan;
use App\Http\Controllers\Controller;
use App\Enums\CreatedUsingAutoBilling;
use App\Repositories\SubscriberRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\UpdateSubscriptionRequest;
use App\Http\Requests\Subscriptions\CancelSubscriptionsRequest;

class SubscriptionController extends Controller
{
    protected $project;
    protected $subscriberRepository;
    protected $subscriptionRepository;
    protected $subscriptionPlanRepository;

    public function __construct()
    {
        $project = Project::findOrFail(request()->route('project'));
        $subscription = request()->route('subscription') ? Subscription::findOrFail(request()->route('subscription')) : null;

        $this->subscriberRepository = new SubscriberRepository($project, null);
        $this->subscriptionPlanRepository = new SubscriptionPlanRepository($project, null);
        $this->subscriptionRepository = new SubscriptionRepository($project, $subscription);
    }

    public function showSubscriptions()
    {
        //  Get the total subscribers
        $totalSubscribers = $this->subscriberRepository->countProjectSubscribers();

        // Fetch the subscription plans using the repository with the required relationships and pagination
        $subscriptionPlans = $this->subscriptionPlanRepository->queryProjectSubscriptionPlans()->get();

        // Fetch the subscriptions using the repository with the required relationships and pagination
        $subscriptions = $this->subscriptionRepository->getProjectSubscriptions(['subscriber:id,msisdn', 'subscriptionPlan']);

        // Render the subscriptions view
        return Inertia::render('Subscriptions/List/Main', [
            'totalSubscribers' => $totalSubscribers,
            'subscriptionsPayload' => $subscriptions,
            'subscriptionPlans' => $subscriptionPlans
        ]);
    }

    public function createSubscription(CreateSubscriptionRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Get the subscription plan to be used when creating this subscription
        $subscriptionPlan = SubscriptionPlan::find($request->input('subscription_plan_id'));

        // Create a new subscription using the repository
        $this->subscriptionRepository->createProjectSubscription($subscriber, $subscriptionPlan, CreatedUsingAutoBilling::NO);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscription(UpdateSubscriptionRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Get the subscription plan to be used when updating this subscription
        $subscriptionPlan = SubscriptionPlan::find($request->input('subscription_plan_id'));

        // Update existing subscription using the repository
        $this->subscriptionRepository->updateProjectSubscription($subscriber, $subscriptionPlan);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function cancelSubscription()
    {
        //  Cancel the subscription
        $this->subscriptionRepository->cancelProjectSubscription();

        return redirect()->back()->with('message', 'Subscription cancelled successfully');
    }

    public function uncancelSubscription()
    {
        //  Uncancel the subscription
        $this->subscriptionRepository->uncancelProjectSubscription();

        return redirect()->back()->with('message', 'Subscription uncancelled successfully');
    }

    public function deleteSubscription()
    {
        // Delete the subscription using the repository
        $this->subscriptionRepository->deleteProjectSubscription();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
