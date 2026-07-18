@php
    $backUrl = route('filament.admin.pages.users');
    $availableRoles = \Spatie\Permission\Models\Role::orderBy('name')->get();
@endphp

<div class="cu">

<style>
.cu,.cu *,.cu *::before,.cu *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cu {
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:56px;
    display:grid;
    gap:20px;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#7c3aed;
    --accent2:#6d28d9;
    color:var(--t1);
}
html:not(.dark) .cu {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes cuUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.cua {
    opacity:0;
    animation:cuUp .38s cubic-bezier(.16,1,.3,1) forwards;
}
.cu1 {
    animation-delay:.04s;
}
.cu2 {
    animation-delay:.09s;
}
.cu3 {
    animation-delay:.14s;
}
.cu4 {
    animation-delay:.19s;
}
.cu5 {
    animation-delay:.24s;
}

/* ── Header ── */
.cu-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.cu-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.cu-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:9px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:none;
    font-family:inherit;
    transition:all .15s;
    white-space:nowrap;
}
.cu-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.cu-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.cu-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.cu-btn-purple {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.cu-btn-purple:hover {
    background:var(--accent2);
}
.cu-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}

/* ── Intro banner ── */
.cu-intro {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    padding:24px 28px;
    display:flex;
    align-items:center;
    gap:20px;
    flex-wrap:wrap;
}
.cu-intro-icon {
    width:64px;
    height:64px;
    border-radius:16px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(124,58,237,.12);
    color:var(--accent);
}
.cu-intro-icon svg {
    width:28px;
    height:28px;
}
.cu-intro-body {
    flex:1;
    min-width:0;
}
.cu-intro-title {
    font-size:18px;
    font-weight:780;
    color:var(--t1);
    letter-spacing:-.015em;
}
.cu-intro-desc {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
    line-height:1.6;
}
.cu-intro-steps {
    display:flex;
    gap:6px;
    margin-top:12px;
    flex-wrap:wrap;
}
.cu-step {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    border:1px solid;
    background:rgba(124,58,237,.08);
    border-color:rgba(124,58,237,.2);
    color:var(--accent);
}
.cu-step-num {
    width:16px;
    height:16px;
    border-radius:50%;
    background:var(--accent);
    color:#fff;
    font-size:9px;
    font-weight:800;
    display:grid;
    place-items:center;
    flex-shrink:0;
}

/* ── Cards ── */
.cu-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    overflow:hidden;
}
.cu-card-head {
    padding:18px 22px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:12px;
}
.cu-card-icon {
    width:34px;
    height:34px;
    border-radius:9px;
    display:grid;
    place-items:center;
    background:rgba(124,58,237,.1);
    color:var(--accent);
    flex-shrink:0;
}
.cu-card-icon svg {
    width:16px;
    height:16px;
}
.cu-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.cu-card-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cu-card-body {
    padding:22px;
    display:grid;
    gap:18px;
}

/* ── Form fields ── */
.cu-label {
    display:block;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.cu-label span {
    color:#ef4444;
    margin-left:2px;
}
.cu-input {
    width:100%;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:9px;
    padding:10px 13px;
    font-size:13.5px;
    font-weight:550;
    color:var(--t1);
    font-family:inherit;
    outline:none;
    transition:border-color .15s,box-shadow .15s;
    -webkit-appearance:none;
}
.cu-input:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(124,58,237,.13);
}
select.cu-input {
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:18px;
    padding-right:36px;
    cursor:pointer;
}
.cu-error {
    display:block;
    font-size:11.5px;
    color:#ef4444;
    margin-top:5px;
}
.cu-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}
@media(max-width:560px) {
    .cu-grid-2 {
        grid-template-columns:1fr;
    }
}

/* ── Role chips ── */
.cu-roles-grid {
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}
.cu-role-chip {
    display:inline-flex;
    align-items:center;
    cursor:pointer;
}
.cu-role-check {
    position:absolute;
    opacity:0;
    width:0;
    height:0;
    pointer-events:none;
}
.cu-role-label {
    padding:5px 13px;
    border-radius:20px;
    font-size:12px;
    font-weight:700;
    border:1.5px solid var(--bd2);
    color:var(--t2);
    background:var(--p2);
    transition:all .15s;
    user-select:none;
}
.cu-role-chip:hover .cu-role-label {
    border-color:var(--accent);
    color:var(--accent);
}
.cu-role-check:checked+.cu-role-label {
    background:var(--accent);
    color:#fff;
    border-color:var(--accent);
}
.cu-role-check:checked+.cu-role-label:hover {
    background:var(--accent2);
    border-color:var(--accent2);
}

