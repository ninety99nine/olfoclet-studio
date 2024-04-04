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
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        h1, h2, h3, p {
            margin: 0;
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

        th {
            white-space: nowrap;
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
            <thead>
                <tr>
                    <th>Gross Revenue</th>
                    <th>Costs</th>
                    <th>Sharable Revenue</th>
                    <th>Our Share ({{ $project->our_share_percentage }}%)</th>
                    <th>Their Share ({{ $project->thier_share_percentage }}%)</th>
                    <th class="text-center">Total Transactions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $billingReport->gross_revenue->amount_with_currency }}</td>
                    <td>{{ $billingReport->costs->amount_with_currency }}</td>
                    <td>{{ $billingReport->sharable_revenue->amount_with_currency }}</td>
                    <td><strong>{{ $billingReport->our_share->amount_with_currency }}</strong></td>
                    <td>{{ $billingReport->their_share->amount_with_currency }}</td>
                    <td class="text-center">{{ $billingReport->total_transactions }}</td>
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
                    <td>Total</td>
                    <td><strong>{{ $billingReport->costs->amount_with_currency }}</strong></td>
                </tr>
            </tbody>
        </table>

    </div>
</body>

</html>
