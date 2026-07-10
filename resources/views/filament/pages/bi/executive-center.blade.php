@include('filament.pages.bi._bi-shared')

<style>
.bi-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(215,164,65,.12);
    color: var(--acc);
    border: 1px solid rgba(215,164,65,.25);
}

.bi-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid var(--bd2);
    background: var(--p1);
    color: var(--t1);
}

.bi-btn:hover {
    background: var(--p2);
}

.bi-charts {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
}

@media (max-width: 900px) {
    .bi-charts {
        grid-template-columns: 1fr;
    }
}

.bi-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--p2);
    border: 1px solid var(--bd);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--acc);
    flex-shrink: 0;
}
</style>

<div>
<div class="bi">

    {{-- Header --}}
    <div class="bi-header">
        <div>
            <h1>Executive Center <span class="bi-badge">👑 CEO View</span></h1>
            <p>Platform health, growth, and revenue at a glance.</p>
        </div>
        <div class="bi-actions">
            <div class="rp-drp-wrap" wire:key="bi-drp-exec"
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
                            @if($key !== 'custom')
                            <button type="button" class="rp-drp-pre" :class="{active:preset==='{{ $key }}'}" @click="pick('{{ $key }}')">{{ $label }}</button>
                            @endif
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

    {{-- KPI Cards --}}
    <div class="bi-kpi-grid">
        <div class="bi-kpi">
            <div class="bi-kpi-label">Gross Revenue</div>
            <div class="bi-kpi-value">${{ number_format($kpis['grossRevenue'], 2) }}</div>
            @if($kpis['revenueGrowth'] != 0)
            <div class="bi-kpi-grow {{ $kpis['revenueGrowth'] >= 0 ? 'up' : 'dn' }}">
                {{ $kpis['revenueGrowth'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['revenueGrowth']) }}% vs prev period
            </div>
            @endif
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Net Revenue</div>
            <div class="bi-kpi-value">${{ number_format($kpis['netRevenue'], 2) }}</div>
            <div class="bi-kpi-sub">After refunds</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Platform Commission</div>
            <div class="bi-kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div>
            <div class="bi-kpi-sub">Platform earnings</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Instructor Earnings</div>
            <div class="bi-kpi-value">${{ number_format($kpis['instructorEarnings'], 2) }}</div>
            <div class="bi-kpi-sub">Paid to instructors</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Orders</div>
            <div class="bi-kpi-value">{{ number_format($kpis['orderCount']) }}</div>
            <div class="bi-kpi-sub">AOV ${{ number_format($kpis['aov'], 2) }}</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">New Enrollments</div>
            <div class="bi-kpi-value">{{ number_format($kpis['newEnrollments']) }}</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Active Students</div>
            <div class="bi-kpi-value">{{ number_format($kpis['activeStudents']) }}</div>
            <div class="bi-kpi-sub">All-time active</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Active Instructors</div>
            <div class="bi-kpi-value">{{ number_format($kpis['activeInstructors']) }}</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Published Courses</div>
            <div class="bi-kpi-value">{{ number_format($kpis['publishedCourses']) }}</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Refund Rate</div>
            <div class="bi-kpi-value {{ $kpis['refundRate'] > 5 ? '' : '' }}">{{ $kpis['refundRate'] }}%</div>
            <div class="bi-kpi-sub">{{ $kpis['refundRate'] <= 5 ? 'Healthy' : 'Review needed' }}</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Completion Rate</div>
            <div class="bi-kpi-value">{{ $kpis['completionRate'] }}%</div>
            <div class="bi-kpi-sub">All-time average</div>
        </div>
        <div class="bi-kpi">
            <div class="bi-kpi-label">Health Score</div>
            <div class="bi-health">{{ $kpis['healthScore'] }}<span style="font-size:16px;font-weight:700;-webkit-text-fill-color:var(--t2)">/100</span></div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="bi-charts">
        <div class="bi-card">
            <h3>Revenue Trend — Last 12 Months</h3>
            <div class="bi-chart-wrap">
                <canvas id="bi-exec-revenue-trend"></canvas>
            </div>
        </div>
        <div class="bi-card">
            <h3>Student &amp; Instructor Growth — 6 Months</h3>
            <div class="bi-chart-wrap">
                <canvas id="bi-exec-growth"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Courses chart --}}
    <div class="bi-card">
        <h3>Top 5 Courses by All-Time Revenue</h3>
        <div class="bi-chart-wrap" style="height:180px">
            <canvas id="bi-exec-top-courses"></canvas>
        </div>
    </div>

    {{-- Tables --}}
    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Top Instructors (Period)</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Instructor</th><th>Earnings</th><th>Sales</th></tr></thead>
                <tbody>
                    @forelse($topInstructors as $i => $instructor)
                    <tr>
                        <td><span class="bi-rank">{{ $i + 1 }}</span></td>
                        <td>{{ $instructor['name'] }}</td>
                        <td>${{ number_format($instructor['earnings'], 2) }}</td>
                        <td>{{ $instructor['sales'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No data for this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Recent Orders</h3>
            <table class="bi-table">
                <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td style="font-family:monospace;font-size:11px">{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name ?? $order->user?->name ?? '—' }}</td>
                        <td>${{ number_format((float)$order->final_amount, 2) }}</td>
                        <td style="color:var(--t2)">{{ $order->paid_at?->format('M d') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Courses table --}}
    <div class="bi-table-card">
        <h3>Top Courses by All-Time Revenue</h3>
        <table class="bi-table">
            <thead><tr><th>#</th><th>Course</th><th>Instructor</th><th>Total Revenue</th><th>Total Sales</th></tr></thead>
            <tbody>
                @forelse($topCourses as $i => $sale)
                <tr>
                    <td><span class="bi-rank">{{ $i + 1 }}</span></td>
                    <td>{{ $sale->course?->title ?? '—' }}</td>
                    <td style="color:var(--t2)">{{ $sale->course?->instructor?->name ?? '—' }}</td>
                    <td>${{ number_format((float)$sale->total_revenue, 2) }}</td>
                    <td>{{ number_format((int)$sale->total_sales) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:var(--t2);padding:20px">No sales data yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</div>

<script>
window.__biRun(function() {
    var isDark = document.documentElement.classList.contains('cl-dark') || document.documentElement.classList.contains('dark');
    var gridColor = 'rgba(148,163,184,0.12)';
    var tickColor = '#64748b';
    var fontFamily = "'DM Sans', ui-sans-serif, system-ui, sans-serif";

    var baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 500 },
        plugins: {
            legend: { labels: { color: tickColor, font: { family: fontFamily, size: 11 }, boxWidth: 12 } },
            tooltip: { backgroundColor: isDark ? '#1e293b' : '#fff', titleColor: isDark ? '#e2e8f0' : '#0f172a', bodyColor: '#64748b', borderColor: 'rgba(148,163,184,0.2)', borderWidth: 1 }
        },
        scales: {
            x: { ticks: { color: tickColor, font: { family: fontFamily, size: 10 } }, grid: { color: gridColor } },
            y: { ticks: { color: tickColor, font: { family: fontFamily, size: 10 } }, grid: { color: gridColor }, beginAtZero: true }
        }
    };

    // Revenue trend
    (function() {
        var el = document.getElementById('bi-exec-revenue-trend');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var data = @json($revenueTrendData);
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: data.revenue,
                    borderColor: '#D7A441',
                    backgroundColor: 'rgba(215,164,65,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2
                }]
            },
            options: Object.assign({}, baseOpts, {
                plugins: Object.assign({}, baseOpts.plugins, {
                    legend: { display: false }
                })
            })
        });
    })();

    // Growth chart
    (function() {
        var el = document.getElementById('bi-exec-growth');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var data = @json($growthData);
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Students', data: data.students, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 2 },
                    { label: 'Instructors', data: data.instructors, borderColor: '#D7A441', backgroundColor: 'rgba(215,164,65,0.08)', fill: true, tension: 0.4, pointRadius: 3, borderWidth: 2 }
                ]
            },
            options: baseOpts
        });
    })();

    // Top courses bar
    (function() {
        var el = document.getElementById('bi-exec-top-courses');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($topCoursesChartLabels),
                datasets: [{ label: 'Revenue ($)', data: @json($topCoursesChartValues), backgroundColor: 'rgba(215,164,65,0.75)', borderColor: '#D7A441', borderWidth: 1, borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                plugins: { legend: { display: false }, tooltip: baseOpts.plugins.tooltip },
                scales: {
                    x: { ticks: { color: tickColor, font: { family: fontFamily, size: 10 } }, grid: { color: gridColor }, beginAtZero: true },
                    y: { ticks: { color: tickColor, font: { family: fontFamily, size: 9 } }, grid: { display: false } }
                }
            }
        });
    })();
});
</script>
