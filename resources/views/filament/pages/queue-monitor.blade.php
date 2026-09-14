<style>
.qm, .qm *, .qm *::before, .qm *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.qm {
    font-family:Inter, ui-sans-serif, system-ui, sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:48px;
    display:grid;
    gap:20px;
    --bg:#0f172a;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --accent:#2563eb;
    color:var(--t1);
}
html:not(.dark) .qm {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f1f5f9;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#94a3b8;
}
.qm-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.qm-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.qm-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.qm-actions {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
.qm-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:1px solid var(--bd2);
    background:var(--p1);
    color:var(--t1);
}
.qm-btn:hover {
    background:var(--p2);
}
.qm-btn-danger {
    background:#ef444415;
    color:#ef4444;
    border-color:#ef444430;
}
.qm-btn-warn {
    background:#f59e0b15;
    color:#f59e0b;
    border-color:#f59e0b30;
}
.qm-kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));
    gap:12px;
}
.qm-kpi {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:14px 16px;
}
.qm-kpi-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.qm-kpi-value {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.qm-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}
@media(max-width:800px) {
    .qm-grid-2 {
        grid-template-columns:1fr;
    }
}
.qm-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
    min-width:0;
}
.qm-card-title {
    padding:14px 16px;
    font-size:13px;
    font-weight:700;
    border-bottom:1px solid var(--bd);
    color:var(--t1);
}
.qm-row {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:10px 16px;
    border-bottom:1px solid var(--bd);
    font-size:12.5px;
}
.qm-row:last-child {
    border-bottom:none;
}
.qm-bar {
    flex:1;
    height:6px;
    background:var(--p2);
    border-radius:99px;
    overflow:hidden;
    margin:0 12px;
}
.qm-bar-fill {
    height:100%;
    border-radius:99px;
    background:var(--accent);
}
.qm-table-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
}
.qm-table-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
}
.qm-table-header span {
    font-size:13px;
    font-weight:700;
    color:var(--t1);
}
.qm-table {
    width:100%;
    border-collapse:collapse;
}
.qm-table th {
    padding:10px 14px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    border-bottom:1px solid var(--bd);
}
.qm-table td {
    padding:10px 14px;
    border-bottom:1px solid var(--bd);
    font-size:12px;
    color:var(--t1);
}
.qm-table tr:last-child td {
    border-bottom:none;
}
.qm-trace-toggle {
    font-size:11px;
    color:var(--accent);
    cursor:pointer;
    background:none;
    border:none;
    padding:0;
    text-decoration:underline;
}
.qm-trace {
    font-family:monospace;
    font-size:11px;
    color:var(--t2);
    background:var(--p2);
    border-radius:6px;
    padding:8px 10px;
    margin-top:4px;
    white-space:pre-wrap;
    word-break:break-all;
    display:none;
}
.qm-trace.open {
    display:block;
}
.qm-empty {
    padding:32px;
    text-align:center;
    color:var(--t2);
}
</style>

