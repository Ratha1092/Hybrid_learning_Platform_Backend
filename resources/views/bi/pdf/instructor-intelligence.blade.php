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
        <td class="kpi-cell"><div class="kpi-label">Total Instructors</div><div class="kpi-value">{{ number_format($kpis['totalInstructors']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Total Earnings</div><div class="kpi-value">${{ number_format($kpis['totalEarnings'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Avg Earnings</div><div class="kpi-value">${{ number_format($kpis['avgEarnings'], 2) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Avg Rating</div><div class="kpi-value">{{ $kpis['avgRating'] }}</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">New Courses</div><div class="kpi-value">{{ number_format($kpis['newCourses']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Pending Payout</div><div class="kpi-value">${{ number_format($kpis['pendingPayout'], 2) }}</div></td>
    </tr>
</table>

@php $growthMax = empty($growthValues) ? 1 : max(1, ...$growthValues); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Instructor Growth (6 Months)</div>
            <table class="trend-chart">
                <tr>
                    @foreach($growthLabels as $i => $label)
                        <td style="text-align:center; vertical-align:bottom; padding:0 4px; border:none;">
                            <div style="font-size:8px; color:#374151; font-weight:bold; margin-bottom:2px;">
                                @if($growthValues[$i] > 0) {{ $growthValues[$i] }} @endif
                            </div>
                            <div style="height:60px; display:block; vertical-align:bottom; position:relative;">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:#D7A441; border-radius:3px 3px 0 0; height:{{ $growthValues[$i] > 0 ? max(4, ($growthValues[$i]/$growthMax)*100) : 0 }}%;"></div>
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
        <tr><th>Instructor</th><th class="amount">Earnings</th><th class="amount">Sales</th><th class="amount">Courses</th><th class="amount">Students</th><th class="amount">Rating</th></tr>
    </thead>
    <tbody>
        @forelse($topInstructors as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td class="amount">${{ number_format($row['earnings'], 2) }}</td>
                <td class="amount">{{ $row['sales'] }}</td>
                <td class="amount">{{ $row['courses'] }}</td>
                <td class="amount">{{ $row['students'] }}</td>
                <td class="amount">{{ $row['avg_rating'] }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#9ca3af;">No instructor data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
