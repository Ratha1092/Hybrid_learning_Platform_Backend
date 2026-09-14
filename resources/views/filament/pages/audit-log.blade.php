@php
    $activeTab = collect($tabs ?? [])->firstWhere('active', true) ?? ($tabs[0] ?? ['key' => 'all', 'label' => 'All', 'color' => '#6366f1']);

    $accent = '#6366f1';

    $actionStyle = fn($act) => match($act) {
        'login'            => ['bg' => 'rgba(16,185,129,.13)',  'color' => '#10b981', 'icon' => 'login'],
        'registration'     => ['bg' => 'rgba(59,130,246,.13)',  'color' => '#3b82f6', 'icon' => 'reg'],
        'failed_login'     => ['bg' => 'rgba(248,113,113,.13)', 'color' => '#f87171', 'icon' => 'fail'],
        'password_changed' => ['bg' => 'rgba(245,158,11,.13)',  'color' => '#f59e0b', 'icon' => 'pw'],
        'email_verified'   => ['bg' => 'rgba(6,182,212,.13)',   'color' => '#06b6d4', 'icon' => 'email'],
        default            => str_starts_with($act, 'settings.')
            ? ['bg' => 'rgba(249,115,22,.12)', 'color' => '#fb923c', 'icon' => 'settings']
            : ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'icon' => 'default'],
    };
@endphp

<div>
<div class="al" id="al-wrap" style="--accent:{{ $accent }}">

<style>
.al, .al *, .al *::before, .al *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.al {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
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
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .al {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes alUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.ala {
    opacity:0;
    animation:alUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.al1 {
    animation-delay:.04s;
}
.al2 {
    animation-delay:.09s;
}
.al3 {
    animation-delay:.14s;
}

/* ── Header ─────────────────────────────────────────────────── */
.al-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.al-header-text h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.15;
}
.al-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}

/* stats row */
.al-stats {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
}
.al-stat {
    display:flex;
    align-items:center;
    gap:14px;
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:18px 20px;
    box-shadow:var(--sh);
}
.al-stat-icon {
    width:44px;
    height:44px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.al-stat-icon svg {
    width:20px;
    height:20px;
}
.al-stat-val {
    font-size:26px;
    font-weight:800;
    letter-spacing:-.03em;
    color:var(--t1);
    line-height:1;
}
.al-stat-lbl {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
}
@media(max-width:640px) {
    .al-stats {
        grid-template-columns:1fr;
    }
}

/* ── Card ───────────────────────────────────────────────────── */
.al-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    box-shadow:var(--sh);
    min-width:0;
}

/* ── Toolbar ────────────────────────────────────────────────── */
.al-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.al-role-filter {
    position:relative;
    flex-shrink:0;
}
.al-role-filter-btn {
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:9px;
    font-size:12.5px;
    font-weight:700;
    color:var(--t1);
    background:var(--p2);
    border:1px solid var(--bd2);
    cursor:pointer;
    font-family:inherit;
    transition:background .15s, border-color .15s;
}
.al-role-filter-btn:hover {
    background:var(--p2);
    border-color:var(--bd2);
}
.al-role-filter-dot {
    width:8px;
    height:8px;
    border-radius:50%;
    flex-shrink:0;
}
.al-role-filter-chevron {
    width:13px;
    height:13px;
    color:var(--t2);
    transition:transform .15s;
    margin-left:2px;
}
.al-role-filter:focus-within .al-role-filter-chevron {
    transform:rotate(180deg);
}
.al-role-filter-menu {
    display:none;
    position:absolute;
    left:0;
    top:calc(100% + 6px);
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:10px;
    box-shadow:0 12px 32px rgba(0,0,0,.35);
    min-width:220px;
    max-height:340px;
    overflow-y:auto;
    z-index:50;
    padding:6px;
}
.al-role-filter:focus-within .al-role-filter-menu {
    display:block;
}
.al-role-filter-item {
    display:flex;
    align-items:center;
    gap:9px;
    padding:8px 10px;
    border-radius:7px;
    font-size:12.5px;
    font-weight:600;
    color:var(--t1);
    text-decoration:none;
    transition:background .12s;
}
.al-role-filter-item:hover {
    background:var(--p2);
}
.al-role-filter-item.active {
    background:var(--p2);
}
.al-role-filter-item-label {
    flex:1 1 auto;
}

/* filters row */
.al-filters {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
    padding:10px 16px;
    border-bottom:1px solid var(--bd);
    background:var(--p2);
}
.al-search-box {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.al-search-box svg {
    width:13px;
    height:13px;
    color:var(--t2);
    flex-shrink:0;
}
.al-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:190px;
}
.al-search-box input::placeholder {
    color:var(--t2);
}
.al-date-box {
    display:flex;
    align-items:center;
    gap:6px;
}
.al-date-box label {
    font-size:11px;
    color:var(--t2);
    white-space:nowrap;
}
.al-date-input {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:5px 10px;
    font-size:12px;
    color:var(--t1);
    font-family:inherit;
    outline:none;
    cursor:pointer;
}
.al-filter-btn {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:6px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    border:none;
    font-family:inherit;
    transition:opacity .15s;
}
.al-filter-btn:hover {
    opacity:.85;
}
.al-clear-link {
    font-size:12px;
    color:var(--t2);
    text-decoration:none;
    padding:6px 10px;
    border-radius:8px;
    transition:background .15s;
    white-space:nowrap;
}
.al-clear-link:hover {
    background:var(--p2);
    color:var(--t1);
}

/* ── Table ──────────────────────────────────────────────────── */
.al-table {
    width:100%;
    border-collapse:collapse;
}
.al-table thead tr {
    border-bottom:1px solid var(--bd);
}
.al-table th {
    padding:10px 14px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.al-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.al-table tbody tr:last-child {
    border-bottom:none;
}
.al-table tbody tr:hover {
    background:var(--p2);
}
.al-table td {
    padding:11px 14px;
    vertical-align:middle;
}

.al-seq {
    font-size:11px;
    font-weight:700;
    color:var(--t3);
    font-variant-numeric:tabular-nums;
}
.al-user {
    display:flex;
    align-items:center;
    gap:9px;
}
.al-av {
    width:30px;
    height:30px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
}
.al-av-ph {
    width:30px;
    height:30px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:800;
    flex-shrink:0;
}
.al-uname {
    font-size:12.5px;
    font-weight:650;
    color:var(--t1);
}
.al-uemail {
    font-size:11.5px;
    color:var(--t2);
}
.al-guest {
    font-size:12px;
    color:var(--t2);
    font-style:italic;
}

.al-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}
.al-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}

