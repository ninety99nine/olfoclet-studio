<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Pivots\AutoBillingSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

    /**
     * Get progress stats for auto billing: total subscribers due and how many have been processed in recent batches.
     *
     * @return array{total_due: int, processed: int, total_in_batches: int}
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
            return [
                'total_due'         => $totalDue,
                'processed'          => 0,
                'total_in_batches'  => 0,
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

        return [
            'total_due'         => $totalDue,
            'processed'          => $processed,
            'total_in_batches'  => $totalInBatches,
        ];
    }
}
