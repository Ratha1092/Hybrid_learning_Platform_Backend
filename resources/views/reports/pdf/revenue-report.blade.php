<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Total Revenue</div><div class="kpi-value">${{ number_format($kpis['totalRevenue'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Platform Revenue</div><div class="kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Instructor Payable</div><div class="kpi-value">${{ number_format($kpis['instructorPayable'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Orders</div><div class="kpi-value">{{ number_format($kpis['orderCount']) }}</div></td>
</tr></table>

@php
    $trendMax = max(1, $revenueTrend->max('revenue'));
@endphp
<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Revenue Trend (6 Months)</div>
            <table class="trend-chart">
                <tr>
                    @foreach($revenueTrend as $point)
                        <td style="text-align:center; vertical-align:bottom; padding:0 4px; border:none;">
                            <div style="font-size:8px; color:#374151; font-weight:bold; margin-bottom:2px;">
                                @if($point['revenue'] > 0) ${{ number_format($point['revenue'], 0) }} @endif
                            </div>
                            <div style="height:60px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:#D7A441; border-radius:3px 3px 0 0; height:{{ $point['revenue'] > 0 ? max(4, ($point['revenue']/$trendMax)*100) : 0 }}%;"></div>
                            </div>
                            <div style="font-size:8.5px; color:#9ca3af; margin-top:4px; text-align:center;">{{ $point['month'] }}</div>
                        </td>
                    @endforeach
                </tr>
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Top Coupons</div>
            @forelse($topCoupons as $coupon)
                <div class="coupon-row">
                    <span class="coupon-code">{{ $coupon->code }}</span>
                    <span class="coupon-uses">{{ $coupon->used_count }} uses</span>
                </div>
            @empty
                <div style="font-size:10px; color:#9ca3af;">No coupon usage yet.</div>
            @endforelse
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Order #</th><th>Date</th><th>Customer</th><th>Discount</th><th class="amount">Final Amount</th></tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $order->customer_name ?? $order->user?->name ?? '—' }}</td>
                <td>{{ $order->discount_amount > 0 ? '-$' . number_format((float) $order->discount_amount, 2) : '—' }}</td>
                <td class="amount">${{ number_format((float) $order->final_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Revenue Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
