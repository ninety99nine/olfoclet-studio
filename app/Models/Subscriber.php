<?php

namespace App\Models;

use App\Casts\JsonToArray;
use App\Enums\MessageType;
use App\Models\Pivots\SubscriberTopic;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pivots\AutoBillingSchedule;
use App\Models\Pivots\SmsCampaignSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscriber extends Model
{
    use HasFactory;

    protected $casts = [
        'metadata' => JsonToArray::class.':null',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msisdn', 'metadata', 'project_id'
    ];

    public function scopeExcludeIds($query, $subscriberIds = [])
    {
        return $query->whereNotIn('id', $subscriberIds);
    }

    public function scopeHasActiveNonCancelledSubscription($query, array|int|null $pricingPlanId = null, int|null $endsInNumberOfHours = null)
    {
        return $query->whereHas('subscriptions', function (Builder $query) use ($pricingPlanId, $endsInNumberOfHours) {

            /**
             *  Must be any non cancelled but active subscription
             */
            $query->notCancelled()->active();

            if( !is_null($pricingPlanId) ) {

                if( is_array($pricingPlanIds = $pricingPlanId) ) {

                    /**
                     *  Must be any subscription that matches the specified plan ids
                     */
                    $query->whereIn('subscriptions.pricing_plan_id', $pricingPlanIds);

                }else{

                    /**
                     *  Must be any subscription that matches the specified plan id
                     */
                    $query->where('subscriptions.pricing_plan_id', $pricingPlanId);

                }

            }

            if( !is_null($endsInNumberOfHours) ) {

                /**
                 *  Must be any subscription that will end in x number of hours or less
                 *  but must be greater than the current date and time.
                 */
                $query->where('end_at', '<=', now()->addHours($endsInNumberOfHours))
                      ->where('end_at', '>', now());

            }

        });
    }

    /**
     *  Get the project associated with the subscriber
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     *  Get the topics that this subscriber received
     */
    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'subscriber_topics')
                    ->withPivot(SubscriberTopic::VISIBLE_COLUMNS)
                    ->using(SubscriberTopic::class)
                    ->withTimestamps();
    }

    /**
     *  Get the latest topic that this subscriber received.
     *
     *  We need to retrieve only the first topic of each subscriber when eager loading
     *  is being implemented e.g Subscriber::with(['latestMessage']). Normally using:
     *  limit(1) only works for the first Subscriber record and then the next records
     *  will not contain any eager loaded topic. We are using a package called
     *  "Eloquent Eager Limit" to avoid this, so that every Subscriber record
     *  will contain a single eager loaded topic.
     *
     *  Reference: https://stackoverflow.com/questions/61179886/how-to-eager-load-only-1-result-from-many-to-many-relationship-laravel
     */
    public function latestTopic()
    {
        return $this->topics()->latest()->take(1);
    }

    /**
     *  Get the messages that this subscriber received
     */
    public function messages()
    {
        return $this->hasMany(SubscriberMessage::class);
    }

    /**
     *  Get the content messages that this subscriber received
     */
    public function contentMessages()
    {
        return $this->belongsToMany(Message::class, 'subscriber_messages')
                    ->withTimestamps();
    }

    /**
     *  Get the latest message that this subscriber received.
     *
     *  We need to retrieve only the first message of each subscriber when eager loading
     *  is being implemented e.g Subscriber::with(['latestMessage']). Normally using:
     *  limit(1) only works for the first Subscriber record and then the next records
     *  will not contain any eager loaded message. We are using a package called
     *  "Eloquent Eager Limit" to avoid this, so that every Subscriber record
     *  will contain a single eager loaded message.
     *
     *  Reference: https://stackoverflow.com/questions/61179886/how-to-eager-load-only-1-result-from-many-to-many-relationship-laravel
     */
    public function latestMessage()
    {
        return $this->messages()->limit(1);
    }

    /**
     *  Get the messages that this subscriber received as content
     */
    public function messagesAsContent()
    {
        return $this->messages()->where('type', MessageType::Content->value);
    }

    /**
     *  Get the messages that this subscriber received as payment confirmation
     */
    public function messagesAsPaymentConfirmation()
    {
        return $this->messages()->where('type', MessageType::PaymentConfirmation->value);
    }

    /**
     *  Get the messages that this subscriber received as auto billing reminder
     */
    public function messagesAsAutoBillingReminder()
    {
        return $this->messages()->where('type', MessageType::AutoBillingReminder->value);
    }

    /**
     *  Get the sms campaigns associated with the subscriber
     */
    public function smsCampaigns()
    {
        return $this->belongsToMany(SmsCampaign::class, 'sms_campaign_schedules')
                    ->withPivot(SmsCampaignSchedule::VISIBLE_COLUMNS)
                    ->using(SmsCampaignSchedule::class);
    }

    /**
     *  Get the auto billing schedules associated with the subscriber
     */
    public function autoBillingSchedules()
    {
        return $this->hasMany(AutoBillingSchedule::class);
    }

    /**
     *  Get the auto billing pricing plans associated with the subscriber
     */
    public function autoBillingPricingPlans()
    {
        return $this->belongsToMany(PricingPlan::class, 'auto_billing_schedules')
                    ->using(AutoBillingSchedule::class);
    }

    /**
     *  Get the auto billing pricing plans associated with the subscriber
     */
    public function autoBillingPricingPlansWithPivotSchedules()
    {
        return $this->autoBillingPricingPlans()
                    ->withPivot(AutoBillingSchedule::VISIBLE_COLUMNS)
                    ->withTimestamps();
    }

    /**
     *  Get the subscriber lists of this subscriber
     */
    public function subscriberLists()
    {
        return $this->belongsToMany(SubscriberList::class, 'subscriber_list_distribution');
    }

    /**
     *  Get the subscriptions associated with the subscriber.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     *  Get the active subscriptions associated with the subscriber.
     */
    public function activeSubscriptions()
    {
        return $this->subscriptions()->active();
    }

    /**
     *  Get the latest subscription associated with the subscriber.
     */
    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    /**
     *  Get the subscription with the furthest end at datetime associated with the subscriber.
     */
    public function subscriptionWithFurthestEndAt()
    {
        return $this->hasOne(Subscription::class)->orderByDesc('end_at')->latest();
    }

    /**
     *  Get the user billing transactions associated with the subscriber.
     */
    public function userBillingTransactions()
    {
        return $this->hasMany(BillingTransaction::class)->notCreatedUsingAutoBilling()->latest();
    }

    /**
     *  Get the successful user billing transactions associated with the subscriber.
     */
    public function successfulUserBillingTransactions()
    {
        return $this->userBillingTransactions()->successful();
    }

    /**
     *  Get the unsuccessful user billing transactions associated with the subscriber.
     */
    public function unsuccessfulUserBillingTransactions()
    {
        return $this->userBillingTransactions()->unsuccessful();
    }

    /**
     *  Get the latest user billing transaction associated with the subscriber.
     */
    public function latestUserBillingTransaction()
    {
        return $this->hasOne(BillingTransaction::class)->notCreatedUsingAutoBilling()->latest();
    }

    /**
     *  Get the auto billing transactions associated with the subscriber.
     */
    public function autoBillingTransactions()
    {
        return $this->hasMany(BillingTransaction::class)->createdUsingAutoBilling()->latest();
    }

    /**
     *  Get the successful auto billing transactions associated with the subscriber.
     */
    public function successfulAutoBillingTransactions()
    {
        return $this->autoBillingTransactions()->successful();
    }

    /**
     *  Get the unsuccessful auto billing transactions associated with the subscriber.
     */
    public function unsuccessfulAutoBillingTransactions()
    {
        return $this->autoBillingTransactions()->unsuccessful();
    }

    /**
     *  Get the latest auto billing transaction associated with the subscriber.
     */
    public function latestAutoBillingTransaction()
    {
        return $this->hasOne(BillingTransaction::class)->createdUsingAutoBilling()->latest();
    }
}
