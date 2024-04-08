<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $billingReport->name }} Billing Report</title>
    <style>
        /* Styles for the report */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 20px auto;
            padding: 20px;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            white-space: nowrap;
        }

        th, .bg-gray {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-20">{{ $billingReport->name }} Billing Report</h1>

        <p>Service: <strong>{{ $project->name }}</strong></p>
        <p class="mb-20">Created Date: <strong>{{ $billingReport->created_at->format('d M Y H:i') }}</strong></p>

        <h2 class="mb-20">Report Overview</h2>

        <table>
            <tbody>
                <tr>
                    <td>Total Transactions</td>
                    <td>{{ $billingReport->total_transactions }}</td>
                </tr>
                <tr>
                    <td>Gross Revenue</td>
                    <td>{{ $billingReport->gross_revenue->amount_with_currency }}</td>
                </tr>
                <tr>
                    <td>Costs</td>
                    <td>{{ $billingReport->costs->amount_with_currency }}</td>
                </tr>
                <tr>
                    <td>Sharable Revenue</td>
                    <td>{{ $billingReport->sharable_revenue->amount_with_currency }}</td>
                </tr>
                <tr>
                    <td>Their Share ({{ $project->their_share_percentage }}%)</td>
                    <td>{{ $billingReport->their_share->amount_with_currency }}</td>
                </tr>
                <tr class="bg-gray">
                    <td><strong>Our Share ({{ $project->our_share_percentage }}%)</strong></td>
                    <td><strong>{{ $billingReport->our_share->amount_with_currency }}</strong></td>
                </tr>
            </tbody>
        </table>

        <h3 class="mb-20">Cost Breakdown</h3>

        <table>
            <thead>
                <tr>
                    <th>Cost</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billingReport->cost_breakdown as $costName => $costAmount)
                    <tr>
                        <td>{{ $costName }}</td>
                        <td>{{ $costAmount->amount_with_currency }}</td>
                    </tr>
                @endforeach
                <tr class="bg-gray">
                    <td><strong>Total</strong></td>
                    <td><strong>{{ $billingReport->costs->amount_with_currency }}</strong></td>
                </tr>
            </tbody>
        </table>

        <hr class="mt-20 mb-20">

        <p>
            This report outlines the performance of the <strong>{{ $project->name }}</strong> service for <strong>{{ $billingReport->name }}</strong>.
            It indicates a total of <strong>{{ $billingReport->total_transactions }} {{ $billingReport->total_transactions == 1 ? 'transaction' : 'transactions'}}</strong>,
            generating a gross revenue of <strong>{{ $billingReport->gross_revenue->amount_with_currency }}</strong>. After deducting costs amounting to <strong>{{ $billingReport->costs->amount_with_currency }}</strong>,
            the sharable revenue is calculated to be <strong>{{ $billingReport->sharable_revenue->amount_with_currency }}</strong>. Their share, at <strong>40%</strong>, is <strong>{{ $billingReport->their_share->amount_with_currency }}</strong>,
            while our share, at <strong>60%</strong>, is <strong>{{ $billingReport->our_share->amount_with_currency }}</strong>. The cost breakdown reveals that expenses include USAF, BOCRA, VAT, and dealer commission, totaling <strong>{{ $billingReport->costs->amount_with_currency }}</strong>.
        </p>

    </div>
</body>

</html>
