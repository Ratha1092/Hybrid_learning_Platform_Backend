@php
    /** @var \Spatie\Permission\Models\Role $role */
    $role = $role ?? $record;

    $backUrl = route('filament.admin.pages.roles');
    $viewUrl = route('filament.admin.resources.roles.view', ['record' => $role->id]);
    $updateUrl = route('admin.roles.update', ['role' => $role->id]);

    $protectedRoles = \App\Filament\Resources\Roles\RoleResource::protectedRoleNames();
    $isSystem = in_array($role->name, $protectedRoles, true);

    $roleColor = match ($role->name) {
        'super-admin'     => '#dc2626',
        'admin'           => '#a855f7',
        'finance-manager', 'accountant' => '#0d9488',
        'content-manager', 'moderator'  => '#d97706',
        'support-staff', 'instructor'   => '#3b82f6',
        'student'         => '#10b981',
        default           => '#6366f1',
    };

    $totalPermissions = $grouped->flatten()->count();
    $grantedCount = count($grantedNames);
@endphp

<div class="re">

<style>
.re,.re *,.re *::before,.re *::after{box-sizing:border-box;margin:0;padding:0}
.re{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:48px;display:grid;gap:20px;
    --bg:#0f172a;--p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .re{
    --bg:#f1f5f9;--p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes reUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.rea{opacity:0;animation:reUp .45s cubic-bezier(.16,1,.3,1) forwards}
.re1{animation-delay:.04s}.re2{animation-delay:.09s}.re3{animation-delay:.14s}

.re-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.re-header-text h1{font-size:clamp(20px,2.2vw,26px);font-weight:780;letter-spacing:-.018em;color:var(--t1);line-height:1.15}
.re-header-text p{font-size:12px;color:var(--t2);margin-top:5px}
.re-header-btns{display:flex;align-items:center;gap:10px;flex-shrink:0}
.re-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:.02em;cursor:pointer;text-decoration:none;transition:opacity .18s,transform .15s;white-space:nowrap;border:none;font-family:inherit}
.re-btn:hover{opacity:.85;transform:translateY(-1px)}
.re-btn-primary{background:#16a34a;color:#fff}
.re-btn-gray{background:var(--p2);color:var(--t1);border:1px solid var(--bd2)}

.re-alert{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600}
.re-alert svg{width:18px;height:18px;flex-shrink:0;margin-top:1px}
.re-alert-success{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.25);color:#059669}
.re-alert-error{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:#dc2626}

.re-card{background:var(--p1);border:1px solid var(--bd);border-radius:12px;box-shadow:var(--sh);padding:20px}

.re-hero{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.re-hero-icon{width:56px;height:56px;border-radius:14px;display:grid;place-items:center;flex-shrink:0}
.re-hero-icon svg{width:26px;height:26px}
.re-field{flex:1 1 240px;min-width:200px}
.re-label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--t2);margin-bottom:6px}
.re-input{width:100%;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:9px 12px;font-size:14px;font-weight:650;color:var(--t1);font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s}
.re-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.12)}
.re-input:disabled{opacity:.6;cursor:not-allowed}
.re-helper{font-size:11.5px;color:var(--t2);margin-top:6px}
.re-badge-system{font-size:10.5px;font-weight:700;color:var(--t2);background:var(--p2);border:1px solid var(--bd2);border-radius:6px;padding:2px 8px;display:inline-block;margin-bottom:6px}
.re-stat{text-align:right}
.re-stat-val{font-size:18px;font-weight:780;color:var(--t1)}
.re-stat-label{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);margin-top:2px}

