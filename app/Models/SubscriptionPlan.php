<?php

namespace App\Models;

use App\Casts\Money;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends Model
{
    use HasFactory, NodeTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'active' => 'boolean',
        'price' => Money::class,
        'is_folder' => 'boolean',
        'can_auto_bill' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'active', 'is_folder', 'price', 'frequency', 'duration',
        'can_auto_bill', 'max_auto_billing_attempts', 'insufficient_funds_message',
        'successful_payment_sms_message', 'next_auto_billing_reminder_sms_message',
        'project_id'
    ];

    /**
     *  Scope: Return subscription plans that can auto bill
     */
    public function scopeCanAutoBill($query)
    {
        return $query->where('can_auto_bill', '1');
    }

    /*
     *  Scope: Return active subscription plans
     */
    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }

    /*
     *  Scope: Return inactive subscription plans
     */
    public function scopeInActive($query)
    {
        return $query->where('active', '0');
    }

    /*
     *  Scope: Return non-folder subscription plans
     */
    public function scopeNonFolder($query)
    {
        return $query->where('is_folder', '0');
    }

    /**
     * Get the project associated with the subscription plan.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriptions associated with the subscription plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the billing transactions associated with the subscription plan.
     */
    public function bililngTransactions()
    {
        return $this->hasMany(BillingTransaction::class);
    }

    /**
     * Get the auto billing job batches associated with the subscription plan.
     */
    public function autoBillingJobBatches()
    {
        return $this->belongsToMany(JobBatches::class, 'auto_billing_job_batches', 'subscription_plan_id', 'job_batch_id');
    }

    /**
     * Get the latest auto billing job batch associated with the subscription plan.
     */
    public function latestAutoBillingJobBatch()
    {
        return $this->autoBillingJobBatches()->latest()->limit(1);
    }

    /**
     * Get the auto billing reminder job batches associated with the subscription plan.
     */
    public function autoBillingReminderJobBatches()
    {
        return $this->belongsToMany(JobBatches::class, 'auto_billing_reminder_job_batches', 'subscription_plan_id', 'job_batch_id');
    }

    /**
     * Get the latest auto billing reminder job batch associated with the subscription plan.
     */
    public function latestAutoBillingReminderJobBatches()
    {
        return $this->autoBillingReminderJobBatches()->latest()->limit(1);
    }

    /**
     * Get the auto billing reminders associated with the subscription plan.
     */
    public function autoBillingReminders()
    {
        return $this->belongsToMany(AutoBillingReminder::class, 'subscription_plan_auto_billing_reminders', 'subscription_plan_id', 'auto_billing_reminder_id');
    }
}
