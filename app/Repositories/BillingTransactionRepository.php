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
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @param array $relationships The relationships to eager load on the billing transactions.
     *  @param array $countableRelationships The relationships to count on the billing transactions.
     *  @return HasMany
     */
    public function queryProjectBillingTransactions($msisdn = null, $relationships = [], $countableRelationships = []): hasMany
    {
        $query = $this->project->billingTransactions()->with($relationships)->withCount($countableRelationships);

        return empty($msisdn) ? $query : $query->whereHas('subscriber', function (Builder $query) use ($msisdn) {
            return $query->where('msisdn', $msisdn);
         });
    }

    /**
     *  Get the project billing transactions with optional relationships
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
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