.re-card-title{font-size:14px;font-weight:750;color:var(--t1)}
.re-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}
.re-select-all{display:flex;align-items:center;gap:10px}
.re-select-all-btn{font-size:11.5px;font-weight:700;color:#6366f1;background:none;border:none;cursor:pointer;font-family:inherit;text-decoration:underline;text-underline-offset:2px}

.re-modules{display:grid;gap:14px;margin-top:18px}
.re-module{border:1px solid var(--bd);border-radius:10px;overflow:hidden}
.re-module-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 14px;background:var(--p2)}
.re-module-name{font-size:11.5px;font-weight:780;letter-spacing:.03em;text-transform:uppercase;color:var(--t2)}
.re-module-toggle{font-size:10.5px;font-weight:700;color:#6366f1;background:none;border:none;cursor:pointer;font-family:inherit}
.re-module-perms{display:flex;flex-wrap:wrap;gap:8px;padding:12px 14px}
.re-perm-label{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:600;padding:6px 10px 6px 8px;border-radius:7px;background:var(--p2);border:1px solid var(--bd2);cursor:pointer;transition:background .15s,border-color .15s,color .15s;color:var(--t2)}
.re-perm-label:has(input:checked){background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);color:#16a34a}
.re-perm-label input{width:14px;height:14px;accent-color:#16a34a;cursor:pointer}

.re-footer-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-top:4px}
.re-footer-info{font-size:12px;color:var(--t2)}
.re-footer-info b{color:var(--t1)}

/* Suppress any Filament-rendered schema sections that appear alongside this custom form */
.fi-section,.fi-fo-component-ctn,.fi-schema{display:none!important}
</style>

<form method="POST" action="{{ $updateUrl }}">
    @csrf

    {{-- Header --}}
    <div class="re-header rea re1">
        <div class="re-header-text">
            <h1>Edit role</h1>
            <p>Update the role name and the permissions it grants.</p>
        </div>
        <div class="re-header-btns">
            <a href="{{ $backUrl }}" class="re-btn re-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Back to Roles
            </a>
            <a href="{{ $viewUrl }}" class="re-btn re-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/></svg>
                View Role
            </a>
            <button type="submit" class="re-btn re-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Save Changes
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('role_success'))
    <div class="re-alert re-alert-success rea re1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        {{ session('role_success') }}
    </div>
    @endif
    @if(session('role_error'))
    <div class="re-alert re-alert-error rea re1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        {{ session('role_error') }}
    </div>
    @endif

    {{-- Hero / name --}}
    <div class="re-card rea re2">
        <div class="re-hero">
            <div class="re-hero-icon" style="background:{{ $roleColor }}20;color:{{ $roleColor }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <div class="re-field">
                @if($isSystem)
                    <span class="re-badge-system">System Role</span>
                @endif
                <label class="re-label">Role Name</label>
                <input
                    type="text"
                    name="name"
                    class="re-input"
                    value="{{ $role->name }}"
                    {{ $isSystem ? 'disabled' : '' }}
                    maxlength="255"
                >
                <p class="re-helper">
                    @if($isSystem)
                        System role names are locked — the platform depends on this exact name.
                    @else
                        Lowercase, hyphen-separated, e.g. "content-manager".
                    @endif
                </p>
            </div>
            <div class="re-stat">
                <div class="re-stat-val" id="re-selected-count">{{ $grantedCount }}</div>
                <div class="re-stat-label">of {{ $totalPermissions }} permissions</div>
            </div>
        </div>
    </div>

    {{-- Permissions --}}
    <div class="re-card rea re3">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div>
                <div class="re-card-title">Permissions</div>
                <div class="re-card-sub">Toggle every action this role is allowed to perform.</div>
            </div>
            <div class="re-select-all">
                <button type="button" class="re-select-all-btn" onclick="reToggleAll(true)">Select all</button>
                <button type="button" class="re-select-all-btn" onclick="reToggleAll(false)">Clear all</button>
            </div>
        </div>

        <div class="re-modules">
            @foreach ($grouped as $module => $permissions)
            <div class="re-module">
                <div class="re-module-head">
                    <span class="re-module-name">{{ \Illuminate\Support\Str::headline($module) }}</span>
                    <button type="button" class="re-module-toggle" onclick="reToggleModule(this, '{{ $module }}')">Toggle all</button>
                </div>
                <div class="re-module-perms" data-module="{{ $module }}">
                    @foreach ($permissions as $permission)
                        @php $granted = in_array($permission->name, $grantedNames, true); @endphp
                        <label class="re-perm-label">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="re-perm-checkbox" {{ $granted ? 'checked' : '' }}>
                            {{ explode('.', $permission->name)[1] ?? $permission->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="re-footer-bar">
            <div class="re-footer-info"><b id="re-selected-count-2">{{ $grantedCount }}</b> of {{ $totalPermissions }} permissions selected</div>
            <button type="submit" class="re-btn re-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Save Changes
            </button>
        </div>
    </div>
</form>

</div>

<script>
(function() {
    function allCheckboxes() {
        return document.querySelectorAll('.re-perm-checkbox');
    }

    function updateCount() {
        var count = document.querySelectorAll('.re-perm-checkbox:checked').length;
        var a = document.getElementById('re-selected-count');
        var b = document.getElementById('re-selected-count-2');
        if (a) a.textContent = count;
        if (b) b.textContent = count;
    }

    window.reToggleAll = function(state) {
        allCheckboxes().forEach(function(cb) { cb.checked = state; });
        updateCount();
    };

    window.reToggleModule = function(btn, module) {
        var wrap = document.querySelector('.re-module-perms[data-module="' + module + '"]');
        if (!wrap) return;
        var boxes = wrap.querySelectorAll('.re-perm-checkbox');
        var allChecked = Array.prototype.every.call(boxes, function(cb) { return cb.checked; });
        boxes.forEach(function(cb) { cb.checked = !allChecked; });
        updateCount();
    };

    allCheckboxes().forEach(function(cb) {
        cb.addEventListener('change', updateCount);
    });
})();
</script>
