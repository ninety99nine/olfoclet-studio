<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionPlanResource;
use App\Repositories\SubscriptionPlanRepository;
use Illuminate\Support\Facades\Cache;

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

    /*
    public function showSubscriptionPlans(): JsonResponse
    {
        // Fetch the subscription plans using the repository with the required relationships and pagination
        $subscriptionPlans = $this->subscriptionPlanRepository->getProjectSubscriptionPlans();

        // Return subscription plans as a JSON response using SubscriptionPlanResource
        return SubscriptionPlanResource::collection($subscriptionPlans)->response();
    }
    */

    public function showSubscriptionPlans()
    {
        $time = now()->addDay();
        $pageNumber = ($number = (int) request()->input('page')) > 0 ? $number : 1;
        $perPage = ($number = (int) request()->input('per_page')) > 0 ? $number : 15;

        /// Retrieve the result from the cache or make a request and cache the response for one day
        $response = $this->project->subscriptionPlans()->whereIsRoot()->withCount('children')->latest()->paginate();

        return SubscriptionPlanResource::collection($response)->response();
    }

    public function showSubscriptionPlan()
    {
        $time = now()->addDay();
        $type = request()->type;
        $pageNumber = ($number = (int) request()->input('page')) > 0 ? $number : 1;
        $perPage = ($number = (int) request()->input('per_page')) > 0 ? $number : 15;

        /// Set the cache name
        $cacheName = 'projects-'.$this->project->id.'-subscription-plan-'.$this->subscriptionPlan->id.'-'.$type.'-'.$perPage.'-'.$pageNumber;

        if( $type == 'children') {

            $response = $this->subscriptionPlan->children()->withCount('children')->latest()->paginate($perPage);

        }else if( $type == 'descendants') {

            $response = $this->subscriptionPlan->descendants()->withCount('descendants')->latest()->paginate($perPage);

        }else if( $type == 'ancestors') {

            $response = $this->subscriptionPlan->ancestors()->withCount('ancestors')->latest()->paginate($perPage);

        }else if( $type == 'parent') {

            $response = $this->subscriptionPlan->parent;

        }else{

            $response = $this->subscriptionPlan;

        }

        if(is_null($response)) {

            return [
                'subscription_plan' => null
            ];

        }else{

            if($response instanceOf SubscriptionPlan) {

                return [
                    'subscription_plan' => new SubscriptionPlanResource($response)
                ];

            }else{

                return SubscriptionPlanResource::collection($response);

            }
        }

    }

}