.al-ip {
    display:flex;
    align-items:center;
    gap:5px;
    font-size:12px;
    color:var(--t2);
    font-variant-numeric:tabular-nums;
    white-space:nowrap;
}
.al-ip svg {
    width:12px;
    height:12px;
    flex-shrink:0;
    opacity:.6;
}

.al-ua {
    font-size:11.5px;
    color:var(--t2);
    max-width:200px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    display:block;
}
.al-date {
    font-size:11.5px;
    color:var(--t2);
    white-space:nowrap;
}
.al-time {
    font-size:10.5px;
    color:var(--t3);
    margin-top:1px;
}

.al-data-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:26px;
    height:26px;
    border-radius:6px;
    background:none;
    border:1px solid var(--bd2);
    cursor:pointer;
    color:var(--t2);
    transition:background .15s, color .15s;
}
.al-data-btn:hover {
    background:var(--p2);
    color:var(--t1);
}
.al-data-btn svg {
    width:12px;
    height:12px;
}

/* ── Data modal ─────────────────────────────────────────────── */
.al-modal-backdrop {
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    z-index:9998;
    display:none;
    align-items:center;
    justify-content:center;
}
.al-modal-backdrop.open {
    display:flex;
}
.al-modal {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:14px;
    padding:20px 24px;
    width:min(540px, 92vw);
    max-height:70vh;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    box-shadow:0 20px 60px rgba(0,0,0,.5);
}
.al-modal-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:14px;
}
.al-modal-title {
    font-size:14px;
    font-weight:750;
    color:var(--t1);
}
.al-modal-close {
    width:28px;
    height:28px;
    border-radius:7px;
    background:none;
    border:1px solid var(--bd2);
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--t2);
    transition:background .15s;
}
.al-modal-close:hover {
    background:var(--p2);
    color:var(--t1);
}
.al-modal-close svg {
    width:13px;
    height:13px;
}
.al-modal-body {
    overflow-y:auto;
}
.al-json {
    font-family:'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
    font-size:12px;
    color:#94a3b8;
    background:var(--bg);
    border:1px solid var(--bd);
    border-radius:8px;
    padding:14px 16px;
    white-space:pre-wrap;
    word-break:break-all;
    line-height:1.7;
}

/* ── Empty ──────────────────────────────────────────────────── */
.al-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.al-empty svg {
    width:40px;
    height:40px;
    opacity:.3;
}
.al-empty p {
    font-size:13px;
}

