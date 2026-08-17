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
        <td class="kpi-cell"><div class="kpi-label">Total Views</div><div class="kpi-value">{{ number_format($kpis['totalViews']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Unique Viewers</div><div class="kpi-value">{{ number_format($kpis['uniqueViewers']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">GMV</div><div class="kpi-value">${{ number_format($kpis['gmv'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Conversion Rate</div><div class="kpi-value">{{ $kpis['conversionRate'] }}%</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Total Orders</div><div class="kpi-value">{{ number_format($kpis['totalOrders']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Avg Price</div><div class="kpi-value">${{ number_format($kpis['avgPrice'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Coupon Usage</div><div class="kpi-value">{{ $kpis['couponUsageRate'] }}%</div></td>
    </tr>
</table>

@php $funnelMax = max(1, collect($funnel)->max('value')); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Conversion Funnel</div>
            <table class="bar-chart">
                @foreach($funnel as $step)
                    <tr>
                        <td class="bar-label">{{ $step['label'] }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $step['value'] > 0 ? max(3, ($step['value']/$funnelMax)*100) : 0 }}%;background:#D7A441;"></div></div></td>
                        <td class="bar-value">{{ number_format($step['value']) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Course</th><th class="amount">Wishlisted</th><th class="amount">Price</th></tr>
    </thead>
    <tbody>
        @forelse($mostWishlisted as $row)
            <tr>
                <td>{{ $row->course?->title ?? '—' }}</td>
                <td class="amount">{{ $row->wish_count }}</td>
                <td class="amount">${{ number_format((float) ($row->course?->price ?? 0), 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;color:#9ca3af;">No wishlist data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
