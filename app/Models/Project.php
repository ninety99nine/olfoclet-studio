<?php

namespace App\Models;

use App\Casts\Json;
use App\Traits\Models\ProjectTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pivots\ProjectUserAsTeamMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Pivots\SubscriptionPlanAutoBillingReminder;

class Project extends Model
{
    use HasFactory, ProjectTrait;

    const PERMISSIONS = [
        'View users', 'Manage users',
        'View topics', 'Manage topics',
        'View messages', 'Manage messages',
        'View subscribers', 'Manage subscribers',
        'View subscriptions', 'Manage subscriptions',
        'View sms campaigns', 'Manage sms campaigns',
        'View project settings', 'Manage project settings',
        'View subscription plans', 'Manage subscription plans',
        'View auto billing subscription plans', 'Manage auto billing subscription plans',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'settings' => Json::class,
        'can_auto_bill' => 'boolean',
        'can_send_messages' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'can_auto_bill', 'can_send_messages', 'settings'];

    /**
     *  Scope projects that can auto bill
     */
    public function scopeCanAutoBill($query)
    {
        return $query->where('can_auto_bill', '1')
                    ->whereNotNull('settings->auto_billing_client_id')
                    ->where('settings->auto_billing_client_id', '!=', '')
                    ->whereNotNull('settings->auto_billing_client_secret')
                    ->where('settings->auto_billing_client_secret', '!=', '');
    }

    /**
     *  Scope projects that can send messages
     */
    public function scopeCanSendMessages($query)
    {
        return $query->where('can_send_messages', '1');
    }

    /**
     *  Get the Users that have been assigned to this Project as a team member
     *
     *  @return Illuminate\Database\Eloquent\Concerns\HasRelationships::belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_projects', 'project_id', 'user_id')
                    ->withPivot(ProjectUserAsTeamMember::VISIBLE_COLUMNS)
                    ->using(ProjectUserAsTeamMember::class);
    }

    /**
     * Get the topics associated with the project.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Get the topics associated with the project.
     */
    public function mainTopics()
    {
        return $this->topics()->whereNull('parent_topic_id');
    }

    /**
     * Get the messages associated with the project.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the messages associated with the project.
     */
    public function mainMessages()
    {
        return $this->messages()->whereNull('parent_message_id');
    }

    /**
     * Get the sms campaigns associated with the project.
     */
    public function smsCampaigns()
    {
        return $this->hasMany(SmsCampaign::class);
    }

    /**
     * Get the subscribers associated with the project.
     */
    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

    /**
     * Get the subscriptions associated with the project.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the subscriptions associated with the project.
     */
    public function subscriptionPlans()
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    /**
     * Get the auto billing reminders associated with the project.
     */
    public function autoBillingReminders()
    {
        return $this->belongsToMany(User::class, 'subscription_plan_auto_billing_reminders', 'project_id', 'auto_billing_reminder_id')
                    ->using(SubscriptionPlanAutoBillingReminder::class);
    }

}
