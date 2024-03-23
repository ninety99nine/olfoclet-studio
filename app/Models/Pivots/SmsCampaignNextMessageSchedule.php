<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SmsCampaignNextMessageSchedule extends Pivot
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
        'next_message_date' => 'datetime'
    ];

    const VISIBLE_COLUMNS = [
        'id', 'next_message_date', 'sent_sms_count'
    ];
}
