@include('filament.pages.bi._bi-shared')

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Course Intelligence 📚</h1>
            <p>Course performance, ratings, enrollments, and completion analysis.</p>
        </div>
        <div class="bi-actions">
            <div class="rp-drp-wrap" wire:key="bi-drp-course"
                x-data="rpDate({preset:'{{ $activePreset }}',from:'{{ $activeDateFrom }}',to:'{{ $activeDateTo }}',extras:{}})"
                data-rp-preset="{{ $activePreset }}" data-rp-from="{{ $activeDateFrom }}" data-rp-to="{{ $activeDateTo }}"
                @click.outside="open=false">
                <div class="rp-drp-pill" @click="open=!open">
                    <span class="rp-drp-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    <span class="rp-drp-from" x-text="pFrom"></span><span class="rp-drp-sep">|</span><span class="rp-drp-to" x-text="pTo"></span>
                    <span class="rp-drp-chev" :class="{open}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></span>
                </div>
                <div class="rp-drp-panel" :class="{open}">
                    <div class="rp-drp-presets">
                        @foreach(\App\Support\Concerns\HasDateRangePresets::dateRangePresetOptions() as $key => $label)
                            @if($key !== 'custom')<button type="button" class="rp-drp-pre" :class="{active:preset==='{{ $key }}'}" @click="pick('{{ $key }}')">{{ $label }}</button>@endif
                        @endforeach
                    </div>
                    <hr class="rp-drp-hr">
                    <div class="rp-drp-cus-lbl">Custom Range</div>
                    <div class="rp-drp-cus-row">
                        <input type="date" class="rp-drp-dt" x-model="from" @change="preset='custom'">
                        <span class="rp-drp-dt-sep">—</span>
                        <input type="date" class="rp-drp-dt" x-model="to" @change="preset='custom'">
                    </div>
                    <div class="rp-drp-btns">
                        <button type="button" class="rp-drp-apply" @click="apply()">Apply</button>
                        <button type="button" class="rp-drp-reset" @click="reset()">Reset</button>
                    </div>
                </div>
            </div>
        </div>
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
                datasets: [{ data: Object.values(d), backgroundColor: ['#22c55e','#64748b','#D7A441','#ef4444'], borderWidth: 1, borderColor: 'rgba(0,0,0,0.08)' }]
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
                datasets: [{ label: 'Avg Rating', data: d.map(function(r){ return r.avg; }), borderColor: '#D7A441', backgroundColor: 'rgba(215,164,65,0.08)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 2 }]
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