<div class="qm">
    <div class="qm-header">
        <div>
            <h1>Queue Monitor</h1>
            <p>Pending and failed job statistics. Use <a href="/horizon" style="color:var(--accent)">Horizon</a> for real-time monitoring.</p>
        </div>
        <div class="qm-actions">
            <a href="{{ url()->current() }}" class="qm-btn">Refresh</a>
            @if(count($recentFailed) > 0)
                <button type="button" wire:click="clearAllFailed" wire:confirm="Delete all failed jobs? This cannot be undone." class="qm-btn qm-btn-danger">
                    Clear All Failed
                </button>
            @endif
        </div>
    </div>

    @if($kpis['using_redis'])
    <div style="background:#6366f115;border:1px solid #6366f130;border-radius:10px;padding:10px 16px;font-size:12px;color:#818cf8">
        <strong>Redis / Horizon queues active.</strong> Pending job counts are read from Redis. For real-time workers and job details use <a href="/horizon" style="color:#818cf8;text-decoration:underline">Horizon</a>.
    </div>
    @endif

    {{-- KPIs --}}
    <div class="qm-kpi-grid">
        <div class="qm-kpi">
            <div class="qm-kpi-label">Pending Jobs</div>
            <div class="qm-kpi-value" style="color:{{ $kpis['pending'] > 0 ? '#f59e0b' : '#10b981' }}">{{ number_format($kpis['pending']) }}</div>
        </div>
        <div class="qm-kpi">
            <div class="qm-kpi-label">Failed Jobs</div>
            <div class="qm-kpi-value" style="color:{{ $kpis['failed'] > 0 ? '#f87171' : '#10b981' }}">{{ number_format($kpis['failed']) }}</div>
        </div>
        <div class="qm-kpi">
            <div class="qm-kpi-label">Active Queues</div>
            <div class="qm-kpi-value">{{ number_format($kpis['queues']) }}</div>
        </div>
        <div class="qm-kpi">
            <div class="qm-kpi-label">Oldest Pending</div>
            <div class="qm-kpi-value" style="font-size:16px;{{ $kpis['oldest_job_min'] > 60 ? 'color:#f87171' : '' }}">
                {{ $kpis['oldest_job_min'] > 0 ? $kpis['oldest_job_min'] . ' min' : '—' }}
            </div>
        </div>
    </div>

    <div class="qm-grid-2">
        {{-- Pending by queue --}}
        <div class="qm-card">
            <div class="qm-card-title">Pending by Queue</div>
            @if($queueStats->isEmpty())
                <div class="qm-empty" style="padding:20px">No pending jobs.</div>
            @else
                @php $max = max(1, $queueStats->max('count')); @endphp
                @foreach($queueStats as $q)
                    <div class="qm-row">
                        <span style="min-width:80px;font-weight:600">{{ $q->queue }}</span>
                        <div class="qm-bar"><div class="qm-bar-fill" style="width:{{ ($q->count / $max) * 100 }}%"></div></div>
                        <span style="font-weight:700;min-width:40px;text-align:right">{{ number_format($q->count) }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Failed by queue --}}
        <div class="qm-card">
            <div class="qm-card-title">Failed by Queue</div>
            @if($failedStats->isEmpty())
                <div class="qm-empty" style="padding:20px">No failed jobs.</div>
            @else
                @php $fmax = max(1, $failedStats->max('count')); @endphp
                @foreach($failedStats as $q)
                    <div class="qm-row">
                        <span style="min-width:80px;font-weight:600">{{ $q->queue }}</span>
                        <div class="qm-bar"><div class="qm-bar-fill" style="width:{{ ($q->count / $fmax) * 100 }};background:#f87171"></div></div>
                        <span style="font-weight:700;min-width:40px;text-align:right;color:#f87171">{{ number_format($q->count) }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Recent failed jobs --}}
    <div class="qm-table-card">
        <div class="qm-table-header">
            <span>Recent Failed Jobs ({{ count($recentFailed) }})</span>
        </div>
        @if(empty($recentFailed))
            <div class="qm-empty">No failed jobs — queue is clean.</div>
        @else
            <div style="overflow-x:auto">
            <table class="qm-table">
                <thead>
                    <tr><th>Job</th><th>Queue</th><th>Error</th><th>Failed At</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($recentFailed as $i => $job)
                        <tr wire:key="failed-job-{{ $job['uuid'] }}">
                            <td style="font-family:monospace;font-size:11px;max-width:200px;word-break:break-all">{{ \Illuminate\Support\Str::limit($job['job'], 50) }}</td>
                            <td><span style="background:var(--p2);color:var(--t2);padding:2px 8px;border-radius:6px;font-size:11px">{{ $job['queue'] }}</span></td>
                            <td>
                                <div style="color:#f87171;font-size:11px">{{ $job['error'] }}</div>
                                @if(strlen($job['exception'] ?? '') > strlen($job['error']))
                                    <button type="button" class="qm-trace-toggle" onclick="var t=document.getElementById('qt-{{ $i }}');t.classList.toggle('open');this.textContent=t.classList.contains('open')?'Hide':'Show trace'">Show trace</button>
                                    <pre class="qm-trace" id="qt-{{ $i }}">{{ $job['exception'] }}</pre>
                                @endif
                            </td>
                            <td style="color:var(--t2);font-size:11px;white-space:nowrap">{{ $job['failed_at'] }}</td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <button type="button" wire:click="retryJob('{{ $job['uuid'] }}')" class="qm-btn qm-btn-warn" style="padding:4px 10px;font-size:11px">Retry</button>
                                    <button type="button" wire:click="deleteFailedJob('{{ $job['uuid'] }}')" wire:confirm="Delete this failed job?" class="qm-btn qm-btn-danger" style="padding:4px 10px;font-size:11px">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</div>
