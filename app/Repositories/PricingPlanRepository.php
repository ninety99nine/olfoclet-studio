<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

class PricingPlanRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var PricingPlan|null The pricing plan instance associated with the repository.
     */
    protected ?PricingPlan $pricingPlan;

    /**
     *  Create a new PricingPlanRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param PricingPlan|null $pricingPlan The pricing plan instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?PricingPlan $pricingPlan = null)
    {
        $this->project = $project;
        $this->pricingPlan = $pricingPlan;
    }

    /**
     *  Get the pricing plans for the project with optional relationships
     *
     *  @param array $relationships The relationships to eager load on the pricing plans.
     *  @param array $countableRelationships The relationships to count on the pricing plans.
     *  @return HasMany
     */
    public function queryProjectPricingPlans($relationships = [], $countableRelationships = []): HasMany
    {
        return $this->project->pricingPlans()->whereIsRoot()->with($relationships)->withCount($countableRelationships)->latest();
    }

    /**
     *  Get the pricing plans for the project with optional relationships
     *
     *  @param array $relationships - The relationships to eager load on the pricing plans.
     *  @param array $countableRelationships - The relationships to count on the pricing plans.
     *
     *  @return LengthAwarePaginator - The paginated list of project pricing plans.
     */
    public function getProjectPricingPlans($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectPricingPlans($relationships, $countableRelationships)->paginate();
    }

    /**
     *  Get the pricing plan children for the project
     *
     *  @param array $relationships - The relationships to eager load on the pricing plans.
     *  @param array $countableRelationships - The relationships to count on the pricing plans.
     *
     *  @return LengthAwarePaginator The paginated list of project pricing plans.
     */
    public function getProjectPricingPlanChildren($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->pricingPlan->children()->with($relationships)->withCount($countableRelationships)->latest()->paginate();
    }

    /**
     *  Get the pricing plan breadcrumb navigation
     *
     *  @return array The breadcrumb navigation
     */
    public function getProjectPricingPlanBreadcrumbNavigation(): array
    {
        return collect($this->pricingPlan->ancestorsAndSelf($this->pricingPlan->id))->map(fn($pricingPlan) => collect($pricingPlan)->only(['id', 'name']))->toArray();
    }

    /**
     *  Create a new pricing plan for the project.
     *
     *  @param string $name - The name of the pricing plan.
     *  @param string|null $description - The description of the pricing plan.
     *  @param bool $active - Whether the pricing plan is active.
     *  @param bool $isFolder - Whether the pricing plan is a folder.
     *  @param string|null $price - The price of the pricing plan.
     *  @param int|null $trialDays - The number of trial days before billing.
     *  @param string|null $duration - The duration of the pricing plan.
     *  @param string|null $frequency - The frequency of the pricing plan.
     *  @param array|null $tags - The tags of the pricing plan.
     *  @param bool|null $canAutoBill - Can auto bill status of the pricing plan.
     *  @param string $billingProductId - Used by the Mobile Network AAS Payment System to uniquely identify the product being purchased.
     *  @param string|null $billingPurchaseCategoryCode - Used by the Mobile Network AAS Payment System to specify the category defining the content type (This parameter MUST be filled with values validated by AAS integration team).
     *  @param int|null $maxAutoBillingAttempts - Maximum auto billing attempts of the pricing plan.
     *  @param string|null $insufficientFundsMessage - The insufficient funds message of the pricing plan.
     *  @param string|null $trialStartedSmsMessage - The trial started SMS message of the pricing plan.
     *  @param string|null $successfulPaymentSmsMessage - The successful payment SMS message of the pricing plan.
     *  @param string|null $nextAutoBillingReminderSmsMessage - The next auto bill reminder SMS message.
     *  @param array|null $autoBillingReminderIds - The auto billing reminder ids of the pricing plan.
     *  @param string|null $autoBillingDisabledSmsMessage - The auto billing disabled SMS message.
     *  @param int|null $parentId - The id of the parent pricing plan.
     *
     *  @return PricingPlan The newly created pricing plan instance.
     */
    public function createProjectPricingPlan(
        string $name, string|null $description, bool $active, bool $isFolder, string|null $price, int|null $trialDays, string|null $billingType,
        string|null $duration, string|null $frequency, array|null $tags, bool|null $canAutoBill, string $billingProductId,
        string|null $billingPurchaseCategoryCode, int|null $maxAutoBillingAttempts,
        string|null $insufficientFundsMessage, string|null $trialStartedSmsMessage,
        string|null $successfulPaymentSmsMessage,
        string|null $successfulAutoBillingPaymentSmsMessage,
        string|null $nextAutoBillingReminderSmsMessage,
        string|null $autoBillingDisabledSmsMessage,
        array|null $autoBillingReminderIds,
        int|null $parentId
    ): PricingPlan
    {
        $this->pricingPlan = PricingPlan::create([
            'successful_auto_billing_payment_sms_message' => $successfulAutoBillingPaymentSmsMessage,
            'next_auto_billing_reminder_sms_message' => $nextAutoBillingReminderSmsMessage,
            'auto_billing_disabled_sms_message' => $autoBillingDisabledSmsMessage,
            'successful_payment_sms_message' => $successfulPaymentSmsMessage,
            'billing_purchase_category_code' => $billingPurchaseCategoryCode,
            'max_auto_billing_attempts' => $maxAutoBillingAttempts ?? 3,
            'insufficient_funds_message' => $insufficientFundsMessage,
            'trial_started_sms_message' => $trialStartedSmsMessage,
            'billing_product_id' => $billingProductId,
            'can_auto_bill' => $canAutoBill ?? false,
            'project_id' => $this->project->id,
            'trial_days' => $trialDays ?? 0,
            'billing_type' => $billingType,
            'description' => $description,
            'frequency' => $frequency,
            'is_folder' => $isFolder,
            'duration' => $duration,
            'active' => $active,
            'price' => $price,
            'name' => $name,
            'tags' => $tags,
        ]);

        if( $parentPricingPlan = PricingPlan::find($parentId) ) {

            $parentPricingPlan->prependNode($this->pricingPlan);

        }

        if(!is_null($autoBillingReminderIds)) {

            //  Update the pricing plan auto billing reminders
            $this->updateAutoBillingReminders($autoBillingReminderIds);

        }

        //  Clear the entire cache since we cache the API pricing plans on the Api\PricingPlanController
        Cache::flush();

        return $this->pricingPlan;
    }

    /**
     *  Update an existing pricing plan for the project.
     *
     *  @param string $name - The name of the pricing plan.
     *  @param string|null $description - The description of the pricing plan.
     *  @param bool $active - Whether the pricing plan is active.
     *  @param bool $isFolder - Whether the pricing plan is a folder.
     *  @param string|null $price - The price of the pricing plan.
     *  @param int|null $trialDays - The number of trial days before billing.
     *  @param string|null $duration - The duration of the pricing plan.
     *  @param string|null $frequency - The frequency of the pricing plan.
     *  @param array|null $tags - The tags of the pricing plan.
     *  @param bool|null $canAutoBill - Can auto bill status of the pricing plan.
     *  @param string $billingProductId - Used by the Mobile Network AAS Payment System to uniquely identify the product being purchased.
     *  @param string|null $billingPurchaseCategoryCode - Used by the Mobile Network AAS Payment System to specify the category defining the content type (This parameter MUST be filled with values validated by AAS integration team).
     *  @param int|null $maxAutoBillingAttempts - Maximum auto billing attempts of the pricing plan.
     *  @param string|null $insufficientFundsMessage - The insufficient funds message of the pricing plan.
     *  @param string|null $trialStartedSmsMessage - The trial started SMS message of the pricing plan.
     *  @param string|null $successfulPaymentSmsMessage - The successful payment SMS message of the pricing plan.
     *  @param string|null $nextAutoBillingReminderSmsMessage - The next auto bill reminder SMS message.
     *  @param string|null $autoBillingDisabledSmsMessage - The auto billing disabled SMS message.
     *  @param array|null $autoBillingReminderIds - The auto billing reminder ids of the pricing plan.
     *
     *  @return bool True if the update is successful, false otherwise.
     *
     *  @throws ModelNotFoundException If the associated subscription is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function updateProjectPricingPlan(
        string $name, string|null $description, bool $active, bool $isFolder, string|null $price, int|null $trialDays, string|null $billingType,
        string|null $duration, string|null $frequency, array|null $tags, bool|null $canAutoBill, string $billingProductId,
        string|null $billingPurchaseCategoryCode, int|null $maxAutoBillingAttempts,
        string|null $insufficientFundsMessage, string|null $trialStartedSmsMessage,
        string|null $successfulPaymentSmsMessage,
        string|null $successfulAutoBillingPaymentSmsMessage,
        string|null $nextAutoBillingReminderSmsMessage,
        string|null $autoBillingDisabledSmsMessage,
        array|null $autoBillingReminderIds
    ): bool
    {
        // Make sure the pricing plan exists and belongs to the project
        if ($this->pricingPlan === null || $this->pricingPlan->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        $status = $this->pricingPlan->update([
            'max_auto_billing_attempts' => !is_null($maxAutoBillingAttempts) ? $maxAutoBillingAttempts : $this->pricingPlan->max_auto_billing_attempts,
            'can_auto_bill' => !is_null($canAutoBill) ? $canAutoBill : $this->pricingPlan->can_auto_bill,
            'successful_auto_billing_payment_sms_message' => $successfulAutoBillingPaymentSmsMessage,
            'trial_days' => !is_null($trialDays) ? $trialDays : $this->pricingPlan->trial_days,
            'is_folder' => !is_null($isFolder) ? $isFolder : $this->pricingPlan->is_folder,
            'next_auto_billing_reminder_sms_message' => $nextAutoBillingReminderSmsMessage,
            'active' => !is_null($active) ? $active : $this->pricingPlan->active,
            'auto_billing_disabled_sms_message' => $autoBillingDisabledSmsMessage,
            'successful_payment_sms_message' => $successfulPaymentSmsMessage,
            'billing_purchase_category_code' => $billingPurchaseCategoryCode,
            'insufficient_funds_message' => $insufficientFundsMessage,
            'trial_started_sms_message' => $trialStartedSmsMessage,
            'billing_product_id' => $billingProductId,
            'project_id' => $this->project->id,
            'billing_type' => $billingType,
            'description' => $description,
            'frequency' => $frequency,
            'duration' => $duration,
            'price' => $price,
            'name' => $name,
            'tags' => $tags
        ]);

        if(!is_null($autoBillingReminderIds)) {

            //  Update the pricing plan auto billing reminders
            $this->updateAutoBillingReminders($autoBillingReminderIds);

        }

        //  Clear the entire cache since we cache the API pricing plans on the Api\PricingPlanController
        Cache::flush();

        return $status;
    }

    /**
     *  Update the pricing plan auto billing reminders for the project.
     *
     *  @param $autoBillingReminderIds - The auto billing reminder ids
     *  @return void
     */
    public function updateAutoBillingReminders(array $autoBillingReminderIds = []): void
    {
        if( count( $autoBillingReminderIds ) ) {

            //  Sync the auto billing reminders
            $this->pricingPlan->autoBillingReminders()->syncWithPivotValues($autoBillingReminderIds, [
                'project_id' => $this->project->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }
    }

    /**
     *  Delete a pricing plan for the project.
     *
     *  @return bool|null True if the deletion is successful, false if the pricing plan is not found.
     *  @throws ModelNotFoundException If the associated pricing plan is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteProjectPricingPlan(): bool|null
    {
        // Make sure the pricing plan exists and belongs to the project
        if ($this->pricingPlan === null || $this->pricingPlan->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        // Delete pricing plan
        $status = $this->pricingPlan->delete();

        //  Clear the entire cache since we cache the API pricing plans on the Api\PricingPlanController
        Cache::flush();

        return $status;
    }
}
