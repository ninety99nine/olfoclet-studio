<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\SubscriberRepository;
use App\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\UpdateSubscriptionRequest;

class SubscriptionApiController extends Controller
{
    protected $project;
    protected $subscriberRepository;
    protected $subscriptionRepository;

    public function __construct()
    {
        $project = Project::findOrFail(request()->route('project'));
        $subscription = request()->route('subscription') ? Subscription::findOrFail(request()->route('subscription')) : null;

        $this->subscriberRepository = new SubscriberRepository($project, null);
        $this->subscriptionRepository = new SubscriptionRepository($project, $subscription);
    }

    public function index(): JsonResponse
    {
        // Fetch the subscriptions using the repository with the required relationships and pagination
        $subscriptions = $this->subscriptionRepository->getProjectSubscriptions(['subscriber:id,msisdn', 'subscriptionPlan:id,name']);

        // Return subscriptions as a JSON response using SubscriptionResource
        return SubscriptionResource::collection($subscriptions)->response();
    }

    public function create(CreateSubscriptionRequest $request): JsonResponse
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Create a new subscription using the repository
        $subscriptionPlan = SubscriptionPlan::find($request->input('subscription_plan_id'));
        $subscription = $this->subscriptionRepository->createProjectSubscription($subscriber, $subscriptionPlan);

        // Return the created subscription as a JSON response using SubscriptionResource
        return (new SubscriptionResource($subscription))->response()->setStatusCode(201);
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Update the subscription using the repository
        $subscriptionPlan = SubscriptionPlan::find($request->input('subscription_plan_id'));
        $this->subscriptionRepository->updateProjectSubscription($subscriber, $subscriptionPlan);

        // Return a success JSON response
        return response()->json(['message' => 'Updated Successfully']);
    }

    public function delete(Subscription $subscription): JsonResponse
    {
        // Delete the subscription using the repository
        $this->subscriptionRepository->deleteProjectSubscription($subscription);

        // Return a success JSON response
        return response()->json(['message' => 'Deleted Successfully']);
    }
}
