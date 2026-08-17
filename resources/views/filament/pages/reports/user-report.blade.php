@php
    $roleLabel = fn(string $r) => match($r) {
        'student' => 'Student', 'instructor' => 'Instructor',
        'super-admin' => 'Super Admin', 'finance' => 'Finance',
        default => ucwords(str_replace('-', ' ', $r)),
    };
    $statusColor = fn(string $s) => match($s) {
        'active' => '#34d399', 'suspended' => '#f87171',
        default => '#94a3b8',
    };
@endphp


<style>
.rp, .rp *, .rp *::before, .rp *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.rp {
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
    --accent:#2563EB;
    color:var(--t1);
}
html:not(.dark) .rp {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#cbd5e1;
}
.rp-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.rp-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.rp-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.rp-actions {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}
.rp-btn {
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
.rp-btn:hover {
    background:var(--p2);
}
.rp-btn-primary {
    background:var(--accent);
    color:#fff;
    border-color:transparent;
}
.rp-btn-primary:hover {
    background:#c4923a !important;
    color:#fff !important;
}
.rp-filters {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}
.rp-filter-select {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:7px 10px;
    font-size:12px;
    color:var(--t1);
}
.rp-kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
    gap:12px;
}
.rp-kpi-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:16px;
}
.rp-kpi-label {
    font-size:10.5px;
    font-weight:700;
    letter-spacing:.05em;
    text-transform:uppercase;
    color:var(--t2);
    margin-bottom:6px;
}
.rp-kpi-value {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.rp-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}
@media (max-width: 900px) {
    .rp-grid-2 {
        grid-template-columns:1fr;
    }
}
.rp-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:16px;
}
.rp-card h3 {
    font-size:13px;
    font-weight:700;
    margin-bottom:12px;
    color:var(--t1);
}
.rp-bar-row {
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:8px;
    font-size:12px;
}
.rp-bar-label {
    width:120px;
    flex-shrink:0;
    color:var(--t2);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
.rp-bar-track {
    flex:1;
    height:8px;
    background:var(--p2);
    border-radius:99px;
    overflow:hidden;
}
.rp-bar-fill {
    height:100%;
    border-radius:99px;
}
.rp-bar-value {
    width:50px;
    text-align:right;
    flex-shrink:0;
    font-weight:700;
    color:var(--t1);
}
.rp-table-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow-x:auto;
}
.rp-table {
    width:100%;
    border-collapse:collapse;
}
.rp-table thead tr {
    border-bottom:1px solid var(--bd);
}
.rp-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.rp-table td {
    padding:10px 12px;
    border-bottom:1px solid var(--bd);
    font-size:12.5px;
    color:var(--t1);
}
.rp-status-pill {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 9px;
    border-radius:99px;
    font-size:11px;
    font-weight:700;
}
.rp-role-pill {
    display:inline-flex;
    align-items:center;
    padding:2px 8px;
    border-radius:99px;
    font-size:10.5px;
    font-weight:700;
    background:var(--p2);
    color:var(--t2);
    margin-right:3px;
}
.rp-pagination {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    font-size:12px;
    color:var(--t2);
}
/* date range pill */
.rp-drp-wrap {
    position:relative;
}
.rp-drp-pill {
    display:inline-flex;
    align-items:center;
    background:var(--p1);
    border:1.5px solid #14B8A6;
    border-radius:10px;
    padding:.38rem .65rem;
    cursor:pointer;
    user-select:none;
    white-space:nowrap;
    box-shadow:0 0 0 3px rgba(20,184,166,.08);
    transition:box-shadow .15s;
    font-size:12px;
    color:var(--t1);
}
.rp-drp-pill:hover {
    box-shadow:0 0 0 4px rgba(20,184,166,.14);
}
.rp-drp-icon {
    color:#14B8A6;
    margin-right:.4rem;
    flex-shrink:0;
}
.rp-drp-from,.rp-drp-to {
    font-weight:700;
}
.rp-drp-sep {
    margin:0 .4rem;
    color:var(--bd2);
    font-weight:300;
}
.rp-drp-chev {
    margin-left:.45rem;
    color:#14B8A6;
    transition:transform .2s;
    flex-shrink:0;
}
.rp-drp-chev.open {
    transform:rotate(180deg);
}
.rp-drp-panel {
    display:none;
    position:absolute;
    right:0;
    top:calc(100% + 8px);
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,.25);
    padding:1rem;
    min-width:255px;
    z-index:300;
}
.rp-drp-panel.open {
    display:block;
}
.rp-drp-presets {
    display:flex;
    flex-direction:column;
    gap:2px;
    margin-bottom:.65rem;
}
.rp-drp-pre {
    display:block;
    width:100%;
    text-align:left;
    background:none;
    border:none;
    padding:.38rem .55rem;
    border-radius:6px;
    font-size:11.5px;
    font-weight:500;
    color:var(--t2);
    cursor:pointer;
    font-family:inherit;
    transition:background .12s;
}
.rp-drp-pre:hover {
    background:rgba(20,184,166,.06);
    color:var(--t1);
}
.rp-drp-pre.active {
    background:rgba(20,184,166,.1);
    color:#14B8A6;
    font-weight:700;
}
.rp-drp-hr {
    border:none;
    border-top:1px solid var(--bd);
    margin:.45rem 0;
}
.rp-drp-cus-lbl {
    font-size:9.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    margin-bottom:.35rem;
}
.rp-drp-cus-row {
    display:flex;
    align-items:center;
    gap:.35rem;
}
.rp-drp-dt {
    flex:1;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:6px;
    padding:.28rem .4rem;
    font-size:11.5px;
    color:var(--t1);
    outline:none;
}
.rp-drp-dt:focus {
    border-color:#14B8A6;
}
.rp-drp-dt-sep {
    color:var(--t2);
    font-size:11px;
    flex-shrink:0;
}
.rp-drp-btns {
    display:flex;
    gap:.4rem;
    margin-top:.65rem;
}
.rp-drp-apply {
    flex:1;
    background:#14B8A6;
    color:#fff;
    border:none;
    border-radius:6px;
    padding:.35rem .65rem;
    font-size:11.5px;
    font-weight:700;
    cursor:pointer;
    font-family:inherit;
}
.rp-drp-apply:hover {
    opacity:.88;
}
.rp-drp-reset {
    background:transparent;
    color:var(--t2);
    border:1px solid var(--bd2);
    border-radius:6px;
    padding:.35rem .55rem;
    font-size:11.5px;
    cursor:pointer;
    font-family:inherit;
}
.rp-drp-reset:hover {
    border-color:#14B8A6;
    color:#14B8A6;
}
</style>

