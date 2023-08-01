<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pivots\CampaignNextMessageSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscriber extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['msisdn', 'project_id'];

    public function scopeExcludeIds($query, $subscriberIds = [])
    {
        return $query->whereNotIn('id', $subscriberIds);
    }

    public function scopeHasActiveSubscription($query, $subcriptionPlanIds = [])
    {
        return $query->whereHas('subscriptions', function (Builder $query) use ($subcriptionPlanIds) {

            if( count($subcriptionPlanIds) ) {

                /**
                 *  Must be an active subscription that matches the specified plan ids
                 */
                $query->whereIn('subscriptions.subscription_plan_id', $subcriptionPlanIds)->active();

            }else{

                /**
                 *  Must be any active subscription
                 */
                $query->active();

            }
        });
    }

    /**
     * Get the project associated with the subscriber
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the messages that this subscriber received
     */
    public function messages()
    {
        return $this->belongsToMany(Message::class, 'subscriber_messages')
                    ->withPivot(['sent_sms_count'])
                    ->withTimestamps();
    }

    /**
     * Get the campaigns associated with the subscriber
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_subscriber')
                    ->withPivot(CampaignNextMessageSchedule::VISIBLE_COLUMNS)
                    ->using(CampaignNextMessageSchedule::class);
    }

    /**
     * Get the latest message that this subscriber received
     */
    public function latestMessage()
    {
        return $this->messages()->latest()->take(1);
    }

    /**
     * Get the topics that this subscriber received
     */
    public function topics()
    {
        return $this->belongsToMany(Message::class, 'subscriber_topics')
                    ->using(SubscriberTopic::class)
                    ->withTimestamps();
    }

    /**
     * Get the latest topic that this subscriber read
     */
    public function latestTopic()
    {
        return $this->topics()->latest()->take(1);
    }

    /**
     * Get the subscriber lists of this subscriber
     */
    public function subscriberLists()
    {
        return $this->belongsToMany(SubscriberList::class, 'subscriber_list_distribution');
    }

    /**
     * Get the subscriptions associated with the subscriber.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the latest subscription that this subscriber received
     */
    public function latestSubscription()
    {
        return $this->subscriptions()->latest()->take(1);
    }

    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($subscriber) {

                DB::table('subscriber_messages')->where(['subscriber_id' => $subscriber->id])->delete();
                DB::table('subscriber_list_distribution')->where(['subscriber_id' => $subscriber->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }

}
