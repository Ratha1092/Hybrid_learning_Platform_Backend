<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Total Payments</div><div class="kpi-value">{{ number_format($kpis['total']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Total Paid</div><div class="kpi-value">${{ number_format($kpis['totalPaidAmount'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Success Rate</div><div class="kpi-value">{{ $kpis['successRate'] }}%</div></td>
    <td class="kpi-cell"><div class="kpi-label">Failed</div><div class="kpi-value">{{ number_format($kpis['failedCount']) }}</div></td>
</tr></table>

@php
    $gwMax  = max(1, collect($gatewayBreakdown)->max('amount'));
    $stMax  = max(1, collect($statusBreakdown)->max('count'));
    $stColors = ['paid'=>'#34d399','pending'=>'#fbbf24','failed'=>'#f87171'];
@endphp
<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Revenue by Gateway</div>
            <table class="bar-chart">
                @foreach($gatewayBreakdown as $gw => $row)
                    <tr>
                        <td class="bar-label">{{ strtoupper($gw) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $row['amount'] > 0 ? max(3, ($row['amount']/$gwMax)*100) : 0 }}%;background:#D7A441;"></div></div></td>
                        <td class="bar-value">${{ number_format($row['amount'], 0) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Count by Status</div>
            <table class="bar-chart">
                @foreach($statusBreakdown as $st => $row)
                    <tr>
                        <td class="bar-label">{{ ucfirst($st) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $row['count'] > 0 ? max(3, ($row['count']/$stMax)*100) : 0 }}%;background:{{ $stColors[$st] ?? '#94a3b8' }};"></div></div></td>
                        <td class="bar-value">{{ number_format($row['count']) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Transaction</th><th>Order #</th><th>Gateway</th><th class="amount">Amount</th><th>Status</th><th>Paid At</th></tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
            <tr>
                <td>{{ \Illuminate\Support\Str::limit($payment->transaction_id ?? '—', 18) }}</td>
                <td>{{ $payment->order?->order_number ?? '—' }}</td>
                <td>{{ strtoupper($payment->payment_gateway?->value ?? '') }}</td>
                <td class="amount">${{ number_format((float) $payment->amount, 2) }}</td>
                <td>{{ ucfirst($payment->status?->value ?? '') }}</td>
                <td>{{ $payment->paid_at?->format('M d, Y') ?? '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Payment Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
