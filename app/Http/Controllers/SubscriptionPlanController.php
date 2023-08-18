<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Http\Requests\SubscriptionPlans\CreateSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlans\UpdateSubscriptionPlanRequest;

class SubscriptionPlanController extends Controller
{
    protected $project;
    protected $subscriptionRepository;
    protected $subscriptionPlanRepository;

    public function __construct()
    {
        $project = Project::findOrFail(request()->route('project'));
        $subscriptionPlan = request()->route('subscription_plan') ? SubscriptionPlan::findOrFail(request()->route('subscription_plan')) : null;

        $this->subscriptionRepository = new SubscriptionRepository($project, null);
        $this->subscriptionPlanRepository = new SubscriptionPlanRepository($project, $subscriptionPlan);
    }

    public function showSubscriptionPlans()
    {
        //  Get the total subscriptions
        $totalSubscriptions = $this->subscriptionRepository->countProjectSubscriptions();

        // Fetch the subscription plans using the repository with the required relationships and pagination
        $subscriptionPlans = $this->subscriptionPlanRepository->getProjectSubscriptionPlans([], ['subscriptions']);

        // Render the subscriptions view
        return Inertia::render('SubscriptionPlans/List/Main', [
            'totalSubscriptions' => $totalSubscriptions,
            'subscriptionPlansPayload' => $subscriptionPlans
        ]);
    }

    public function createSubscriptionPlan(CreateSubscriptionPlanRequest $request)
    {
        //  Get the subscription plan name
        $name = $request->input('name');

        //  Get the subscription plan price
        $price = $request->input('price');

        //  Get the subscription plan duration
        $duration = $request->input('duration');

        //  Get the subscription plan frequency
        $frequency = $request->input('frequency');

        //  Get the subscription plan categories
        $categories = $request->input('categories');

        // Create a new subscription plan using the repository
        $this->subscriptionPlanRepository->createProjectSubscriptionPlan($name, $price, $duration, $frequency, $categories);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscriptionPlan(UpdateSubscriptionPlanRequest $request)
    {
        //  Get the subscription plan name
        $name = $request->input('name');

        //  Get the subscription plan price
        $price = $request->input('price');

        //  Get the subscription plan duration
        $duration = $request->input('duration');

        //  Get the subscription plan frequency
        $frequency = $request->input('frequency');

        //  Get the subscription plan categories
        $categories = $request->input('categories');

        // Update existing subscription plan using the repository
        $this->subscriptionPlanRepository->updateProjectSubscriptionPlan($name, $price, $duration, $frequency, $categories);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteSubscriptionPlan()
    {
        // Delete the subscription plan using the repository
        $this->subscriptionPlanRepository->deleteProjectSubscriptionPlan();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
