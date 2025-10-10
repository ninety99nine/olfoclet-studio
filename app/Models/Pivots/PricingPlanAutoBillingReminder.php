<?php

namespace App\Models\Pivots;

use App\Models\Project;
use App\Models\PricingPlan;
use App\Models\AutoBillingReminder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PricingPlanAutoBillingReminder extends Pivot
{
    protected $table = 'pricing_plan_auto_billing_reminders';

    /**
     *  Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     *  Get the pricing plan
     */
    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    /**
     *  Get the auto billing reminder
     */
    public function autoBillingReminder()
    {
        return $this->belongsTo(AutoBillingReminder::class);
    }
}
