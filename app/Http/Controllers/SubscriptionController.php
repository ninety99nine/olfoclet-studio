<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\PricingPlan;
use App\Http\Controllers\Controller;
use App\Enums\CreatedUsingAutoBilling;
use App\Repositories\SubscriberRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\PricingPlanRepository;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\ListSubscriptionsRequest;
use App\Http\Requests\Subscriptions\UpdateSubscriptionRequest;

class SubscriptionController extends Controller
{
    protected $project;
    protected $subscription;
    protected $subscriberRepository;
    protected $subscriptionRepository;
    protected $pricingPlanRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));

        $subscriptionId = request()->route('subscription');

        if (request()->routeIs('api.show.subscription')) {
            $this->subscription = $subscriptionId
                ? $this->project->subscriptions()->where('id', $subscriptionId)->with(['pricingPlan'])->first()
                : null;
        } elseif (!empty($subscriptionId)) {
            $this->subscription = $this->project->subscriptions()->where('id', $subscriptionId)->firstOrFail();
        }

        $this->subscriberRepository = new SubscriberRepository($this->project, null);
        $this->pricingPlanRepository = new PricingPlanRepository($this->project, null);
        $this->subscriptionRepository = new SubscriptionRepository($this->project, $this->subscription);
    }

    /**
     * Build filters array from validated request (for JSON and list).
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildFilters(array $validated): array
    {
        return [
            'msisdn' => $validated['msisdn'] ?? null,
            'status' => $validated['status'] ?? null,
            'pricing_plan_id' => $validated['pricing_plan_id'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? null,
            'sort' => $validated['sort'] ?? null,
        ];
    }

    public function showSubscriptions(Request $request)
    {
        $totalSubscribers = $this->subscriberRepository->countProjectSubscribers();

        // JSON request (e.g. from Axios): validate and return paginated list
        if ($request->expectsJson()) {
            $validated = $request->validate((new ListSubscriptionsRequest())->rules());
            $filters = $this->buildFilters($validated);
            $subscriptions = $this->subscriptionRepository->getProjectSubscriptions(
                $filters,
                ['subscriber:id,msisdn', 'pricingPlan', 'latestBillingTransaction'],
                []
            );

            return response()->json([
                'subscriptionsPayload' => $subscriptions,
                'totalSubscribers' => $totalSubscribers,
            ]);
        }

        // Inertia: render shell only; frontend fetches list via Axios
        $pricingPlans = $this->pricingPlanRepository->queryProjectPricingPlans()->get();

        return Inertia::render('Subscriptions/List/Main', [
            'totalSubscribers' => $totalSubscribers,
            'pricingPlans' => $pricingPlans,
        ]);
    }

    /**
     * Show a single subscription with details and relationships.
     */
    public function showSubscription()
    {
        $subscription = $this->subscription;
        if ($subscription === null) {
            abort(404, 'Subscription not found.');
        }

        $subscription->load([
            'subscriber:id,msisdn,project_id',
            'pricingPlan:id,name,project_id',
            'latestBillingTransaction',
        ]);

        return Inertia::render('Subscriptions/Show/Main', [
            'subscription' => $subscription,
            'project' => $this->project,
        ]);
    }

    public function createSubscription(CreateSubscriptionRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Get the pricing plan to be used when creating this subscription
        $pricingPlan = PricingPlan::find($request->input('pricing_plan_id'));

        // Create a new subscription using the repository
        $this->subscriptionRepository->createProjectSubscription($subscriber, $pricingPlan, CreatedUsingAutoBilling::NO);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscription(UpdateSubscriptionRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Fetch the subscriber from the subscriber repository
        $subscriber = $this->subscriberRepository->findOrCreateSubscriber($msisdn);

        // Get the pricing plan to be used when updating this subscription
        $pricingPlan = PricingPlan::find($request->input('pricing_plan_id'));

        // Update existing subscription using the repository
        $this->subscriptionRepository->updateProjectSubscription($subscriber, $pricingPlan);

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
