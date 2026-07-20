<style>
.sh, .sh *, .sh *::before, .sh *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.sh {
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
    --ok:#10b981;
    --warn:#f59e0b;
    --err:#f87171;
    color:var(--t1);
}
html:not(.dark) .sh {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f1f5f9;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#94a3b8;
}
.sh-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.sh-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.sh-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.sh-btn {
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
.sh-btn:hover {
    background:var(--p2);
}
.sh-btn-danger {
    background:#ef444415;
    color:#ef4444;
    border-color:#ef444430;
}
.sh-btn-danger:hover {
    background:#ef444425;
}
.sh-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
    gap:16px;
}
.sh-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    padding:20px;
}
.sh-card-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:14px;
}
.sh-card-title {
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.07em;
    color:var(--t2);
}
.sh-status-dot {
    width:10px;
    height:10px;
    border-radius:50%;
    flex-shrink:0;
}
.sh-stat {
    display:flex;
    justify-content:space-between;
    align-items:baseline;
    padding:6px 0;
    border-bottom:1px solid var(--bd);
}
.sh-stat:last-child {
    border-bottom:none;
}
.sh-stat-label {
    font-size:12px;
    color:var(--t2);
}
.sh-stat-value {
    font-size:13px;
    font-weight:700;
    color:var(--t1);
    text-align:right;
}
.sh-progress-bar {
    height:6px;
    border-radius:99px;
    background:var(--p2);
    overflow:hidden;
    margin-top:10px;
}
.sh-progress-fill {
    height:100%;
    border-radius:99px;
}
.sh-table-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    overflow:hidden;
}
.sh-table-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
}
.sh-table-header span {
    font-size:13px;
    font-weight:700;
    color:var(--t1);
}
.sh-table {
    width:100%;
    border-collapse:collapse;
}
.sh-table th {
    padding:10px 14px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    border-bottom:1px solid var(--bd);
}
.sh-table td {
    padding:10px 14px;
    border-bottom:1px solid var(--bd);
    font-size:12px;
    color:var(--t1);
}
.sh-table tr:last-child td {
    border-bottom:none;
}
.sh-trace {
    font-family:'JetBrains Mono',monospace;
    font-size:11px;
    color:var(--t2);
    background:var(--p2);
    border-radius:6px;
    padding:8px 10px;
    margin-top:4px;
    overflow-x:auto;
    white-space:pre-wrap;
    word-break:break-all;
    display:none;
}
.sh-trace.open {
    display:block;
}
.sh-trace-toggle {
    font-size:11px;
    color:var(--accent);
    cursor:pointer;
    background:none;
    border:none;
    padding:0;
    text-decoration:underline;
}
.sh-empty {
    padding:32px;
    text-align:center;
    color:var(--t2);
}
.sh-badge {
    display:inline-flex;
    padding:2px 9px;
    border-radius:99px;
    font-size:11px;
    font-weight:700;
}
</style>

@php
    $statusDot = fn(string $s) => match($s) {
        'ok'   => '#10b981',
        'warn' => '#f59e0b',
        default => '#f87171',
    };
    $statusLabel = fn(string $s) => match($s) {
        'ok'   => 'Healthy',
        'warn' => 'Warning',
        default => 'Error',
    };
@endphp

