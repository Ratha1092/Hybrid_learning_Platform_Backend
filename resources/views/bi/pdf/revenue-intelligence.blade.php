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
        <td class="kpi-cell"><div class="kpi-label">Platform Revenue</div><div class="kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Instructor Revenue</div><div class="kpi-value">${{ number_format($kpis['instructorRevenue'], 2) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Orders</div><div class="kpi-value">{{ number_format($kpis['orderCount']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Avg Order Value</div><div class="kpi-value">${{ number_format($kpis['aov'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Revenue Growth</div><div class="kpi-value">{{ $kpis['revenueGrowth'] }}%</div></td>
    </tr>
</table>

@php $trendMax = max(1, ...$trendValues); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Revenue Trend</div>
            <table class="trend-chart">
                <tr>
                    @foreach($trendLabels as $i => $label)
                        <td style="text-align:center; vertical-align:bottom; padding:0 4px; border:none;">
                            <div style="font-size:8px; color:#374151; font-weight:bold; margin-bottom:2px;">
                                @if($trendValues[$i] > 0) ${{ number_format($trendValues[$i], 0) }} @endif
                            </div>
                            <div style="height:60px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:#D7A441; border-radius:3px 3px 0 0; height:{{ $trendValues[$i] > 0 ? max(4, ($trendValues[$i]/$trendMax)*100) : 0 }}%;"></div>
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
        <tr><th>Course</th><th class="amount">Revenue</th><th class="amount">Sales</th></tr>
    </thead>
    <tbody>
        @forelse($topCourses as $row)
            <tr>
                <td>{{ $row->course?->title ?? '—' }}</td>
                <td class="amount">${{ number_format((float) $row->revenue, 2) }}</td>
                <td class="amount">{{ $row->sales }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;color:#9ca3af;">No revenue data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
