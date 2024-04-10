<?php

namespace App\Models\Pivots;

use App\Models\Message;
use App\Models\Project;
use App\Models\Subscriber;
use App\Enums\MessageType;
use Illuminate\Support\Str;
use App\Enums\MessageFailureType;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SubscriberMessage extends Pivot
{
    protected $table = 'subscriber_messages';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     *  Reference 1: https://stackoverflow.com/questions/71439425/cant-get-id-from-created-eloquent-model-extending-pivot-in-laravel
     *  Reference 2: https://laravel.com/docs/9.x/eloquent-relationships#custom-pivot-models-and-incrementing-ids
     *
     * @var bool
     */
    public $incrementing = true;

    const TYPES = [
        MessageType::Content->value,
        MessageType::PaymentConfirmation->value,
        MessageType::AutoBillingReminder->value,
        MessageType::AutoBillingDisabled->value,
    ];

    const FAILURE_TYPES = [
        MessageFailureType::InternalFailure->value,
        MessageFailureType::MessageSendingFailed->value,
        MessageFailureType::TokenGenerationFailed->value
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_successful' => 'boolean',
        'delivery_status_update_is_successful' => 'boolean',
    ];

    const VISIBLE_COLUMNS = [
        'content', 'is_successful', 'failure_reason', 'delivery_status', 'delivery_status_endpoint',
        'delivery_status_update_is_successful', 'delivery_status_update_failure_reason'
    ];

    /**
     *  Scope subscriber messages with the delivery status of "MessageWaiting"
     */
    public function scopeMessageWaiting($query)
    {
        return $query->where('delivery_status', 'MessageWaiting');
    }

    /**
     *  Get the project associated with the subscriber message.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     *  Get the message associated with the subscriber message
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     *  Get the subscriber associated with the subscriber message
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     *  Format the delivery_status
     */
    protected function deliveryStatus(): Attribute
    {
        return Attribute::make(
            get: function($value) {
                return Str::headline(Str::snake($value, ' '));
            }
        );
    }
}
