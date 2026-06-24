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
