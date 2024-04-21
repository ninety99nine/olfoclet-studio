<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pivots\SmsCampaignSchedule;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SmsCampaignScheduleRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var SmsCampaignSchedule|null The SmsCampaignSchedule instance associated with the repository.
     */
    protected ?SmsCampaignSchedule $smsCampaignSchedule;

    /**
     *  Create a new SmsCampaignScheduleRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param SmsCampaignSchedule|null $smsCampaignSchedule The SmsCampaignSchedule instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?SmsCampaignSchedule $smsCampaignSchedule)
    {
        $this->project = $project;
        $this->smsCampaignSchedule = $smsCampaignSchedule;
    }

    /**
     *  Query the project sms campaign schedules with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the sms campaign schedules.
     *  @param array $countableRelationships The relationships to count on the sms campaign schedules.
     *  @return HasMany
     */
    public function queryProjectSmsCampaignSchedules($msisdn = null, $relationships = [], $countableRelationships = []): hasMany
    {
        $query = $this->project->smsCampaignSchedules()->with($relationships)->withCount($countableRelationships);

        return empty($msisdn) ? $query : $query->whereHas('subscriber', function (Builder $query) use ($msisdn) {
            return $query->where('msisdn', $msisdn);
         });
    }

    /**
     *  Get the project sms campaign schedules with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the sms campaign schedules.
     *  @param array $countableRelationships The relationships to count on the sms campaign schedules.
     *  @return LengthAwarePaginator The paginated list of project sms campaign schedules.
     */
    public function getProjectSmsCampaignSchedules($msisdn = null, $relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectSmsCampaignSchedules($msisdn, $relationships, $countableRelationships)->latest()->paginate();
    }

    /**
     *  Count the project sms campaign schedules
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the sms campaign schedules.
     *  @param array $countableRelationships The relationships to count on the sms campaign schedules.
     *  @return int The total project sms campaign schedules.
     */
    public function countProjectSmsCampaignSchedules($msisdn = null): int
    {
        return $this->queryProjectSmsCampaignSchedules($msisdn)->count();
    }

}
