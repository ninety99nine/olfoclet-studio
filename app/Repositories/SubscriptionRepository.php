<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use App\Enums\CreatedUsingAutoBilling;
use Illuminate\Database\Eloquent\Builder;
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
     *  Count the total number of subscriptions for the associated project.
     *
     *  @return int The total number of subscriptions.
     */
    public function countProjectSubscriptions(): int
    {
        return $this->project->subscriptions()->count();
    }

    /**
     *  Get the subscriptions for the project with optional relationships
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
     *  @param CreatedUsingAutoBilling $createdUsingAutoBilling Whether this subscription was created using auto billing
     *
     *  @return Subscription The newly created subscription instance.
     */
    public function createProjectSubscription(Subscriber $subscriber, SubscriptionPlan $subscriptionPlan, CreatedUsingAutoBilling $createdUsingAutoBilling = CreatedUsingAutoBilling::NO): Subscription
    {
        $startAt = Carbon::now();
        $endAt = $this->calculateEndDate($subscriptionPlan);

        //  Create a new subscription
        $subscription = Subscription::create([
            'created_using_auto_billing' => $createdUsingAutoBilling->value,
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscriber_id' => $subscriber->id,
            'project_id' => $this->project->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        //  Update the auto billing schedule
        $this->updateAutoBillingSchedule($subscriber, $subscriptionPlan, $createdUsingAutoBilling);

        return $subscription;
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

        //  Update existing subscription
        $status = $this->subscription->update([
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscriber_id' => $subscriber->id,
            'project_id' => $this->project->id,
            'start_at' => $startAt,
            'end_at' => $endAt
        ]);

        //  Update the auto billing schedule
        $this->updateAutoBillingSchedule($subscriber, $subscriptionPlan);

        return $status;
    }

    /**
     *  Update the auto billing schedule.
     *
     *  @param Subscriber $subscriber The subscriber for whom the auto billing schedule will be updated.
     *  @param SubscriptionPlan $subscriptionPlan The new subscription plan associated with the subscription.
     *  @param CreatedUsingAutoBilling $createdUsingAutoBilling Whether this subscription was created using auto billing
     *
     *  @return void
     */
    public function updateAutoBillingSchedule(Subscriber $subscriber, SubscriptionPlan $subscriptionPlan, CreatedUsingAutoBilling $createdUsingAutoBilling = CreatedUsingAutoBilling::NO): void
    {
        /**
         *  @var bool $autoBillingEnabled Whether auto billing is enabled for the auto billing schedule.
         *
         *  This will also disable auto billing on an existing auto billing schedule if the subscription
         *  plan no longer supports auto billing. Remember that when the subscription plan can_auto_bill
         *  is "false", the AutoBillingBySubscriptionPlans Job will ignore the subscription plan since
         *  it does not support auto billing. So the auto_billing_enabled is not necessary to stop
         *  auto billing since this will be stopped at the AutoBillingBySubscriptionPlans Job
         *  layer, but its just to ensure that if the subscription plan can_auto_bill is set
         *  back to "true" and the AutoBillingBySubscriptionPlans Job recognises the same
         *  subscription plan for auto billing, then we do not auto bill subscribers who
         *  opted in while the subscription plan can_auto_bill was disabled. This avoids
         *  situations where a user subscribes while auto billing is not supported,
         *  then when it is supported after they have already subscribed, they
         *  then get auto billed eventhough they opted in while the
         *  subscription plan was not running on auto billing.
         */
        $autoBillingEnabled = $subscriptionPlan->can_auto_bill;

        /**
         *  @var Subscription $subscriptionWithFurthestEndAt
         */
        $subscriptionWithFurthestEndAt = $subscriber->subscriptionWithFurthestEndAt()
                                      ->where('subscription_plan_id', $subscriptionPlan->id)->first();

        /**
         *  @var bool $nextAttemptDate The date and time for auto billing to be attempted
         */
        $nextAttemptDate = $subscriptionWithFurthestEndAt->end_at;

        //  Auto billing schedule information
        $autoBillingSchedule = [
            'attempts' => 0,
            'reminded_one_hour_before' => 0,
            'reminded_six_hours_before' => 0,
            'subscriber_id' => $subscriber->id,
            'reminded_twelve_hours_before' => 0,
            'reminded_twenty_four_hours_before' => 0,
            'reminded_forty_eight_hours_before' => 0,
            'reminded_seventy_two_hours_before' => 0,
            'auto_billing_enabled' => $autoBillingEnabled,
            'subscription_plan_id' => $subscriptionPlan->id,
            'next_attempt_date' => $autoBillingEnabled ? $nextAttemptDate : null,
        ];

        //  Query the existing auto billing schedule (if any)
        $existingAutoBillingSchedule = DB::table('auto_billing_schedules')->where([
            'subscriber_id' => $subscriber->id,
            'subscription_plan_id' => $subscriptionPlan->id
        ])->first();

        //  If the auto billing schedule exists
        if( $existingAutoBillingSchedule ) {

            $autoBillingSchedule['total_successful_attempts'] =
                ($createdUsingAutoBilling == CreatedUsingAutoBilling::YES)
                    ? ($existingAutoBillingSchedule->total_successful_attempts + 1) : $existingAutoBillingSchedule->total_successful_attempts;

            //  Update existing auto billing schedule
            $existingAutoBillingSchedule->update($autoBillingSchedule);

        }else {

            //  Check if this subscription plan supports auto billing
            if($subscriptionPlan->can_auto_bill) {

                //  Create a new auto billing schedule
                DB::table('auto_billing_schedules')->insert($autoBillingSchedule);

            }

        }
    }

    /**
     *  Cancel an existing subscription for the project.
     *
     *  @return bool True if the subscription cancellation is successful, false otherwise.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function cancelProjectSubscription(): bool
    {
        return $this->subscription->update([
            'cancelled_at' => Carbon::now()
        ]);
    }

    /**
     *  Uncancel an existing subscription for the project.
     *
     *  @return bool True if the subscription uncancellation is successful, false otherwise.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function uncancelProjectSubscription(): bool
    {
        return $this->subscription->update([
            'cancelled_at' => null
        ]);
    }

    /**
     *  Cancel subscriptions matching the subscriber msisdn.
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @return bool True if the update is successful, false otherwise.
     */
    public function cancelProjectSubscriberSubscriptions(string $msisdn): bool
    {
        return $this->project->subscriptions()->active()->whereHas('subscriber', function (Builder $query) use ($msisdn) {

            $query->where('msisdn', $msisdn);

        })->update([
            'cancelled_at' => Carbon::now()
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
            default:
                return Carbon::now()->addDay();
        }
    }
}
