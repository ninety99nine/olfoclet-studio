<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutoBillingReminder extends Model
{
    use HasFactory, NodeTrait;

    /**
     *  The attributes that are mass assignable.
     *
     *  @var array
     */
    protected $fillable = ['name', 'hours'];

    /**
     * Get the pricing plans associated with the auto billing reminder
     */
    public function pricingPlans()
    {
        return $this->belongsToMany(PricingPlan::class, 'subscription_plan_auto_billing_reminders', 'auto_billing_reminder_id', 'pricing_plan_id');
    }

    /**
     *  Get the auto billing reminder job batches.
     */
    public function autoBillingReminderJobBatches()
    {
        return $this->belongsToMany(JobBatches::class, 'auto_billing_reminder_job_batches', 'auto_billing_reminder_id', 'job_batch_id');
    }

    /**
     *  Get the latest auto billing reminder job batch.
     */
    public function latestAutoBillingReminderJobBatches()
    {
        return $this->autoBillingReminderJobBatches()->latest()->limit(1);
    }
}
