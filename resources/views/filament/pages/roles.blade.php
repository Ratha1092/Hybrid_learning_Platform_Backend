@php
    $createUrl  = route('filament.admin.resources.roles.create');
    $viewUrl    = fn($role) => route('filament.admin.resources.roles.view', ['record' => $role->id]);
    $editUrl    = fn($role) => route('filament.admin.resources.roles.edit', ['record' => $role->id]);

    $canCreate = auth()->user()?->can('roles.create') ?? false;
    $canUpdate = auth()->user()?->can('roles.update') ?? false;

    $protectedRoles = \App\Filament\Resources\Roles\RoleResource::protectedRoleNames();
    $undeletableRoles = \App\Filament\Resources\Roles\RoleResource::undeletableRoleNames();

    $roleColor = fn($name) => match ($name) {
        'super-admin' => '#dc2626',
        'finance'     => '#0d9488',
        'instructor'  => '#3b82f6',
        'student'     => '#10b981',
        default       => '#6366f1',
    };

    $accent = '#6366f1';
@endphp

<div>
<div class="lp" id="lp-roles">

<style>
.lp, .lp *, .lp *::before, .lp *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.lp {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:48px;
    display:grid;
    gap:20px;
    --accent:#6366f1;
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
html:not(.dark) .lp {
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

@keyframes lpUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.lpa {
    opacity:0;
    animation:lpUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.lp1 {
    animation-delay:.04s;
}
.lp2 {
    animation-delay:.09s;
}

.lp-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.lp-header-text h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.15;
}
.lp-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.lp-header-btns {
    display:flex;
    align-items:center;
    gap:10px;
    flex-shrink:0;
}
.lp-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    letter-spacing:.02em;
    cursor:pointer;
    text-decoration:none;
    transition:opacity .18s, transform .15s;
    white-space:nowrap;
    border:none;
    font-family:inherit;
}
.lp-btn:hover {
    opacity:.85;
    transform:translateY(-1px);
}
.lp-btn-primary {
    background:var(--accent);
    color:#fff;
}
.lp-btn-gray {
    background:var(--p2);
    color:var(--t1);
    border:1px solid var(--bd2);
}

.lp-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    box-shadow:var(--sh);
}

.lp-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.lp-search-box {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.lp-search-box svg {
    width:14px;
    height:14px;
    color:var(--t2);
    flex-shrink:0;
}
.lp-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.lp-search-box input::placeholder {
    color:var(--t2);
}

.lp-table {
    width:100%;
    border-collapse:collapse;
}
.lp-table thead tr {
    border-bottom:1px solid var(--bd);
}
.lp-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.lp-table th.th-center {
    text-align:center;
}
.lp-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.lp-table tbody tr:last-child {
    border-bottom:none;
}
.lp-table tbody tr:hover {
    background:var(--p2);
}
.lp-row-link {
    cursor:pointer;
}
.lp-table td {
    padding:12px 12px;
    vertical-align:middle;
}

.lp-id {
    font-size:11.5px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.lp-role-cell {
    display:flex;
    align-items:center;
    gap:10px;
}
.lp-role-dot {
    width:9px;
    height:9px;
    border-radius:50%;
    flex-shrink:0;
}
.lp-role-name {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.lp-role-system {
    font-size:10.5px;
    font-weight:700;
    color:var(--t2);
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:5px;
    padding:1px 6px;
    margin-left:6px;
}
.lp-count-cell {
    text-align:center;
}
.lp-count-val {
    display:inline-flex;
    align-items:center;
    gap:5px;
    font-size:12.5px;
    font-weight:600;
    color:var(--t1);
}
.lp-count-val svg {
    width:13px;
    height:13px;
    color:var(--t2);
}
.lp-date {
    font-size:12px;
    color:var(--t2);
    white-space:nowrap;
}

.lp-actions {
    display:flex;
    align-items:center;
    gap:4px;
    justify-content:flex-end;
}
.lp-act-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    border-radius:7px;
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    color:var(--t2);
    text-decoration:none;
    transition:background .15s, border-color .15s, color .15s;
}
.lp-act-btn:hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.lp-act-btn svg {
    width:14px;
    height:14px;
}
.lp-act-btn-danger:hover {
    background:rgba(220,38,38,.12);
    border-color:rgba(220,38,38,.35);
    color:#dc2626;
}

.lp-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.lp-empty svg {
    width:40px;
    height:40px;
    opacity:.35;
}
.lp-empty p {
    font-size:13px;
}

.lp-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.lp-footer-info {
    font-size:12px;
    color:var(--t2);
}
.lp-pages {
    display:flex;
    align-items:center;
    gap:6px;
}
.lp-page-btn {
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
.lp-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
.lp-page-btn:not(.disabled):hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.lp-page-btn.active {
    background:var(--accent);
    color:#fff;
    border-color:transparent;
}
.lp-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
.lp-per-page {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    color:var(--t2);
}
.lp-per-page select {
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

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Roles</h1>
            <p>Manage roles and the permissions assigned to each one.</p>
        </div>
        @if($canCreate)
        <div class="lp-header-btns">
            <a href="{{ $createUrl }}" wire:navigate class="lp-btn lp-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Role
            </a>
        </div>
        @endif
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar: search --}}
        <div class="lp-toolbar">
            <div></div>
            <div class="lp-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Filter roles...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;border-radius:0 0 12px 12px;" wire:loading.class="lp-loading" wire:target="gotoPage,search,setPerPage">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th class="th-center">Permissions</th>
                    <th class="th-center">Users</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                @php
                    $color = $roleColor($role->name);
                    $isSystem = in_array($role->name, $protectedRoles, true);
                    $isUndeletable = in_array($role->name, $undeletableRoles, true);
                @endphp
                <tr class="lp-row-link" onclick="Livewire.navigate('{{ $viewUrl($role) }}')">
                    <td><span class="lp-id">{{ $role->id }}</span></td>

                    <td>
                        <div class="lp-role-cell">
                            <span class="lp-role-dot" style="background:{{ $color }}"></span>
                            <span class="lp-role-name">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                            @if($isSystem)
                                <span class="lp-role-system">System</span>
                            @endif
                        </div>
                    </td>

                    <td class="lp-count-cell">
                        <span class="lp-count-val">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                            </svg>
                            {{ number_format($role->permissions_count) }}
                        </span>
                    </td>

                    <td class="lp-count-cell">
                        <span class="lp-count-val">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            {{ number_format($role->users_count) }}
                        </span>
                    </td>

                    <td><span class="lp-date">{{ $role->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="lp-actions">
                            <a href="{{ $viewUrl($role) }}" wire:navigate class="lp-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            @if($canUpdate)
                            <a href="{{ $editUrl($role) }}" wire:navigate class="lp-act-btn" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/>
                                </svg>
                            </a>
                            @endif
                            @if($canDelete && !$isUndeletable)
                            <button type="button" class="lp-act-btn lp-act-btn-danger" title="Delete"
                                wire:click="deleteRole({{ $role->id }})"
                                wire:confirm="Delete the &quot;{{ \Illuminate\Support\Str::headline($role->name) }}&quot; role? This cannot be undone.">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <p>No roles found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="lp-footer">
            <div class="lp-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} roles
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="lp-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="lp-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="lp-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="lp-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="lp-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
</div>
