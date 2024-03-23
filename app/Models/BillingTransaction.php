<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BillingTransactionFailureType;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingTransaction extends Model
{
    use HasFactory, HasEagerLimit;

    const FAILURE_TYPES = [
        BillingTransactionFailureType::InactiveAccount->value,
        BillingTransactionFailureType::InsufficientFunds->value,
        BillingTransactionFailureType::TokenGenerationFailed->value,
        BillingTransactionFailureType::ProductInventoryRetrievalFailed->value,
        BillingTransactionFailureType::UsageConsumptionRetrievalFailed->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => Money::class,
        'is_successful' => 'boolean',
        'is_prepaid_account' => 'boolean',
        'funds_after_deduction' => Money::class,
        'funds_before_deduction' => Money::class,
        'created_using_auto_billing' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount', 'is_successful', 'is_prepaid_account', 'funds_before_deduction', 'funds_after_deduction',
        'description', 'failure_type', 'failure_reason', 'subscriber_id', 'project_id', 'subscription_plan_id',
        'created_using_auto_billing'
    ];

    /*
     *  Scope: Return billing transactions that are successful
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', '1');
    }

    /*
     *  Scope: Return billing transactions that are unsuccessful
     */
    public function scopeUnsuccessful($query)
    {
        return $query->where('is_successful', '0');
    }

    /*
     *  Scope: Return billing transactions using auto billing
     */
    public function scopeCreatedUsingAutoBilling($query)
    {
        return $query->where('created_using_auto_billing', '1');
    }

    /*
     *  Scope: Return billing transactions not using auto billing
     */
    public function scopeNotCreatedUsingAutoBilling($query)
    {
        return $query->where('created_using_auto_billing', '0');
    }

    /**
     * Get the project associated with the subscription.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriber associated with the subscription.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the subscription plans associated with the subscription.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
