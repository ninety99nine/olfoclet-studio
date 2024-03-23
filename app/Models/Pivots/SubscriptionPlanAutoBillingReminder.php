<?php

namespace App\Models\Pivots;

use App\Models\AutoBillingReminder;
use App\Models\Project;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SubscriptionPlanAutoBillingReminder extends Pivot
{
    protected $table = 'subscription_plan_auto_billing_reminders';

    /**
     *  Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     *  Get the subscription plan
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     *  Get the auto billing reminder
     */
    public function autoBillingReminder()
    {
        return $this->belongsTo(AutoBillingReminder::class);
    }
}
