@php
    /** @var \Spatie\Permission\Models\Role $record */
    $role       = $role ?? $record;
    $backUrl    = route('filament.admin.pages.roles');
    $editUrl    = route('filament.admin.resources.roles.edit', ['record' => $role->id]);
    $canUpdate  = $canUpdate ?? (auth()->user()?->can('roles.update') ?? false);
    $protectedRoles = \App\Filament\Resources\Roles\RoleResource::protectedRoleNames();
    $isSystem   = in_array($role->name, $protectedRoles, true);
    $assignUrl  = $assignUrl ?? route('admin.roles.users.assign', ['role' => $role->id]);
    $roleColor  = match ($role->name) {
        'super-admin'     => '#dc2626',
        'admin'           => '#a855f7',
        'finance-manager', 'accountant' => '#0d9488',
        'content-manager', 'moderator'  => '#d97706',
        'support-staff', 'instructor'   => '#3b82f6',
        'student'         => '#10b981',
        default           => '#6366f1',
    };

    $grantedNames = $grantedNames ?? $role->permissions->pluck('name')->all();
    $grouped      = $grouped ?? $role->permissions->sortBy('name')->groupBy(fn ($p) => explode('.', $p->name)[0]);
    $users        = $users ?? $role->users()->orderBy('name')->get();
    $usersCount   = $usersCount ?? $users->count();
    $allUsers     = $allUsers ?? \App\Domains\Users\Models\User::orderBy('name')
        ->whereNotIn('id', $users->pluck('id')->all())
        ->get(['id', 'name', 'email']);
@endphp

<div class="rv">