<div>
<div class="rp">
    <div class="rp-header">
        <div>
            <h1>User Report</h1>
            <p>User growth, roles, and activity across the platform.</p>
        </div>
        <div class="rp-actions">
            <div class="rp-drp-wrap" wire:key="rp-drp-user" x-data="rpDate({preset:'{{ $activePreset }}',from:'{{ $activeDateFrom }}',to:'{{ $activeDateTo }}',extras:{role:'{{ $activeRole }}',status:'{{ $activeStatus }}'}})" data-rp-preset="{{ $activePreset }}" data-rp-from="{{ $activeDateFrom }}" data-rp-to="{{ $activeDateTo }}" @click.outside="open=false">
                <div class="rp-drp-pill" @click="open=!open">
                    <span class="rp-drp-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    <span class="rp-drp-from" x-text="pFrom"></span>
                    <span class="rp-drp-sep">|</span>
                    <span class="rp-drp-to" x-text="pTo"></span>
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
            <a href="{{ route('admin.reports.csv', array_filter(['type' => 'user', 'preset' => $activePreset, 'date_from' => $activePreset === 'custom' ? $activeDateFrom : null, 'date_to' => $activePreset === 'custom' ? $activeDateTo : null, 'role' => $activeRole !== 'all' ? $activeRole : null, 'status' => $activeStatus !== 'all' ? $activeStatus : null])) }}" class="rp-btn">Export CSV</a>
            <a href="{{ route('admin.reports.pdf', array_filter(['type' => 'user', 'preset' => $activePreset, 'date_from' => $activePreset === 'custom' ? $activeDateFrom : null, 'date_to' => $activePreset === 'custom' ? $activeDateTo : null, 'role' => $activeRole !== 'all' ? $activeRole : null, 'status' => $activeStatus !== 'all' ? $activeStatus : null])) }}" class="rp-btn">Export PDF</a>
            @if(\App\Support\PanelAccess::can('reports.schedule'))
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-schedule-modal'))" class="rp-btn rp-btn-primary">Schedule</button>
            @endif
        </div>
    </div>

    <div class="rp-filters">
        <select wire:model.live="role" class="rp-filter-select">
            <option value="all">All Roles</option>
            @foreach(['student','instructor','super-admin','finance'] as $r)
                <option value="{{ $r }}">{{ $roleLabel($r) }}</option>
            @endforeach
        </select>
        <select wire:model.live="status" class="rp-filter-select">
            <option value="all">All Statuses</option>
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    <div class="rp-kpi-grid">
        <div class="rp-kpi-card"><div class="rp-kpi-label">New Users</div><div class="rp-kpi-value">{{ number_format($kpis['totalNew']) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Active</div><div class="rp-kpi-value">{{ number_format($kpis['activeCount']) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Suspended</div><div class="rp-kpi-value">{{ number_format($kpis['suspendedCount']) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Email Verified</div><div class="rp-kpi-value">{{ number_format($kpis['verifiedCount']) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">New Enrollments</div><div class="rp-kpi-value">{{ number_format($kpis['newEnrollments']) }}</div></div>
    </div>

    <div class="rp-grid-2">
        <div class="rp-card">
            <h3>By Role</h3>
            @php $roleMax = max(1, max($roleBreakdown)); @endphp
            @foreach($roleBreakdown as $r => $count)
                @if($count > 0)
                <div class="rp-bar-row">
                    <div class="rp-bar-label">{{ $roleLabel($r) }}</div>
                    <div class="rp-bar-track"><div class="rp-bar-fill" style="width:{{ max(4, ($count/$roleMax)*100) }}%;background:#2563EB"></div></div>
                    <div class="rp-bar-value">{{ number_format($count) }}</div>
                </div>
                @endif
            @endforeach
        </div>
        <div class="rp-card">
            <h3>By Status</h3>
            @php $stMax = max(1, max($statusBreakdown)); @endphp
            @foreach($statusBreakdown as $st => $count)
                <div class="rp-bar-row">
                    <div class="rp-bar-label">{{ ucfirst($st) }}</div>
                    <div class="rp-bar-track"><div class="rp-bar-fill" style="width:{{ $count > 0 ? max(4, ($count/$stMax)*100) : 0 }}%;background:{{ $statusColor($st) }}"></div></div>
                    <div class="rp-bar-value">{{ number_format($count) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rp-table-card">
        <table class="rp-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Enrollments</th><th>Orders</th><th>Last Login</th><th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td style="color:var(--t2)">{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="rp-role-pill">{{ $roleLabel($role->name) }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="rp-status-pill" style="background:{{ $statusColor($user->status ?? 'active') }}22;color:{{ $statusColor($user->status ?? 'active') }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td>{{ number_format($user->enrollments_count) }}</td>
                        <td>{{ number_format($user->orders_count) }}</td>
                        <td>{{ $user->last_login_at?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:var(--t2);padding:24px;">No users found for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="rp-pagination">
            <span>Showing {{ $total > 0 ? (($curPage-1)*$perPage)+1 : 0 }}–{{ min($curPage*$perPage, $total) }} of {{ number_format($total) }}</span>
            <div style="display:flex;gap:6px;">
                @if($curPage > 1)<button type="button" wire:click="$set('page', {{ $curPage-1 }})" class="rp-btn">Prev</button>@endif
                @if($curPage < $totalPages)<button type="button" wire:click="$set('page', {{ $curPage+1 }})" class="rp-btn">Next</button>@endif
            </div>
        </div>
    </div>
</div>

@if(\App\Support\PanelAccess::can('reports.schedule'))
    @include('filament.pages.partials._schedule-modal', ['reportLabel' => 'User'])
@endif

</div>
