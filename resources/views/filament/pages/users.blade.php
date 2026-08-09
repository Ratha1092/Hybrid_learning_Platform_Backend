@php
    $activeTab = collect($tabs ?? [])->firstWhere('key', $tab) ?? ($tabs[0] ?? ['key' => 'all', 'label' => 'All', 'count' => 0, 'color' => '#2563eb']);

    $accent = '#2563eb';

    $roleStyle = fn($role) => match($role) {
        'super-admin'     => ['bg' => 'rgba(220,38,38,.12)',  'color' => '#dc2626', 'label' => 'Super Admin'],
        'admin'           => ['bg' => 'rgba(168,85,247,.12)', 'color' => '#a855f7', 'label' => 'Admin'],
        'finance-manager' => ['bg' => 'rgba(13,148,136,.12)', 'color' => '#0d9488', 'label' => 'Finance Manager'],
        'accountant'      => ['bg' => 'rgba(13,148,136,.12)', 'color' => '#0d9488', 'label' => 'Accountant'],
        'content-manager' => ['bg' => 'rgba(217,119,6,.12)',  'color' => '#d97706', 'label' => 'Content Manager'],
        'moderator'       => ['bg' => 'rgba(217,119,6,.12)',  'color' => '#d97706', 'label' => 'Moderator'],
        'support-staff'   => ['bg' => 'rgba(59,130,246,.12)', 'color' => '#3b82f6', 'label' => 'Support Staff'],
        'instructor'      => ['bg' => 'rgba(59,130,246,.12)', 'color' => '#3b82f6', 'label' => 'Instructor'],
        'student'         => ['bg' => 'rgba(16,185,129,.12)', 'color' => '#10b981', 'label' => 'Student'],
        default           => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => $role ? ucfirst($role) : '—'],
    };

    $statusStyle = fn($status) => match($status) {
        'active'    => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Active'],
        'suspended' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Suspended'],
        default     => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($status ?? 'Active')],
    };

    $createUrl = route('filament.admin.resources.users.create');
    $viewUrl   = fn($u) => route('filament.admin.resources.users.view', ['record' => $u->id]);
    $editUrl   = fn($u) => route('filament.admin.resources.users.edit', ['record' => $u->id]);
@endphp

<div>
<div class="lp" id="lp-users" style="--accent:{{ $accent }}">

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
    color:#fff;
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
.lp-toolbar > form {
    flex-shrink:0;
}
.lp-tab-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:18px;
    height:18px;
    padding:0 5px;
    border-radius:5px;
    font-size:10px;
    font-weight:800;
}

