<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Database\Eloquent\Relations\HasMany;
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
     *  Get the subscription plans for the project with optional relationships
     *
     *  @param array $relationships The relationships to eager load on the subscription plans.
     *  @param array $countableRelationships The relationships to count on the subscription plans.
     *  @return HasMany
     */
    public function queryProjectSubscriptionPlans($relationships = [], $countableRelationships = []): HasMany
    {
        return $this->project->subscriptionPlans()->whereIsRoot()->with($relationships)->withCount($countableRelationships)->latest();
    }

    /**
     *  Get the subscription plans for the project with optional relationships
     *
     *  @param array $relationships - The relationships to eager load on the subscription plans.
     *  @param array $countableRelationships - The relationships to count on the subscription plans.
     *
     *  @return LengthAwarePaginator - The paginated list of project subscription plans.
     */
    public function getProjectSubscriptionPlans($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectSubscriptionPlans($relationships, $countableRelationships)->paginate();
    }

    /**
     *  Get the subscription plan children for the project
     *
     *  @param array $relationships - The relationships to eager load on the subscription plans.
     *  @param array $countableRelationships - The relationships to count on the subscription plans.
     *
     *  @return LengthAwarePaginator The paginated list of project subscription plans.
     */
    public function getProjectSubscriptionPlanChildren($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->subscriptionPlan->children()->with($relationships)->withCount($countableRelationships)->latest()->paginate();
    }

    /**
     *  Get the subscription plan breadcrumb navigation
     *
     *  @return array The breadcrumb navigation
     */
    public function getProjectSubscriptionPlanBreadcrumbNavigation(): array
    {
        return collect($this->subscriptionPlan->ancestorsAndSelf($this->subscriptionPlan->id))->map(fn($subscriptionPlan) => collect($subscriptionPlan)->only(['id', 'name']))->toArray();
    }

    /**
     *  Create a new subscription plan for the project.
     *
     *  @param string $name - The name of the subscription plan.
     *  @param string|null $description - The description of the subscription plan.
     *  @param bool $active - Whether the subscription plan is active.
     *  @param bool $isFolder - Whether the subscription plan is a folder.
     *  @param string|null $price - The price of the subscription plan.
     *  @param string|null $duration - The duration of the subscription plan.
     *  @param string|null $frequency - The frequency of the subscription plan.
     *  @param array|null $tags - The tags of the subscription plan.
     *  @param bool|null $canAutoBill - Can auto bill status of the subscription plan.
     *  @param string $billingProductId - Used by the Mobile Network AAS Payment System to uniquely identify the product being purchased.
     *  @param string|null $billingPurchaseCategoryCode - Used by the Mobile Network AAS Payment System to specify the category defining the content type (This parameter MUST be filled with values validated by AAS integration team).
     *  @param int|null $maxAutoBillingAttempts - Maximum auto billing attempts of the subscription plan.
     *  @param string|null $insufficientFundsMessage - The insufficient funds message of the subscription plan.
     *  @param string|null $successfulPaymentSmsMessage - The successful payment SMS message of the subscription plan.
     *  @param string|null $nextAutoBillingReminderSmsMessage - The next auto bill reminder SMS message.
     *  @param array|null $autoBillingReminderIds - The auto billing reminder ids of the subscription plan.
     *  @param string|null $autoBillingDisabledSmsMessage - The auto billing disabled SMS message.
     *  @param int|null $parentId - The id of the parent subscription plan.
     *
     *  @return SubscriptionPlan The newly created subscription plan instance.
     */
    public function createProjectSubscriptionPlan(
        string $name, string|null $description, bool $active, bool $isFolder, string|null $price, string|null $duration,
        string|null $frequency, array|null $tags, bool|null $canAutoBill, string $billingProductId,
        string|null $billingPurchaseCategoryCode, int|null $maxAutoBillingAttempts,
        string|null $insufficientFundsMessage, string|null $successfulPaymentSmsMessage,
        string|null $successfulAutoBillingPaymentSmsMessage,
        string|null $nextAutoBillingReminderSmsMessage,
        string|null $autoBillingDisabledSmsMessage,
        array|null $autoBillingReminderIds,
        int|null $parentId
    ): SubscriptionPlan
    {
        $this->subscriptionPlan = SubscriptionPlan::create([
            'successful_auto_billing_payment_sms_message' => $successfulAutoBillingPaymentSmsMessage,
            'next_auto_billing_reminder_sms_message' => $nextAutoBillingReminderSmsMessage,
            'auto_billing_disabled_sms_message' => $autoBillingDisabledSmsMessage,
            'successful_payment_sms_message' => $successfulPaymentSmsMessage,
            'billing_purchase_category_code' => $billingPurchaseCategoryCode,
            'max_auto_billing_attempts' => $maxAutoBillingAttempts ?? 3,
            'insufficient_funds_message' => $insufficientFundsMessage,
            'billing_product_id' => $billingProductId,
            'can_auto_bill' => $canAutoBill ?? false,
            'project_id' => $this->project->id,
            'description' => $description,
            'frequency' => $frequency,
            'is_folder' => $isFolder,
            'duration' => $duration,
            'active' => $active,
            'price' => $price,
            'name' => $name,
            'tags' => $tags,
        ]);

        if( $parentSubscriptionPlan = SubscriptionPlan::find($parentId) ) {

            $parentSubscriptionPlan->prependNode($this->subscriptionPlan);

        }

        if(!is_null($autoBillingReminderIds)) {

            //  Update the subscription plan auto billing reminders
            $this->updateAutoBillingReminders($autoBillingReminderIds);

        }

        //  Clear the entire cache since we cache the API subscription plans on the Api\SubscriptionPlanController
        Cache::flush();

        return $this->subscriptionPlan;
    }

    /**
     *  Update an existing subscription plan for the project.
     *
     *  @param string $name - The name of the subscription plan.
     *  @param string|null $description - The description of the subscription plan.
     *  @param bool $active - Whether the subscription plan is active.
     *  @param bool $isFolder - Whether the subscription plan is a folder.
     *  @param string|null $price - The price of the subscription plan.
     *  @param string|null $duration - The duration of the subscription plan.
     *  @param string|null $frequency - The frequency of the subscription plan.
     *  @param array|null $tags - The tags of the subscription plan.
     *  @param bool|null $canAutoBill - Can auto bill status of the subscription plan.
     *  @param string $billingProductId - Used by the Mobile Network AAS Payment System to uniquely identify the product being purchased.
     *  @param string|null $billingPurchaseCategoryCode - Used by the Mobile Network AAS Payment System to specify the category defining the content type (This parameter MUST be filled with values validated by AAS integration team).
     *  @param int|null $maxAutoBillingAttempts - Maximum auto billing attempts of the subscription plan.
     *  @param string|null $insufficientFundsMessage - The insufficient funds message of the subscription plan.
     *  @param string|null $successfulPaymentSmsMessage - The successful payment SMS message of the subscription plan.
     *  @param string|null $nextAutoBillingReminderSmsMessage - The next auto bill reminder SMS message.
     *  @param string|null $autoBillingDisabledSmsMessage - The auto billing disabled SMS message.
     *  @param array|null $autoBillingReminderIds - The auto billing reminder ids of the subscription plan.
     *
     *  @return bool True if the update is successful, false otherwise.
     *
     *  @throws ModelNotFoundException If the associated subscription is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function updateProjectSubscriptionPlan(
        string $name, string|null $description, bool $active, bool $isFolder, string|null $price, string|null $duration,
        string|null $frequency, array|null $tags, bool|null $canAutoBill, string $billingProductId,
        string|null $billingPurchaseCategoryCode, int|null $maxAutoBillingAttempts,
        string|null $insufficientFundsMessage, string|null $successfulPaymentSmsMessage,
        string|null $successfulAutoBillingPaymentSmsMessage,
        string|null $nextAutoBillingReminderSmsMessage,
        string|null $autoBillingDisabledSmsMessage,
        array|null $autoBillingReminderIds
    ): bool
    {
        // Make sure the subscription plan exists and belongs to the project
        if ($this->subscriptionPlan === null || $this->subscriptionPlan->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        $status = $this->subscriptionPlan->update([
            'max_auto_billing_attempts' => !is_null($maxAutoBillingAttempts) ? $maxAutoBillingAttempts : $this->subscriptionPlan->max_auto_billing_attempts,
            'can_auto_bill' => !is_null($canAutoBill) ? $canAutoBill : $this->subscriptionPlan->can_auto_bill,
            'successful_auto_billing_payment_sms_message' => $successfulAutoBillingPaymentSmsMessage,
            'is_folder' => !is_null($isFolder) ? $isFolder : $this->subscriptionPlan->is_folder,
            'next_auto_billing_reminder_sms_message' => $nextAutoBillingReminderSmsMessage,
            'active' => !is_null($active) ? $active : $this->subscriptionPlan->active,
            'auto_billing_disabled_sms_message' => $autoBillingDisabledSmsMessage,
            'successful_payment_sms_message' => $successfulPaymentSmsMessage,
            'billing_purchase_category_code' => $billingPurchaseCategoryCode,
            'insufficient_funds_message' => $insufficientFundsMessage,
            'billing_product_id' => $billingProductId,
            'project_id' => $this->project->id,
            'description' => $description,
            'frequency' => $frequency,
            'duration' => $duration,
            'price' => $price,
            'name' => $name,
            'tags' => $tags
        ]);

        if(!is_null($autoBillingReminderIds)) {

            //  Update the subscription plan auto billing reminders
            $this->updateAutoBillingReminders($autoBillingReminderIds);

        }

        //  Clear the entire cache since we cache the API subscription plans on the Api\SubscriptionPlanController
        Cache::flush();

        return $status;
    }

    /**
     *  Update the subscription plan auto billing reminders for the project.
     *
     *  @param $autoBillingReminderIds - The auto billing reminder ids
     *  @return void
     */
    public function updateAutoBillingReminders(array $autoBillingReminderIds = []): void
    {
        if( count( $autoBillingReminderIds ) ) {

            //  Sync the auto billing reminders
            $this->subscriptionPlan->autoBillingReminders()->syncWithPivotValues($autoBillingReminderIds, [
                'project_id' => $this->project->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }
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
        $status = $this->subscriptionPlan->delete();

        //  Clear the entire cache since we cache the API subscription plans on the Api\SubscriptionPlanController
        Cache::flush();

        return $status;
    }
}
