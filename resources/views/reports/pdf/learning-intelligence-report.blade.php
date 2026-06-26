<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Learning Hours</div><div class="kpi-value">{{ number_format($kpis['totalLearningHours'], 1) }}h</div></td>
    <td class="kpi-cell"><div class="kpi-label">Active Learners</div><div class="kpi-value">{{ number_format($kpis['activeLearners']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Completion Rate</div><div class="kpi-value">{{ $kpis['completionRate'] }}%</div></td>
    <td class="kpi-cell"><div class="kpi-label">Dropout Rate</div><div class="kpi-value">{{ $kpis['dropoutRate'] }}%</div></td>
</tr></table>

@php
    $topCourses   = $courses->take(6);
    $watchMax     = max(1, $topCourses->max('totalWatchHours'));
    $completionMax = max(1, $topCourses->max('completionRate'));
@endphp
@if($topCourses->count() > 0)
<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Watch Hours by Course</div>
            <table class="bar-chart">
                @foreach($topCourses as $c)
                    <tr>
                        <td class="bar-label">{{ \Illuminate\Support\Str::limit($c['title'], 18) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $c['totalWatchHours'] > 0 ? max(3, ($c['totalWatchHours']/$watchMax)*100) : 0 }}%;background:#D7A441;"></div></div></td>
                        <td class="bar-value">{{ $c['totalWatchHours'] }}h</td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Completion Rate by Course</div>
            <table class="bar-chart">
                @foreach($topCourses as $c)
                    <tr>
                        <td class="bar-label">{{ \Illuminate\Support\Str::limit($c['title'], 18) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $c['completionRate'] > 0 ? max(3, ($c['completionRate']/$completionMax)*100) : 0 }}%;background:#34d399;"></div></div></td>
                        <td class="bar-value">{{ $c['completionRate'] }}%</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>
@endif

<table class="items">
    <thead>
        <tr><th>Course</th><th class="amount">Enrollments</th><th class="amount">Completion %</th><th class="amount">Watch Hours</th></tr>
    </thead>
    <tbody>
        @foreach($courses as $course)
            <tr>
                <td>{{ \Illuminate\Support\Str::limit($course['title'], 40) }}</td>
                <td class="amount">{{ number_format($course['enrollments']) }}</td>
                <td class="amount">{{ $course['completionRate'] }}%</td>
                <td class="amount">{{ $course['totalWatchHours'] }}h</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Learning Intelligence Report — Generated {{ now()->format('F j, Y') }} — Avg. Quiz Score: coming soon (requires a Quiz/Assessment model)</div>
</body>
</html>
