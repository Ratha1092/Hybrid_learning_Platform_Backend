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
        <td class="kpi-cell"><div class="kpi-label">Net Revenue</div><div class="kpi-value">${{ number_format($kpis['netRevenue'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Platform Revenue</div><div class="kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Instructor Earnings</div><div class="kpi-value">${{ number_format($kpis['instructorEarnings'], 2) }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Orders</div><div class="kpi-value">{{ number_format($kpis['orderCount']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Active Students</div><div class="kpi-value">{{ number_format($kpis['activeStudents']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Active Instructors</div><div class="kpi-value">{{ number_format($kpis['activeInstructors']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Health Score</div><div class="kpi-value">{{ $kpis['healthScore'] }}</div></td>
    </tr>
</table>

@php $revMax = max(1, ...$revenueTrendData['revenue']); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Revenue Trend</div>
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

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
