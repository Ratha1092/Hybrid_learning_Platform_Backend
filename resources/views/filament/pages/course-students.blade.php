{{-- Filament page content (rendered by CourseStudents page class) --}}
<div class="cs">
<style>
.cs, .cs *, .cs *::before, .cs *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cs {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
    font-size:13px;
    line-height:1.5;
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
html:not(.dark) .cs {
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

.cs-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.cs-back {
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    font-weight:600;
    color:var(--t2);
    text-decoration:none;
    padding:6px 12px;
    border-radius:8px;
    border:1px solid var(--bd2);
    transition:background .15s, color .15s;
}
.cs-back:hover {
    background:var(--p2);
    color:var(--t1);
}
.cs-back svg {
    width:14px;
    height:14px;
}
.cs-title h1 {
    font-size:clamp(18px, 2vw, 24px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.2;
}
.cs-title p {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
}

.cs-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    box-shadow:var(--sh);
}
.cs-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.cs-count {
    font-size:12px;
    color:var(--t2);
}
.cs-count strong {
    color:var(--t1);
    font-weight:700;
}
.cs-search {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.cs-search svg {
    width:14px;
    height:14px;
    color:var(--t2);
    flex-shrink:0;
}
.cs-search input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.cs-search input::placeholder {
    color:var(--t2);
}

.cs-table {
    width:100%;
    border-collapse:collapse;
}
.cs-table thead tr {
    border-bottom:1px solid var(--bd);
}
.cs-table th {
    padding:10px 16px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.cs-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.cs-table tbody tr:last-child {
    border-bottom:none;
}
.cs-table tbody tr:hover {
    background:var(--p2);
}
.cs-row-link {
    cursor:pointer;
}
.cs-table td {
    padding:12px 16px;
    vertical-align:middle;
}

.cs-user {
    display:flex;
    align-items:center;
    gap:10px;
}
.cs-avatar {
    width:32px;
    height:32px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
}
.cs-name {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.cs-email {
    font-size:11.5px;
    color:var(--t2);
}
.cs-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 9px;
    border-radius:6px;
    font-size:11px;
    font-weight:700;
    white-space:nowrap;
}
.cs-dot {
    width:5px;
    height:5px;
    border-radius:50%;
    flex-shrink:0;
}
.cs-progress-wrap {
    display:flex;
    align-items:center;
    gap:8px;
}
.cs-progress-bar {
    flex:1;
    height:5px;
    border-radius:3px;
    background:var(--bd2);
    max-width:80px;
    overflow:hidden;
}
.cs-progress-fill {
    height:100%;
    border-radius:3px;
    background:#7c3aed;
}
.cs-progress-val {
    font-size:11.5px;
    font-weight:700;
    color:var(--t2);
    min-width:30px;
}
.cs-date {
    font-size:12px;
    color:var(--t2);
    white-space:nowrap;
}

/* ── Action menu ── */
.cs-actions {
    position:relative;
}
.cs-action-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:28px;
    height:28px;
    border-radius:7px;
    border:1px solid var(--bd2);
    background:transparent;
    color:var(--t2);
    cursor:pointer;
    transition:background .12s, color .12s;
    font-family:inherit;
}
.cs-action-btn:hover {
    background:var(--p2);
    color:var(--t1);
    border-color:var(--bd2);
}
.cs-action-btn svg {
    width:14px;
    height:14px;
    pointer-events:none;
}
.cs-menu {
    position:absolute;
    right:0;
    top:32px;
    z-index:50;
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:10px;
    box-shadow:0 8px 32px rgba(0,0,0,.3);
    min-width:160px;
    overflow:hidden;
}
html:not(.dark) .cs-menu {
    box-shadow:0 4px 20px rgba(15,23,42,.18);
}
.cs-menu-item {
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
}
.cs-menu-item svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.cs-menu-item:hover {
    background:var(--p2);
}
.cs-menu-item.warn {
    color:#f59e0b;
}
.cs-menu-item.warn:hover {
    background:rgba(245,158,11,.08);
}
.cs-menu-item.success {
    color:#34d399;
}
.cs-menu-item.success:hover {
    background:rgba(52,211,153,.08);
}
.cs-menu-item.danger {
    color:#f87171;
}
.cs-menu-item.danger:hover {
    background:rgba(248,113,113,.08);
}
.cs-menu-divider {
    height:1px;
    background:var(--bd);
    margin:4px 0;
}

.cs-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.cs-empty svg {
    width:40px;
    height:40px;
    opacity:.3;
}
.cs-empty p {
    font-size:13px;
}

.cs-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.cs-footer-info {
    font-size:12px;
    color:var(--t2);
}
.cs-pages {
    display:flex;
    align-items:center;
    gap:6px;
}
.cs-page-btn {
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
.cs-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
.cs-page-btn:hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.cs-page-btn.active {
    background:#7c3aed;
    color:#fff;
    border-color:transparent;
}
.cs-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
</style>

@php
    $statusStyle = fn($s) => match($s) {
        'active'    => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Active'],
        'completed' => ['bg' => 'rgba(99,102,241,.12)',  'color' => '#818cf8', 'label' => 'Completed'],
        'expired'   => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Expired'],
        'suspended' => ['bg' => 'rgba(245,158,11,.12)',  'color' => '#f59e0b', 'label' => 'Suspended'],
        default     => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($s ?? 'Unknown')],
    };
@endphp

    {{-- Header --}}
    <div class="cs-header">
        <div style="display:flex;flex-direction:column;gap:10px">
            <a href="{{ url('/admin/courses?tab=published') }}" wire:navigate class="cs-back" style="width:fit-content">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                </svg>
                Back to Courses
            </a>
            <div class="cs-title">
                <h1>{{ $course->title }}</h1>
                <p>Enrolled students &mdash; {{ number_format($total) }} total</p>
            </div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="cs-card">

        {{-- Toolbar --}}
        <div class="cs-toolbar">
            <span class="cs-count">Showing <strong>{{ $total }}</strong> student{{ $total !== 1 ? 's' : '' }}</span>
            <div class="cs-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search by name or email...">
            </div>
        </div>

        {{-- Table --}}
        <div wire:loading.class="cs-loading" wire:target="gotoPage,search">
        <table class="cs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Enrolled At</th>
                    <th>Last Access</th>
                    <th>Completed At</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($enrollments as $i => $enrollment)
                @php
                    $user = $enrollment->user;
                    $ss   = $statusStyle($enrollment->status);
                    $progress = (float) $enrollment->progress_percentage;
                    $bgHex = substr(md5($user?->name ?? 'x'), 0, 6);
                    $avatarUrl = $user?->avatar_url
                        ?? 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                    $studentViewUrl = $user ? route('filament.admin.resources.users.view', ['record' => $user->id]) : null;
                    $isSuspended = $enrollment->status === 'suspended';
                @endphp
                <tr @if($studentViewUrl) class="cs-row-link" onclick="Livewire.navigate('{{ $studentViewUrl }}')" @endif>
                    <td><span style="font-size:11.5px;color:var(--t2);font-weight:700">{{ ($curPage - 1) * $perPage + $i + 1 }}</span></td>

                    <td>
                        <div class="cs-user">
                            <img src="{{ $avatarUrl }}" class="cs-avatar" alt="">
                            <div>
                                <div class="cs-name">{{ $user?->name ?? '—' }}</div>
                                <div class="cs-email">{{ $user?->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="cs-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="cs-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td>
                        <div class="cs-progress-wrap">
                            <div class="cs-progress-bar">
                                <div class="cs-progress-fill" style="width:{{ min(100, $progress) }}%"></div>
                            </div>
                            <span class="cs-progress-val">{{ number_format($progress, 0) }}%</span>
                        </div>
                    </td>

                    <td><span class="cs-date">{{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</span></td>
                    <td><span class="cs-date">{{ $enrollment->last_accessed_at?->diffForHumans() ?? '—' }}</span></td>
                    <td>
                        @if($enrollment->completed_at)
                            <span class="cs-date" style="color:#34d399">{{ $enrollment->completed_at->format('M d, Y') }}</span>
                        @else
                            <span class="cs-date">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td onclick="event.stopPropagation()">
                        <div class="cs-actions" x-data="{ open: false }" @click.outside="open = false" @cs-menu-open.window="if ($event.detail !== {{ $enrollment->id }}) open = false">
                            <button type="button" class="cs-action-btn" @click.stop="open = !open; if (open) $dispatch('cs-menu-open', {{ $enrollment->id }})" title="Actions">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.5 12a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0zm6 0a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0zm6 0a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <div class="cs-menu" x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" style="display:none">

                                @if($studentViewUrl)
                                <button type="button" class="cs-menu-item" onclick="Livewire.navigate('{{ $studentViewUrl }}')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    View Profile
                                </button>
                                <div class="cs-menu-divider"></div>
                                @endif

                                @if($isSuspended)
                                <button type="button" class="cs-menu-item success"
                                    @click="open=false"
                                    wire:click="unsuspendEnrollment({{ $enrollment->id }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                    Unsuspend
                                </button>
                                @else
                                <button type="button" class="cs-menu-item warn"
                                    @click="open=false"
                                    wire:click="suspendEnrollment({{ $enrollment->id }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Suspend
                                </button>
                                @endif

                                <button type="button" class="cs-menu-item danger"
                                    @click="open=false"
                                    wire:click="removeEnrollment({{ $enrollment->id }})"
                                    wire:confirm="Remove {{ $user?->name ?? 'this student' }} from the course? This cannot be undone easily.">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="cs-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 3.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            <p>No students enrolled{{ $search ? ' matching "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        @if($totalPages > 1)
        <div class="cs-footer">
            <div class="cs-footer-info">
                Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} students
            </div>
            <div class="cs-pages">
                <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                   class="cs-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
                @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                    <button type="button" wire:click="gotoPage({{ $p }})"
                       class="cs-page-btn {{ $curPage === $p ? 'active' : '' }}">
                        {{ $p }}
                    </button>
                @endfor
                <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                   class="cs-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
        @endif

    </div>

</div>
