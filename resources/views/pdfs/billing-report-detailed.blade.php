<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Report: {{ $billingReport->name }} — Financial Overview</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.4; color: #334155; margin: 0; padding: 24px; }
        h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; }
        h2 { font-size: 14px; font-weight: 700; color: #1e293b; margin: 24px 0 12px 0; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }
        h3 { font-size: 12px; font-weight: 700; color: #475569; margin: 16px 0 8px 0; text-transform: uppercase; letter-spacing: 0.05em; }
        .meta { font-size: 11px; color: #64748b; margin-bottom: 8px; }
        .overview-note { font-size: 10px; color: #64748b; margin-bottom: 20px; font-style: italic; }
        .metrics { display: table; width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .metric-cell { display: table-cell; width: 16.66%; padding: 12px 10px; border: 1px solid #e2e8f0; background: #f8fafc; vertical-align: top; }
        .metric-cell.our-share { background: #eef2ff; border-color: #c7d2fe; }
        .metric-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 4px; }
        .metric-value { font-size: 14px; font-weight: 700; color: #1e293b; }
        .metric-cell.our-share .metric-value { color: #3730a3; }
        .cost-table { width: 100%; max-width: 400px; border-collapse: collapse; margin: 8px 0; }
        .cost-table th, .cost-table td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; }
        .cost-table th { background: #f1f5f9; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #475569; width: 60%; }
        .cost-table td { font-size: 11px; font-weight: 600; }
        .analytics-cell { display: table-cell; width: 50%; padding: 14px; border: 1px solid #e2e8f0; background: #fff; vertical-align: top; }
        .analytics-label { font-size: 10px; font-weight: 600; color: #64748b; margin-bottom: 6px; }
        .analytics-value { font-size: 16px; font-weight: 700; color: #1e293b; }
        .analytics-value .success-rate { font-size: 12px; color: #4f46e5; font-weight: 600; }
        .chart-block { margin: 16px 0; text-align: center; }
        .chart-block img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
        .chart-title { font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 10px; }
        .no-chart { padding: 24px; text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; color: #64748b; font-size: 11px; }
        .summary { margin: 20px 0; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 11px; line-height: 1.5; }
        .footer-note { margin-top: 28px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
        .print-banner { display: none; padding: 12px 24px; background: #fef3c7; border-bottom: 1px solid #f59e0b; font-size: 13px; color: #92400e; }
        @media screen { .print-banner { display: block; } }
        @media print { .print-banner { display: none !important; } }
    </style>
</head>
<body>
    @if(isset($showPrintBanner) && $showPrintBanner)
    <div class="print-banner" role="alert">
        <strong>Save as PDF:</strong> Use your browser’s Print (Mac: <kbd>Cmd+P</kbd>, Windows: <kbd>Ctrl+P</kbd>) and choose <strong>Save as PDF</strong> or <strong>Print to PDF</strong>.
    </div>
    @endif
    <h1>{{ $billingReport->name }}</h1>
    <p class="meta">{{ $project->name }} · Report period: {{ $periodLabel }} · Generated {{ now()->format('d M Y H:i') }}</p>
    <p class="overview-note">Financial overview for business, accounting and decision-making. Individual transactions are not included in this PDF; use the CSV or Excel export on the report page for transaction-level data.</p>

    <h2>Financial summary</h2>
    <table class="metrics">
        <tr>
            <td class="metric-cell">
                <div class="metric-label">Gross revenue</div>
                <div class="metric-value">{{ $billingReport->gross_revenue?->amount_with_currency ?? '—' }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-label">Costs</div>
                <div class="metric-value">{{ $billingReport->costs?->amount_with_currency ?? '—' }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-label">Sharable revenue</div>
                <div class="metric-value">{{ $billingReport->sharable_revenue?->amount_with_currency ?? '—' }}</div>
            </td>
            <td class="metric-cell our-share">
                <div class="metric-label">Our share ({{ $project->our_share_percentage ?? 60 }}%)</div>
                <div class="metric-value">{{ $billingReport->our_share?->amount_with_currency ?? '—' }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-label">Their share ({{ $project->their_share_percentage ?? 40 }}%)</div>
                <div class="metric-value">{{ $billingReport->their_share?->amount_with_currency ?? '—' }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-label">Total transactions</div>
                <div class="metric-value">{{ number_format($billingReport->total_transactions ?? 0) }}</div>
            </td>
        </tr>
    </table>

    <h3>Cost breakdown</h3>
    @if($billingReport->cost_breakdown && count((array) $billingReport->cost_breakdown) > 0)
        <table class="cost-table">
            @foreach($billingReport->cost_breakdown as $costName => $costAmount)
                @php
                    $amountStr = '—';
                    if (is_object($costAmount) && (property_exists($costAmount, 'amount_with_currency') || method_exists($costAmount, 'amount_with_currency'))) {
                        $amountStr = $costAmount->amount_with_currency ?? '—';
                    } elseif (is_array($costAmount) && isset($costAmount['amount_with_currency'])) {
                        $amountStr = $costAmount['amount_with_currency'];
                    }
                @endphp
                <tr>
                    <th>{{ $costName }}</th>
                    <td>{{ $amountStr }}</td>
                </tr>
            @endforeach
        </table>
        <p style="margin-top:8px;"><strong>Total costs:</strong> {{ $billingReport->costs?->amount_with_currency ?? '—' }}</p>
    @else
        <p>No cost breakdown available.</p>
    @endif

    <h2>Analytics for this period</h2>
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td class="analytics-cell">
                <div class="analytics-label">Successful vs all transactions</div>
                @if($overview)
                    <div class="analytics-value">
                        {{ number_format($overview['successful_transactions']) }} successful / {{ number_format($overview['total_transactions']) }} total
                        @if($overview['transaction_success_rate'] !== null)
                            <span class="success-rate">({{ $overview['transaction_success_rate'] }}% success rate)</span>
                        @endif
                    </div>
                @else
                    <div class="analytics-value">—</div>
                @endif
            </td>
            <td class="analytics-cell">
                <div class="analytics-label">Revenue (period)</div>
                @if($overview && isset($overview['total_revenue']))
                    <div class="analytics-value">P{{ number_format($overview['total_revenue'], 2) }}</div>
                @else
                    <div class="analytics-value">—</div>
                @endif
            </td>
        </tr>
    </table>

    <h2>Transactions over time</h2>
    @if($transactionsChartImage)
        <div class="chart-block">
            <div class="chart-title">Daily transaction counts (all vs successful)</div>
            <img src="{{ $transactionsChartImage }}" alt="Transactions over time" style="max-width:600px;height:280px;object-fit:contain;" />
        </div>
    @else
        <div class="no-chart">No transaction data for this period.</div>
    @endif

    <h2>Revenue over time</h2>
    @if($revenueChartImage)
        <div class="chart-block">
            <div class="chart-title">Daily revenue (successful transactions)</div>
            <img src="{{ $revenueChartImage }}" alt="Revenue over time" style="max-width:600px;height:280px;object-fit:contain;" />
        </div>
    @else
        <div class="no-chart">No revenue data for this period.</div>
    @endif

    <h2>Summary</h2>
    <div class="summary">
        <p style="margin:0 0 8px 0;">
            This financial overview covers <strong>{{ $project->name }}</strong> for <strong>{{ $billingReport->name }}</strong>.
            Total of <strong>{{ number_format($billingReport->total_transactions ?? 0) }} {{ ($billingReport->total_transactions ?? 0) == 1 ? 'transaction' : 'transactions' }}</strong>;
            gross revenue <strong>{{ $billingReport->gross_revenue?->amount_with_currency ?? '—' }}</strong>; costs <strong>{{ $billingReport->costs?->amount_with_currency ?? '—' }}</strong>;
            sharable revenue <strong>{{ $billingReport->sharable_revenue?->amount_with_currency ?? '—' }}</strong>.
            Their share ({{ $project->their_share_percentage ?? 40 }}%): <strong>{{ $billingReport->their_share?->amount_with_currency ?? '—' }}</strong>.
            Our share ({{ $project->our_share_percentage ?? 60 }}%): <strong>{{ $billingReport->our_share?->amount_with_currency ?? '—' }}</strong>.
        </p>
        <p style="margin:0;">This document is intended for business, accounting and decision-making use. Transaction-level detail is available via CSV or Excel export from the report page.</p>
    </div>

    <p class="footer-note">Billing Report — Financial Overview generated by TelcoFlo Studio. No individual transactions are included in this PDF.</p>
</body>
</html>
