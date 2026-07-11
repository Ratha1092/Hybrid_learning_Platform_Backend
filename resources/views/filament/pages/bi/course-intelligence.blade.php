@include('filament.pages.bi._bi-shared')

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Course Intelligence 📚</h1>
            <p>Course performance, ratings, enrollments, and completion analysis.</p>
        </div>
        @include('filament.pages.bi._bi-actions', ['biKey' => 'course'])
    </div>

    <div class="bi-kpi-grid cols-3">
        <div class="bi-kpi"><div class="bi-kpi-label">Published</div><div class="bi-kpi-value">{{ number_format($kpis['published']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Draft</div><div class="bi-kpi-value">{{ number_format($kpis['draft']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Pending Review</div><div class="bi-kpi-value @if($kpis['pending'] > 0) style="color:var(--acc)" @endif">{{ number_format($kpis['pending']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Archived</div><div class="bi-kpi-value">{{ number_format($kpis['archived']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Period Views</div><div class="bi-kpi-value">{{ number_format($kpis['periodViews']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Period Enrollments</div><div class="bi-kpi-value">{{ number_format($kpis['periodEnrollments']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Period Wishlists</div><div class="bi-kpi-value">{{ number_format($kpis['periodWishlists']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Rating</div><div class="bi-kpi-value">⭐ {{ $kpis['avgRating'] }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Completion Rate</div><div class="bi-kpi-value">{{ $kpis['completionRate'] }}%</div><div class="bi-kpi-sub">All-time average</div></div>
    </div>

    <div class="bi-charts-3">
        <div class="bi-card">
            <h3>Enrollment Trend — 12 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-course-enroll-trend"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Course Status Breakdown</h3>
            <div class="bi-chart-wrap"><canvas id="bi-course-status"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Rating Trend — 6 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-course-rating-trend"></canvas></div>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Best Selling Courses</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Course</th><th>Revenue</th><th>Sales</th></tr></thead>
                <tbody>
                    @forelse($topRevenueCourses as $i => $sale)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $sale->course?->title ?? '—' }}</td>
                        <td>${{ number_format((float)$sale->total_revenue,2) }}</td>
                        <td>{{ number_format((int)$sale->total_sales) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No sales data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Highest Rated Courses</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Course</th><th>Rating</th><th>Reviews</th></tr></thead>
                <tbody>
                    @forelse($highestRated as $i => $row)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $row->course?->title ?? '—' }}</td>
                        <td>⭐ {{ number_format((float)$row->avg_rating,2) }}</td>
                        <td style="color:var(--t2)">{{ $row->review_count }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No reviews yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Most Viewed (Period)</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Course</th><th>Views</th></tr></thead>
                <tbody>
                    @forelse($mostViewed as $i => $row)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $row->course?->title ?? '—' }}</td>
                        <td>{{ number_format($row->views) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--t2);padding:20px">No views recorded</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Lowest Completion Rate</h3>
            <table class="bi-table">
                <thead><tr><th>Course</th><th>Completion</th><th>Students</th></tr></thead>
                <tbody>
                    @forelse($lowestCompletion as $row)
                    <tr>
                        <td>{{ $row['title'] }}</td>
                        <td><span class="bi-tag bi-tag-err">{{ $row['rate'] }}%</span></td>
                        <td style="color:var(--t2)">{{ $row['enrollments'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--t2);padding:20px">No data</td></tr>
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
    var isDark = document.documentElement.classList.contains('cl-dark') || document.documentElement.classList.contains('dark');
    var baseScales = {
        x: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor }, beginAtZero: true }
    };

    // Enrollment trend
    (function() {
        var el = document.getElementById('bi-course-enroll-trend');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var d = @json($enrollmentTrend);
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: d.map(function(r){ return r.label; }),
                datasets: [{ label: 'Enrollments', data: d.map(function(r){ return r.count; }), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 2, borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();

    // Status donut
    (function() {
        var el = document.getElementById('bi-course-status');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var d = @json($statusBreakdown);
        el._ch = new window.Chart(el, {
            type: 'doughnut',
            data: {
                labels: Object.keys(d),
                datasets: [{ data: Object.values(d), backgroundColor: ['#22c55e','#64748b','#2563eb','#ef4444'], borderWidth: 1, borderColor: 'rgba(0,0,0,0.08)' }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { position: 'right', labels: { color: tickColor, font: { family: ff, size: 10 }, boxWidth: 10 } } } }
        });
    })();

    // Rating trend
    (function() {
        var el = document.getElementById('bi-course-rating-trend');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var d = @json($ratingTrend);
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: d.map(function(r){ return r.label; }),
                datasets: [{ label: 'Avg Rating', data: d.map(function(r){ return r.avg; }), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 2 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                plugins: { legend: { display: false } },
                scales: { x: baseScales.x, y: Object.assign({}, baseScales.y, { min: 0, max: 5 }) }
            }
        });
    })();
});
</script>
