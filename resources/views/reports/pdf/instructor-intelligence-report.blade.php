<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Active Instructors</div><div class="kpi-value">{{ number_format($kpis['totalInstructors']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Total Earnings</div><div class="kpi-value">${{ number_format($kpis['totalEarnings'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Avg. Earnings</div><div class="kpi-value">${{ number_format($kpis['averageEarnings'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Avg. Rating</div><div class="kpi-value">{{ $kpis['averageRating'] }}★</div></td>
</tr></table>

<table class="items">
    <thead>
        <tr><th>Instructor</th><th class="amount">Courses</th><th class="amount">Students</th><th class="amount">Earnings</th><th class="amount">Rating</th></tr>
    </thead>
    <tbody>
        @foreach($instructors as $instructor)
            <tr>
                <td>{{ $instructor['name'] }}</td>
                <td class="amount">{{ $instructor['courseCount'] }}</td>
                <td class="amount">{{ number_format($instructor['studentCount']) }}</td>
                <td class="amount">${{ number_format($instructor['earnings'], 2) }}</td>
                <td class="amount">{{ $instructor['avgRating'] }}★</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Instructor Intelligence Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
