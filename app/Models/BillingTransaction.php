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
        BillingTransactionFailureType::InternalFailure->value,
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
        'failure_reason' => 'array',
        'is_successful' => 'boolean',
        'funds_after_deduction' => Money::class,
        'funds_before_deduction' => Money::class,
        'created_using_auto_billing' => 'boolean',
        'more_data' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount', 'is_successful', 'rating_type', 'funds_before_deduction', 'funds_after_deduction',
        'description', 'failure_type', 'failure_reason', 'subscriber_id', 'project_id',
        'subscription_plan_id', 'subscription_id', 'created_using_auto_billing',
        'client_correlator', 'more_data'
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
     * Get the project associated with the billing transaction.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriber associated with the billing transaction.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the subscription associated with the billing transaction.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the subscription plans associated with the billing transaction.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    protected function lastFailureMessage(): Attribute
    {
        return Attribute::make(
            get: function() {

                if($this->failure_reason == null || $this->failure_reason->failed_attempts == null) return null;

                $failedAttempts = $this->failure_reason->failed_attempts;
                $lastAttempt = end($failedAttempts);
                $failureMessage = null;

                if($this->failure_type == BillingTransactionFailureType::TokenGenerationFailed) {

                    if(isset($lastAttempt['response']['error']) && isset($lastAttempt['response']['error_description'])) {

                        $failureMessage = $lastAttempt['response']['error'];
                        $failureMessage .= (empty($failureMessage) ? '' : ' - ') . $lastAttempt['response']['error_description'];

                    }else{

                        if(isset($lastAttempt['response']['error'])) {
                            $failureMessage = $lastAttempt['response']['error'];
                        }else if(isset($lastAttempt['response']['error_description'])) {
                            $failureMessage = $lastAttempt['response']['error_description'];
                        }else{
                            $failureMessage = ucfirst(BillingTransactionFailureType::TokenGenerationFailed->value);
                        }

                    }

                }else if($this->failure_type == BillingTransactionFailureType::ProductInventoryRetrievalFailed) {

                    if(isset($lastAttempt['response']['message']) && isset($lastAttempt['response']['description'])) {

                        $failureMessage = $lastAttempt['response']['message'];
                        $failureMessage .= (empty($failureMessage) ? '' : ' - ') . $lastAttempt['response']['description'];

                    }else{

                        if(isset($lastAttempt['response']['message'])) {
                            $failureMessage = $lastAttempt['response']['message'];
                        }else if(isset($lastAttempt['response']['description'])) {
                            $failureMessage = $lastAttempt['response']['description'];
                        }else{
                            $failureMessage = ucfirst(BillingTransactionFailureType::ProductInventoryRetrievalFailed->value);
                        }

                    }

                }else if($this->failure_type == BillingTransactionFailureType::UsageConsumptionRetrievalFailed) {

                    if(isset($lastAttempt['response']['message']) && isset($lastAttempt['response']['description'])) {

                        $failureMessage = $lastAttempt['response']['message'];
                        $failureMessage .= (empty($failureMessage) ? '' : ' - ') . $lastAttempt['response']['description'];

                    }else{

                        if(isset($lastAttempt['response']['message'])) {
                            $failureMessage = $lastAttempt['response']['message'];
                        }else if(isset($lastAttempt['response']['description'])) {
                            $failureMessage = $lastAttempt['response']['description'];
                        }else{
                            $failureMessage = ucfirst(BillingTransactionFailureType::UsageConsumptionRetrievalFailed->value);
                        }

                    }

                }else if($this->failure_type == BillingTransactionFailureType::DeductFeeFailed) {

                    if(isset($lastAttempt['response']['requestError']['policyException']['text']) && isset($lastAttempt['response']['requestError']['serviceException']['text'])) {

                        $failureMessage = $lastAttempt['response']['error'];
                        $failureMessage .= (empty($failureMessage) ? '' : ' - ') . $lastAttempt['response']['error_description'];

                    }else{

                        if(isset($lastAttempt['response']['requestError']['policyException']['text'])) {
                            $failureMessage = $lastAttempt['response']['requestError']['policyException']['text'];
                        }else if(isset($lastAttempt['response']['requestError']['serviceException']['text'])) {
                            $failureMessage = $lastAttempt['response']['requestError']['serviceException']['text'];
                        }else{
                            $failureMessage = ucfirst(BillingTransactionFailureType::DeductFeeFailed->value);
                        }

                    }

                }else if($this->failure_type == BillingTransactionFailureType::InternalFailure) {

                    $failureMessage = 'Could not process this transaction because of missing information on your account';

                }else if($this->failure_type == BillingTransactionFailureType::InactiveAccount) {

                    $failureMessage = 'This account is currently inactive. Please contact customer support';

                }else if($this->failure_type == BillingTransactionFailureType::InsufficientFunds) {

                    $failureMessage = 'You do not have enough funds to complete this transaction';

                }else if($this->failure_type == BillingTransactionFailureType::InternalFailure) {

                    $failureMessage = 'Could not process this transaction, please try again';

                }

                return $failureMessage ?? 'Billing failed';

            }
        );
    }
}
