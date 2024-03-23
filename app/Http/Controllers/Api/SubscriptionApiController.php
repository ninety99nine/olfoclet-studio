<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use App\Models\BillingTransaction;
use App\Http\Controllers\Controller;
use App\Enums\CreatedUsingAutoBilling;
use App\Repositories\SubscriberRepository;
use App\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\UpdateSubscriptionRequest;
use App\Http\Requests\Subscriptions\CancelSubscriptionsRequest;

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

    public function showSubscriptions(): JsonResponse
    {
        // Fetch the subscriptions using the repository with the required relationships and pagination
        $subscriptions = $this->subscriptionRepository->getProjectSubscriptions(['subscriber:id,msisdn', 'subscriptionPlan:id,name']);

        // Return subscriptions as a JSON response using SubscriptionResource
        return SubscriptionResource::collection($subscriptions)->response();
    }

    public function createSubscription(CreateSubscriptionRequest $request): JsonResponse
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Get the subscription plan to be used when creating this subscription
        $subscriptionPlan = SubscriptionPlan::find($request->input('subscription_plan_id'));

        /**
         *  Bill the subscriber using artime.
         *
         *  @var BillingTransaction $billingTransaction
         */
        $billingTransaction = BillingService::billUsingAirtime($this->project, $subscriptionPlan, $subscriber, CreatedUsingAutoBilling::NO);

        //  Set the billing transaction status
        $isSuccessful = $billingTransaction->is_successful;

        //  If the subscriber was billed successfully
        if($isSuccessful) {

            //  Success message
            $message = 'Subscription created successfully';

            // Create a new subscription using the repository
            $subscription = $this->subscriptionRepository->createProjectSubscription($subscriber, $subscriptionPlan, CreatedUsingAutoBilling::NO);

        }else {

            //  Failure message
            $message = $billingTransaction->failure_reason;

        }

        // Return JSON response
        return response()->json([
            'message' => $message,
            'created' => $isSuccessful,
            'subscription' => $isSuccessful ? new SubscriptionResource($subscription) : null,
        ]);
    }

    public function updateSubscription(UpdateSubscriptionRequest $request): JsonResponse
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Get the subscription plan to be used when updating this subscription
        $subscriptionPlan = SubscriptionPlan::find($request->input('subscription_plan_id'));

        // Update existing subscription using the repository
        $this->subscriptionRepository->updateProjectSubscription($subscriber, $subscriptionPlan);

        // Return a success JSON response
        return response()->json(['message' => 'Updated Successfully']);
    }

    public function cancelSubscription(CancelSubscriptionsRequest $request): JsonResponse
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Cancel the active subscriptions matching the specified subscriber msisdn
        $status = $this->subscriptionRepository->cancelProjectSubscriberSubscriptions($msisdn);

        // Return a success JSON response
        return response()->json([
            'message' => $status ? 'Subscriptions cancelled successfully' : 'Failed to cancel subscriptions',
            'status' => $status
        ]);
    }

    public function deleteSubscription(): JsonResponse
    {
        // Delete the subscription using the repository
        $this->subscriptionRepository->deleteProjectSubscription();

        // Return a success JSON response
        return response()->json(['message' => 'Deleted Successfully']);
    }
}
