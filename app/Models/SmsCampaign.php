<?php

namespace App\Models;

use App\Casts\JsonToArray;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\SmsCampaignTrait;
use App\Models\Pivots\SmsCampaignSchedule;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsCampaign extends Model
{
    use HasFactory, SmsCampaignTrait, HasEagerLimit;

    const SCHEDULE_TYPE = [
        'Send Now',
        'Send Later',
        'Send Recurring',
    ];

    const MESSAGE_TO_SEND = [
        'Specific Message',
        'Any Message'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
        'has_end_date' => 'boolean',
        'has_start_date' => 'boolean',
        'can_send_messages' => 'boolean',
        'can_repeat_messages' => 'boolean',
        'message_ids' => JsonToArray::class,
        'days_of_the_week' => JsonToArray::class,
        'pricing_plan_ids' => JsonToArray::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'schedule_type', 'recurring_duration', 'recurring_frequency',
        'message_to_send', 'message_ids', 'has_start_date', 'start_date', 'start_time',
        'has_end_date', 'end_date', 'end_time', 'can_send_messages',
        'can_repeat_messages', 'days_of_the_week',
        'pricing_plan_ids', 'project_id'
    ];

    /**
     *  Scope sms campaigns that can send messages
     */
    public function scopeCanSendMessages($query)
    {
        return $query->where('can_send_messages', '1');
    }

    /**
     * Get the project associated with the sms campaign.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscribers associated with the sms campaign.
     *
     * When a subscriber is associated with a campaign it means
     * that they have received content before (at least one message)
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'sms_campaign_schedules')
                    ->withPivot(SmsCampaignSchedule::VISIBLE_COLUMNS)
                    ->using(SmsCampaignSchedule::class);
    }

    /**
     * Get the sms campaign batch jobs associated with the sms campaign.
     */
    public function smsCampaignBatchJobs()
    {
        return $this->belongsToMany(JobBatches::class, 'sms_campaign_job_batches', 'sms_campaign_id', 'job_batch_id');
    }

    /**
     * Get the latest sms campaign batch job associated with the sms campaign.
     */
    public function latestSmsCampaignBatchJob()
    {
        return $this->smsCampaignBatchJobs()->latest()->limit(1);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['start_datetime', 'end_datetime'];

    /**
     * Get the start date and time
     */
    protected function startDatetime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->combineDateAndTime($this->start_date, $this->start_time)
        );
    }

    /**
     * Get the end date and time
     */
    protected function endDatetime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->combineDateAndTime($this->end_date, $this->end_time)
        );
    }

}