/* ── Password reveal ── */
.cu-input-wrap {
    position:relative;
    display:flex;
    align-items:center;
}
.cu-input-wrap .cu-input {
    padding-right:40px;
}
.cu-reveal-btn {
    position:absolute;
    right:10px;
    background:none;
    border:none;
    cursor:pointer;
    color:var(--t2);
    padding:4px;
    border-radius:5px;
    display:flex;
    align-items:center;
    transition:color .15s;
}
.cu-reveal-btn:hover {
    color:var(--t1);
}
.cu-reveal-btn svg {
    width:16px;
    height:16px;
}

/* ── Save bar ── */
.cu-save-bar {
    display:flex;
    align-items:center;
    gap:10px;
    padding-top:20px;
    border-top:1px solid var(--bd);
}
@keyframes spin {
    from {
        transform:rotate(0deg);
    }
    to {
        transform:rotate(360deg);
    }
}
</style>

{{-- ── Header ── --}}
<div class="cu-header cua cu1">
    <h1 class="cu-page-title">Create User</h1>
    <a href="{{ $backUrl }}" wire:navigate class="cu-btn cu-btn-gray">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        Back to users
    </a>
</div>

{{-- ── Intro banner ── --}}
<div class="cu-intro cua cu2">
    <div class="cu-intro-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
    </div>
    <div class="cu-intro-body">
        <div class="cu-intro-title">New Platform User</div>
        <div class="cu-intro-desc">Create an account, assign at least one role, and set an initial password. The user can sign in immediately with these credentials.</div>
        <div class="cu-intro-steps">
            <span class="cu-step"><span class="cu-step-num">1</span> Profile</span>
            <span class="cu-step"><span class="cu-step-num">2</span> Roles &amp; status</span>
            <span class="cu-step"><span class="cu-step-num">3</span> Password</span>
        </div>
    </div>
</div>

{{-- ── Profile ── --}}
<div class="cu-card cua cu3">
    <div class="cu-card-head">
        <div class="cu-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        </div>
        <div>
            <div class="cu-card-title">Profile</div>
            <div class="cu-card-sub">Personal information and contact details</div>
        </div>
    </div>
    <div class="cu-card-body">
        <div class="cu-grid-2">
            <div>
                <label class="cu-label" for="cu-name">Full Name <span>*</span></label>
                <input id="cu-name" type="text" class="cu-input" wire:model="data.name" placeholder="John Doe" autocomplete="off" required>
                @error('data.name') <span class="cu-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cu-label" for="cu-email">Email Address <span>*</span></label>
                <input id="cu-email" type="email" class="cu-input" wire:model="data.email" placeholder="john@example.com" autocomplete="off" required>
                @error('data.email') <span class="cu-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="cu-label" for="cu-phone">Phone Number</label>
            <input id="cu-phone" type="tel" class="cu-input" wire:model="data.phone" placeholder="+1 (555) 000-0000">
            @error('data.phone') <span class="cu-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Access Control ── --}}
<div class="cu-card cua cu4">
    <div class="cu-card-head">
        <div class="cu-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        </div>
        <div>
            <div class="cu-card-title">Access Control</div>
            <div class="cu-card-sub">Roles and account status</div>
        </div>
    </div>
    <div class="cu-card-body">
        <div>
            <label class="cu-label">Assigned Roles <span>*</span></label>
            <div class="cu-roles-grid">
                @foreach($availableRoles as $r)
                    <label class="cu-role-chip">
                        <input type="checkbox" class="cu-role-check" wire:model="selectedRoleIds" value="{{ $r->id }}">
                        <span class="cu-role-label">{{ ucfirst(str_replace('-', ' ', $r->name)) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="cu-label" for="cu-status">Account Status <span>*</span></label>
            <select id="cu-status" class="cu-input" wire:model="data.status">
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
            </select>
            @error('data.status') <span class="cu-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Security ── --}}
<div class="cu-card cua cu5" x-data="{ show: false }">
    <div class="cu-card-head">
        <div class="cu-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
        </div>
        <div>
            <div class="cu-card-title">Security</div>
            <div class="cu-card-sub">Set an initial password for this account</div>
        </div>
    </div>
    <div class="cu-card-body">
        <div>
            <label class="cu-label" for="cu-password">Password <span>*</span></label>
            <div class="cu-input-wrap">
                <input id="cu-password" :type="show ? 'text' : 'password'" class="cu-input" wire:model="data.password" placeholder="••••••••" autocomplete="new-password" required>
                <button type="button" class="cu-reveal-btn" @click="show = !show" tabindex="-1">
                    <svg x-show="!show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                    <svg x-show="show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            @error('data.password') <span class="cu-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Save bar ── --}}
<div class="cu-save-bar cua cu5">
    <button type="button" wire:click="create" wire:loading.attr="disabled" class="cu-btn cu-btn-purple">
        <span wire:loading.remove wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </span>
        <span wire:loading wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Create User
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="cu-btn cu-btn-gray">Cancel</a>
</div>

</div>
