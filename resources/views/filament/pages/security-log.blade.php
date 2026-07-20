<style>
.sl, .sl *, .sl *::before, .sl *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.sl {
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
html:not(.dark) .sl {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f1f5f9;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#94a3b8;
}
.sl-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.sl-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.sl-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.sl-btn {
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
.sl-btn:hover {
    background:var(--p2);
}
.sl-kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
    gap:12px;
}
.sl-kpi {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:16px;
}
.sl-kpi-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.sl-kpi-value {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.sl-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
}
.sl-card-title {
    padding:14px 16px;
    font-size:13px;
    font-weight:700;
    border-bottom:1px solid var(--bd);
    color:var(--t1);
}
.sl-filters {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}
.sl-input {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:7px 10px;
    font-size:12px;
    color:var(--t1);
}
.sl-input::placeholder {
    color:var(--t2);
}
.sl-select {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:7px 10px;
    font-size:12px;
    color:var(--t1);
}
.sl-table {
    width:100%;
    border-collapse:collapse;
}
.sl-table th {
    padding:10px 14px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    border-bottom:1px solid var(--bd);
}
.sl-table td {
    padding:10px 14px;
    border-bottom:1px solid var(--bd);
    font-size:12.5px;
    color:var(--t1);
}
.sl-table tr:last-child td {
    border-bottom:none;
}
.sl-badge {
    display:inline-flex;
    align-items:center;
    padding:2px 9px;
    border-radius:99px;
    font-size:11px;
    font-weight:700;
}
.sl-risk-high {
    color:#ef4444;
    background:#ef444415;
}
.sl-risk-med {
    color:#f59e0b;
    background:#f59e0b15;
}
.sl-pagination {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    font-size:12px;
    color:var(--t2);
    border-top:1px solid var(--bd);
}
.sl-action-tabs {
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}
.sl-action-pill {
    padding:4px 12px;
    border-radius:99px;
    font-size:11px;
    font-weight:700;
    text-decoration:none;
    border:1px solid transparent;
}
.sl-empty {
    padding:32px;
    text-align:center;
    color:var(--t2);
}
</style>

@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $actions = \App\Filament\Pages\SecurityLog::SECURITY_ACTIONS;
@endphp

<div class="sl">
    <div class="sl-header">
        <div>
            <h1>Security Events</h1>
            <p>Authentication anomalies, brute-force attempts, and account security events.</p>
        </div>
        <a href="{{ $url(['page' => 1]) }}" class="sl-btn">Refresh</a>
    </div>

    {{-- KPI cards --}}
    <div class="sl-kpi-grid">
        <div class="sl-kpi">
            <div class="sl-kpi-label">Failed Logins Today</div>
            <div class="sl-kpi-value" style="color:#f87171">{{ number_format($kpis['failedLoginsToday']) }}</div>
        </div>
        <div class="sl-kpi">
            <div class="sl-kpi-label">Unique Attacker IPs (24h)</div>
            <div class="sl-kpi-value" style="color:#f59e0b">{{ number_format($kpis['uniqueAttackerIPs']) }}</div>
        </div>
        <div class="sl-kpi">
            <div class="sl-kpi-label">Locked Accounts Today</div>
            <div class="sl-kpi-value" style="color:#ef4444">{{ number_format($kpis['lockedAccounts']) }}</div>
        </div>
        <div class="sl-kpi">
            <div class="sl-kpi-label">Successful Logins Today</div>
            <div class="sl-kpi-value" style="color:#10b981">{{ number_format($kpis['successfulLoginsToday']) }}</div>
        </div>
    </div>

    {{-- IP Risk table --}}
    @if($riskyIps->count() > 0)
    <div class="sl-card">
        <div class="sl-card-title">
            ⚠ High-Risk IPs (3+ failed logins in last 24h)
        </div>
        <table class="sl-table">
            <thead>
                <tr><th>IP Address</th><th>Failed Attempts</th><th>Last Seen</th><th>Filter</th></tr>
            </thead>
            <tbody>
                @foreach($riskyIps as $risky)
                    <tr>
                        <td style="font-family:monospace;font-size:12px">{{ $risky->ip_address }}</td>
                        <td>
                            <span class="sl-badge {{ $risky->attempts >= 5 ? 'sl-risk-high' : 'sl-risk-med' }}">
                                {{ $risky->attempts }} attempts
                            </span>
                        </td>
                        <td style="color:var(--t2)">{{ \Carbon\Carbon::parse($risky->last_seen)->diffForHumans() }}</td>
                        <td><a href="{{ $url(['ip' => $risky->ip_address, 'action' => 'all', 'page' => 1]) }}" wire:navigate class="sl-btn" style="padding:4px 10px;font-size:11px">Filter</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Event type filter pills --}}
    <div class="sl-action-tabs">
        @foreach($actions as $key => $def)
            @php $active = $action === $key; @endphp
            <a href="{{ $url(['action' => $key, 'page' => 1]) }}"
               class="sl-action-pill"
               style="background:{{ $active ? $def['color'] : 'transparent' }};
                      color:{{ $active ? '#fff' : $def['color'] }};
                      border-color:{{ $def['color'] }}30">
                {{ $def['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Search / date filters --}}
    <form method="GET" class="sl-filters">
        <input type="hidden" name="action" value="{{ $action }}">
        <input name="search" value="{{ $search }}" placeholder="Search user or IP…" class="sl-input" style="flex:1;min-width:180px">
        <input name="ip" value="{{ $ip }}" placeholder="Exact IP…" class="sl-input" style="width:140px">
        <input name="from" type="date" value="{{ $from }}" class="sl-input">
        <input name="to" type="date" value="{{ $to }}" class="sl-input">
        <button type="submit" class="sl-btn" style="background:var(--accent);color:#fff;border-color:transparent">Filter</button>
        @if($search || $ip || $from || $to)
            <a href="{{ $url(['search' => '', 'ip' => '', 'from' => '', 'to' => '', 'page' => 1]) }}" class="sl-btn">Clear</a>
        @endif
    </form>

    {{-- Events table --}}
    <div class="sl-card">
        @if($total === 0)
            <div class="sl-empty">No security events match your filters.</div>
        @else
            <table class="sl-table">
                <thead>
                    <tr><th>Event</th><th>User</th><th>IP Address</th><th>User Agent</th><th>Time</th></tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        @php
                            $def = \App\Filament\Pages\SecurityLog::SECURITY_ACTIONS[$event->action] ?? ['label' => $event->action, 'color' => '#94a3b8'];
                        @endphp
                        <tr>
                            <td>
                                <span class="sl-badge" style="background:{{ $def['color'] }}22;color:{{ $def['color'] }}">
                                    {{ $def['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($event->user)
                                    <div style="font-weight:600">{{ $event->user->name }}</div>
                                    <div style="font-size:11px;color:var(--t2)">{{ $event->user->email }}</div>
                                @else
                                    <span style="color:var(--t2)">—</span>
                                @endif
                            </td>
                            <td style="font-family:monospace;font-size:12px">
                                @if($event->ip_address)
                                    <a href="{{ $url(['ip' => $event->ip_address, 'page' => 1]) }}" style="color:var(--accent);text-decoration:none">{{ $event->ip_address }}</a>
                                @else
                                    <span style="color:var(--t2)">—</span>
                                @endif
                            </td>
                            <td style="color:var(--t2);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px" title="{{ $event->user_agent }}">
                                {{ \Illuminate\Support\Str::limit($event->user_agent ?? '—', 40) }}
                            </td>
                            <td style="color:var(--t2);white-space:nowrap">
                                <span title="{{ $event->created_at?->format('Y-m-d H:i:s') }}">
                                    {{ $event->created_at?->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="sl-pagination">
                <span>Showing {{ $total > 0 ? (($curPage-1)*$perPage)+1 : 0 }}–{{ min($curPage*$perPage, $total) }} of {{ number_format($total) }}</span>
                <div style="display:flex;gap:6px">
                    @if($curPage > 1)<a href="{{ $url(['page' => $curPage-1]) }}" class="sl-btn">Prev</a>@endif
                    @if($curPage < $totalPages)<a href="{{ $url(['page' => $curPage+1]) }}" class="sl-btn">Next</a>@endif
                </div>
            </div>
        @endif
    </div>
</div>