/* ── Footer / pagination ────────────────────────────────────── */
.al-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.al-footer-info {
    font-size:12px;
    color:var(--t2);
}
.al-pages {
    display:flex;
    align-items:center;
    gap:5px;
}
.al-page-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:30px;
    height:30px;
    padding:0 8px;
    border-radius:7px;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    color:var(--t2);
    background:none;
    font-family:inherit;
    cursor:pointer;
    border:1px solid transparent;
    transition:background .15s, border-color .15s, color .15s;
}
.al-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
.al-page-btn:not(.disabled):hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.al-page-btn.active {
    color:#fff;
    border-color:transparent;
}
.al-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
.al-per-page {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    color:var(--t2);
}
.al-per-page select {
    appearance:none;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:7px;
    padding:4px 22px 4px 9px;
    font-size:12px;
    font-weight:700;
    color:var(--t1);
    font-family:inherit;
    cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 6px center;
    outline:none;
}
</style>

{{-- ── Header ──────────────────────────────────────────────── --}}
<div class="al-header ala al1">
    <div class="al-header-text">
        <h1>Audit Log</h1>
        <p>Track every user action across the platform — logins, registrations, and security events.</p>
    </div>
    <button type="button" wire:click="exportCsv" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:1px solid var(--bd2, rgba(255,255,255,.13));background:transparent;color:inherit;">Export CSV</button>
</div>

{{-- ── Stat cards ──────────────────────────────────────────── --}}
@php
    $todayCount  = \App\Domains\Auth\Models\ActivityLog::whereDate('created_at', today())->count();
    $failedCount = \App\Domains\Auth\Models\ActivityLog::where('action', 'failed_login')
                      ->where('created_at', '>=', now()->subHours(24))->count();
    $totalCount  = \App\Domains\Auth\Models\ActivityLog::count();
@endphp
<div class="al-stats ala al2">
    <div class="al-stat">
        <div class="al-stat-icon" style="background:rgba(99,102,241,.15)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div class="al-stat-body">
            <div class="al-stat-val">{{ number_format($todayCount) }}</div>
            <div class="al-stat-lbl">Today's Events</div>
        </div>
    </div>
    <div class="al-stat">
        <div class="al-stat-icon" style="background:rgba(248,113,113,.15)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <div class="al-stat-body">
            <div class="al-stat-val" style="color:#f87171">{{ number_format($failedCount) }}</div>
            <div class="al-stat-lbl">Failed Logins (24h)</div>
        </div>
    </div>
    <div class="al-stat">
        <div class="al-stat-icon" style="background:rgba(16,185,129,.15)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        </div>
        <div class="al-stat-body">
            <div class="al-stat-val">{{ number_format($totalCount) }}</div>
            <div class="al-stat-lbl">Total Events</div>
        </div>
    </div>
</div>

