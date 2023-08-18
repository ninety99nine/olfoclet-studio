<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionPlanResource;
use App\Repositories\SubscriptionPlanRepository;

class SubscriptionPlanApiController extends Controller
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

    public function showSubscriptionPlans(): JsonResponse
    {
        //  Get the subscription plan categories
        $categories = request()->filled('categories') ? array_map('trim', explode(',', request()->input('categories'))) : [];

        // Fetch the subscription plans using the repository with the required relationships and pagination
        $subscriptionPlans = $this->subscriptionPlanRepository->getProjectSubscriptionPlans([], [], $categories);

        // Return subscription plans as a JSON response using SubscriptionPlanResource
        return SubscriptionPlanResource::collection($subscriptionPlans)->response();
    }
}
