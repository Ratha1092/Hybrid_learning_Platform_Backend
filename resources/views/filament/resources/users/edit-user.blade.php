@php
    /** @var \App\Domains\Users\Models\User $record */
    $user = $record;

    $backUrl = route('filament.admin.pages.users');
    $viewUrl = url('/admin/users/' . $user->id);

    $statusVal = $user->status ?? 'active';
    $statusStyle = match ($statusVal) {
        'active'    => ['bg' => 'rgba(22,163,74,.1)',   'color' => '#16a34a', 'dot' => '#22c55e', 'label' => 'Active'],
        'suspended' => ['bg' => 'rgba(220,38,38,.1)',   'color' => '#dc2626', 'dot' => '#ef4444', 'label' => 'Suspended'],
        default     => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'dot' => '#94a3b8', 'label' => ucfirst($statusVal)],
    };

    $role = $user->getRoleNames()->first() ?? '';
    $roleStyle = match ($role) {
        'super-admin'      => ['bg' => 'rgba(220,38,38,.1)',   'color' => '#dc2626', 'label' => 'Super Admin'],
        'admin'            => ['bg' => 'rgba(124,58,237,.1)',  'color' => '#7c3aed', 'label' => 'Admin'],
        'finance-manager'  => ['bg' => 'rgba(13,148,136,.1)',  'color' => '#0d9488', 'label' => 'Finance Manager'],
        'accountant'       => ['bg' => 'rgba(13,148,136,.1)',  'color' => '#0d9488', 'label' => 'Accountant'],
        'content-manager'  => ['bg' => 'rgba(217,119,6,.1)',   'color' => '#d97706', 'label' => 'Content Manager'],
        'moderator'        => ['bg' => 'rgba(217,119,6,.1)',   'color' => '#d97706', 'label' => 'Moderator'],
        'support-staff'    => ['bg' => 'rgba(37,99,235,.1)',   'color' => '#2563eb', 'label' => 'Support Staff'],
        'instructor'       => ['bg' => 'rgba(245,158,11,.1)',  'color' => '#d97706', 'label' => 'Instructor'],
        'student'          => ['bg' => 'rgba(37,99,235,.1)',   'color' => '#2563eb', 'label' => 'Student'],
        default            => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'label' => $role ? ucfirst($role) : '—'],
    };

    $nameParts = explode(' ', trim($user->name ?? '?'));
    $initials  = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr(end($nameParts), 0, 1));
    $avatarBg  = '#' . substr(md5($user->name ?? ''), 0, 6);
    $avatarUrl = $user->avatar_url ?? null;

    $availableRoles = \Spatie\Permission\Models\Role::orderBy('name')->get();
    $selectedRoles  = (array) ($data['roles'] ?? []);

    $isSelf    = $user->id === auth()->id();
    $isSuspended = ($data['status'] ?? $user->status) === 'suspended';
    $canAct    = !$isSelf && (!$user->hasRole('super-admin') || auth()->user()?->hasRole('super-admin'));
@endphp

<div class="uv">

<style>
.uv,.uv *,.uv *::before,.uv *::after{box-sizing:border-box;margin:0;padding:0}
.uv{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;
    display:grid;gap:20px;padding-bottom:48px;
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 1px 4px rgba(15,23,42,.06),0 4px 16px rgba(15,23,42,.06);
    --accent:#7c3aed;--radius:14px;
    color:var(--t1);
}
html.dark .uv{
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
}

