<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\SubscriptionPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionPlanRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var SubscriptionPlan|null The subscription plan instance associated with the repository.
     */
    protected ?SubscriptionPlan $subscriptionPlan;

    /**
     *  Create a new SubscriptionPlanRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param SubscriptionPlan|null $subscriptionPlan The subscription plan instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?SubscriptionPlan $subscriptionPlan = null)
    {
        $this->project = $project;
        $this->subscriptionPlan = $subscriptionPlan;
    }

    /**
     *  Get the subscription plans for the project with optional relationships and pagination.
     *
     *  @param array $relationships The relationships to eager load on the subscription plans.
     *  @param array $countableRelationships The relationships to count on the subscription plans.
     *  @return LengthAwarePaginator The paginated list of project subscription plans.
     */
    public function getProjectSubscriptionPlans($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->project->subscriptionPlans()->with($relationships)->withCount($countableRelationships)->latest()->paginate();
    }

    /**
     *  Create a new subscription plan for the project.
     *
     *  @param string $name The name of the subscription plan to be created.
     *  @param string $price The price of the subscription plan to be created.
     *  @param string $duration The duration of the subscription plan to be created.
     *  @param string $frequency The frequency of the subscription plan to be created.
     *  @return SubscriptionPlan The newly created subscription plan instance.
     */
    public function createProjectSubscriptionPlan(string $name, string $price, string $duration, string $frequency): SubscriptionPlan
    {
        return SubscriptionPlan::create([
            'project_id' => $this->project->id,
            'frequency' => $frequency,
            'duration' => $duration,
            'price' => $price,
            'name' => $name,
        ]);
    }

    /**
     *  Update an existing subscription plan for the project.
     *
     *  @param string $name The name of the subscription plan to be created.
     *  @param string $price The price of the subscription plan to be created.
     *  @param string $duration The duration of the subscription plan to be created.
     *  @param string $frequency The frequency of the subscription plan to be created.
     *  @return bool True if the update is successful, false otherwise.
     *  @throws ModelNotFoundException If the associated subscription is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function updateProjectSubscriptionPlan(string $name, string $price, string $duration, string $frequency): bool
    {
        // Make sure the subscription plan exists and belongs to the project
        if ($this->subscriptionPlan === null || $this->subscriptionPlan->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        return $this->subscriptionPlan->update([
            'project_id' => $this->project->id,
            'frequency' => $frequency,
            'duration' => $duration,
            'price' => $price,
            'name' => $name,
        ]);
    }

    /**
     *  Delete a subscription plan for the project.
     *
     *  @return bool|null True if the deletion is successful, false if the subscription plan is not found.
     *  @throws ModelNotFoundException If the associated subscription plan is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteProjectSubscriptionPlan(): bool|null
    {
        // Make sure the subscription plan exists and belongs to the project
        if ($this->subscriptionPlan === null || $this->subscriptionPlan->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        // Delete subscription plan
        return $this->subscriptionPlan->delete();
    }
}
