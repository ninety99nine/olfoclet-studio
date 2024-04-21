<?php

namespace App\Models\Pivots;

use App\Models\Project;
use App\Models\Subscriber;
use App\Models\SmsCampaign;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SmsCampaignSchedule extends Pivot
{
    protected $table = 'sms_campaign_schedules';

    /**
     *  Indicates if the IDs are auto-incrementing.
     *
     *  After creating the SmsCampaignSchedule using create method, its not possible to retrieve the id e.g
     *
     *  $smsCampaignSchedule = SmsCampaignSchedule::create([ ... ]);
     *
     *  $id = $smsCampaignSchedule->id;     //  Returns null
     *
     *  This is because the SmsCampaignSchedule is a Pivot Model, therefore we need to intentionally
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
        'next_message_date' => 'datetime'
    ];

    const VISIBLE_COLUMNS = [
        'id', 'next_message_date', 'attempts', 'total_successful_attempts', 'total_failed_attempts'
    ];

    /**
     * Get the project associated with the sms campaign schedule.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriber associated with the sms campaign schedule.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the sms campaign associated with the sms campaign schedule.
     */
    public function smsCampaign()
    {
        return $this->belongsTo(SmsCampaign::class);
    }

    /**
     *  ATTRIBUTES
     */
    protected $appends = [
        'next_message_date_milli_seconds_left'
    ];

    protected function nextMessageDateMilliSecondsLeft(): Attribute
    {
        return Attribute::make(
            get: fn() => ($milliseconds = ($this->next_message_date->timestamp - now()->timestamp) * 1000) >= 0 ? $milliseconds : 0
        );
    }
}
