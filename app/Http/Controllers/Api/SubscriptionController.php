<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Services\BillingService;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use App\Models\BillingTransaction;
use App\Http\Controllers\Controller;
use App\Enums\CreatedUsingAutoBilling;
use App\Repositories\SubscriberRepository;
use App\Repositories\SubscriptionRepository;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\BillingTransactionResource;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\UpdateSubscriptionRequest;
use App\Http\Requests\Subscriptions\CancelSubscriptionsRequest;

class SubscriptionController extends Controller
{
    protected $project;
    protected $subscription;
    protected $subscriberRepository;
    protected $subscriptionRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));

        if(request()->routeIs('api.show.subscription')) {

            $this->subscription = $this->project->subscriptions()->where('id', request()->subscription)->with(['subscriptionPlan'])->first();

        }else{

            if(!empty(request()->subscription)) {

                $this->subscription = $this->project->subscriptions()->where('id', request()->subscription)->firstOrFail();

            }

        }

        $this->subscriberRepository = new SubscriberRepository($this->project, null);
        $this->subscriptionRepository = new SubscriptionRepository($this->project, $this->subscription);
    }

    public function showSubscription()
    {
        // Return JSON response
        return response()->json([
            'exists' =>  !is_null($this->subscription),
            'subscription' => !is_null($this->subscription) ? new SubscriptionResource($this->subscription) : null
        ]);
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
            $subscription = $this->subscriptionRepository->createProjectSubscription($subscriber, $subscriptionPlan, CreatedUsingAutoBilling::NO, $billingTransaction);

        }else {

            //  Failure message
            $message = $billingTransaction->last_failure_message;

        }

        // Return JSON response
        return response()->json([
            'message' => $message,
            'successful' => $isSuccessful,
            'billingTransaction' => new BillingTransactionResource($billingTransaction),
            'subscription' => $isSuccessful ? new SubscriptionResource($subscription) : null
        ], 201);
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

    public function cancelSubscription(): JsonResponse
    {
        // Cancel the subscription
        $status = $this->subscriptionRepository->cancelProjectSubscription();

        // Return a success JSON response
        return response()->json([
            'message' => $status ? 'Subscription cancelled successfully' : 'Failed to cancel subscription',
            'status' => $status
        ]);
    }

    public function uncancelSubscription(): JsonResponse
    {
        // Uncancel the subscription
        $status = $this->subscriptionRepository->uncancelProjectSubscription();

        // Return a success JSON response
        return response()->json([
            'message' => $status ? 'Subscription uncancelled successfully' : 'Failed to uncancel subscription',
            'status' => $status
        ]);
    }

    public function cancelSubscriptions(CancelSubscriptionsRequest $request): JsonResponse
    {
        $data = [
            'msisdn' => $request->input('msisdn') ?? null,
            'tags' => $request->input('tags') ?? null
        ];

        // Cancel the subscriptions matching the specified subscriber msisdn
        $status = $this->subscriptionRepository->cancelProjectSubscriptions($data);

        // Return a success JSON response
        return response()->json([
            'message' => $status ? 'Subscriptions cancelled successfully' : 'No subscriptions cancelled',
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
