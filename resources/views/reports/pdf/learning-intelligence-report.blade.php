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