<style>
.rv,.rv *,.rv *::before,.rv *::after{box-sizing:border-box;margin:0;padding:0}
.rv{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:48px;display:grid;gap:20px;
    --bg:#0f172a;--p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .rv{
    --bg:#f1f5f9;--p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes rvUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.rva{opacity:0;animation:rvUp .45s cubic-bezier(.16,1,.3,1) forwards}
.rv1{animation-delay:.04s}.rv2{animation-delay:.09s}.rv3{animation-delay:.14s}

.rv-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.rv-header-text h1{font-size:clamp(20px,2.2vw,26px);font-weight:780;letter-spacing:-.018em;color:var(--t1);line-height:1.15}
.rv-header-text p{font-size:12px;color:var(--t2);margin-top:5px}
.rv-header-btns{display:flex;align-items:center;gap:10px;flex-shrink:0}
.rv-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:.02em;cursor:pointer;text-decoration:none;transition:opacity .18s,transform .15s;white-space:nowrap;border:none;font-family:inherit}
.rv-btn:hover{opacity:.85;transform:translateY(-1px)}
.rv-btn-primary{background:#16a34a;color:#fff}
.rv-btn-gray{background:var(--p2);color:var(--t1);border:1px solid var(--bd2)}

.rv-card{background:var(--p1);border:1px solid var(--bd);border-radius:12px;box-shadow:var(--sh);padding:20px}

.rv-hero{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.rv-hero-icon{width:56px;height:56px;border-radius:14px;display:grid;place-items:center;flex-shrink:0}
.rv-hero-icon svg{width:26px;height:26px}
.rv-hero-name{font-size:19px;font-weight:780;color:var(--t1);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.rv-badge-system{font-size:10.5px;font-weight:700;color:var(--t2);background:var(--p2);border:1px solid var(--bd2);border-radius:6px;padding:2px 8px}
.rv-hero-sub{font-size:12px;color:var(--t2);margin-top:3px}
.rv-stats{display:flex;align-items:center;gap:24px;margin-left:auto;flex-wrap:wrap}
.rv-stat{text-align:right}
.rv-stat-val{font-size:18px;font-weight:780;color:var(--t1)}
.rv-stat-label{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);margin-top:2px}
.rv-stat-divider{width:1px;height:32px;background:var(--bd)}

.rv-card-title{font-size:14px;font-weight:750;color:var(--t1)}
.rv-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}

.rv-modules{display:grid;gap:14px;margin-top:18px}
.rv-module{border:1px solid var(--bd);border-radius:10px;overflow:hidden}
.rv-module-head{padding:9px 14px;background:var(--p2);font-size:11.5px;font-weight:780;letter-spacing:.03em;text-transform:uppercase;color:var(--t2)}
.rv-module-perms{display:flex;flex-wrap:wrap;gap:8px;padding:12px 14px}
.rv-perm{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:5px 10px;border-radius:7px}
.rv-perm-on{background:rgba(34,197,94,.1);color:#16a34a}
.rv-perm-off{background:var(--p2);color:var(--t2);opacity:.55}
.rv-perm svg{width:12px;height:12px;flex-shrink:0}

.rv-users{display:grid;gap:8px;margin-top:14px}
.rv-user-row{display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;transition:background .12s}
.rv-user-row:hover{background:var(--p2)}
.rv-user-avatar{width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0}
.rv-user-name{font-size:12.5px;font-weight:650;color:var(--t1)}
.rv-user-email{font-size:11.5px;color:var(--t2)}
.rv-users-empty{font-size:12.5px;color:var(--t2);padding:8px 0}
.rv-users-more{font-size:11.5px;color:var(--t2);padding-top:4px}

.rv-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media (max-width:860px){.rv-grid-2{grid-template-columns:1fr}}
</style>

{{-- Header --}}
<div class="rv-header rva rv1">
    <div class="rv-header-text">
        <h1>View role</h1>
        <p>Role details, granted permissions, and assigned users.</p>
    </div>
    <div class="rv-header-btns">
        <a href="{{ $backUrl }}" class="rv-btn rv-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to Roles
        </a>
        @if($canUpdate)
        <a href="{{ $editUrl }}" class="rv-btn rv-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
            Edit Role
        </a>
        @endif
    </div>
</div>

{{-- Hero --}}
<div class="rv-card rva rv2">
    <div class="rv-hero">
        <div class="rv-hero-icon" style="background:{{ $roleColor }}20;color:{{ $roleColor }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        </div>
        <div>
            <div class="rv-hero-name">
                {{ \Illuminate\Support\Str::headline($role->name) }}
                @if($isSystem)
                    <span class="rv-badge-system">System Role</span>
                @endif
            </div>
            <div class="rv-hero-sub">{{ $role->name }} &middot; {{ $role->guard_name }} guard</div>
        </div>
        <div class="rv-stats">
            <div class="rv-stat">
                <div class="rv-stat-val">{{ number_format($grantedNames ? count($grantedNames) : 0) }}</div>
                <div class="rv-stat-label">Permissions</div>
            </div>
            <div class="rv-stat-divider"></div>
            <div class="rv-stat">
                <div class="rv-stat-val">{{ number_format($usersCount) }}</div>
                <div class="rv-stat-label">Users</div>
            </div>
            <div class="rv-stat-divider"></div>
            <div class="rv-stat">
                <div class="rv-stat-val">{{ $role->created_at?->format('M d, Y') ?? '—' }}</div>
                <div class="rv-stat-label">Created</div>
            </div>
        </div>
    </div>
</div>

<div class="rv-grid-2">
    {{-- Permissions --}}
    <div class="rv-card rva rv3">
        <div class="rv-card-title">Permissions</div>
        <div class="rv-card-sub">Actions this role is allowed to perform, grouped by module.</div>
        <div class="rv-modules">
            @forelse ($grouped as $module => $permissions)
            <div class="rv-module">
                <div class="rv-module-head">{{ \Illuminate\Support\Str::headline($module) }}</div>
                <div class="rv-module-perms">
                    @foreach ($permissions as $permission)
                        <span class="rv-perm rv-perm-on">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            {{ explode('.', $permission->name)[1] ?? $permission->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @empty
                <div class="rv-users-empty">This role has no permissions assigned yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Users with this role --}}
    <div class="rv-card rva rv3">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px">
            <div>
                <div class="rv-card-title">Users with this role</div>
                <div class="rv-card-sub">{{ number_format($usersCount) }} {{ Str::plural('user', $usersCount) }} currently assigned.</div>
            </div>
        </div>

        {{-- Assign user form --}}
        @if($canUpdate)
        @if(session('role_success'))
        <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#16a34a;font-size:12.5px;font-weight:600;margin-bottom:12px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            {{ session('role_success') }}
        </div>
        @endif
        @if(session('role_error'))
        <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#dc2626;font-size:12.5px;font-weight:600;margin-bottom:12px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ session('role_error') }}
        </div>
        @endif
        <form method="POST" action="{{ $assignUrl }}" style="display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap">
            @csrf
            <input
                type="text"
                name="user_query"
                list="role-users-list"
                placeholder="Search by name or email…"
                style="flex:1;min-width:180px;background:var(--p2);border:1px solid var(--bd2);border-radius:8px;padding:8px 12px;font-size:12.5px;color:var(--t1);font-family:inherit;outline:none"
                autocomplete="off"
                required
            >
            <datalist id="role-users-list">
                @foreach($allUsers as $au)
                    <option value="{{ $au->email }}">{{ $au->name }}</option>
                @endforeach
            </datalist>
            <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;background:#16a34a;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;font-family:inherit;white-space:nowrap">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Assign User
            </button>
        </form>
        @endif

        {{-- User list --}}
        <div class="rv-users">
            @forelse ($users as $u)
                @php
                    $bgHex = substr(md5($u->name ?? ''), 0, 6);
                    $avUrl = $u->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($u->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64');
                    $removeUrl = route('admin.roles.users.remove', ['role' => $role->id, 'user' => $u->id]);
                @endphp
                <div class="rv-user-row" style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px">
                    <a href="{{ route('filament.admin.resources.users.view', ['record' => $u->id]) }}" style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;text-decoration:none;color:inherit">
                        <img src="{{ $avUrl }}" class="rv-user-avatar" alt="">
                        <div style="min-width:0">
                            <div class="rv-user-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $u->name }}</div>
                            <div class="rv-user-email" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $u->email }}</div>
                        </div>
                    </a>
                    @if($canUpdate)
                    <form method="POST" action="{{ $removeUrl }}" onsubmit="return confirm('Remove {{ addslashes($u->name) }} from this role?')">
                        @csrf
                        <button type="submit" title="Remove from role" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#ef4444;cursor:pointer;flex-shrink:0">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            @empty
                <div class="rv-users-empty">No users currently have this role.</div>
            @endforelse
        </div>
    </div>
</div>

</div>
