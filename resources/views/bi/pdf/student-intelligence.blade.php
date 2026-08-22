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
        <td class="kpi-cell"><div class="kpi-label">Total Students</div><div class="kpi-value">{{ number_format($kpis['totalStudents']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">New Students</div><div class="kpi-value">{{ number_format($kpis['newStudents']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Active Learners</div><div class="kpi-value">{{ number_format($kpis['activeLearners']) }}</div></td>
        <td class="kpi-cell"><div class="kpi-label">Completion Rate</div><div class="kpi-value">{{ $kpis['completionRate'] }}%</div></td>
    </tr>
    <tr>
        <td class="kpi-cell"><div class="kpi-label">Avg Learning Hours</div><div class="kpi-value">{{ $kpis['avgLearningHours'] }}h</div></td>
        <td class="kpi-cell"><div class="kpi-label">Dropout Rate</div><div class="kpi-value">{{ $kpis['dropoutRate'] }}%</div></td>
        <td class="kpi-cell"><div class="kpi-label">Returning Students</div><div class="kpi-value">{{ number_format($kpis['returningStudents']) }}</div></td>
    </tr>
</table>

@php $growthMax = empty($growthValues) ? 1 : max(1, ...$growthValues); @endphp
<table class="charts-row">
    <tr>
        <td class="chart-card full">
            <div class="chart-title">Student Growth (12 Months)</div>
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
        <tr><th>Student</th><th class="amount">Watch Hours</th><th class="amount">Enrolled</th><th class="amount">Completed</th></tr>
    </thead>
    <tbody>
        @forelse($mostActive as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td class="amount">{{ $row['hours'] }}h</td>
                <td class="amount">{{ $row['enrolled'] }}</td>
                <td class="amount">{{ $row['completed'] }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#9ca3af;">No activity data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">{{ $siteName }} — {{ $title }} — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