{{-- ── Main card ───────────────────────────────────────────── --}}
<div class="al-card ala al3">

    {{-- Action tabs --}}
    <div class="al-toolbar">
        <div class="al-role-filter" tabindex="0">
            <button type="button" class="al-role-filter-btn">
                <span class="al-role-filter-dot" style="background:{{ $activeTab['color'] }}"></span>
                {{ $activeTab['label'] }}
                <svg class="al-role-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div class="al-role-filter-menu">
                @foreach ($tabs as $t)
                <button type="button" wire:click="selectAction('{{ $t['key'] }}')" @click="$el.blur()" class="al-role-filter-item {{ $t['active'] ? 'active' : '' }}" style="width:100%;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left">
                    <span class="al-role-filter-dot" style="background:{{ $t['color'] }}"></span>
                    <span class="al-role-filter-item-label">{{ $t['label'] }}</span>
                    @if ($t['key'] === 'failed_login')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    @endif
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Search + date filters --}}
    <div class="al-filters">
        <div class="al-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
            </svg>
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search user, email, IP…">
        </div>

        <div class="al-date-box">
            <label>From</label>
            <input type="date" wire:model.live="from" class="al-date-input">
        </div>
        <div class="al-date-box">
            <label>To</label>
            <input type="date" wire:model.live="to" class="al-date-input">
        </div>

        @if ($search || $from || $to)
            <button type="button" wire:click="clearFilters" class="al-clear-link" style="border:none;cursor:pointer;background:none;font-family:inherit">
                Clear filters
            </button>
        @endif
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;border-radius:0 0 12px 12px;" wire:loading.class="al-loading" wire:target="selectAction,gotoPage,search,from,to,setPerPage,clearFilters">
    <table class="al-table">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Date & Time</th>
                <th style="text-align:center">Data</th>
                <th style="text-align:center">View</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
            @php
                $as     = $actionStyle($log->action);
                $label  = $actions[$log->action]['label'] ?? (str_starts_with($log->action, 'settings.')
                    ? ucwords(str_replace('_', ' ', str_replace(['settings.', '_updated'], ['', ''], $log->action))) . ' Settings Updated'
                    : ucwords(str_replace(['.', '_'], ' ', $log->action)));
                $bgHex  = $log->user ? substr(md5($log->user->name ?? ''), 0, 6) : '64748b';
                $avUrl  = $log->user
                    ? 'https://ui-avatars.com/api/?name=' . urlencode($log->user->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64'
                    : null;
                $hasData = !empty($log->data);
            @endphp
            <tr onclick="Livewire.navigate('{{ route('filament.admin.resources.audit-log-entries.view', $log) }}')" style="cursor:pointer">
                {{-- Sequence --}}
                <td><span class="al-seq">{{ $log->id }}</span></td>

                {{-- User --}}
                <td>
                    @if ($log->user)
                        <div class="al-user">
                            <img src="{{ $avUrl }}" class="al-av" alt="">
                            <div>
                                <div class="al-uname">{{ $log->user->name }}</div>
                                <div class="al-uemail">{{ $log->user->email }}</div>
                            </div>
                        </div>
                    @else
                        <span class="al-guest">— Guest —</span>
                    @endif
                </td>

                {{-- Action badge --}}
                <td>
                    <span class="al-badge" style="background:{{ $as['bg'] }};color:{{ $as['color'] }}">
                        <span class="al-dot" style="background:{{ $as['color'] }}"></span>
                        {{ $label }}
                    </span>
                </td>

                {{-- IP --}}
                <td>
                    <div class="al-ip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>
                        </svg>
                        {{ $log->ip_address ?? '—' }}
                    </div>
                </td>

                {{-- User agent --}}
                <td>
                    <span class="al-ua" title="{{ $log->user_agent }}">
                        {{ $log->user_agent ? \Illuminate\Support\Str::limit($log->user_agent, 45) : '—' }}
                    </span>
                </td>

                {{-- Date --}}
                <td>
                    <div class="al-date">{{ $log->created_at?->setTimezone(config('app.timezone'))->format('M d, Y') }}</div>
                    <div class="al-time">{{ $log->created_at?->setTimezone(config('app.timezone'))->format('H:i:s') }}</div>
                </td>

                {{-- Data button --}}
                <td style="text-align:center" onclick="event.stopPropagation()">
                    @if ($hasData)
                        <button
                            class="al-data-btn"
                            title="View data"
                            onclick="alOpenModal({{ json_encode(json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }})"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/>
                            </svg>
                        </button>
                    @else
                        <span style="color:var(--t3);font-size:12px">—</span>
                    @endif
                </td>

                {{-- View button --}}
                <td style="text-align:center" onclick="event.stopPropagation()">
                    <a href="{{ route('filament.admin.resources.audit-log-entries.view', $log) }}"
                       wire:navigate
                       class="al-data-btn"
                       title="View full entry">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        </svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="al-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                        </svg>
                        <p>No audit log entries found{{ $search ? ' for "'.$search.'"' : '' }}.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Footer / pagination --}}
    <div class="al-footer">
        <div class="al-footer-info">
            @if ($total > 0)
                Showing {{ ($curPage - 1) * $perPage + 1 }}–{{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} events
            @else
                No results
            @endif
        </div>
        <div style="display:flex;align-items:center;gap:16px">
            <div class="al-per-page">
                Per page
                <select wire:change="setPerPage($event.target.value)">
                    @foreach([25, 50, 100] as $n)
                        <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            @if ($totalPages > 1)
            <div class="al-pages">
                <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                   class="al-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
                @for ($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                    <button type="button" wire:click="gotoPage({{ $p }})"
                       class="al-page-btn {{ $curPage === $p ? 'active' : '' }}"
                       style="{{ $curPage === $p ? 'background:' . $accent : '' }}">
                        {{ $p }}
                    </button>
                @endfor
                <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                   class="al-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>


{{-- ── JSON Data Modal ─────────────────────────────────────────── --}}
{{-- wire:ignore: this modal is opened/populated by plain JS (alOpenModal),
     untracked by Livewire. Without wire:ignore, any wire:click elsewhere on
     the page (pagination, filters) re-renders and morphs this div back to
     its server-rendered closed state while it's open. --}}
<div class="al-modal-backdrop" id="al-modal" wire:ignore onclick="if(event.target===this)alCloseModal()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9998;align-items:center;justify-content:center;">
    <div class="al-modal">
        <div class="al-modal-head">
            <span class="al-modal-title">Event Data</span>
            <button class="al-modal-close" onclick="alCloseModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="al-modal-body">
            <pre class="al-json" id="al-json-content"></pre>
        </div>
    </div>
</div>

<script>
function alOpenModal(json) {
    document.getElementById('al-json-content').textContent = json;
    document.getElementById('al-modal').classList.add('open');
    document.getElementById('al-modal').style.display = 'flex';
}
function alCloseModal() {
    document.getElementById('al-modal').classList.remove('open');
    document.getElementById('al-modal').style.display = 'none';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') alCloseModal(); });
</script>

@include('filament.pages.partials._csv-download-script')

</div>
</div>{{-- single root closes here --}}
