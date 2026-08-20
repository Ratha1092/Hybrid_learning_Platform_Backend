@php
    /** @var \App\Domains\Users\Models\User $record */
    $user = $record;

    $backUrl = route('filament.admin.pages.users');
    $editUrl = url('/admin/users/' . $user->id . '/edit');

    $statusVal = $user->status ?? 'active';
    $statusStyle = match ($statusVal) {
        'active'    => ['bg' => 'rgba(22,163,74,.1)',   'color' => '#16a34a', 'dot' => '#22c55e', 'label' => 'Active'],
        'suspended' => ['bg' => 'rgba(220,38,38,.1)',   'color' => '#dc2626', 'dot' => '#ef4444', 'label' => 'Suspended'],
        default     => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'dot' => '#94a3b8', 'label' => ucfirst($statusVal)],
    };

    $role = $user->getRoleNames()->first() ?? '';
    $roleStyle = match ($role) {
        'super-admin' => ['bg' => 'rgba(220,38,38,.1)',   'color' => '#dc2626', 'label' => 'Super Admin'],
        'finance'     => ['bg' => 'rgba(13,148,136,.1)',  'color' => '#0d9488', 'label' => 'Finance'],
        'instructor'  => ['bg' => 'rgba(245,158,11,.1)',  'color' => '#d97706', 'label' => 'Instructor'],
        'student'     => ['bg' => 'rgba(37,99,235,.1)',   'color' => '#2563eb', 'label' => 'Student'],
        default       => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'label' => $role ? ucfirst($role) : '—'],
    };

    $instructorStatus = $user->instructorVerification?->status;
    $instructorStatusStyle = match ($instructorStatus) {
        'approved'  => ['bg' => 'rgba(22,163,74,.1)',   'color' => '#16a34a', 'label' => 'Approved'],
        'pending'   => ['bg' => 'rgba(245,158,11,.1)',  'color' => '#d97706', 'label' => 'Pending'],
        'rejected'  => ['bg' => 'rgba(220,38,38,.1)',   'color' => '#dc2626', 'label' => 'Rejected'],
        'suspended' => ['bg' => 'rgba(220,38,38,.1)',   'color' => '#dc2626', 'label' => 'Suspended'],
        default     => null,
    };

    $nameParts  = explode(' ', trim($user->name ?? '?'));
    $initials   = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr(end($nameParts), 0, 1));
    $avatarBg   = '#' . substr(md5($user->name ?? ''), 0, 6);
    $avatarUrl  = $user->avatar_url ?? null;
@endphp

<div class="uv">

<style>
.uv,.uv *,.uv *::before,.uv *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.uv {
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;
    line-height:1.5;
    display:grid;
    gap:20px;
    padding-bottom:48px;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 1px 4px rgba(15,23,42,.06),0 4px 16px rgba(15,23,42,.06);
    --accent:#7c3aed;
    --radius:14px;
    color:var(--t1);
}
html.dark .uv {
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
}

/* Header */
.uv-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.uv-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.uv-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
}

