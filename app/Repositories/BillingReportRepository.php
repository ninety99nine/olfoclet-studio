<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\BillingReport;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillingReportRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var BillingReport|null The BillingReport message instance associated with the repository.
     */
    protected ?BillingReport $billingReport;

    /**
     *  Create a new BillingReportRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param BillingReport|null $billingReport The BillingReport instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?BillingReport $billingReport)
    {
        $this->project = $project;
        $this->billingReport = $billingReport;
    }

    /**
     *  Query the project billing reports with optional relationships
     *
     *  @param array $relationships The relationships to eager load on the billing reports.
     *  @param array $countableRelationships The relationships to count on the billing reports.
     *  @return HasMany
     */
    public function queryProjectBillingReports($relationships = [], $countableRelationships = []): hasMany
    {
        $query = $this->project->billingReports()->with($relationships)->withCount($countableRelationships);

        return empty($msisdn) ? $query : $query->whereHas('subscriber', function (Builder $query) use ($msisdn) {
            return $query->where('msisdn', $msisdn);
         });
    }

    /**
     *  Get the project billing reports with optional relationships
     *
     *  @param array $relationships The relationships to eager load on the billing reports.
     *  @param array $countableRelationships The relationships to count on the billing reports.
     *  @return LengthAwarePaginator The paginated list of project billing reports.
     */
    public function getProjectBillingReports($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectBillingReports($relationships, $countableRelationships)->latest()->paginate();
    }

    /**
     *  Count the project billing reports
     *
     *  @param array $relationships The relationships to eager load on the billing reports.
     *  @param array $countableRelationships The relationships to count on the billing reports.
     *  @return int The total project billing reports.
     */
    public function countProjectBillingReports($msisdn = null): int
    {
        return $this->queryProjectBillingReports($msisdn)->count();
    }

}
