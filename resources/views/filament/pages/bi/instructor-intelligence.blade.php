@include('filament.pages.bi._bi-shared')
<div>
<div class="bi">
    <div class="bi-header">
        <div>
            <h1>Instructor Intelligence 👨‍🏫</h1>
            <p>Instructor performance, earnings, ratings, and growth.</p>
        </div>
        @include('filament.pages.bi._bi-actions', ['biKey' => 'instr'])
    </div>

    <div class="bi-kpi-grid cols-3">
        <div class="bi-kpi"><div class="bi-kpi-label">Total Instructors</div><div class="bi-kpi-value">{{ number_format($kpis['totalInstructors']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Period Earnings</div><div class="bi-kpi-value">${{ number_format($kpis['totalEarnings'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Earnings</div><div class="bi-kpi-value">${{ number_format($kpis['avgEarnings'],2) }}</div><div class="bi-kpi-sub">Per instructor</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Platform Avg Rating</div><div class="bi-kpi-value">⭐ {{ $kpis['avgRating'] }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">New Courses (Period)</div><div class="bi-kpi-value">{{ number_format($kpis['newCourses']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Total Pending Payout</div><div class="bi-kpi-value">${{ number_format($kpis['pendingPayout'],2) }}</div></div>
    </div>

    <div class="bi-charts-3">
        <div class="bi-card">
            <h3>Top Instructors by Earnings</h3>
            <div class="bi-chart-wrap"><canvas id="bi-instr-earnings"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Instructor Growth — 6 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-instr-growth"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Rating Distribution</h3>
            <div class="bi-chart-wrap"><canvas id="bi-instr-ratings"></canvas></div>
        </div>
    </div>

    <div class="bi-table-card">
        <h3>Top Instructors by Period Revenue</h3>
        <table class="bi-table">
            <thead><tr><th>#</th><th>Instructor</th><th>Earnings</th><th>Sales</th><th>Courses</th><th>Students</th><th>Rating</th><th>Wallet Balance</th></tr></thead>
            <tbody>
                @forelse($topInstructors as $i => $row)
                <tr>
                    <td><span class="bi-rank">{{ $i+1 }}</span></td>
                    <td>{{ $row['name'] }}</td>
                    <td>${{ number_format($row['earnings'],2) }}</td>
                    <td>{{ $row['sales'] }}</td>
                    <td style="color:var(--t2)">{{ $row['courses'] }}</td>
                    <td style="color:var(--t2)">{{ number_format($row['students']) }}</td>
                    <td>{{ $row['avg_rating'] > 0 ? '⭐ '.$row['avg_rating'] : '—' }}</td>
                    <td style="color:var(--t2)">${{ number_format($row['wallet_balance'],2) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--t2);padding:24px">No earnings data for this period</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bi-table-card">
        <h3>Top Rated Instructors</h3>
        <table class="bi-table">
            <thead><tr><th>#</th><th>Instructor</th><th>Avg Rating</th><th>Reviews</th></tr></thead>
            <tbody>
                @forelse($topRated as $i => $row)
                <tr>
                    <td><span class="bi-rank">{{ $i+1 }}</span></td>
                    <td>{{ $row['name'] }}</td>
                    <td>⭐ {{ $row['avg_rating'] }}</td>
                    <td style="color:var(--t2)">{{ $row['reviews'] }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No ratings yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</div>

<script>
window.__biRun(function() {
    var tickColor = '#64748b';
    var gridColor = 'rgba(148,163,184,0.12)';
    var ff = "'DM Sans', ui-sans-serif, system-ui, sans-serif";
    var isDark = document.documentElement.classList.contains('cl-dark') || document.documentElement.classList.contains('dark');
    var baseScales = {
        x: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor }, beginAtZero: true }
    };

    // Earnings horizontal bar
    (function() {
        var el = document.getElementById('bi-instr-earnings');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($earningsChartLabels),
                datasets: [{ label: 'Earnings ($)', data: @json($earningsChartValues), backgroundColor: 'rgba(215,164,65,0.7)', borderColor: '#D7A441', borderWidth: 1, borderRadius: 3 }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                plugins: { legend: { display: false } },
                scales: {
                    x: baseScales.x,
                    y: { ticks: { color: tickColor, font: { family: ff, size: 9 } }, grid: { display: false } }
                }
            }
        });
    })();

    // Growth line
    (function() {
        var el = document.getElementById('bi-instr-growth');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: @json($growthLabels),
                datasets: [{ label: 'New Instructors', data: @json($growthValues), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();

    // Rating distribution bar
    (function() {
        var el = document.getElementById('bi-instr-ratings');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($ratingDistLabels),
                datasets: [{ label: 'Reviews', data: @json($ratingDistValues), backgroundColor: ['#ef4444','#f97316','#D7A441','#22c55e','#2563eb'], borderWidth: 0, borderRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();
});
</script>
