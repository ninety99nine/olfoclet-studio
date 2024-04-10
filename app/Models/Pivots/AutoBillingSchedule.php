<?php

namespace App\Models\Pivots;

use App\Models\Project;
use App\Models\Subscriber;
use App\Models\SubscriptionPlan;
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
        'reminded_one_hour_before' => 'boolean',
        'reminded_six_hours_before' => 'boolean',
        'reminded_twelve_hours_before' => 'boolean',
        'reminded_twenty_four_hours_before' => 'boolean',
        'reminded_forty_eight_hours_before' => 'boolean',
        'reminded_seventy_two_hours_before' => 'boolean',
    ];

    const VISIBLE_COLUMNS = [
        'id', 'subscriber_id', 'subscription_plan_id', 'auto_billing_enabled', 'next_attempt_date', 'attempts',
        'total_successful_attempts', 'total_failed_attempts',

        'reminded_one_hour_before', 'reminded_six_hours_before', 'reminded_twelve_hours_before',
        'reminded_twenty_four_hours_before', 'reminded_forty_eight_hours_before',
        'reminded_seventy_two_hours_before',

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
     * Get the subscription plans associated with the auto billing schedule.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     *  ATTRIBUTES
     */
    protected $appends = [
        'next_attempt_date_milli_seconds_left'
    ];

    protected function nextAttemptDateMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: fn() => ($milliseconds = ($this->next_attempt_date->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0
        );
    }
}
