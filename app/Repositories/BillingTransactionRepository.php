<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\BillingTransaction;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillingTransactionRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var BillingTransaction|null The BillingTransaction message instance associated with the repository.
     */
    protected ?BillingTransaction $billingTransaction;

    /**
     *  Create a new BillingTransactionRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param BillingTransaction|null $billingTransaction The BillingTransaction instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?BillingTransaction $billingTransaction)
    {
        $this->project = $project;
        $this->billingTransaction = $billingTransaction;
    }

    /**
     *  Query the project billing transactions with optional relationships
     *
     *  @param string|null $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the billing transactions.
     *  @param array $countableRelationships The relationships to count on the billing transactions.
     *  @return HasMany
     */
    public function queryProjectBillingTransactions($msisdn = null, $relationships = [], $countableRelationships = []): HasMany
    {
        $query = $this->project->billingTransactions()->with($relationships)->withCount($countableRelationships);

        return empty($msisdn) ? $query : $query->whereHas('subscriber', function (Builder $q) use ($msisdn) {
            $q->where('msisdn', $msisdn);
        });
    }

    /**
     *  Get the project billing transactions with optional filters, relationships and countable relationships.
     *
     *  @param array<string, mixed>|null $filters Optional filters (msisdn, status, created_using_auto_billing, pricing_plan_id, date_from, date_to, per_page, sort).
     *  @param array $relationships The relationships to eager load on the billing transactions.
     *  @param array $countableRelationships The relationships to count on the billing transactions.
     *  @return LengthAwarePaginator The paginated list of project billing transactions.
     */
    public function getProjectTransactions(?array $filters = null, array $relationships = [], array $countableRelationships = []): LengthAwarePaginator
    {
        $query = $this->project->billingTransactions()->with($relationships)->withCount($countableRelationships);

        if ($filters) {
            if (!empty($filters['msisdn'])) {
                $query->whereHas('subscriber', function (Builder $q) use ($filters) {
                    $q->where('msisdn', 'like', '%' . $filters['msisdn'] . '%');
                });
            }

            if (!empty($filters['status'])) {
                if (strtolower($filters['status']) === 'successful') {
                    $query->successful();
                } elseif (strtolower($filters['status']) === 'unsuccessful') {
                    $query->unsuccessful();
                }
            }

            if (isset($filters['created_using_auto_billing']) && $filters['created_using_auto_billing'] !== '' && $filters['created_using_auto_billing'] !== null) {
                $query->where('created_using_auto_billing', (bool) $filters['created_using_auto_billing']);
            }

            if (!empty($filters['pricing_plan_id'])) {
                $query->where('pricing_plan_id', (int) $filters['pricing_plan_id']);
            }

            if (!empty($filters['date_from'])) {
                $query->where('billing_transactions.created_at', '>=', \Carbon\Carbon::parse($filters['date_from'])->startOfDay());
            }
            if (!empty($filters['date_to'])) {
                $query->where('billing_transactions.created_at', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay());
            }

            if (!empty($filters['sort']) && preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'], $m)) {
                $column = $m[1];
                $direction = $m[2];
                $allowed = ['created_at', 'id', 'amount'];
                if (in_array($column, $allowed, true)) {
                    $query->orderBy('billing_transactions.' . $column, $direction);
                }
            }
        }

        $perPage = (is_array($filters) && isset($filters['per_page'])) ? (int) $filters['per_page'] : 15;

        if (empty($filters['sort']) || !preg_match('/^([\w_]+):(asc|desc)$/', $filters['sort'] ?? '', $m) || !in_array($m[1], ['created_at', 'id', 'amount'], true)) {
            $query->latest('billing_transactions.created_at');
        }

        return $query->paginate($perPage);
    }

    /**
     *  Get the project billing transactions with optional relationships
     *
     *  @param string|null $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the billing transactions.
     *  @param array $countableRelationships The relationships to count on the billing transactions.
     *  @return LengthAwarePaginator The paginated list of project billing transactions.
     */
    public function getProjectBillingTransactions($msisdn = null, $relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->queryProjectBillingTransactions($msisdn, $relationships, $countableRelationships)->latest()->paginate();
    }

    /**
     *  Count the project billing transactions
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the billing transactions.
     *  @param array $countableRelationships The relationships to count on the billing transactions.
     *  @return int The total project billing transactions.
     */
    public function countProjectBillingTransactions($msisdn = null): int
    {
        return $this->queryProjectBillingTransactions($msisdn)->count();
    }

}
