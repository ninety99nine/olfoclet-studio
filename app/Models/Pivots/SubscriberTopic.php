<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SubscriberTopic extends Pivot
{
    protected $table = 'subscriber_topics';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     *  Reference 1: https://stackoverflow.com/questions/71439425/cant-get-id-from-created-eloquent-model-extending-pivot-in-laravel
     *  Reference 2: https://laravel.com/docs/9.x/eloquent-relationships#custom-pivot-models-and-incrementing-ids
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_reading' => 'boolean',
        'finished_reading' => 'boolean'
    ];

    const VISIBLE_COLUMNS = [
        'started_reading', 'finished_reading'
    ];
}
