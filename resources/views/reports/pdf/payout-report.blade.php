<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Requested</div><div class="kpi-value">${{ number_format($kpis['requestedAmount'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Approved</div><div class="kpi-value">${{ number_format($kpis['approvedAmount'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Pending</div><div class="kpi-value">${{ number_format($kpis['pendingAmount'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Outstanding Balance</div><div class="kpi-value">${{ number_format($kpis['totalOutstandingBalance'], 2) }}</div></td>
</tr></table>

<table class="items">
    <thead>
        <tr><th>Instructor</th><th>Method</th><th>Status</th><th class="amount">Amount</th><th>Requested</th><th>Processed</th></tr>
    </thead>
    <tbody>
        @foreach($payouts as $payout)
            <tr>
                <td>{{ $payout->instructor?->name ?? '—' }}</td>
                <td>{{ $payout->payment_method }}</td>
                <td>{{ ucfirst($payout->status) }}</td>
                <td class="amount">${{ number_format((float) $payout->amount, 2) }}</td>
                <td>{{ $payout->created_at?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $payout->processed_at?->format('M d, Y') ?? '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Payout Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
