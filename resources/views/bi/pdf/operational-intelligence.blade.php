<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid">
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Pending Course Reviews</div><div class="kpi-value">{{ number_format($kpis['pendingCourseReviews']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Pending Verifications</div><div class="kpi-value">{{ number_format($kpis['pendingVerifications']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Failed Payments Today</div><div class="kpi-value">{{ number_format($kpis['failedPaymentsToday']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Open Refunds</div><div class="kpi-value">{{ number_format($kpis['openRefunds']) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Failed Queue Jobs</div><div class="kpi-value">{{ number_format($kpis['failedJobs']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Queued Jobs</div><div class="kpi-value">{{ number_format($kpis['queueJobs']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Avg Order Processing</div><div class="kpi-value">{{ $kpis['avgProcessingMins'] }}m</div></td>
    </tr>
</table>

@php $osMax = max(1, ...array_values($orderStatusBreakdown)); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Order Status Breakdown</div>
            <table class="bar-chart">
                @foreach($orderStatusBreakdown as $status => $count)
                    <tr>
                        <td class="bar-label">{{ $status }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $count > 0 ? max(3, ($count/$osMax)*100) : 0 }}%;background:{{ $status === 'Failed' ? '#ef4444' : ($status === 'Pending' ? '#D7A441' : '#22c55e') }};"></div></div></td>
                        <td class="bar-value">{{ number_format($count) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Order</th><th class="amount">Amount</th><th>Gateway</th><th>Time</th></tr>
    </thead>
    <tbody>
        @forelse($failedPayments as $pay)
            <tr>
                <td>{{ $pay->order?->order_number ?? '—' }}</td>
                <td class="amount">${{ number_format((float) $pay->amount, 2) }}</td>
                <td>{{ $pay->payment_gateway ?? '—' }}</td>
                <td>{{ $pay->created_at?->format('M d, Y g:i A') ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#9ca3af;">No failed payments in this period</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
