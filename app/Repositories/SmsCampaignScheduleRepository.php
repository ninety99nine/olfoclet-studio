<?php

namespace App\Repositories;

use Carbon\Carbon;
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
     *  Get the project sms campaign schedules with filters (search, up_for_message, sort).
     *
     *  @param array<string, mixed> $filters Optional filters: msisdn (search by subscriber msisdn or schedule id), up_for_message (bool), sort (column:direction), per_page, page.
     *  @param array $relationships The relationships to eager load.
     *  @param array $countableRelationships The relationships to count.
     *  @return LengthAwarePaginator
     */
    public function getProjectSmsCampaignSchedulesFiltered(array $filters, array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        $query = $this->project->smsCampaignSchedules()->with($relationships)->withCount($countableRelationships);

        if (!empty($filters['msisdn'])) {
            $term = trim($filters['msisdn']);
            $query->where(function (Builder $q) use ($term) {
                $q->whereHas('subscriber', function (Builder $sub) use ($term) {
                    $sub->where('msisdn', 'like', '%' . $term . '%');
                });
                if (is_numeric($term)) {
                    $q->orWhere('sms_campaign_schedules.id', (int) $term);
                }
            });
        }

        if (isset($filters['up_for_message']) && $filters['up_for_message']) {
            $query->where('next_message_date', '<=', Carbon::now());
        }

        $sortApplied = false;
        if (!empty($filters['sort']) && preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'], $m)) {
            $column = $m[1];
            $direction = $m[2];
            $allowed = ['id', 'next_message_date', 'attempts', 'total_successful_attempts', 'total_failed_attempts', 'created_at'];
            if (in_array($column, $allowed, true)) {
                if ($column === 'next_message_date' && $direction === 'asc') {
                    // Next message soonest: rows with a datetime first (soonest first), nulls last
                    $query->orderByRaw('CASE WHEN sms_campaign_schedules.next_message_date IS NULL THEN 1 ELSE 0 END ASC')
                        ->orderBy('sms_campaign_schedules.next_message_date', 'asc');
                } else {
                    $query->orderBy('sms_campaign_schedules.' . $column, $direction);
                }
                $sortApplied = true;
            }
        }

        if (!$sortApplied) {
            $query->latest('sms_campaign_schedules.id');
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
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
