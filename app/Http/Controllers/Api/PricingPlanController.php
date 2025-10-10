<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\PricingPlan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\SubscriptionRepository;
use App\Http\Resources\PricingPlanResource;
use App\Repositories\PricingPlanRepository;
use Illuminate\Support\Facades\Cache;

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

    /*
    public function showPricingPlans(): JsonResponse
    {
        // Fetch the pricing plans using the repository with the required relationships and pagination
        $pricingPlans = $this->pricingPlanRepository->getProjectPricingPlans();

        // Return pricing plans as a JSON response using PricingPlanResource
        return PricingPlanResource::collection($pricingPlans)->response();
    }
    */

    public function showPricingPlans()
    {
        $time = now()->addDay();
        $pageNumber = ($number = (int) request()->input('page')) > 0 ? $number : 1;
        $perPage = ($number = (int) request()->input('per_page')) > 0 ? $number : 15;

        /// Retrieve the result from the cache or make a request and cache the response for one day
        $response = $this->project->pricingPlans()->whereIsRoot()->withCount('children')->latest()->paginate();

        return PricingPlanResource::collection($response)->response();
    }

    public function showPricingPlan()
    {
        $time = now()->addDay();
        $type = request()->type;
        $pageNumber = ($number = (int) request()->input('page')) > 0 ? $number : 1;
        $perPage = ($number = (int) request()->input('per_page')) > 0 ? $number : 15;

        /// Set the cache name
        $cacheName = 'projects-'.$this->project->id.'-pricing-plan-'.$this->pricingPlan->id.'-'.$type.'-'.$perPage.'-'.$pageNumber;

        if( $type == 'children') {

            $response = $this->pricingPlan->children()->withCount('children')->latest()->paginate($perPage);

        }else if( $type == 'descendants') {

            $response = $this->pricingPlan->descendants()->withCount('descendants')->latest()->paginate($perPage);

        }else if( $type == 'ancestors') {

            $response = $this->pricingPlan->ancestors()->withCount('ancestors')->latest()->paginate($perPage);

        }else if( $type == 'parent') {

            $response = $this->pricingPlan->parent;

        }else{

            $response = $this->pricingPlan;

        }

        if(is_null($response)) {

            return [
                'pricing_plan' => null
            ];

        }else{

            if($response instanceOf PricingPlan) {

                return [
                    'pricing_plan' => new PricingPlanResource($response)
                ];

            }else{

                return PricingPlanResource::collection($response);

            }
        }

    }

}
