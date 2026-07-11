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
        <td class="kpi-cell"><div class="kpi-label">Published</div><div class="kpi-value">{{ number_format($kpis['published']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Draft</div><div class="kpi-value">{{ number_format($kpis['draft']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Pending Review</div><div class="kpi-value">{{ number_format($kpis['pending']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Archived</div><div class="kpi-value">{{ number_format($kpis['archived']) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Period Views</div><div class="kpi-value">{{ number_format($kpis['periodViews']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Period Enrollments</div><div class="kpi-value">{{ number_format($kpis['periodEnrollments']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Avg Rating</div><div class="kpi-value">{{ $kpis['avgRating'] }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Completion Rate</div><div class="kpi-value">{{ $kpis['completionRate'] }}%</div></td>
    </tr>
</table>

@php $eMax = max(1, collect($enrollmentTrend)->max('count')); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Enrollment Trend (12 Months)</div>
            <table class="trend-chart">
                <tr>
                    @foreach($enrollmentTrend as $point)
                        <td style="text-align:center; vertical-align:bottom; padding:0 4px; border:none;">
                            <div style="font-size:8px; color:#374151; font-weight:bold; margin-bottom:2px;">
                                @if($point['count'] > 0) {{ $point['count'] }} @endif
                            </div>
                            <div style="height:60px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:#D7A441; border-radius:3px 3px 0 0; height:{{ $point['count'] > 0 ? max(4, ($point['count']/$eMax)*100) : 0 }}%;"></div>
                            </div>
                            <div style="font-size:8.5px; color:#9ca3af; margin-top:4px; text-align:center;">{{ $point['label'] }}</div>
                        </td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Course</th><th class="amount">Revenue</th><th class="amount">Sales</th></tr>
    </thead>
    <tbody>
        @forelse($topRevenueCourses as $row)
            <tr>
                <td>{{ $row->course?->title ?? '—' }}</td>
                <td class="amount">${{ number_format((float) $row->total_revenue, 2) }}</td>
                <td class="amount">{{ $row->total_sales }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;color:#9ca3af;">No sales data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
