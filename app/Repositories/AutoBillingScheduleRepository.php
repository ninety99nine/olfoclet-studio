<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Pivots\AutoBillingSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     *  Get the project auto billing schedules with optional relationships.
     *
     *  @param string|null $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the auto billing schedules.
     *  @param array $countableRelationships The relationships to count on the auto billing schedules.
     *  @return LengthAwarePaginator The paginated list of project auto billing schedules.
     */
    public function getProjectAutoBillingSchedules($msisdn = null, $relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectAutoBillingSchedules($msisdn, $relationships, $countableRelationships)->latest()->paginate();
    }

    /**
     *  Get the project auto billing schedules with filters (search, up_for_schedule, sort).
     *
     *  @param array<string, mixed> $filters Optional filters: msisdn (search by subscriber msisdn or schedule id), up_for_schedule (bool), sort (column:direction), per_page, page.
     *  @param array $relationships The relationships to eager load.
     *  @param array $countableRelationships The relationships to count.
     *  @return LengthAwarePaginator
     */
    public function getProjectAutoBillingSchedulesFiltered(array $filters, array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        $query = $this->project->autoBillingSchedules()->with($relationships)->withCount($countableRelationships);

        if (!empty($filters['msisdn'])) {
            $term = trim($filters['msisdn']);
            $query->where(function (Builder $q) use ($term) {
                $q->whereHas('subscriber', function (Builder $sub) use ($term) {
                    $sub->where('msisdn', 'like', '%' . $term . '%');
                });
                if (is_numeric($term)) {
                    $q->orWhere('auto_billing_schedules.id', (int) $term);
                }
            });
        }

        if (isset($filters['up_for_schedule']) && $filters['up_for_schedule']) {
            $query->where('auto_billing_enabled', '1')
                ->where('next_attempt_date', '<=', Carbon::now());
        }

        $sortApplied = false;
        if (!empty($filters['sort']) && preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'], $m)) {
            $column = $m[1];
            $direction = $m[2];
            $allowed = ['id', 'next_attempt_date', 'attempt', 'overall_attempts', 'created_at'];
            if (in_array($column, $allowed, true)) {
                $query->orderBy('auto_billing_schedules.' . $column, $direction);
                $sortApplied = true;
            }
        }

        if (!$sortApplied) {
            $query->latest('auto_billing_schedules.id');
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
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

    /**
     * Get progress stats for auto billing: total subscribers due, processed count, batches, and next run datetime.
     *
     * @return array{total_due: int, processed: int, total_in_batches: int, next_run_at: string|null}
     */
    public function getAutoBillingProgress(): array
    {
        $projectId = $this->project->id;
        $totalDue = (int) $this->project->autoBillingSchedules()
            ->where('auto_billing_enabled', '1')
            ->where('next_attempt_date', '<=', Carbon::now())
            ->count();

        $pricingPlanIds = $this->project->pricingPlans()->pluck('id')->all();
        if (empty($pricingPlanIds)) {
            $nextRunRaw = $this->project->autoBillingSchedules()
                ->where('auto_billing_enabled', '1')
                ->where('next_attempt_date', '>', Carbon::now())
                ->min('next_attempt_date');
            $nextRunAt = $nextRunRaw ? Carbon::parse($nextRunRaw)->toIso8601String() : null;
            return [
                'total_due'         => $totalDue,
                'processed'          => 0,
                'total_in_batches'  => 0,
                'next_run_at'       => $nextRunAt,
            ];
        }

        $cutoff = Carbon::now()->subHours(24)->timestamp;
        $batchStats = DB::table('auto_billing_job_batches')
            ->join('job_batches', 'auto_billing_job_batches.job_batch_id', '=', 'job_batches.id')
            ->whereIn('auto_billing_job_batches.pricing_plan_id', $pricingPlanIds)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('job_batches.finished_at')
                    ->orWhere('job_batches.created_at', '>=', $cutoff);
            })
            ->selectRaw('SUM(job_batches.total_jobs) as total_jobs, SUM(job_batches.total_jobs - job_batches.pending_jobs) as processed_jobs')
            ->first();

        $totalInBatches = (int) ($batchStats->total_jobs ?? 0);
        $processed = (int) ($batchStats->processed_jobs ?? 0);

        // Closest next run (enabled schedules with next_attempt_date in the future)
        $nextRunRaw = $this->project->autoBillingSchedules()
            ->where('auto_billing_enabled', '1')
            ->where('next_attempt_date', '>', Carbon::now())
            ->min('next_attempt_date');
        $nextRunAt = $nextRunRaw ? Carbon::parse($nextRunRaw)->toIso8601String() : null;

        return [
            'total_due'         => $totalDue,
            'processed'          => $processed,
            'total_in_batches'  => $totalInBatches,
            'next_run_at'       => $nextRunAt,
        ];
    }
}