<div class="sh">
    <div class="sh-header">
        <div>
            <h1>System Health</h1>
            <p>Live platform status — queue, database, cache, storage and logs. Updated on page load.</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ url()->current() }}" class="sh-btn">Refresh</a>
        </div>
    </div>

    {{-- App Info bar --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;padding:10px 14px;background:var(--p1);border:1px solid var(--bd);border-radius:10px;font-size:12px;color:var(--t2)">
        <span>Env: <strong style="color:var(--t1)">{{ $app['env'] }}</strong></span>
        <span>Debug: <strong style="color:{{ $app['debug'] ? '#f87171' : '#10b981' }}">{{ $app['debug'] ? 'ON' : 'off' }}</strong></span>
        <span>Laravel: <strong style="color:var(--t1)">{{ $app['version'] }}</strong></span>
        <span>PHP: <strong style="color:var(--t1)">{{ $app['php'] }}</strong></span>
        <span>Time: <strong style="color:var(--t1)">{{ $app['now'] }}</strong></span>
    </div>

    <div class="sh-grid">
        {{-- Queue card --}}
        <div class="sh-card">
            <div class="sh-card-header">
                <span class="sh-card-title">Queue</span>
                <div class="sh-status-dot" style="background:{{ $queue['failed'] > 0 ? '#f59e0b' : '#10b981' }}"></div>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Pending Jobs</span>
                <span class="sh-stat-value">{{ number_format($queue['pending']) }}</span>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Failed Jobs</span>
                <span class="sh-stat-value" style="color:{{ $queue['failed'] > 0 ? '#f87171' : 'var(--t1)' }}">{{ number_format($queue['failed']) }}</span>
            </div>
        </div>

        {{-- Database card --}}
        <div class="sh-card">
            <div class="sh-card-header">
                <span class="sh-card-title">Database</span>
                <div class="sh-status-dot" style="background:{{ $statusDot($database['status']) }}"></div>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Driver</span>
                <span class="sh-stat-value">{{ strtoupper($database['driver']) }}</span>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Ping Latency</span>
                <span class="sh-stat-value" style="color:{{ ($database['ping_ms'] ?? 0) > 100 ? '#f59e0b' : '#10b981' }}">
                    {{ $database['ping_ms'] !== null ? $database['ping_ms'] . ' ms' : 'N/A' }}
                </span>
            </div>
            @if($database['status'] !== 'ok')
            <div style="margin-top:8px;font-size:11px;color:#f87171">{{ $database['error'] ?? '' }}</div>
            @endif
        </div>

        {{-- Cache card --}}
        <div class="sh-card">
            <div class="sh-card-header">
                <span class="sh-card-title">Cache</span>
                <div class="sh-status-dot" style="background:{{ $statusDot($cache['status']) }}"></div>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Driver</span>
                <span class="sh-stat-value">{{ strtoupper($cache['driver']) }}</span>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Read/Write Test</span>
                <span class="sh-stat-value" style="color:{{ $cache['status'] === 'ok' ? '#10b981' : '#f87171' }}">
                    {{ $cache['status'] === 'ok' ? 'Passed' : 'Failed' }}
                </span>
            </div>
        </div>

        {{-- Storage card --}}
        <div class="sh-card">
            <div class="sh-card-header">
                <span class="sh-card-title">Storage</span>
                <div class="sh-status-dot" style="background:{{ $storage['used_pct'] > 85 ? '#f87171' : ($storage['used_pct'] > 70 ? '#f59e0b' : '#10b981') }}"></div>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Free Space</span>
                <span class="sh-stat-value">{{ $storage['free_gb'] }} GB</span>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Total Space</span>
                <span class="sh-stat-value">{{ $storage['total_gb'] }} GB</span>
            </div>
            <div class="sh-progress-bar">
                <div class="sh-progress-fill" style="width:{{ $storage['used_pct'] }}%;background:{{ $storage['used_pct'] > 85 ? '#f87171' : ($storage['used_pct'] > 70 ? '#f59e0b' : '#10b981') }}"></div>
            </div>
            <div style="font-size:11px;color:var(--t2);margin-top:6px">{{ $storage['used_pct'] }}% used</div>
        </div>

        {{-- Logs card --}}
        <div class="sh-card">
            <div class="sh-card-header">
                <span class="sh-card-title">Application Logs</span>
                <div class="sh-status-dot" style="background:{{ $statusDot($logs['status']) }}"></div>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Log File Size</span>
                <span class="sh-stat-value" style="color:{{ $logs['size_mb'] > 100 ? '#f59e0b' : 'var(--t1)' }}">{{ $logs['size_mb'] }} MB</span>
            </div>
            <div class="sh-stat">
                <span class="sh-stat-label">Errors (last 24h)</span>
                <span class="sh-stat-value" style="color:{{ $logs['errors_24h'] > 0 ? '#f87171' : '#10b981' }}">{{ number_format($logs['errors_24h']) }}</span>
            </div>
        </div>
    </div>

    {{-- Failed Jobs --}}
    <div class="sh-table-card">
        <div class="sh-table-header">
            <span>Recent Failed Jobs ({{ count($failedJobs) }})</span>
            @if(count($failedJobs) > 0)
                <button wire:click="clearFailedJobs" wire:confirm="Delete all failed jobs? This cannot be undone." class="sh-btn sh-btn-danger">
                    Clear All Failed Jobs
                </button>
            @endif
        </div>
        @if(count($failedJobs) === 0)
            <div class="sh-empty">No failed jobs — queue is clean.</div>
        @else
            <table class="sh-table">
                <thead>
                    <tr><th>Job</th><th>Queue</th><th>Failed At</th><th>Exception</th></tr>
                </thead>
                <tbody>
                    @foreach($failedJobs as $i => $job)
                        <tr>
                            <td style="font-size:11px;font-family:monospace;word-break:break-all">{{ $job['job'] }}</td>
                            <td><span class="sh-badge" style="background:var(--p2);color:var(--t2)">{{ $job['queue'] }}</span></td>
                            <td style="color:var(--t2);white-space:nowrap;font-size:11px">{{ $job['failed_at'] }}</td>
                            <td>
                                @if($job['exception'])
                                    @php $firstLine = explode("\n", $job['exception'])[0]; @endphp
                                    <div style="font-size:11px;color:#f87171">{{ \Illuminate\Support\Str::limit($firstLine, 80) }}</div>
                                    @if(strlen($job['exception']) > strlen($firstLine))
                                        <button class="sh-trace-toggle" onclick="
                                            var t=document.getElementById('trace-{{ $i }}');
                                            t.classList.toggle('open');
                                            this.textContent=t.classList.contains('open')?'Hide trace':'Show trace';
                                        ">Show trace</button>
                                        <pre class="sh-trace" id="trace-{{ $i }}">{{ $job['exception'] }}</pre>
                                    @endif
                                @else
                                    <span style="color:var(--t2)">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if(session('success'))
        <div style="background:#10b98115;border:1px solid #10b98130;color:#10b981;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600">
            {{ session('success') }}
        </div>
    @endif
</div>
