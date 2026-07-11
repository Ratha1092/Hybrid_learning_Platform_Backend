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
        <td class="kpi-cell"><div class="kpi-label">Gross Revenue</div><div class="kpi-value">${{ number_format($kpis['grossRevenue'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Platform Profit</div><div class="kpi-value">${{ number_format($kpis['platformProfit'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Instructor Earnings</div><div class="kpi-value">${{ number_format($kpis['instructorEarnings'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Cash Flow</div><div class="kpi-value">${{ number_format($kpis['cashFlow'], 2) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Pending Payout</div><div class="kpi-value">${{ number_format($kpis['pendingPayout'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Completed Payout</div><div class="kpi-value">${{ number_format($kpis['completedPayout'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Wallet Balance</div><div class="kpi-value">${{ number_format($kpis['totalWalletBalance'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Operating Margin</div><div class="kpi-value">{{ $kpis['operatingMargin'] }}%</div></td>
    </tr>
</table>

@php $cfMax = max(1, ...$cfRevenue); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Cash Flow — Revenue (6 Months)</div>
            <table class="trend-chart">
                <tr>
                    @foreach($cfLabels as $i => $label)
                        <td style="text-align:center; vertical-align:bottom; padding:0 4px; border:none;">
                            <div style="font-size:8px; color:#374151; font-weight:bold; margin-bottom:2px;">
                                @if($cfRevenue[$i] > 0) ${{ number_format($cfRevenue[$i], 0) }} @endif
                            </div>
                            <div style="height:60px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:#22c55e; border-radius:3px 3px 0 0; height:{{ $cfRevenue[$i] > 0 ? max(4, ($cfRevenue[$i]/$cfMax)*100) : 0 }}%;"></div>
                            </div>
                            <div style="font-size:8.5px; color:#9ca3af; margin-top:4px; text-align:center;">{{ $label }}</div>
                        </td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Instructor</th><th class="amount">Amount</th><th>Requested</th></tr>
    </thead>
    <tbody>
        @forelse($pendingPayouts as $row)
            <tr>
                <td>{{ $row->instructor?->name ?? '—' }}</td>
                <td class="amount">${{ number_format((float) $row->amount, 2) }}</td>
                <td>{{ $row->created_at?->format('M d, Y') ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;color:#9ca3af;">No pending payouts</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
