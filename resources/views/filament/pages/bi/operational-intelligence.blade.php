@include('filament.pages.bi._bi-shared')

<style>
.bi-alert-row {
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 14px;
    border-radius:8px;
    font-size:12.5px;
}

.bi-alert-row.warn {
    background:rgba(37,99,235,.09);
    border:1px solid rgba(37,99,235,.2);
    color:var(--acc);
}

.bi-alert-row.err {
    background:rgba(239,68,68,.09);
    border:1px solid rgba(239,68,68,.2);
    color:var(--err);
}

.bi-alert-row.ok {
    background:rgba(34,197,94,.07);
    border:1px solid rgba(34,197,94,.2);
    color:var(--ok);
}

.bi-alert-icon {
    font-size:16px;
    flex-shrink:0;
}

.bi-alert-msg {
    flex:1;
    font-weight:600;
}

.bi-alert-val {
    font-size:18px;
    font-weight:800;
}

.bi-alerts-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

@media (max-width: 700px) {
    .bi-alerts-grid {
        grid-template-columns:1fr;
    }
}
</style>

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Operational Intelligence ⚙️</h1>
            <p>Live system state — pending reviews, failed payments, queue health, and alerts.</p>
        </div>
        @include('filament.pages.bi._bi-actions', ['biKey' => 'ops'])
    </div>

    {{-- Alert summary cards --}}
    <div class="bi-alerts-grid">
        <div class="bi-alert-row {{ $kpis['pendingCourseReviews'] > 0 ? 'warn' : 'ok' }}">
            <span class="bi-alert-icon">{{ $kpis['pendingCourseReviews'] > 0 ? '⏳' : '✅' }}</span>
            <span class="bi-alert-msg">Pending Course Reviews</span>
            <span class="bi-alert-val">{{ $kpis['pendingCourseReviews'] }}</span>
        </div>
        <div class="bi-alert-row {{ $kpis['pendingVerifications'] > 0 ? 'warn' : 'ok' }}">
            <span class="bi-alert-icon">{{ $kpis['pendingVerifications'] > 0 ? '🔍' : '✅' }}</span>
            <span class="bi-alert-msg">Pending Verifications</span>
            <span class="bi-alert-val">{{ $kpis['pendingVerifications'] }}</span>
        </div>
        <div class="bi-alert-row {{ $kpis['failedPaymentsToday'] > 0 ? 'err' : 'ok' }}">
            <span class="bi-alert-icon">{{ $kpis['failedPaymentsToday'] > 0 ? '❌' : '✅' }}</span>
            <span class="bi-alert-msg">Failed Payments Today</span>
            <span class="bi-alert-val">{{ $kpis['failedPaymentsToday'] }}</span>
        </div>
        <div class="bi-alert-row {{ $kpis['openRefunds'] > 0 ? 'warn' : 'ok' }}">
            <span class="bi-alert-icon">{{ $kpis['openRefunds'] > 0 ? '↩️' : '✅' }}</span>
            <span class="bi-alert-msg">Open Refunds ({{ $periodLabel }})</span>
            <span class="bi-alert-val">{{ $kpis['openRefunds'] }}</span>
        </div>
        <div class="bi-alert-row {{ $kpis['failedJobs'] > 0 ? 'err' : 'ok' }}">
            <span class="bi-alert-icon">{{ $kpis['failedJobs'] > 0 ? '🔥' : '✅' }}</span>
            <span class="bi-alert-msg">Failed Queue Jobs</span>
            <span class="bi-alert-val">{{ $kpis['failedJobs'] }}</span>
        </div>
        <div class="bi-alert-row {{ $kpis['queueJobs'] > 50 ? 'warn' : 'ok' }}">
            <span class="bi-alert-icon">{{ $kpis['queueJobs'] > 50 ? '⚠️' : '✅' }}</span>
            <span class="bi-alert-msg">Queued Jobs Pending</span>
            <span class="bi-alert-val">{{ $kpis['queueJobs'] }}</span>
        </div>
    </div>

    <div class="bi-kpi-grid">
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Order Processing</div><div class="bi-kpi-value">{{ $kpis['avgProcessingMins'] }}m</div><div class="bi-kpi-sub">{{ $periodLabel }} average</div></div>
    </div>

    <div class="bi-charts-3">
        <div class="bi-card">
            <h3>Order Status Distribution</h3>
            <div class="bi-chart-wrap"><canvas id="bi-ops-order-status"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Payment Status — 7 Days</h3>
            <div class="bi-chart-wrap"><canvas id="bi-ops-pay-trend"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Pending Reviews — 30 Days</h3>
            <div class="bi-chart-wrap"><canvas id="bi-ops-review-trend"></canvas></div>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Pending Course Reviews</h3>
            <table class="bi-table">
                <thead><tr><th>Course</th><th>Instructor</th><th>Submitted</th></tr></thead>
                <tbody>
                    @forelse($pendingCourses as $course)
                    <tr>
                        <td>{{ $course->title }}</td>
                        <td style="color:var(--t2)">{{ $course->instructor?->name ?? '—' }}</td>
                        <td style="color:var(--t2)">{{ $course->updated_at?->diffForHumans() ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--ok);padding:20px">All clear — no pending reviews</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Verification Queue</h3>
            <table class="bi-table">
                <thead><tr><th>Name</th><th>Email</th><th>Submitted</th></tr></thead>
                <tbody>
                    @forelse($verificationQueue as $v)
                    <tr>
                        <td>{{ $v->user?->name ?? '—' }}</td>
                        <td style="color:var(--t2);font-size:11px">{{ $v->user?->email ?? '—' }}</td>
                        <td style="color:var(--t2)">{{ $v->created_at?->diffForHumans() ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--ok);padding:20px">No pending verifications</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Failed Payments ({{ $periodLabel }})</h3>
            <table class="bi-table">
                <thead><tr><th>Order</th><th>Amount</th><th>Gateway</th><th>Time</th></tr></thead>
                <tbody>
                    @forelse($failedPayments as $pay)
                    <tr>
                        <td style="font-family:monospace;font-size:11px">{{ $pay->order?->order_number ?? '—' }}</td>
                        <td style="color:var(--err)">${{ number_format((float)$pay->amount,2) }}</td>
                        <td style="color:var(--t2)">{{ $pay->payment_gateway ?? '—' }}</td>
                        <td style="color:var(--t2)">{{ $pay->created_at?->diffForHumans() ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--ok);padding:20px">No failed payments in this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Recent Refunds</h3>
            <table class="bi-table">
                <thead><tr><th>Order</th><th>Amount</th><th>Status</th><th>Time</th></tr></thead>
                <tbody>
                    @forelse($recentRefunds as $refund)
                    <tr>
                        <td style="font-family:monospace;font-size:11px">{{ $refund->order?->order_number ?? '—' }}</td>
                        <td>${{ number_format((float)$refund->amount,2) }}</td>
                        <td><span class="bi-tag bi-tag-warn">{{ ucfirst($refund->status ?? 'pending') }}</span></td>
                        <td style="color:var(--t2)">{{ $refund->created_at?->diffForHumans() ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No recent refunds</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(count($failedJobsList) > 0)
    <div class="bi-table-card">
        <h3>Failed Queue Jobs</h3>
        <table class="bi-table">
            <thead><tr><th>Queue</th><th>Connection</th><th>Failed At</th><th>Exception</th></tr></thead>
            <tbody>
                @foreach($failedJobsList as $job)
                <tr>
                    <td style="font-family:monospace;font-size:11px">{{ $job->queue }}</td>
                    <td style="color:var(--t2)">{{ $job->connection }}</td>
                    <td style="color:var(--err)">{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</td>
                    <td style="color:var(--t2);font-size:11px;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ Str::limit($job->exception ?? 'Unknown', 120) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

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

    // Order status donut
    (function() {
        var el = document.getElementById('bi-ops-order-status');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var d = @json($orderStatusBreakdown);
        el._ch = new window.Chart(el, {
            type: 'doughnut',
            data: {
                labels: Object.keys(d),
                datasets: [{ data: Object.values(d), backgroundColor: ['#2563eb','#22c55e','#ef4444','#64748b'], borderWidth: 1, borderColor: 'rgba(0,0,0,0.08)' }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { position: 'right', labels: { color: tickColor, font: { family: ff, size: 10 }, boxWidth: 10 } } } }
        });
    })();

    // Payment trend stacked bar
    (function() {
        var el = document.getElementById('bi-ops-pay-trend');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($payTrendLabels),
                datasets: [
                    { label: 'Paid', data: @json($payTrendPaid), backgroundColor: 'rgba(34,197,94,0.7)', borderWidth: 0, borderRadius: 3, stack: 'a' },
                    { label: 'Failed', data: @json($payTrendFailed), backgroundColor: 'rgba(239,68,68,0.7)', borderWidth: 0, borderRadius: 3, stack: 'a' },
                    { label: 'Pending', data: @json($payTrendPending), backgroundColor: 'rgba(37,99,235,0.7)', borderWidth: 0, borderRadius: 3, stack: 'a' }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { labels: { color: tickColor, font: { family: ff, size: 10 }, boxWidth: 10 } } }, scales: baseScales }
        });
    })();

    // Review queue line
    (function() {
        var el = document.getElementById('bi-ops-review-trend');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: @json($reviewTrendLabels),
                datasets: [{ label: 'Pending Reviews', data: @json($reviewTrendValues), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();
});
</script>
