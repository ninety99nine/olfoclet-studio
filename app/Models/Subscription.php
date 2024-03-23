<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_using_auto_billing' => 'boolean',
        'cancelled_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['subscriber_id', 'subscription_plan_id', 'start_at', 'end_at', 'cancelled_at', 'created_using_auto_billing', 'project_id'];

    /*
     *  Scope: Return active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('start_at', '<=' , Carbon::now())->where('end_at', '>' , Carbon::now());
    }

    /*
     *  Scope: Return inactive subscriptions
     */
    public function scopeInActive($query)
    {
        return $query->where('start_at', '>' , Carbon::now())->orWhere('end_at', '<=' , Carbon::now());
    }

    /*
     *  Scope: Return non-cancelled subscriptions
     */
    public function scopeNotCancelled($query)
    {
        return $query->whereNull('cancelled_at');
    }

    /**
     * Get the project associated with the subscription.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriber associated with the subscription.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the subscription plan associated with the subscription.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     *  ATTRIBUTES
     */
    protected $appends = [
        'status'
    ];

    /**
     *  Status
     */
    public function getStatusAttribute()
    {
        return (Carbon::parse($this->start_at)->isCurrentDay() || Carbon::parse($this->start_at)->isPast()) &&
                Carbon::parse($this->end_at)->isFuture() && is_null($this->cancelled_at)
                ? 'Active' : 'Inactive';
    }
}
