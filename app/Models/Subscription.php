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
    protected $fillable = ['subscriber_id', 'pricing_plan_id', 'start_at', 'end_at', 'cancelled_at', 'created_using_auto_billing', 'project_id'];

    /*
    *  Scope: Return active subscriptions
    */
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where('start_at', '<=', Carbon::now())
                ->where('end_at', '>', Carbon::now())
                ->whereNull('cancelled_at');
        });
    }

    /*
    *  Scope: Return inactive subscriptions
    */
    public function scopeInActive($query)
    {
        return $query->where(function ($query) {
            $query->where('start_at', '>', Carbon::now())
                ->orWhere('end_at', '<=', Carbon::now())
                ->orWhereNotNull('cancelled_at');
        });
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
     * Get the pricing plan associated with the subscription.
     */
    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    /**
     *  ATTRIBUTES
     */
    protected $appends = [
        'is_active'
    ];

    /**
     *  Status
     */
    public function getIsActiveAttribute()
    {
        return $this->start_at <= Carbon::now()
            && $this->end_at > Carbon::now()
            && is_null($this->cancelled_at);
    }
}