/* Buttons */
.uv-btn {
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
.uv-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.uv-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.uv-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.uv-btn-green {
    background:#16a34a;
    color:#fff;
    border:1px solid transparent;
}
.uv-btn-green:hover {
    background:#15803d;
}
.uv-btn-outline {
    background:transparent;
    border:1px solid var(--bd2);
    color:var(--t2);
}
.uv-btn-outline:hover {
    border-color:var(--accent);
    color:var(--accent);
}

/* Hero card */
.uv-hero {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:var(--radius);
    box-shadow:var(--sh);
    padding:24px 28px;
    display:flex;
    align-items:center;
    gap:20px;
    flex-wrap:wrap;
}
.uv-avatar-wrap {
    flex-shrink:0;
}
.uv-avatar-img {
    width:80px;
    height:80px;
    border-radius:16px;
    object-fit:cover;
    border:2px solid var(--bd2);
}
.uv-avatar-initials {
    width:80px;
    height:80px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    font-weight:800;
    color:#fff;
    letter-spacing:-.01em;
    flex-shrink:0;
}
.uv-hero-info {
    flex:1;
    min-width:0;
}
.uv-hero-name {
    font-size:clamp(18px,2vw,22px);
    font-weight:800;
    color:var(--t1);
    letter-spacing:-.02em;
    margin-bottom:8px;
}
.uv-badges-row {
    display:flex;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
}
.uv-badge {
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:3px 11px;
    border-radius:20px;
    font-size:12px;
    font-weight:700;
}
.uv-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.uv-hero-meta {
    display:flex;
    align-items:center;
    gap:32px;
    margin-left:auto;
    flex-shrink:0;
}
.uv-hero-stat {
    text-align:right;
}
.uv-hero-stat-label {
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.1em;
    color:var(--t2);
    margin-bottom:4px;
}
.uv-hero-stat-value {
    font-size:20px;
    font-weight:800;
    color:var(--t1);
    letter-spacing:-.01em;
}

/* Cards */
.uv-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}
@media(max-width:860px) {
    .uv-grid-2 {
        grid-template-columns:1fr;
    }
}
.uv-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:var(--sh);
}
.uv-card-header {
    padding:16px 20px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:flex-start;
    gap:12px;
}
.uv-card-icon {
    width:36px;
    height:36px;
    border-radius:9px;
    background:var(--p2);
    border:1px solid var(--bd);
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.uv-card-icon svg {
    width:17px;
    height:17px;
    color:var(--t2);
}
.uv-card-title {
    font-size:14px;
    font-weight:700;
    color:var(--t1);
}
.uv-card-subtitle {
    font-size:12px;
    color:var(--t2);
    margin-top:2px;
}

/* Field rows */
.uv-field-row {
    display:flex;
    align-items:center;
    padding:13px 20px;
    border-bottom:1px solid var(--bd);
}
.uv-field-row:last-child {
    border-bottom:none;
}
.uv-field-label {
    font-size:12.5px;
    color:var(--t2);
    min-width:100px;
    flex-shrink:0;
}
.uv-field-value {
    font-size:13px;
    font-weight:500;
    color:var(--t1);
    margin-left:auto;
    text-align:right;
}
.uv-field-value.mono {
    font-family:ui-monospace,SFMono-Regular,monospace;
    font-size:12.5px;
}
.uv-field-value.muted {
    color:var(--t2);
    font-style:italic;
    font-weight:400;
}

/* date · time split */
.uv-datetime {
    display:flex;
    align-items:center;
    gap:6px;
}
.uv-datetime-date {
    font-weight:600;
    color:var(--t1);
}
.uv-datetime-sep {
    color:var(--t3);
}
.uv-datetime-time {
    color:var(--t2);
}

@keyframes uvUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.uva {
    opacity:0;
    animation:uvUp .4s cubic-bezier(.16,1,.3,1) forwards;
}
.uv1 {
    animation-delay:.04s;
}
.uv2 {
    animation-delay:.09s;
}
.uv3 {
    animation-delay:.14s;
}
.uv4 {
    animation-delay:.19s;
}
</style>

{{-- ── Header ─────────────────────────────────────────────────────────── --}}
<div class="uv-header uva uv1">
    <h1 class="uv-page-title">View user</h1>
    <div class="uv-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="uv-btn uv-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to users
        </a>
        <a href="{{ $editUrl }}" wire:navigate class="uv-btn uv-btn-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
            Edit user
        </a>
    </div>
</div>

{{-- ── Hero card ───────────────────────────────────────────────────────── --}}
<div class="uv-hero uva uv2">
    <div class="uv-avatar-wrap">
        @if($avatarUrl)
            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="uv-avatar-img">
        @else
            <div class="uv-avatar-initials" style="background:{{ $avatarBg }}">{{ $initials }}</div>
        @endif
    </div>

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
            @if($instructorStatusStyle)
                <span class="uv-badge" style="background:{{ $instructorStatusStyle['bg'] }};color:{{ $instructorStatusStyle['color'] }}">
                    Instructor · {{ $instructorStatusStyle['label'] }}
                </span>
            @endif
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

{{-- ── Profile + Account ───────────────────────────────────────────────── --}}
<div class="uv-grid-2 uva uv3">

    {{-- Profile --}}
    <div class="uv-card">
        <div class="uv-card-header">
            <div class="uv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </div>
            <div>
                <div class="uv-card-title">Profile</div>
                <div class="uv-card-subtitle">Identity &amp; enrollment</div>
            </div>
        </div>

        <div class="uv-field-row">
            <span class="uv-field-label">Name</span>
            <span class="uv-field-value">{{ $user->name ?? '—' }}</span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Email</span>
            <span class="uv-field-value">{{ $user->email ?? '—' }}</span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Role</span>
            <span class="uv-field-value">
                <span class="uv-badge" style="background:{{ $roleStyle['bg'] }};color:{{ $roleStyle['color'] }}">{{ $roleStyle['label'] }}</span>
            </span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Status</span>
            <span class="uv-field-value">
                <span class="uv-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                    <span class="uv-dot" style="background:{{ $statusStyle['dot'] }}"></span>
                    {{ $statusStyle['label'] }}
                </span>
            </span>
        </div>
        @if($user->hasRole('instructor') && $instructorStatusStyle)
        <div class="uv-field-row">
            <span class="uv-field-label">Instructor</span>
            <span class="uv-field-value">
                <span class="uv-badge" style="background:{{ $instructorStatusStyle['bg'] }};color:{{ $instructorStatusStyle['color'] }}">{{ $instructorStatusStyle['label'] }}</span>
            </span>
        </div>
        @endif
    </div>

    {{-- Account --}}
    <div class="uv-card">
        <div class="uv-card-header">
            <div class="uv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <div>
                <div class="uv-card-title">Account</div>
                <div class="uv-card-subtitle">System &amp; verification</div>
            </div>
        </div>

        <div class="uv-field-row">
            <span class="uv-field-label">User ID</span>
            <span class="uv-field-value mono">{{ $user->id }}</span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Email verified</span>
            <span class="uv-field-value">
                @if($user->email_verified_at)
                    <span class="uv-datetime">
                        <span class="uv-datetime-date">{{ $user->email_verified_at->format('M d, Y') }}</span>
                        <span class="uv-datetime-sep">·</span>
                        <span class="uv-datetime-time">{{ $user->email_verified_at->format('H:i') }}</span>
                    </span>
                @else
                    <span class="uv-field-value muted">Not verified</span>
                @endif
            </span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Created</span>
            <span class="uv-field-value">
                <span class="uv-datetime">
                    <span class="uv-datetime-date">{{ $user->created_at?->format('M d, Y') }}</span>
                    <span class="uv-datetime-sep">·</span>
                    <span class="uv-datetime-time">{{ $user->created_at?->format('H:i') }}</span>
                </span>
            </span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Updated</span>
            <span class="uv-field-value">
                <span class="uv-datetime">
                    <span class="uv-datetime-date">{{ $user->updated_at?->format('M d, Y') }}</span>
                    <span class="uv-datetime-sep">·</span>
                    <span class="uv-datetime-time">{{ $user->updated_at?->format('H:i') }}</span>
                </span>
            </span>
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Phone</span>
            @if($user->phone)
                <span class="uv-field-value">{{ $user->phone }}</span>
            @else
                <span class="uv-field-value muted">Not provided</span>
            @endif
        </div>
        <div class="uv-field-row">
            <span class="uv-field-label">Last login</span>
            @if($user->last_login_at)
                <span class="uv-field-value">
                    <span class="uv-datetime">
                        <span class="uv-datetime-date">{{ $user->last_login_at->format('M d, Y') }}</span>
                        <span class="uv-datetime-sep">·</span>
                        <span class="uv-datetime-time">{{ $user->last_login_at->format('H:i') }}</span>
                    </span>
                </span>
            @else
                <span class="uv-field-value muted">Never</span>
            @endif
        </div>
    </div>

</div>

{{-- ── Activity summary ─────────────────────────────────────────────────── --}}
<div class="uv-card uva uv4">
    <div class="uv-card-header">
        <div class="uv-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125z"/></svg>
        </div>
        <div>
            <div class="uv-card-title">Activity</div>
            <div class="uv-card-subtitle">Orders, enrollments &amp; courses</div>
        </div>
    </div>
    @php
        $ordersCount      = $user->orders()->count();
        $enrollCount      = $user->enrollments()->count();
        $coursesCount     = $user->hasRole('instructor') ? $user->courses()->count() : null;
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:0;border-top:none">
        <div style="padding:20px 24px;border-right:1px solid var(--bd);text-align:center">
            <div style="font-size:28px;font-weight:800;color:var(--t1);letter-spacing:-.02em">{{ $ordersCount }}</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--t2);margin-top:4px">Orders</div>
        </div>
        <div style="padding:20px 24px;border-right:1px solid var(--bd);text-align:center">
            <div style="font-size:28px;font-weight:800;color:var(--t1);letter-spacing:-.02em">{{ $enrollCount }}</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--t2);margin-top:4px">Enrollments</div>
        </div>
        @if($coursesCount !== null)
        <div style="padding:20px 24px;text-align:center">
            <div style="font-size:28px;font-weight:800;color:var(--t1);letter-spacing:-.02em">{{ $coursesCount }}</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--t2);margin-top:4px">Courses</div>
        </div>
        @endif
    </div>
</div>

</div>
