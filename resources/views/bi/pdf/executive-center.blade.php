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
        <td class="kpi-cell">
            <div class="kpi-label">Gross Revenue</div>
            <div class="kpi-value">${{ number_format($kpis['grossRevenue'], 2) }}</div>
            @if($kpis['revenueGrowth'] != 0)
                <div style="font-size:9px; font-weight:bold; margin-top:3px; color:{{ $kpis['revenueGrowth'] >= 0 ? '#16a34a' : '#dc2626' }};">
                    {{ $kpis['revenueGrowth'] >= 0 ? '+' : '-' }}{{ abs($kpis['revenueGrowth']) }}% vs prev period
                </div>
            @endif
        </td>
        <td class="kpi-cell"><div class="kpi-label">Platform Commission</div><div class="kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Instructor Earnings</div><div class="kpi-value">${{ number_format($kpis['instructorEarnings'], 2) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Orders</div><div class="kpi-value">{{ number_format($kpis['orderCount']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">New Enrollments</div><div class="kpi-value">{{ number_format($kpis['newEnrollments']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Active Students</div><div class="kpi-value">{{ number_format($kpis['activeStudents']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Active Instructors</div><div class="kpi-value">{{ number_format($kpis['activeInstructors']) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Published Courses</div><div class="kpi-value">{{ number_format($kpis['publishedCourses']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Completion Rate</div><div class="kpi-value">{{ $kpis['completionRate'] }}%</div></td>
        <td class="kpi-cell"><div class="kpi-label">Health Score</div><div class="kpi-value">{{ $kpis['healthScore'] }}/100</div></td>
    </tr>
</table>

@php $revMax = max(1, ...$revenueTrendData['revenue']); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Revenue Trend — Last 12 Months</div>
            <table class="trend-chart">
                <tr>
                    @foreach($revenueTrendData['labels'] as $i => $label)
                        <td style="text-align:center; vertical-align:bottom; padding:0 4px; border:none;">
                            <div style="font-size:8px; color:#374151; font-weight:bold; margin-bottom:2px;">
                                @if($revenueTrendData['revenue'][$i] > 0) ${{ number_format($revenueTrendData['revenue'][$i], 0) }} @endif
                            </div>
                            <div style="height:60px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:#D7A441; border-radius:3px 3px 0 0; height:{{ $revenueTrendData['revenue'][$i] > 0 ? max(4, ($revenueTrendData['revenue'][$i]/$revMax)*100) : 0 }}%;"></div>
                            </div>
                            <div style="font-size:8.5px; color:#9ca3af; margin-top:4px; text-align:center;">{{ $label }}</div>
                        </td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>
</table>

@php
    $growthMax = max(1, ...$growthData['students'], ...$growthData['instructors']);
    $topCourseMax = max(1, ...$topCoursesChartValues);
@endphp
<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Student &amp; Instructor Growth — 6 Months</div>
            <div style="font-size:8.5px; color:#6b7280; margin-bottom:8px;">
                <span style="color:#D7A441; font-weight:bold;">&bull;</span> Students &nbsp;&nbsp;
                <span style="color:#374151; font-weight:bold;">&bull;</span> Instructors
            </div>
            <table class="trend-chart">
                <tr>
                    @foreach($growthData['labels'] as $i => $label)
                        <td style="text-align:center; vertical-align:bottom; padding:0 3px; border:none;">
                            <div style="height:50px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:8%; width:32%; background:#D7A441; border-radius:2px 2px 0 0; height:{{ $growthData['students'][$i] > 0 ? max(4, ($growthData['students'][$i]/$growthMax)*100) : 0 }}%;"></div>
                                <div style="position:absolute; bottom:0; left:52%; width:32%; background:#374151; border-radius:2px 2px 0 0; height:{{ $growthData['instructors'][$i] > 0 ? max(4, ($growthData['instructors'][$i]/$growthMax)*100) : 0 }}%;"></div>
                            </div>
                            <div style="font-size:8px; color:#9ca3af; margin-top:4px; text-align:center;">{{ $label }}</div>
                        </td>
                    @endforeach
                </tr>
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Top 5 Courses by All-Time Revenue</div>
            <table class="bar-chart">
                @forelse($topCoursesChartLabels as $i => $label)
                    <tr>
                        <td class="bar-label">{{ $label }}</td>
                        <td style="width:100%;">
                            <div class="bar-bg"><div class="bar-fill" style="background:#D7A441; width:{{ max(4, ($topCoursesChartValues[$i]/$topCourseMax)*100) }}%;"></div></div>
                        </td>
                        <td class="bar-value">${{ number_format($topCoursesChartValues[$i], 0) }}</td>
                    </tr>
                @empty
                    <tr><td style="text-align:center;color:#9ca3af;">No sales data yet</td></tr>
                @endforelse
            </table>
        </td>
    </tr>
</table>

<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Top Instructors (Period)</div>
            <table class="items">
                <thead>
                    <tr><th>Instructor</th><th class="amount">Earnings</th><th class="amount">Sales</th></tr>
                </thead>
                <tbody>
                    @forelse($topInstructors as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="amount">${{ number_format($row['earnings'], 2) }}</td>
                            <td class="amount">{{ $row['sales'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#9ca3af;">No earnings data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Recent Orders</div>
            <table class="items">
                <thead>
                    <tr><th>Customer</th><th class="amount">Amount</th><th class="amount">Date</th></tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->customer_name ?? $order->user?->name ?? '—' }}</td>
                            <td class="amount">${{ number_format((float) $order->final_amount, 2) }}</td>
                            <td class="amount">{{ $order->paid_at?->format('M d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#9ca3af;">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Course</th><th>Instructor</th><th class="amount">Total Revenue</th><th class="amount">Total Sales</th></tr>
    </thead>
    <tbody>
        @forelse($topCourses as $sale)
            <tr>
                <td>{{ $sale->course?->title ?? '—' }}</td>
                <td>{{ $sale->course?->instructor?->name ?? '—' }}</td>
                <td class="amount">${{ number_format((float) $sale->total_revenue, 2) }}</td>
                <td class="amount">{{ number_format((int) $sale->total_sales) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#9ca3af;">No sales data yet</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