.lp-role-filter {
    position:relative;
    flex-shrink:0;
}
.lp-role-filter-btn {
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
.lp-role-filter-btn:hover {
    background:var(--p3,var(--p2));
    border-color:var(--bd2);
}
.lp-role-filter-dot {
    width:8px;
    height:8px;
    border-radius:50%;
    flex-shrink:0;
}
.lp-role-filter-chevron {
    width:13px;
    height:13px;
    color:var(--t2);
    transition:transform .15s;
    margin-left:2px;
}
.lp-role-filter:focus-within .lp-role-filter-chevron {
    transform:rotate(180deg);
}
.lp-role-filter-menu {
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
.lp-role-filter:focus-within .lp-role-filter-menu {
    display:block;
}
.lp-role-filter-item {
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
.lp-role-filter-item:hover {
    background:var(--p2);
}
.lp-role-filter-item.active {
    background:var(--p2);
}
.lp-role-filter-item-label {
    flex:1 1 auto;
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
    font-size:11.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
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
    font-size:12.5px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.lp-user-cell {
    display:flex;
    align-items:center;
    gap:10px;
}
.lp-user-avatar {
    width:32px;
    height:32px;
    border-radius:50%;
    flex-shrink:0;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:11px;
    font-weight:800;
    letter-spacing:0;
    box-shadow:inset 0 0 0 1px rgba(255,255,255,.14);
}
.lp-user-name {
    font-size:14.5px;
    font-weight:650;
    color:var(--t1);
}
.lp-email {
    font-size:13.5px;
    color:var(--t2);
}
.lp-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:12.5px;
    font-weight:700;
    white-space:nowrap;
}
.lp-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
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
    font-family:inherit;
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

/* ── Dropdown menu ── */
.lp-menu-wrap {
    position:relative;
}
.lp-menu {
    position:absolute;
    right:0;
    top:34px;
    z-index:50;
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:10px;
    box-shadow:0 8px 32px rgba(0,0,0,.3);
    min-width:162px;
    overflow:hidden;
}
.lp-menu-up {
    top:auto;
    bottom:34px;
}
html:not(.dark) .lp-menu {
    box-shadow:0 4px 20px rgba(15,23,42,.18);
}
.lp-menu-item {
    display:flex;
    align-items:center;
    gap:9px;
    width:100%;
    padding:9px 14px;
    font-size:12.5px;
    font-weight:600;
    color:var(--t1);
    background:none;
    border:none;
    cursor:pointer;
    font-family:inherit;
    transition:background .12s;
    text-align:left;
    white-space:nowrap;
    text-decoration:none;
}
.lp-menu-item svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.lp-menu-item:hover {
    background:var(--p2);
}
.lp-menu-item.warn {
    color:#f59e0b;
}
.lp-menu-item.warn:hover {
    background:rgba(245,158,11,.08);
}
.lp-menu-item.ok {
    color:#34d399;
}
.lp-menu-item.ok:hover {
    background:rgba(52,211,153,.08);
}
.lp-menu-item.danger {
    color:#f87171;
}
.lp-menu-item.danger:hover {
    background:rgba(248,113,113,.08);
}
.lp-menu-div {
    height:1px;
    background:var(--bd);
    margin:4px 0;
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
            <h1>Users</h1>
            <p>Manage all platform users — admins, instructors, and students.</p>
        </div>
        <div class="lp-header-btns">
            <a href="{{ $createUrl }}" wire:navigate class="lp-btn lp-btn-primary" style="background:{{ $accent }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New User
            </a>
        </div>
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar --}}
        <div class="lp-toolbar">
            <div class="lp-role-filter" tabindex="0">
                <button type="button" class="lp-role-filter-btn">
                    <span class="lp-role-filter-dot" style="background:{{ $activeTab['color'] }}"></span>
                    {{ $activeTab['label'] }}
                    <span class="lp-tab-badge" style="background:{{ $activeTab['color'] }}20;color:{{ $activeTab['color'] }}">{{ $activeTab['count'] }}</span>
                    <svg class="lp-role-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div class="lp-role-filter-menu">
                    @foreach ($tabs as $t)
                    <button type="button" wire:click="selectTab('{{ $t['key'] }}')" @click="$el.blur()" class="lp-role-filter-item {{ $tab === $t['key'] ? 'active' : '' }}" style="width:100%;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left">
                        <span class="lp-role-filter-dot" style="background:{{ $t['color'] }}"></span>
                        <span class="lp-role-filter-item-label">{{ $t['label'] }}</span>
                        <span class="lp-tab-badge" style="background:{{ $t['color'] }}20;color:{{ $t['color'] }}">{{ $t['count'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="lp-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search name or email...">
            </div>
        </div>

        {{-- Table --}}
        <div class="lp-table-scroll" style="border-radius:0 0 12px 12px;overflow-x:auto;-webkit-overflow-scrolling:touch;" wire:loading.class="lp-loading" wire:target="selectTab,gotoPage,search,perPage">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                @php
                    $rs   = $roleStyle($user->getRoleNames()->first());
                    $ss   = $statusStyle($user->status);
                    $bgHex = substr(md5($user->name ?? ''), 0, 6);
                    $initials = collect(explode(' ', trim($user->name ?? '')))
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => mb_substr($part, 0, 1))
                        ->implode('');
                    $initials = mb_strtoupper($initials ?: '?');
                    $avatarUrl = $user->avatar_url;
                    $isSelf = $user->id === auth()->id();
                    $isSuspended = $user->status === 'suspended';
                @endphp
                <tr class="lp-row-link" onclick="Livewire.navigate('{{ $viewUrl($user) }}')">
                    <td><span class="lp-id">{{ $user->id }}</span></td>

                    <td>
                        <div class="lp-user-cell">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" class="lp-user-avatar" style="object-fit:cover" alt="">
                            @else
                                <span class="lp-user-avatar" style="background:#{{ $bgHex }}">{{ $initials }}</span>
                            @endif
                            <span class="lp-user-name">{{ $user->name }}</span>
                        </div>
                    </td>

                    <td><span class="lp-email">{{ $user->email }}</span></td>

                    <td>
                        <span class="lp-badge" style="background:{{ $rs['bg'] }};color:{{ $rs['color'] }}">
                            {{ $rs['label'] }}
                        </span>
                    </td>

                    <td>
                        <span class="lp-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="lp-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>


                    <td onclick="event.stopPropagation()">
                        <div class="lp-actions">
                            <div class="lp-menu-wrap"
                                 x-data="{
                                    open: false,
                                    openUp: false,
                                    toggle() {
                                        this.open = !this.open;
                                        if (this.open) {
                                            this.$dispatch('lp-menu-open', {{ $user->id }});
                                            this.$nextTick(() => {
                                                const wrap = this.$el;
                                                const menu = this.$refs.menu;
                                                const scroller = wrap.closest('.lp-table-scroll');
                                                if (!scroller) return;
                                                const wrapRect = wrap.getBoundingClientRect();
                                                const scrollerRect = scroller.getBoundingClientRect();
                                                this.openUp = (wrapRect.bottom + menu.offsetHeight + 10) > scrollerRect.bottom;
                                            });
                                        }
                                    }
                                 }"
                                 @click.outside="open = false"
                                 @lp-menu-open.window="if ($event.detail !== {{ $user->id }}) open = false">
                                <button type="button" class="lp-act-btn" @click.stop="toggle()" title="Actions">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M4.5 12a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0zm6 0a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0zm6 0a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0z" clip-rule="evenodd"/></svg>
                                </button>

                                <div class="lp-menu" :class="openUp ? 'lp-menu-up' : ''" x-show="open" x-ref="menu"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     style="display:none">

                                    <a href="{{ $viewUrl($user) }}" wire:navigate class="lp-menu-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/></svg>
                                        View
                                    </a>
                                    <a href="{{ $editUrl($user) }}" wire:navigate class="lp-menu-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                                        Edit
                                    </a>

                                    @if(!$isSelf && !$user->hasRole('super-admin'))
                                    <div class="lp-menu-div"></div>

                                    @if($isSuspended)
                                    <button type="button" class="lp-menu-item ok"
                                        @click="open=false"
                                        wire:click="unsuspendUser({{ $user->id }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                        Unsuspend
                                    </button>
                                    @else
                                    <button type="button" class="lp-menu-item warn"
                                        @click="open=false"
                                        wire:click="suspendUser({{ $user->id }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Suspend
                                    </button>
                                    @endif

                                    <button type="button" class="lp-menu-item danger"
                                        @click="open=false"
                                        wire:click="removeUser({{ $user->id }})"
                                        wire:confirm="Remove {{ $user->name }}? This will soft-delete their account.">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        Remove
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            <p>No users found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
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
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} users
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="lp-per-page">
                    Per page
                    <select wire:model.live="perPage">
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
