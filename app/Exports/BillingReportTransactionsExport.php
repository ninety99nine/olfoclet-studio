<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\BillingTransaction;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;

class BillingReportTransactionsExport implements FromQuery, WithProperties, WithHeadings, WithMapping
{
    use Exportable;

    private $date;
    private $project;

    public function __construct(Project $project, Carbon $date)
    {
        $this->date = $date;
        $this->project = $project;
    }

    public function properties(): array
    {
        return [
            //  'creator'        => 'Patrick Brouwers',
            //  'lastModifiedBy' => 'Patrick Brouwers',
            'title'          => 'Transactions',
            'description'    => 'Transactions for '.$this->date->shortMonthName.' '.$this->date->year,
            'subject'        => 'Transactions',
            //  'keywords'       => 'invoices,export,spreadsheet',
            //  'category'       => 'Invoices',
            //  'manager'        => 'Patrick Brouwers',
            'company'        => $this->project->name,
        ];
    }

    /**
     *  Reference: https://docs.laravel-excel.com/3.1/exports/from-query.html
     */
    public function query()
    {
        return BillingTransaction::query()
                ->where('is_successful', '1')
                ->whereYear('created_at', $this->date->year)
                ->whereMonth('created_at', $this->date->month)
                ->with('subscriber');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Msisdn',
            'Amount',
            'Funds Before Deduction',
            'Funds After Deduction',
            'Rating Type',
            'Description',
            'Created Using Auto Billing',
            'Created Date',
        ];
    }

    /**
     * @param BillingTransaction $billingTransaction
     */
    public function map($billingTransaction): array
    {
        return [
            $billingTransaction->id,
            $billingTransaction->subscriber->msisdn,
            $billingTransaction->amount->amount_without_currency,
            $billingTransaction->funds_before_deduction->amount_without_currency,
            $billingTransaction->funds_after_deduction->amount_without_currency,
            $billingTransaction->rating_type,
            $billingTransaction->description,
            $billingTransaction->created_using_auto_billing ? 'Yes' : 'No',
            $billingTransaction->created_at->format('d M Y H:i:s')
        ];
    }
}
