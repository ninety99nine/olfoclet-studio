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
     *  Get the project subscriber messages with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the subscriber messages.
     *  @param array $countableRelationships The relationships to count on the subscriber messages.
     *  @return LengthAwarePaginator The paginated list of project subscriber messages.
     */
    public function getProjectSubscriberMessages($msisdn = null, $relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectSubscriberMessages($msisdn, $relationships, $countableRelationships)->latest()->paginate();
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
