<style>
.se, .se *, .se *::before, .se *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.se {
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
html:not(.dark) .se {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f1f5f9;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#94a3b8;
}
.se-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.se-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.se-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.se-btn {
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
.se-btn:hover {
    background:var(--p2);
}
.se-btn-danger {
    background:#ef444415;
    color:#ef4444;
    border-color:#ef444430;
}
.se-btn-danger:hover {
    background:#ef444425;
}
.se-kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));
    gap:12px;
}
.se-kpi {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:14px 16px;
}
.se-kpi-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.se-kpi-value {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.se-filters {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
.se-input {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:7px 10px;
    font-size:12px;
    color:var(--t1);
    flex:1;
    min-width:200px;
}
.se-input::placeholder {
    color:var(--t2);
}
.se-table-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
}
.se-table {
    width:100%;
    border-collapse:collapse;
}
.se-table th {
    padding:10px 14px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    border-bottom:1px solid var(--bd);
}
.se-table td {
    padding:10px 14px;
    border-bottom:1px solid var(--bd);
    font-size:12.5px;
    color:var(--t1);
}
.se-table tr:last-child td {
    border-bottom:none;
}
.se-pagination {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    font-size:12px;
    color:var(--t2);
    border-top:1px solid var(--bd);
}
.se-empty {
    padding:32px;
    text-align:center;
    color:var(--t2);
}
</style>

@php $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p)); @endphp

<div class="se">
    <div class="se-header">
        <div>
            <h1>Active Sessions</h1>
            <p>User sessions tracked in <code>user_sessions</code>. Session driver: Redis (auth events only).</p>
        </div>
        <a href="{{ $url(['page' => 1]) }}" class="se-btn">Refresh</a>
    </div>

    <div class="se-kpi-grid">
        <div class="se-kpi"><div class="se-kpi-label">Total Sessions</div><div class="se-kpi-value">{{ number_format($kpis['total']) }}</div></div>
        <div class="se-kpi"><div class="se-kpi-label">Active (24h)</div><div class="se-kpi-value" style="color:#10b981">{{ number_format($kpis['active_24h']) }}</div></div>
        <div class="se-kpi"><div class="se-kpi-label">Unique Users</div><div class="se-kpi-value">{{ number_format($kpis['unique_users']) }}</div></div>
        <div class="se-kpi"><div class="se-kpi-label">Unique IPs</div><div class="se-kpi-value">{{ number_format($kpis['unique_ips']) }}</div></div>
    </div>

    <form method="GET" class="se-filters">
        <input name="search" value="{{ $search }}" placeholder="Search user, email or IP…" class="se-input">
        <button type="submit" class="se-btn" style="background:var(--accent);color:#fff;border-color:transparent">Filter</button>
        @if($search)<a href="{{ $url(['search' => '', 'page' => 1]) }}" class="se-btn">Clear</a>@endif
    </form>

    <div class="se-table-card">
        @if($total === 0)
            <div class="se-empty">No sessions found.</div>
        @else
            <table class="se-table">
                <thead>
                    <tr><th>User</th><th>IP Address</th><th>Last Activity</th><th>User Agent</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                        <tr>
                            <td>
                                <div style="font-weight:600">{{ $session->user_name }}</div>
                                <div style="font-size:11px;color:var(--t2)">{{ $session->user_email }}</div>
                            </td>
                            <td style="font-family:monospace;font-size:12px">{{ $session->ip_address ?? '—' }}</td>
                            <td style="color:var(--t2);white-space:nowrap;font-size:12px">
                                @if($session->last_activity)
                                    <span title="{{ $session->last_activity }}">{{ \Carbon\Carbon::parse($session->last_activity)->diffForHumans() }}</span>
                                @else —
                                @endif
                            </td>
                            <td style="color:var(--t2);font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $session->user_agent }}">
                                {{ \Illuminate\Support\Str::limit($session->user_agent ?? '—', 45) }}
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <button wire:click="revokeSession({{ $session->id }})" wire:confirm="Revoke this session?" class="se-btn se-btn-danger" style="padding:4px 10px;font-size:11px">
                                        Revoke
                                    </button>
                                    <button wire:click="revokeAllForUser({{ $session->user_id }})" wire:confirm="Revoke ALL sessions for {{ $session->user_name }}?" class="se-btn" style="padding:4px 10px;font-size:11px">
                                        Revoke All
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="se-pagination">
                <span>Showing {{ $total > 0 ? (($curPage-1)*$perPage)+1 : 0 }}–{{ min($curPage*$perPage, $total) }} of {{ number_format($total) }}</span>
                <div style="display:flex;gap:6px">
                    @if($curPage > 1)<a href="{{ $url(['page' => $curPage-1]) }}" class="se-btn">Prev</a>@endif
                    @if($curPage < $totalPages)<a href="{{ $url(['page' => $curPage+1]) }}" class="se-btn">Next</a>@endif
                </div>
            </div>
        @endif
    </div>
</div>
