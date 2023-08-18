<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $casts = [
        'categories' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'price', 'frequency', 'duration', 'categories', 'project_id'];

    /**
     * Get the project associated with the subscription plan.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriptions associated with the subscription plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     *  Accessors
     */

    public function getPriceAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     *  Mutators
     */

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = number_format($value, 2);
    }

    public function getCategoriesAttribute($value)
    {
        //  Convert the categories into an empty array if null
        return json_decode($value ?? '[]');
    }
}
