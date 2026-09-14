<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">New Users</div><div class="kpi-value">{{ number_format($kpis['totalNew']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Active</div><div class="kpi-value">{{ number_format($kpis['activeCount']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Suspended</div><div class="kpi-value">{{ number_format($kpis['suspendedCount']) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">New Enrollments</div><div class="kpi-value">{{ number_format($kpis['newEnrollments']) }}</div></td>
</tr></table>

@php
    $roleMax   = max(1, max(array_filter($roleBreakdown, fn($c) => $c > 0) ?: [1]));
    $stMax     = max(1, max($statusBreakdown));
    $stColors  = ['active'=>'#34d399','suspended'=>'#f87171'];
    $roleColors = ['student'=>'#60a5fa','instructor'=>'#D7A441','super-admin'=>'#f97316','finance'=>'#34d399'];
@endphp
<table class="charts-row">
    <tr>
        <td class="chart-card">
            <div class="chart-title">Users by Role</div>
            <table class="bar-chart">
                @foreach($roleBreakdown as $r => $count)
                    @if($count > 0)
                    <tr>
                        <td class="bar-label">{{ ucwords(str_replace('-', ' ', $r)) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ max(3, ($count/$roleMax)*100) }}%;background:{{ $roleColors[$r] ?? '#94a3b8' }};"></div></div></td>
                        <td class="bar-value">{{ number_format($count) }}</td>
                    </tr>
                    @endif
                @endforeach
            </table>
        </td>
        <td class="chart-card">
            <div class="chart-title">Users by Status</div>
            <table class="bar-chart">
                @foreach($statusBreakdown as $st => $count)
                    <tr>
                        <td class="bar-label">{{ ucfirst($st) }}</td>
                        <td><div class="bar-bg"><div class="bar-fill" style="width:{{ $count > 0 ? max(3, ($count/$stMax)*100) : 0 }}%;background:{{ $stColors[$st] ?? '#94a3b8' }};"></div></div></td>
                        <td class="bar-value">{{ number_format($count) }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Enrollments</th><th>Orders</th><th>Joined</th></tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                <td>{{ ucfirst($user->status ?? 'active') }}</td>
                <td>{{ $user->enrollments_count }}</td>
                <td>{{ $user->orders_count }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — User Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>
