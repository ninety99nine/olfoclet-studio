<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriberRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var Subscriber|null The subscriber instance associated with the repository.
     */
    protected ?Subscriber $subscriber;

    /**
     *  Create a new SubscriberRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param Subscriber|null $subscriber The subscriber instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?Subscriber $subscriber)
    {
        $this->project = $project;
        $this->subscriber = $subscriber;
    }

    /**
     *  Find or create a subscriber with the given MSISDN for the associated project.
     *
     *  If the subscriber with the given MSISDN does not exist for the project,
     *  a new subscriber will be created and returned.
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @return Subscriber The found or newly created subscriber instance.
     */
    public function findOrCreateSubscriber(string $msisdn): Subscriber
    {
        //  Get the subscriber if they exist
        $subscriber = $this->findProjectSubscriber($msisdn);

        //  If the subscriber does not exist
        if (!$subscriber) {

            // Create a new subscriber
            $subscriber = $this->createProjectSubscriber($msisdn, null);

        }

        //  Return the existing or new subscriber
        return $subscriber;
    }

    /**
     *  Count the total number of subscribers for the associated project.
     *
     *  @return int The total number of subscribers.
     */
    public function countProjectSubscribers(): int
    {
        return $this->project->subscribers()->count();
    }

    /**
     * Show the project subscribers with optional filters, relationships, and countable relationships.
     *
     * @param array|null $filters The filters to apply (e.g., msisdn, status, billingStatus).
     * @param array $relationships The relationships to eager load on the subscribers.
     * @param array $countableRelationships The relationships to count on the subscribers.
     * @return LengthAwarePaginator The paginated list of project subscribers.
     */
    public function getProjectSubscribers(?array $filters = null, array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        $query = $this->project->subscribers()
            ->select('subscribers.*')
            ->addSelect([
                DB::raw('(SELECT COALESCE(SUM(bt.amount), 0) FROM billing_transactions bt WHERE bt.subscriber_id = subscribers.id AND bt.is_successful = 1) as total_spend_amount'),
            ])
            ->with($relationships)
            ->withCount($countableRelationships);

        // Apply filters if provided
        if ($filters) {

            // Mobile number search (partial match)
            if (!empty($filters['msisdn'])) {
                $query->where('msisdn', 'like', '%' . $filters['msisdn'] . '%');
            }

            // Subscription status filter
            if (!empty($filters['status'])) {
                $query->whereHas('latestSubscription', function ($q) use ($filters) {
                    if(strtolower($filters['status']) === 'active') {
                        $q->active();
                    }else if(strtolower($filters['status']) === 'inactive') {
                        $q->inActive();
                    }
                });
            }

            // User billing status (last user-initiated billing)
            if (!empty($filters['billingStatus'])) {
                $query->whereHas('latestUserBillingTransaction', function ($q) use ($filters) {
                    if (strtolower($filters['billingStatus']) === 'successful') {
                        $q->successful();
                    } elseif (strtolower($filters['billingStatus']) === 'unsuccessful') {
                        $q->unsuccessful();
                    }
                });
            }

            // Auto billing status (last auto billing transaction)
            if (!empty($filters['autoBillingStatus'])) {
                $query->whereHas('latestAutoBillingTransaction', function ($q) use ($filters) {
                    if (strtolower($filters['autoBillingStatus']) === 'successful') {
                        $q->successful();
                    } elseif (strtolower($filters['autoBillingStatus']) === 'unsuccessful') {
                        $q->unsuccessful();
                    }
                });
            }

            // Total spend: has spent (any successful payment) or has not spent
            if (!empty($filters['spendStatus'])) {
                $value = strtolower($filters['spendStatus']);
                if ($value === 'has_spent') {
                    $query->whereRaw('(SELECT COALESCE(SUM(bt.amount), 0) FROM billing_transactions bt WHERE bt.subscriber_id = subscribers.id AND bt.is_successful = 1) > 0');
                } elseif ($value === 'has_not_spent') {
                    $query->whereRaw('(SELECT COALESCE(SUM(bt.amount), 0) FROM billing_transactions bt WHERE bt.subscriber_id = subscribers.id AND bt.is_successful = 1) = 0');
                }
            }

            // Scheduled for billing: scheduled (future) or past_due (date passed, will still be billed)
            if (!empty($filters['scheduledBilling'])) {
                $value = strtolower($filters['scheduledBilling']);
                $query->whereHas('autoBillingSchedules', function ($q) use ($value) {
                    $q->where('auto_billing_enabled', true);
                    if ($value === 'scheduled') {
                        $q->where('next_attempt_date', '>', now());
                    } elseif ($value === 'past_due') {
                        $q->where('next_attempt_date', '<=', now());
                    }
                });
            }

            // Scheduled for SMS: scheduled (future) or past_due (date passed, will still be sent)
            if (!empty($filters['scheduledSms'])) {
                $value = strtolower($filters['scheduledSms']);
                $query->whereExists(function ($q) use ($value) {
                    $q->select(DB::raw(1))
                        ->from('sms_campaign_schedules')
                        ->whereColumn('sms_campaign_schedules.subscriber_id', 'subscribers.id');
                    if ($value === 'scheduled') {
                        $q->where('sms_campaign_schedules.next_message_date', '>', now());
                    } elseif ($value === 'past_due') {
                        $q->where('sms_campaign_schedules.next_message_date', '<=', now());
                    }
                });
            }

            // Cancelled auto billing: yes = has at least one schedule with auto_billing_enabled = false; no = has none
            if (!empty($filters['cancelledAutoBilling'])) {
                $value = strtolower($filters['cancelledAutoBilling']);
                if ($value === 'yes') {
                    $query->whereHas('autoBillingSchedules', function ($q) {
                        $q->where('auto_billing_enabled', false);
                    });
                } elseif ($value === 'no') {
                    $query->whereDoesntHave('autoBillingSchedules', function ($q) {
                        $q->where('auto_billing_enabled', false);
                    });
                }
            }

            // Date filters (subscriber created_at) — use range for index use
            if (!empty($filters['date_from'])) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($filters['date_from'])->startOfDay());
            }
            if (!empty($filters['date_to'])) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay());
            }

            // Sort (e.g. created_at:desc, id:asc, subscriptions_count:desc, messages_count:asc)
            if (!empty($filters['sort']) && preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'], $m)) {
                $column = $m[1];
                $direction = $m[2];
                $allowed = ['created_at', 'id', 'subscriptions_count', 'messages_count', 'total_spend_amount'];
                if (in_array($column, $allowed, true)) {
                    $query->orderBy($column, $direction);
                }
            }
        }

        $perPage = (is_array($filters) && isset($filters['per_page'])) ? (int) $filters['per_page'] : 15;

        if (empty($filters['sort']) || !preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'] ?? '', $m) || !in_array($m[1], ['created_at', 'id', 'subscriptions_count', 'messages_count', 'total_spend_amount'], true)) {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     *  Find a project subscriber with the given MSISDN for the associated project.
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @return Subscriber|null The found subscriber instance or null.
     */
    public function findProjectSubscriber(string $msisdn): Subscriber|null
    {
        //  Get the subscriber if they exist
        return $this->project->subscribers()->where('msisdn', $msisdn)->first();
    }

    /**
     *  Create a new project subscriber with the given MSISDN.
     *
     *  @param string $msisdn - The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array|null $metadata - The metadata of the subscriber.
     *  @return Subscriber The newly created subscriber instance.
     */
    public function createProjectSubscriber(string $msisdn, array|null $metadata): Subscriber
    {
        //  Create subscriber
        return Subscriber::create([
            'msisdn' => $msisdn,
            'metadata' => $metadata,
            'project_id' => $this->project->id
        ]);
    }

    /**
     *  Update the MSISDN of the associated project subscriber.
     *
     *  @param string|null $msisdn - The new MSISDN for the subscriber.
     *  @param array|null $metadata - The metadata of the subscriber.
     *  @return bool True if the update is successful, false otherwise.
     *  @throws ModelNotFoundException If the associated subscriber is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function updateProjectSubscriber(string|null $msisdn, array|null $metadata): bool
    {
        // Make sure the subscriber exists and belongs to the project
        if ($this->subscriber === null || $this->subscriber->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        if($msisdn != null) {
            $this->subscriber->msisdn = $msisdn;
        }

        $this->subscriber->metadata = $metadata;

        return $this->subscriber->save();
    }

    /**
     *  Wipe the metadata of the associated project subscriber (e.g. for data deletion requests).
     *  The subscriber record is kept; only metadata is cleared.
     *
     *  @return bool True if the update is successful, false otherwise.
     *  @throws ModelNotFoundException If the associated subscriber is not found or does not belong to the project.
     */
    public function wipeSubscriberMetadata(): bool
    {
        if ($this->subscriber === null || $this->subscriber->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        $this->subscriber->metadata = null;
        return $this->subscriber->save();
    }

    /**
     *  Get the subscriber instance associated with the repository.
     *
     *  @return Subscriber|null
     */
    public function getSubscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    /**
     *  Delete the associated project subscriber.
     *
     *  @return bool|null True if the deletion is successful, false if the subscriber is not found.
     *  @throws ModelNotFoundException If the associated subscriber is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteProjectSubscriber(): bool|null
    {
        // Make sure the subscriber exists and belongs to the project
        if ($this->subscriber === null || $this->subscriber->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        //  Delete subscriber
        return $this->subscriber->delete();
    }
}
