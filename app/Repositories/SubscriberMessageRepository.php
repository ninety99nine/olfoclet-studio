<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriberMessageRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var SubscriberMessage|null The SubscriberMessage instance associated with the repository.
     */
    protected ?SubscriberMessage $subscriberMessage;

    /**
     *  Create a new SubscriberMessageRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param SubscriberMessage|null $subscriberMessage The SubscriberMessage instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?SubscriberMessage $subscriberMessage)
    {
        $this->project = $project;
        $this->subscriberMessage = $subscriberMessage;
    }

    /**
     *  Query the project subscriber messages with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the subscriber messages.
     *  @param array $countableRelationships The relationships to count on the subscriber messages.
     *  @return HasMany
     */
    public function queryProjectSubscriberMessages($msisdn = null, $relationships = [], $countableRelationships = []): hasMany
    {
        $query = $this->project->subscriberMessages()->with($relationships)->withCount($countableRelationships);

        return empty($msisdn) ? $query : $query->whereHas('subscriber', function (Builder $query) use ($msisdn) {
            return $query->where('msisdn', $msisdn);
         });
    }

    /**
     *  Get the project subscriber messages with optional filters, relationships and pagination.
     *
     *  @param array<string, mixed>|null $filters Optional filters (msisdn, status, type, date_from, date_to, per_page, sort).
     *  @param array $relationships The relationships to eager load on the subscriber messages.
     *  @param array $countableRelationships The relationships to count on the subscriber messages.
     *  @return LengthAwarePaginator The paginated list of project subscriber messages.
     */
    public function getProjectSubscriberMessages(?array $filters = null, array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        $query = $this->project->subscriberMessages()->with($relationships)->withCount($countableRelationships);

        if ($filters) {
            if (! empty($filters['msisdn'])) {
                $query->whereHas('subscriber', function (Builder $q) use ($filters) {
                    $q->where('msisdn', 'like', '%' . $filters['msisdn'] . '%');
                });
            }

            if (! empty($filters['status'])) {
                if (strtolower($filters['status']) === 'successful') {
                    $query->where('is_successful', true);
                } elseif (strtolower($filters['status']) === 'unsuccessful') {
                    $query->where('is_successful', false);
                }
            }

            if (! empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (! empty($filters['date_from'])) {
                $query->where('subscriber_messages.created_at', '>=', \Carbon\Carbon::parse($filters['date_from'])->startOfDay());
            }
            if (! empty($filters['date_to'])) {
                $query->where('subscriber_messages.created_at', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay());
            }

            if (! empty($filters['sort']) && preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'], $m)) {
                $column = $m[1];
                $direction = $m[2];
                $allowed = ['created_at', 'id', 'type'];
                if (in_array($column, $allowed, true)) {
                    $query->orderBy('subscriber_messages.' . $column, $direction);
                }
            } else {
                $query->orderBy('subscriber_messages.created_at', 'desc');
            }
        } else {
            $query->latest('subscriber_messages.created_at');
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
    }

    /**
     *  Count the project subscriber messages
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the subscriber messages.
     *  @param array $countableRelationships The relationships to count on the subscriber messages.
     *  @return int The total project subscriber messages.
     */
    public function countProjectSubscriberMessages($msisdn = null): int
    {
        return $this->queryProjectSubscriberMessages($msisdn)->count();
    }

}
