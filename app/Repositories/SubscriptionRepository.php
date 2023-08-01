<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var Subscription|null The subscription instance associated with the repository.
     */
    protected ?Subscription $subscription;

    /**
     *  Create a new SubscriptionRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param Subscription|null $subscription The subscription instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?Subscription $subscription = null)
    {
        $this->project = $project;
        $this->subscription = $subscription;
    }

    /**
     *  Query the subscription plans for the project.
     *
     *  @return HasMany The relationship to the subscription plans of the project.
     */
    public function queryProjectSubscriptionPlans(): HasMany
    {
        return $this->project->subscriptionPlans();
    }

    /**
     *  Get the subscription plans for the project.
     *
     *  @return Collection A collection of subscription plans associated with the project.
     */
    public function getProjectSubscriptionPlans(): Collection
    {
        return $this->queryProjectSubscriptionPlans()->get();
    }

    /**
     *  Get the subscriptions for the project with optional relationships and pagination.
     *
     *  @param array $relationships The relationships to eager load on the subscriptions.
     *  @param array $countableRelationships The relationships to count on the subscriptions.
     *  @return LengthAwarePaginator The paginated list of project subscriptions.
     */
    public function getProjectSubscriptions($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->project->subscriptions()->with($relationships)->withCount($countableRelationships)->latest()->paginate();
    }

    /**
     *  Create a new subscription for the project.
     *
     *  @param Subscriber $subscriber The subscriber for whom the subscription will be created.
     *  @param SubscriptionPlan $subscriptionPlan The subscription plan associated with the subscription.
     *  @return Subscription The newly created subscription instance.
     */
    public function createProjectSubscription(Subscriber $subscriber, SubscriptionPlan $subscriptionPlan): Subscription
    {
        $startAt = Carbon::now();
        $endAt = $this->calculateEndDate($subscriptionPlan);

        return Subscription::create([
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscriber_id' => $subscriber->id,
            'project_id' => $this->project->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);
    }

    /**
     *  Update an existing subscription for the project.
     *
     *  @param Subscriber $subscriber The subscriber for whom the subscription will be updated.
     *  @param SubscriptionPlan $subscriptionPlan The new subscription plan associated with the subscription.
     *  @return bool True if the update is successful, false otherwise.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function updateProjectSubscription(Subscriber $subscriber, SubscriptionPlan $subscriptionPlan): bool
    {
        // Make sure the subscription exists and belongs to the project
        if ($this->subscription === null || $this->subscription->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        $startAt = Carbon::now();
        $endAt = $this->calculateEndDate($subscriptionPlan);

        return $this->subscription->update([
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscriber_id' => $subscriber->id,
            'project_id' => $this->project->id,
            'start_at' => $startAt,
            'end_at' => $endAt
        ]);
    }

    /**
     *  Delete a subscription for the project.
     *
     *  @return bool|null True if the deletion is successful, false if the subscription is not found.
     *  @throws ModelNotFoundException If the associated subscription is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteProjectSubscription(): bool|null
    {
        // Make sure the subscription exists and belongs to the project
        if ($this->subscription === null || $this->subscription->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        // Delete subscription
        return $this->subscription->delete();
    }

    /**
     *  Calculate the end date based on the subscription plan frequency and duration.
     *
     *  @param SubscriptionPlan $subscriptionPlan The subscription plan to calculate the end date for.
     *  @return Carbon The calculated end date.
     */
    protected function calculateEndDate(SubscriptionPlan $subscriptionPlan): Carbon
    {
        switch ($subscriptionPlan->frequency) {
            case 'Years':
                return Carbon::now()->addYears($subscriptionPlan->duration);
            case 'Months':
                return Carbon::now()->addMonths($subscriptionPlan->duration);
            case 'Weeks':
                return Carbon::now()->addWeeks($subscriptionPlan->duration);
            case 'Days':
                return Carbon::now()->addDays($subscriptionPlan->duration);
            case 'Hours':
                return Carbon::now()->addHours($subscriptionPlan->duration);
            case 'Minutes':
                return Carbon::now()->addMinutes($subscriptionPlan->duration);
            case 'Seconds':
                return Carbon::now()->addSeconds($subscriptionPlan->duration);
            default:
                return Carbon::now()->addDay();
        }
    }
}
