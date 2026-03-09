<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\BillingReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillingReportRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var BillingReport|null The BillingReport instance associated with the repository.
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
     *  Query the project billing reports with optional relationships.
     *
     *  @param array $relationships The relationships to eager load on the billing reports.
     *  @param array $countableRelationships The relationships to count on the billing reports.
     *  @return HasMany
     */
    public function queryProjectBillingReports(array $relationships = [], array $countableRelationships = []): HasMany
    {
        return $this->project->billingReports()->with($relationships)->withCount($countableRelationships);
    }

    /**
     *  Get the project billing reports with optional filters, relationships and pagination.
     *
     *  @param array|null $filters Optional filters (search, date_from, date_to, per_page, sort).
     *  @param array $relationships The relationships to eager load on the billing reports.
     *  @param array $countableRelationships The relationships to count on the billing reports.
     *  @return LengthAwarePaginator The paginated list of project billing reports.
     */
    public function getProjectBillingReportsWithFilters(?array $filters = null, array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        $query = $this->queryProjectBillingReports($relationships, $countableRelationships);

        if ($filters) {
            if (!empty($filters['search'])) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            }

            if (!empty($filters['date_from'])) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($filters['date_from'])->startOfDay());
            }
            if (!empty($filters['date_to'])) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay());
            }

            if (!empty($filters['sort']) && preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'], $m)) {
                $column = $m[1];
                $direction = $m[2];
                $allowed = ['created_at', 'id', 'name', 'total_transactions', 'month', 'year', 'gross_revenue'];
                if (in_array($column, $allowed, true)) {
                    $query->orderBy($column, $direction);
                }
            }
        }

        $perPage = (is_array($filters) && isset($filters['per_page'])) ? (int) $filters['per_page'] : 15;

        if (empty($filters['sort']) || !preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'] ?? '', $m) || !in_array($m[1], ['created_at', 'id', 'name', 'total_transactions', 'month', 'year', 'gross_revenue'], true)) {
            $query->latest('created_at');
        }

        return $query->paginate($perPage);
    }

    /**
     *  Get the project billing reports with optional relationships (no filters).
     *
     *  @param array $relationships The relationships to eager load on the billing reports.
     *  @param array $countableRelationships The relationships to count on the billing reports.
     *  @return LengthAwarePaginator The paginated list of project billing reports.
     */
    public function getProjectBillingReports(array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectBillingReports($relationships, $countableRelationships)->latest('billing_reports.created_at')->paginate(15);
    }

    /**
     *  Count the project billing reports.
     *
     *  @return int The total project billing reports.
     */
    public function countProjectBillingReports(): int
    {
        return $this->queryProjectBillingReports()->count();
    }
}
