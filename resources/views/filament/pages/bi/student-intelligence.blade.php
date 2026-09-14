@include('filament.pages.bi._bi-shared')

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Student Intelligence 🎓</h1>
            <p>Learning activity, completion rates, retention, and student growth.</p>
        </div>
        @include('filament.pages.bi._bi-actions', ['biKey' => 'stu'])
    </div>

    <div class="bi-kpi-grid">
        <div class="bi-kpi"><div class="bi-kpi-label">Total Students</div><div class="bi-kpi-value">{{ number_format($kpis['totalStudents']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">New Students</div><div class="bi-kpi-value">{{ number_format($kpis['newStudents']) }}</div><div class="bi-kpi-sub">Joined this period</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Active Learners</div><div class="bi-kpi-value">{{ number_format($kpis['activeLearners']) }}</div><div class="bi-kpi-sub">Watched lesson in period</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Completion Rate</div><div class="bi-kpi-value">{{ $kpis['completionRate'] }}%</div><div class="bi-kpi-sub">All-time average</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Learning Hours</div><div class="bi-kpi-value">{{ $kpis['avgLearningHours'] }}h</div><div class="bi-kpi-sub">Per active learner</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Dropout Rate</div><div class="bi-kpi-value @if($kpis['dropoutRate'] > 20) style='color:var(--err)' @endif">{{ $kpis['dropoutRate'] }}%</div><div class="bi-kpi-sub">No activity 30+ days</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Returning Students</div><div class="bi-kpi-value">{{ number_format($kpis['returningStudents']) }}</div><div class="bi-kpi-sub">Re-engaged this period</div></div>
    </div>

    <div class="bi-charts-3">
        <div class="bi-card">
            <h3>Student Growth — 12 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-stu-growth"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Learning Hours — 6 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-stu-hours"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Active vs Dormant</h3>
            <div class="bi-chart-wrap"><canvas id="bi-stu-active-donut"></canvas></div>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Most Active Students</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Student</th><th>Watch Hours</th><th>Enrolled</th><th>Completed</th></tr></thead>
                <tbody>
                    @forelse($mostActive as $i => $row)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['hours'] }}h</td>
                        <td style="color:var(--t2)">{{ $row['enrolled'] }}</td>
                        <td style="color:var(--t2)">{{ $row['completed'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--t2);padding:24px">No activity data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Highest Completion Rate</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Student</th><th>Completed</th><th>Enrolled</th><th>Rate</th></tr></thead>
                <tbody>
                    @forelse($highestCompletion as $i => $row)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $row['name'] }}</td>
                        <td style="color:var(--ok)">{{ $row['completed'] }}</td>
                        <td style="color:var(--t2)">{{ $row['enrolled'] }}</td>
                        <td><span class="bi-tag bi-tag-ok">{{ $row['rate'] }}%</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--t2);padding:20px">No completions yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
window.__biRun(function() {
    var tickColor = '#64748b';
    var gridColor = 'rgba(148,163,184,0.12)';
    var ff = "'DM Sans', ui-sans-serif, system-ui, sans-serif";
    var baseScales = {
        x: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor }, beginAtZero: true }
    };

    // Growth area
    (function() {
        var el = document.getElementById('bi-stu-growth');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: @json($growthLabels),
                datasets: [{ label: 'New Students', data: @json($growthValues), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.10)', fill: true, tension: 0.4, pointRadius: 2, borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();

    // Learning hours bar
    (function() {
        var el = document.getElementById('bi-stu-hours');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($hoursLabels),
                datasets: [{ label: 'Hours', data: @json($hoursValues), backgroundColor: 'rgba(37,99,235,0.7)', borderColor: '#2563eb', borderWidth: 1, borderRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();

    // Active vs dormant donut
    (function() {
        var el = document.getElementById('bi-stu-active-donut');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Dormant'],
                datasets: [{ data: [@json($kpis['activeStudents']), @json($kpis['dormantStudents'])], backgroundColor: ['#2563eb', '#334155'], borderWidth: 1, borderColor: 'rgba(0,0,0,0.08)' }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { position: 'right', labels: { color: tickColor, font: { family: ff, size: 11 }, boxWidth: 10 } } } }
        });
    })();
});
</script>
