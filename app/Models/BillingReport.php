<?php

namespace App\Models;

use App\Casts\Money;
use App\Casts\LinkToUploads;
use App\Casts\JsonToArrayMoney;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\BillingReportTrait;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingReport extends Model
{
    use HasFactory, HasEagerLimit, BillingReportTrait;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'costs' => Money::class,
        'our_share' => Money::class,
        'their_share' => Money::class,
        'gross_revenue' => Money::class,
        'sharable_revenue' => Money::class,
        'invoice_pdf_path' => LinkToUploads::class,
        'overview_pdf_path' => LinkToUploads::class,
        'cost_breakdown' => JsonToArrayMoney::class,
        'successful_transactions_csv_path' => LinkToUploads::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gross_revenue', 'costs', 'cost_breakdown', 'sharable_revenue', 'our_share', 'their_share', 'total_transactions',
        'overview_pdf_path', 'successful_transactions_csv_path', 'invoice_pdf_path', 'project_id'
    ];

    /**
     * Get the project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
