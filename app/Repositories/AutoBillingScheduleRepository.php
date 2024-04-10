<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Pivots\AutoBillingSchedule;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AutoBillingScheduleRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var AutoBillingSchedule|null The AutoBillingSchedule instance associated with the repository.
     */
    protected ?AutoBillingSchedule $autoBillingSchedule;

    /**
     *  Create a new AutoBillingScheduleRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param AutoBillingSchedule|null $autoBillingSchedule The AutoBillingSchedule instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?AutoBillingSchedule $autoBillingSchedule)
    {
        $this->project = $project;
        $this->autoBillingSchedule = $autoBillingSchedule;
    }

    /**
     *  Query the project auto billing schedules with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the auto billing schedules.
     *  @param array $countableRelationships The relationships to count on the auto billing schedules.
     *  @return HasMany
     */
    public function queryProjectAutoBillingSchedules($msisdn = null, $relationships = [], $countableRelationships = []): hasMany
    {
        $query = $this->project->autoBillingSchedules()->with($relationships)->withCount($countableRelationships);

        return empty($msisdn) ? $query : $query->whereHas('subscriber', function (Builder $query) use ($msisdn) {
            return $query->where('msisdn', $msisdn);
         });
    }

    /**
     *  Get the project auto billing schedules with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the auto billing schedules.
     *  @param array $countableRelationships The relationships to count on the auto billing schedules.
     *  @return LengthAwarePaginator The paginated list of project auto billing schedules.
     */
    public function getProjectAutoBillingSchedules($msisdn = null, $relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectAutoBillingSchedules($msisdn, $relationships, $countableRelationships)->latest()->paginate();
    }

    /**
     *  Count the project auto billing schedules
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the auto billing schedules.
     *  @param array $countableRelationships The relationships to count on the auto billing schedules.
     *  @return int The total project auto billing schedules.
     */
    public function countProjectAutoBillingSchedules($msisdn = null): int
    {
        return $this->queryProjectAutoBillingSchedules($msisdn)->count();
    }

}