/* ── Header ── */
.uv-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.uv-page-title{font-size:clamp(22px,2.6vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.uv-header-actions{display:flex;align-items:center;gap:8px}

/* ── Buttons ── */
.uv-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.uv-btn svg{width:14px;height:14px;flex-shrink:0}
.uv-btn-gray{background:var(--p1);border:1px solid var(--bd2);color:var(--t2)}
.uv-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.uv-btn-purple{background:var(--accent);color:#fff;border:1px solid transparent}
.uv-btn-purple:hover{background:#6d28d9}
.uv-btn-outline{background:transparent;border:1px solid var(--bd2);color:var(--t2)}
.uv-btn-outline:hover{border-color:var(--accent);color:var(--accent)}
.uv-btn:disabled{opacity:.55;cursor:not-allowed}

/* ── Hero ── */
.uv-hero{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);box-shadow:var(--sh);padding:24px 28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.uv-avatar-initials{width:80px;height:80px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;letter-spacing:-.01em;flex-shrink:0}
.uv-avatar-img{width:80px;height:80px;border-radius:16px;object-fit:cover;border:2px solid var(--bd2)}
.uv-hero-info{flex:1;min-width:0}
.uv-hero-name{font-size:clamp(18px,2vw,22px);font-weight:800;color:var(--t1);letter-spacing:-.02em;margin-bottom:8px}
.uv-badges-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.uv-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:700}
.uv-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.uv-hero-meta{display:flex;align-items:center;gap:32px;margin-left:auto;flex-shrink:0}
.uv-hero-stat{text-align:right}
.uv-hero-stat-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--t2);margin-bottom:4px}
.uv-hero-stat-value{font-size:20px;font-weight:800;color:var(--t1);letter-spacing:-.01em}

/* ── Cards ── */
.uv-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:860px){.uv-grid-2{grid-template-columns:1fr}}
.uv-card{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);overflow:hidden;box-shadow:var(--sh)}
.uv-card-header{padding:16px 20px;border-bottom:1px solid var(--bd);display:flex;align-items:flex-start;gap:12px}
.uv-card-icon{width:36px;height:36px;border-radius:9px;background:var(--p2);border:1px solid var(--bd);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.uv-card-icon svg{width:17px;height:17px;color:var(--t2)}
.uv-card-title{font-size:14px;font-weight:700;color:var(--t1)}
.uv-card-subtitle{font-size:12px;color:var(--t2);margin-top:2px}
.uv-card-body{padding:20px}
.uv-card-body+.uv-card-body{padding-top:0}

/* ── Form fields ── */
.uv-field-group{display:flex;flex-direction:column;gap:6px}
.uv-field-group+.uv-field-group{margin-top:16px}
.uv-label{font-size:12px;font-weight:700;color:var(--t2);text-transform:uppercase;letter-spacing:.06em}
.uv-input{width:100%;padding:9px 13px;border-radius:8px;border:1px solid var(--bd2);background:var(--p2);color:var(--t1);font-size:13px;font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s}
.uv-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(124,58,237,.12)}
.uv-input::placeholder{color:var(--t3)}
html.dark .uv-input{background:rgba(255,255,255,.04);border-color:var(--bd2)}
html.dark .uv-input:hover{border-color:var(--accent)}
html.dark .uv-input:focus{background:rgba(255,255,255,.06)}
html.dark .uv-role-chip:hover .uv-role-label{background:rgba(124,58,237,.12)}
[x-cloak]{display:none!important}
.uv-dd-enter{transition:opacity .1s ease,transform .1s ease;transform-origin:top}
.uv-dd-leave{transition:opacity .1s ease,transform .1s ease;transform-origin:top}
select.uv-input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;padding-right:34px;cursor:pointer}
.uv-error{font-size:11.5px;color:#dc2626;font-weight:500;display:flex;align-items:center;gap:4px}
.uv-error::before{content:'!';display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;border-radius:50%;background:#dc2626;color:#fff;font-size:9px;font-weight:800;flex-shrink:0}

/* ── Input with trailing button (password reveal) ── */
.uv-input-wrap{position:relative;display:flex;align-items:center}
.uv-input-wrap .uv-input{padding-right:40px}
.uv-reveal-btn{position:absolute;right:10px;background:none;border:none;cursor:pointer;color:var(--t2);padding:4px;border-radius:5px;display:flex;align-items:center;transition:color .15s}
.uv-reveal-btn:hover{color:var(--t1)}
.uv-reveal-btn svg{width:16px;height:16px}

/* ── Roles grid ── */
.uv-roles-grid{display:flex;flex-wrap:wrap;gap:8px}
.uv-role-chip{display:inline-flex;align-items:center;cursor:pointer}
.uv-role-check{position:absolute;opacity:0;width:0;height:0;pointer-events:none}
.uv-role-label{padding:5px 13px;border-radius:20px;font-size:12px;font-weight:700;border:1.5px solid var(--bd2);color:var(--t2);background:var(--p2);transition:all .15s;user-select:none}
.uv-role-chip:hover .uv-role-label{border-color:var(--accent);color:var(--accent)}
.uv-role-check:checked+.uv-role-label{background:var(--accent);color:#fff;border-color:var(--accent)}
.uv-role-check:checked+.uv-role-label:hover{background:#6d28d9;border-color:#6d28d9}

/* ── Custom select ── */
.uv-custom-select{position:relative}
.uv-select-trigger{width:100%;display:flex;align-items:center;gap:8px;padding:9px 13px;border-radius:8px;border:1px solid var(--bd2);background:var(--p2);color:var(--t1);font-size:13px;font-family:inherit;cursor:pointer;outline:none;transition:border-color .15s,box-shadow .15s;text-align:left}
.uv-select-trigger:hover{border-color:var(--accent)}
.uv-select-trigger:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(124,58,237,.12)}
html.dark .uv-select-trigger{background:rgba(255,255,255,.04)}
html.dark .uv-select-trigger:hover{background:rgba(255,255,255,.06)}
.uv-select-trigger-text{flex:1}
.uv-select-chevron{width:14px;height:14px;color:var(--t2);flex-shrink:0;transition:transform .15s}
.uv-select-trigger[aria-expanded="true"] .uv-select-chevron{transform:rotate(180deg)}
.uv-select-menu{position:absolute;top:calc(100% + 4px);left:0;right:0;background:var(--p1);border:1px solid var(--bd2);border-radius:9px;box-shadow:0 8px 24px rgba(0,0,0,.15);z-index:50;overflow:hidden}
html.dark .uv-select-menu{box-shadow:0 8px 32px rgba(0,0,0,.4)}
.uv-select-option{width:100%;display:flex;align-items:center;gap:8px;padding:9px 13px;font-size:13px;font-family:inherit;background:none;border:none;cursor:pointer;color:var(--t1);text-align:left;transition:background .12s}
.uv-select-option:hover{background:var(--p2)}
html.dark .uv-select-option:hover{background:rgba(255,255,255,.06)}
.uv-select-option.active-opt{color:var(--accent);font-weight:600}
.uv-status-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}

/* ── Save bar ── */
.uv-save-bar{display:flex;align-items:center;gap:10px}

/* ── Alerts ── */
.uv-alert-errors{background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:14px 18px}
.uv-alert-errors-title{font-size:13px;font-weight:700;color:#dc2626;margin-bottom:8px}
.uv-alert-errors ul{list-style:disc;padding-left:18px;display:flex;flex-direction:column;gap:3px}
.uv-alert-errors li{font-size:12.5px;color:#dc2626}

/* ── Animations ── */
@keyframes uvUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.uva{opacity:0;animation:uvUp .4s cubic-bezier(.16,1,.3,1) forwards}
.uv1{animation-delay:.04s}.uv2{animation-delay:.09s}.uv3{animation-delay:.14s}.uv4{animation-delay:.19s}.uv5{animation-delay:.24s}.uv6{animation-delay:.29s}
</style>

{{-- ── Header ── --}}
<div class="uv-header uva uv1">
    <h1 class="uv-page-title">Edit user</h1>
    <div class="uv-header-actions">
        <a href="{{ $backUrl }}" class="uv-btn uv-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to users
        </a>
        <a href="{{ $viewUrl }}" class="uv-btn uv-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
            View profile
        </a>

        @if($canAct)
            @if($isSuspended)
            <button type="button" class="uv-btn" wire:click="unsuspendUser"
                style="background:rgba(52,211,153,.12);color:#34d399;border:1px solid rgba(52,211,153,.25)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Unsuspend
            </button>
            @else
            <button type="button" class="uv-btn" wire:click="suspendUser"
                style="background:rgba(245,158,11,.1);color:#f59e0b;border:1px solid rgba(245,158,11,.25)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Suspend
            </button>
            @endif

            <button type="button" class="uv-btn"
                wire:click="removeUser"
                wire:confirm="Remove {{ $user->name }}? This will soft-delete their account."
                style="background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.25)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                Remove
            </button>
        @endif
    </div>
</div>

{{-- ── Hero card ── --}}
<div class="uv-hero uva uv2">
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="uv-avatar-img">
    @else
        <div class="uv-avatar-initials" style="background:{{ $avatarBg }}">{{ $initials }}</div>
    @endif

    <div class="uv-hero-info">
        <div class="uv-hero-name">{{ $user->name }}</div>
        <div class="uv-badges-row">
            <span class="uv-badge" style="background:var(--p2);border:1px solid var(--bd);color:{{ $roleStyle['color'] }}">
                {{ $roleStyle['label'] }}
            </span>
            <span class="uv-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                <span class="uv-dot" style="background:{{ $statusStyle['dot'] }}"></span>
                {{ $statusStyle['label'] }}
            </span>
        </div>
    </div>

    <div class="uv-hero-meta">
        <div class="uv-hero-stat">
            <div class="uv-hero-stat-label">User ID</div>
            <div class="uv-hero-stat-value">#{{ $user->id }}</div>
        </div>
        <div style="width:1px;height:36px;background:var(--bd)"></div>
        <div class="uv-hero-stat">
            <div class="uv-hero-stat-label">Member Since</div>
            <div class="uv-hero-stat-value">{{ $user->created_at?->format('M Y') }}</div>
        </div>
    </div>
</div>

{{-- ── Validation error summary ── --}}
@if($errors->any())
<div class="uv-alert-errors uva uv3">
    <div class="uv-alert-errors-title">Please fix the following errors</div>
    <ul>
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ── Profile + Access Control ── --}}
<div class="uv-grid-2 uva uv3">

    {{-- Profile --}}
    <div class="uv-card">
        <div class="uv-card-header">
            <div class="uv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </div>
            <div>
                <div class="uv-card-title">Profile</div>
                <div class="uv-card-subtitle">Personal information &amp; contact</div>
            </div>
        </div>
        <div class="uv-card-body">
            <div class="uv-field-group">
                <label class="uv-label" for="eu-name">Full Name</label>
                <input id="eu-name" type="text" class="uv-input" wire:model="data.name" placeholder="John Doe" autocomplete="off">
                @error('data.name') <span class="uv-error">{{ $message }}</span> @enderror
            </div>

            <div class="uv-field-group">
                <label class="uv-label" for="eu-email">Email Address</label>
                <input id="eu-email" type="email" class="uv-input" wire:model="data.email" placeholder="john@example.com" autocomplete="off">
                @error('data.email') <span class="uv-error">{{ $message }}</span> @enderror
            </div>

            <div class="uv-field-group">
                <label class="uv-label" for="eu-phone">Phone Number</label>
                <input id="eu-phone" type="tel" class="uv-input" wire:model="data.phone" placeholder="+1 (555) 000-0000">
                @error('data.phone') <span class="uv-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    {{-- Access Control --}}
    <div class="uv-card">
        <div class="uv-card-header">
            <div class="uv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <div>
                <div class="uv-card-title">Access Control</div>
                <div class="uv-card-subtitle">Roles &amp; account status</div>
            </div>
        </div>
        <div class="uv-card-body">
            <div class="uv-field-group">
                <label class="uv-label">Assigned Roles</label>
                <div class="uv-roles-grid">
                    @foreach($availableRoles as $r)
                        <label class="uv-role-chip">
                            <input type="checkbox" class="uv-role-check" wire:model="data.roles" value="{{ $r->id }}">
                            <span class="uv-role-label">{{ ucfirst(str_replace('-', ' ', $r->name)) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('data.roles') <span class="uv-error">{{ $message }}</span> @enderror
            </div>

            <div class="uv-field-group">
                <label class="uv-label">Account Status</label>
                <div class="uv-custom-select" x-data="{ open: false, val: @entangle('data.status') }" @click.outside="open = false">
                    <button type="button"
                        class="uv-select-trigger"
                        :aria-expanded="open ? 'true' : 'false'"
                        @click="open = !open">
                        <span class="uv-status-dot"
                            :style="val === 'suspended' ? 'background:#f87171' : 'background:#34d399'"></span>
                        <span class="uv-select-trigger-text"
                            x-text="val === 'suspended' ? 'Suspended' : 'Active'"></span>
                        <svg class="uv-select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div class="uv-select-menu" x-show="open" x-transition:enter="uv-dd-enter" x-transition:leave="uv-dd-leave" style="display:none">
                        <button type="button" class="uv-select-option" :class="val === 'active' ? 'active-opt' : ''" @click="val = 'active'; open = false">
                            <span class="uv-status-dot" style="background:#34d399"></span> Active
                        </button>
                        <button type="button" class="uv-select-option" :class="val === 'suspended' ? 'active-opt' : ''" @click="val = 'suspended'; open = false">
                            <span class="uv-status-dot" style="background:#f87171"></span> Suspended
                        </button>
                    </div>
                </div>
                @error('data.status') <span class="uv-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

</div>

{{-- ── Security ── --}}
<div class="uv-card uva uv4">
    <div class="uv-card-header">
        <div class="uv-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
        </div>
        <div>
            <div class="uv-card-title">Security</div>
            <div class="uv-card-subtitle">Leave blank to keep the current password</div>
        </div>
    </div>
    <div class="uv-card-body">
        <div class="uv-field-group" x-data="{ show: false }">
            <label class="uv-label" for="eu-password">New Password</label>
            <div class="uv-input-wrap">
                <input id="eu-password" :type="show ? 'text' : 'password'" class="uv-input" wire:model="data.password" placeholder="••••••••" autocomplete="new-password">
                <button type="button" class="uv-reveal-btn" @click="show = !show" tabindex="-1">
                    <svg x-show="!show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                    <svg x-show="show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            @error('data.password') <span class="uv-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Save bar ── --}}
<div class="uv-save-bar uva uv5">
    <button
        type="button"
        wire:click="save"
        wire:loading.attr="disabled"
        class="uv-btn uv-btn-purple"
    >
        <span wire:loading.remove wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </span>
        <span wire:loading wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Save changes
    </button>
    <a href="{{ $backUrl }}" class="uv-btn uv-btn-gray">Cancel</a>
</div>

</div>

<style>
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
</style>
