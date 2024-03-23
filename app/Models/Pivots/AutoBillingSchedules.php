<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AutoBillingSchedules extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
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
}
