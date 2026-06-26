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

@php
    $stMax = max(1, collect($statusBreakdown)->max('amount'));
    $stColors = ['approved'=>'#34d399','pending'=>'#fbbf24','rejected'=>'#f87171','processing'=>'#60a5fa'];
@endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full" colspan="2">
            <div class="chart-title">Payout Amount by Status</div>
            <table class="bar-chart">
                @foreach($statusBreakdown as $st => $row)
                    <tr>
                        <td class="bar-label">{{ ucfirst($st) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $row['amount'] > 0 ? max(3, ($row['amount']/$stMax)*100) : 0 }}%;background:{{ $stColors[$st] ?? '#94a3b8' }};"></div></div></td>
                        <td class="bar-value">${{ number_format($row['amount'], 0) }} ({{ $row['count'] }})</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>

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
