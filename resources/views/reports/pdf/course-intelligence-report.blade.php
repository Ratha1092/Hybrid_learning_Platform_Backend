<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Total Views</div><div class="kpi-value">{{ number_format($kpis['totalViews']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Enrollments</div><div class="kpi-value">{{ number_format($kpis['totalEnrollments']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Conversion</div><div class="kpi-value">{{ $kpis['conversionRate'] }}%</div></td>
    <td class="kpi-cell"><div class="kpi-label">Avg. Rating</div><div class="kpi-value">{{ $kpis['avgRating'] }}★</div></td>
</tr></table>

@php
    $topCourses = $courses->take(6);
    $enrolMax = max(1, $topCourses->max('enrollments'));
    $viewMax  = max(1, $topCourses->max('views'));
@endphp
@if($topCourses->count() > 0)
<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Top Courses by Enrollments</div>
            <table class="bar-chart">
                @foreach($topCourses as $c)
                    <tr>
                        <td class="bar-label">{{ \Illuminate\Support\Str::limit($c['title'], 18) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $c['enrollments'] > 0 ? max(3, ($c['enrollments']/$enrolMax)*100) : 0 }}%;background:#D7A441;"></div></div></td>
                        <td class="bar-value">{{ number_format($c['enrollments']) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Top Courses by Views</div>
            <table class="bar-chart">
                @foreach($topCourses as $c)
                    <tr>
                        <td class="bar-label">{{ \Illuminate\Support\Str::limit($c['title'], 18) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $c['views'] > 0 ? max(3, ($c['views']/$viewMax)*100) : 0 }}%;background:#60a5fa;"></div></div></td>
                        <td class="bar-value">{{ number_format($c['views']) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>
@endif

<table class="items">
    <thead>
        <tr><th>Course</th><th>Instructor</th><th class="amount">Views</th><th class="amount">Enrollments</th><th class="amount">Completion %</th></tr>
    </thead>
    <tbody>
        @foreach($courses as $course)
            <tr>
                <td>{{ \Illuminate\Support\Str::limit($course['title'], 40) }}</td>
                <td>{{ $course['instructor'] }}</td>
                <td class="amount">{{ number_format($course['views']) }}</td>
                <td class="amount">{{ number_format($course['enrollments']) }}</td>
                <td class="amount">{{ $course['completionRate'] }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Course Intelligence Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
