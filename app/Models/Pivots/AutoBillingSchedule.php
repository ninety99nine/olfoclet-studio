<?php

namespace App\Models\Pivots;

use App\Models\Project;
use App\Models\Subscriber;
use App\Models\PricingPlan;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AutoBillingSchedule extends Pivot
{
    protected $table = 'auto_billing_schedules';

    /**
     *  Indicates if the IDs are auto-incrementing.
     *
     *  After creating the AutoBillingSchedule using create method, its not possible to retrieve the id e.g
     *
     *  $autoBillingSchedule = AutoBillingSchedule::create([ ... ]);
     *
     *  $id = $autoBillingSchedule->id;     //  Returns null
     *
     *  This is because the AutoBillingSchedule is a Pivot Model, therefore we need to intentionally
     *  set the following property in order for us to acquire the ID of the created Pivot Model:
     *
     *  public $incrementing = true;
     *
     *  Reference 1: https://stackoverflow.com/questions/71439425/cant-get-id-from-created-eloquent-model-extending-pivot-in-laravel
     *  Reference 2: https://laravel.com/docs/9.x/eloquent-relationships#custom-pivot-models-and-incrementing-ids
     *
     * @var bool
     */
    public $incrementing = true;

    protected $casts = [
        'next_attempt_date' => 'datetime',
        'reminded_one_hour_before_at' => 'datetime',
        'reminded_six_hours_before_at' => 'datetime',
        'reminded_twelve_hours_before_at' => 'datetime',
        'reminded_twenty_four_hours_before_at' => 'datetime',
        'reminded_forty_eight_hours_before_at' => 'datetime',
        'reminded_seventy_two_hours_before_at' => 'datetime',
    ];

    const VISIBLE_COLUMNS = [
        'id', 'subscriber_id', 'pricing_plan_id', 'auto_billing_enabled', 'next_attempt_date',

        'attempt', 'overall_attempts', 'overall_failed_attempts', 'overall_successful_attempts',

        'reminded_one_hour_before_at', 'reminded_six_hours_before_at', 'reminded_twelve_hours_before_at',
        'reminded_twenty_four_hours_before_at', 'reminded_forty_eight_hours_before_at',
        'reminded_seventy_two_hours_before_at',

        'project_id'
    ];

    /**
     * Get the project associated with the auto billing schedule.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriber associated with the auto billing schedule.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the pricing plans associated with the auto billing schedule.
     */
    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    /**
     *  ATTRIBUTES
     */
    protected $appends = [
        'next_attempt_date_milli_seconds_left',
        'reminded_one_hour_before_milli_seconds_left', 'reminded_six_hours_before_milli_seconds_left',
        'reminded_twelve_hours_before_milli_seconds_left', 'reminded_twenty_four_hours_before_milli_seconds_left',
        'reminded_forty_eight_hours_milli_seconds_left', 'reminded_seventy_two_hours_before_milli_seconds_left',
    ];

    protected function nextAttemptDateMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }

    protected function remindedOneHourBeforeMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->subHours(1)->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }

    protected function remindedSixHoursBeforeMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->subHours(6)->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }

    protected function remindedTwelveHoursBeforeMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->subHours(12)->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }

    protected function remindedTwentyFourHoursBeforeMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->subHours(24)->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }

    protected function remindedFortyEightHoursMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->subHours(48)->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }

    protected function remindedSeventyTwoHoursBeforeMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(is_null($this->next_attempt_date)) return null;
                return ($milliseconds = ($this->next_attempt_date->subHours(72)->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0;
            }
        );
    }
}
