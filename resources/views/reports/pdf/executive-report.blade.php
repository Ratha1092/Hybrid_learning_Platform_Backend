<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Total Students</div><div class="kpi-value">{{ number_format($data['totalStudents']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Total Instructors</div><div class="kpi-value">{{ number_format($data['totalInstructors']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Total Courses</div><div class="kpi-value">{{ number_format($data['totalCourses']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Total Orders</div><div class="kpi-value">{{ number_format($data['totalOrders']) }}</div></td>
</tr></table>

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Total Revenue</div><div class="kpi-value">${{ number_format((float) $data['totalRevenue'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Completed Payments</div><div class="kpi-value">{{ number_format($data['completedPayments']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Pending Payments</div><div class="kpi-value">{{ number_format($data['pendingPayments']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Outstanding Balance</div><div class="kpi-value">${{ number_format((float) $data['totalInstructorBalance'], 2) }}</div></td>
</tr></table>

<table class="items">
    <thead>
        <tr><th>Month</th><th class="amount">Revenue</th><th class="amount">Orders</th></tr>
    </thead>
    <tbody>
        @foreach($data['revenueTrend'] as $point)
            <tr>
                <td>{{ $point['month'] }}</td>
                <td class="amount">${{ number_format((float) $point['revenue'], 2) }}</td>
                <td class="amount">{{ $point['orders'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Executive Dashboard — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
