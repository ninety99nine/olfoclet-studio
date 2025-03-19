<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use App\Enums\CreatedUsingAutoBilling;
use App\Enums\MessageType;
use App\Models\BillingTransaction;
use App\Models\Pivots\AutoBillingSchedule;
use App\Services\SmsService;
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
     *  @param CreatedUsingAutoBilling $createdUsingAutoBilling Whether this subscription was created using auto billing.
     *  @param BillingTransaction|null $billingTransaction The BillingTransaction associated with the subscription.
     *
     *  @return Subscription The newly created subscription instance.
     */
    public function createProjectSubscription(Subscriber $subscriber, SubscriptionPlan $subscriptionPlan, CreatedUsingAutoBilling $createdUsingAutoBilling = CreatedUsingAutoBilling::NO, BillingTransaction|null $billingTransaction = null): Subscription
    {
        /**
         * @var Subscription|null $subscriptionWithFurthestEndAt
         */
        $subscriptionWithFurthestEndAt = $subscriber->subscriptionWithFurthestEndAt()
                                                    ->where('subscription_plan_id', $subscriptionPlan->id)
                                                    ->first();

        // Use the furthest end date if it's in the future, otherwise use now()
        $startAt = ($subscriptionWithFurthestEndAt && $subscriptionWithFurthestEndAt->end_at && $subscriptionWithFurthestEndAt->end_at->isFuture())
            ? $subscriptionWithFurthestEndAt->end_at
            : now();

        $endAt = $this->calculateEndDate($startAt, $subscriptionPlan);

        //  Create a new subscription
        $subscription = Subscription::create([
            'created_using_auto_billing' => $createdUsingAutoBilling->value,
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscriber_id' => $subscriber->id,
            'project_id' => $this->project->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        if($billingTransaction) {

            $billingTransaction->update([
                'subscription_id' => $subscription->id,
            ]);

            //  Update the auto billing schedule
            $this->updateAutoBillingSchedule($subscriber, $subscriptionPlan, $subscription, $createdUsingAutoBilling);

            //  If the project has the SMS credentials
            if( $this->project->hasSmsCredentials() ) {

                //  Set the message type
                $messageType = MessageType::PaymentConfirmation;

                if($createdUsingAutoBilling == CreatedUsingAutoBilling::YES) {

                    /**
                     *  Set the successful auto billing payment sms message
                     *
                     *  @var string $messageContent
                     */
                    $messageContent = $subscriptionPlan->craftSuccessfulAutoBillingPaymentSmsMessage($subscription);

                }else{

                    /**
                     *  Set the successful payment sms message
                     *
                     *  @var string $messageContent
                     */
                    $messageContent = $subscriptionPlan->craftSuccessfulPaymentSmsMessage($subscription);

                }

                //  Send the successful payment SMS message
                SmsService::sendSms($this->project, $subscriber, $messageContent, $messageType);

            }

        }

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

        /**
         * @var Subscription|null $subscriptionWithFurthestEndAt
         */
        $subscriptionWithFurthestEndAt = $subscriber->subscriptionWithFurthestEndAt()
                                                    ->where('subscription_plan_id', $subscriptionPlan->id)
                                                    ->first();

        // Use the furthest end date if it's in the future, otherwise use now()
        $startAt = ($subscriptionWithFurthestEndAt && $subscriptionWithFurthestEndAt->end_at && $subscriptionWithFurthestEndAt->end_at->isFuture())
            ? $subscriptionWithFurthestEndAt->end_at
            : now();

        $endAt = $this->calculateEndDate($startAt, $subscriptionPlan);

        //  Update existing subscription
        $status = $this->subscription->update([
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscriber_id' => $subscriber->id,
            'project_id' => $this->project->id,
            'start_at' => $startAt,
            'end_at' => $endAt
        ]);

        /**
         *  @var Subscription $subscriptionWithFurthestEndAt
         */
        $subscription = $this->subscription->fresh();

        //  Update the auto billing schedule
        $this->updateAutoBillingSchedule($subscriber, $subscriptionPlan, $subscription);

        return $status;
    }

    /**
     *  Update the auto billing schedule.
     *
     *  @param Subscriber $subscriber The subscriber for whom the auto billing schedule will be updated.
     *  @param SubscriptionPlan $subscriptionPlan The new subscription plan associated with the subscription.
     *  @param Subscription $subscriptionWithFurthestEndAt The subscription with the furthest end at datetime.
     *  @param CreatedUsingAutoBilling $createdUsingAutoBilling Whether this subscription was created using auto billing.
     *
     *  @return void
     */
    public function updateAutoBillingSchedule(Subscriber $subscriber, SubscriptionPlan $subscriptionPlan, Subscription $subscriptionWithFurthestEndAt, CreatedUsingAutoBilling $createdUsingAutoBilling = CreatedUsingAutoBilling::NO): void
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
         *  @var bool $nextAttemptDate The date and time for auto billing to be attempted
         */
        $nextAttemptDate = $subscriptionWithFurthestEndAt->end_at;

        //  Auto billing schedule information
        $autoBillingSchedule = [
            'attempts' => 0,
            'project_id' => $this->project->id,
            'subscriber_id' => $subscriber->id,
            'reminded_one_hour_before_at' => null,
            'reminded_six_hours_before_at' => null,
            'reminded_twelve_hours_before_at' => null,
            'auto_billing_enabled' => $autoBillingEnabled,
            'reminded_twenty_four_hours_before_at' => null,
            'reminded_forty_eight_hours_before_at' => null,
            'reminded_seventy_two_hours_before_at' => null,
            'subscription_plan_id' => $subscriptionPlan->id,
            'next_attempt_date' => $autoBillingEnabled ? $nextAttemptDate : null
        ];

        //  Query the existing auto billing schedule (if any)
        $existingAutoBillingSchedule = DB::table('auto_billing_schedules')->where([
            'subscriber_id' => $subscriber->id,
            'subscription_plan_id' => $subscriptionPlan->id
        ])->first();

        //  If the auto billing schedule exists
        if( $existingAutoBillingSchedule ) {

            $autoBillingSchedule['total_successful_attempts'] = $existingAutoBillingSchedule->total_successful_attempts + 1;

            //  Update existing auto billing schedule
            DB::table('auto_billing_schedules')->where([
                'subscriber_id' => $subscriber->id,
                'subscription_plan_id' => $subscriptionPlan->id
            ])->update($autoBillingSchedule);

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
        // Stop auto billing schedule on this subscription
        $this->stopAutoBillingScheduleOnSubscription();

        $result = $this->project->subscriptions()->active()->where('subscriptions.id', $this->subscription->id)->update([
            'cancelled_at' => Carbon::now()
        ]);

        $this->subscription->load(['subscriptionPlan', 'subscriber']);
        $subscriptionPlan = $this->subscription->subscriptionPlan;
        $subscriber = $this->subscription->subscriber;

        if($subscriptionPlan && $subscriber) {
            $messageContent = $subscriptionPlan->craftAutoBillingDisabledSmsMessage();
            if(!empty($messageContent)) {
                SmsService::sendSms($this->project, $subscriber, $messageContent, MessageType::AutoBillingDisabled);
            }
        }

        return $result;
    }

    /**
     *  Uncancel an existing subscription for the project.
     *
     *  @return bool True if the subscription uncancellation is successful, false otherwise.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function uncancelProjectSubscription(): bool
    {
        return $this->project->subscriptions()->active()->where('subscriptions.id', $this->subscription->id)->update([
            'cancelled_at' => null
        ]);
    }

    /**
     *  Cancel subscriptions matching the subscriber msisdn.
     *
     *  @param array $data
     *  @return bool True if the update is successful, false otherwise.
     */
    public function cancelProjectSubscriptions(array $data): bool
    {
        $tags = $data['tags'] ?? null;
        $msisdn = $data['msisdn'] ?? null;

        $query = $this->project->subscriptions()->active();

        $query = $query->whereHas('subscriber', function (Builder $query) use ($msisdn) {
            $query->where('msisdn', $msisdn);
        });

        if (!empty($tags)) {
            $query->whereHas('subscriptionPlan', function (Builder $query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->whereJsonContains('tags', $tag);
                }
            });
        }

        $subscriptionPlanIds = $query->pluck('subscription_plan_id')->toArray();

        if(count($subscriptionPlanIds)) {

            $this->stopAutoBillingScheduleOnSubscriptions($msisdn, $subscriptionPlanIds);
            return $query->update(['cancelled_at' => Carbon::now()]);

        }

        return false;
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

        // Stop auto billing schedule on this subscription
        $this->stopAutoBillingScheduleOnSubscription();

        // Delete subscription
        $result = $this->subscription->delete();

        $this->subscription->load(['subscriptionPlan', 'subscriber']);
        $subscriptionPlan = $this->subscription->subscriptionPlan;
        $subscriber = $this->subscription->subscriber;

        if($subscriptionPlan && $subscriber) {
            $messageContent = $subscriptionPlan->craftAutoBillingDisabledSmsMessage();
            if(!empty($messageContent)) {
                SmsService::sendSms($this->project, $subscriber, $messageContent, MessageType::AutoBillingDisabled);
            }
        }

        return $result;
    }

    /**
     *  Stop auto billing schedule on subscription
     *
     *  @return bool
     */
    protected function stopAutoBillingScheduleOnSubscription(): bool
    {
        return AutoBillingSchedule::where([
            'subscriber_id' => $this->subscription->subscriber_id,
            'subscription_plan_id' => $this->subscription->subscription_plan_id
        ])->update([
            'attempts' => 0,
            'next_attempt_date' => null,
            'auto_billing_enabled' => false,
            'reminded_one_hour_before_at' => null,
            'reminded_six_hours_before_at' => null,
            'reminded_twelve_hours_before_at' => null,
            'reminded_twenty_four_hours_before_at' => null,
            'reminded_forty_eight_hours_before_at' => null,
            'reminded_seventy_two_hours_before_at' => null,
        ]);
    }

    /**
     *  Stop subscriptions auto billing schedules
     *
     *  @return string $msisdn
     *  @return array $subscriptionPlanIds
     *  @return bool
     */
    protected function stopAutoBillingScheduleOnSubscriptions(string $msisdn, array $subscriptionPlanIds): bool
    {
        return AutoBillingSchedule::whereHas('subscriber', function (Builder $query) use ($msisdn) {
            $query->where('msisdn', $msisdn);
        })->whereIn('subscription_plan_id', $subscriptionPlanIds)->update([
            'attempts' => 0,
            'next_attempt_date' => null,
            'auto_billing_enabled' => false,
            'reminded_one_hour_before_at' => null,
            'reminded_six_hours_before_at' => null,
            'reminded_twelve_hours_before_at' => null,
            'reminded_twenty_four_hours_before_at' => null,
            'reminded_forty_eight_hours_before_at' => null,
            'reminded_seventy_two_hours_before_at' => null,
        ]);
    }

    /**
     *  Calculate the end date based on the subscription plan frequency and duration.
     *
     *  @param Carbon $startAt The start at datetime
     *  @param SubscriptionPlan $subscriptionPlan The subscription plan to calculate the end date for.
     *  @return Carbon The calculated end date.
     */
    protected function calculateEndDate($startAt, SubscriptionPlan $subscriptionPlan): Carbon
    {
        /**
         *  The copy method essentially creates a new Carbon object which you can
         *  apply the changes to without affecting the original $date variable.
         *
         *  Reference: https://stackoverflow.com/questions/34413877/php-carbon-class-changing-my-original-variable-value
         */
        $date = $startAt->copy();

        switch ($subscriptionPlan->frequency) {
            case 'Years':
                return $date->addYears($subscriptionPlan->duration);
            case 'Months':
                return $date->addMonths($subscriptionPlan->duration);
            case 'Weeks':
                return $date->addWeeks($subscriptionPlan->duration);
            case 'Days':
                return $date->addDays($subscriptionPlan->duration);
            case 'Hours':
                return $date->addHours($subscriptionPlan->duration);
            case 'Minutes':
                return $date->addMinutes($subscriptionPlan->duration);
            default:
                return $date->addDay();
        }
    }
}
