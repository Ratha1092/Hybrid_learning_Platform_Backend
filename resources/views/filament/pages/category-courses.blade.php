@php
    $viewUrl   = fn($c) => route('filament.admin.resources.courses.view',   ['record' => $c->id]);
    $editUrl   = fn($c) => route('filament.admin.resources.courses.edit',   ['record' => $c->id]);
    $createUrl = route('filament.admin.resources.courses.create');

    $initials = strtoupper(mb_substr($category->name ?? '?', 0, 2));
    $hue      = abs(crc32($category->name ?? '')) % 360;
    $catBg    = "hsl({$hue},55%,42%)";

    $statusMap = [
        'published'      => ['bg' => '#ECFDF5', 'color' => '#065F46', 'dot' => '#10B981', 'label' => 'Published'],
        'pending_review' => ['bg' => '#FFFBEB', 'color' => '#92400E', 'dot' => '#F59E0B', 'label' => 'Pending Review'],
        'draft'          => ['bg' => '#F1F5F9', 'color' => '#475569', 'dot' => '#94A3B8', 'label' => 'Draft'],
        'rejected'       => ['bg' => '#FEF2F2', 'color' => '#991B1B', 'dot' => '#EF4444', 'label' => 'Rejected'],
        'archived'       => ['bg' => '#F1F5F9', 'color' => '#475569', 'dot' => '#94A3B8', 'label' => 'Archived'],
    ];
@endphp

<div>
<style>
/* ── Strip Filament outer padding ─────────────────────────────── */
.fi-main:has(.cc-wrap) {
    padding-inline:0 !important;
}
.fi-page-header-main-ctn:has(.cc-wrap) {
    padding-block:0 !important;
    row-gap:0 !important;
}
.fi-page-content:has(.cc-wrap) {
    row-gap:0 !important;
}

/* ── Design tokens (matches dashboard db-* values) ────────────── */
:root {
    --cc-bg:#F1F5F9;
    --cc-card:#FFFFFF;
    --cc-bd:#E2E8F0;
    --cc-t1:#0F172A;
    --cc-t2:#334155;
    --cc-t3:#64748B;
    --cc-t4:#94A3B8;
    --cc-blue:#3B82F6;
    --cc-blue-l:#EFF6FF;
    --cc-purple:#8B5CF6;
    --cc-sh:0 1px 3px 0 rgb(0 0 0/.07), 0 1px 2px -1px rgb(0 0 0/.05);
    --cc-sh-md:0 4px 6px -1px rgb(0 0 0/.08), 0 2px 4px -2px rgb(0 0 0/.05);
}
html.dark {
    --cc-bg:#0F172A;
    --cc-card:#1E293B;
    --cc-bd:#334155;
    --cc-t1:#F1F5F9;
    --cc-t2:#CBD5E1;
    --cc-t3:#94A3B8;
    --cc-t4:#64748B;
    --cc-blue:#60A5FA;
    --cc-blue-l:#1E3A5F;
    --cc-purple:#A78BFA;
    --cc-sh:0 1px 3px 0 rgb(0 0 0/.3), 0 1px 2px -1px rgb(0 0 0/.2);
    --cc-sh-md:0 4px 6px -1px rgb(0 0 0/.35), 0 2px 4px -2px rgb(0 0 0/.25);
}
html.dark .cc-status-badge {
    filter:brightness(1.6) saturate(.7);
}

.cc-wrap, .cc-wrap *, .cc-wrap *::before, .cc-wrap *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cc-wrap {
    background:var(--cc-bg);
    min-height:100vh;
    padding:1rem 1.25rem;
    font-family:'Inter',system-ui,sans-serif;
    font-size:13px;
    line-height:1.5;
    color:var(--cc-t1);
    display:flex;
    flex-direction:column;
    gap:1rem;
}

/* Top bar */
.cc-topbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:.75rem;
}
.cc-topbar-left {
    display:flex;
    align-items:center;
    gap:.875rem;
}
.cc-cat-icon {
    width:40px;
    height:40px;
    border-radius:.625rem;
    display:grid;
    place-items:center;
    font-size:.875rem;
    font-weight:800;
    color:#fff;
    flex-shrink:0;
}
.cc-cat-img {
    width:40px;
    height:40px;
    border-radius:.625rem;
    object-fit:cover;
    flex-shrink:0;
}
.cc-topbar-text h1 {
    font-size:1.25rem;
    font-weight:700;
    color:var(--cc-t1);
}
.cc-topbar-meta {
    display:flex;
    align-items:center;
    gap:.5rem;
    margin:.1rem 0 0;
    font-size:.75rem;
    color:var(--cc-t3);
    flex-wrap:wrap;
}
.cc-topbar-meta svg {
    width:12px;
    height:12px;
}

/* Buttons */
.cc-btn {
    display:inline-flex;
    align-items:center;
    gap:.35rem;
    padding:.4rem .875rem;
    border-radius:.5rem;
    font-size:.8125rem;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    border:none;
    font-family:inherit;
    transition:opacity .15s;
    white-space:nowrap;
}
.cc-btn:hover {
    opacity:.85;
}
.cc-btn svg {
    width:13px;
    height:13px;
}
.cc-btn-primary {
    background:var(--cc-blue);
    color:#fff;
}
.cc-btn-ghost {
    background:var(--cc-card);
    color:var(--cc-t2);
    border:1px solid var(--cc-bd);
}

/* Card */
.cc-card {
    background:var(--cc-card);
    border:1px solid var(--cc-bd);
    border-radius:.75rem;
    box-shadow:var(--cc-sh);
    overflow:hidden;
}

/* Toolbar */
.cc-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
    padding:.875rem 1.125rem;
    border-bottom:1px solid var(--cc-bd);
    flex-wrap:wrap;
}
.cc-toolbar-info {
    font-size:.8125rem;
    color:var(--cc-t3);
    font-weight:500;
}
.cc-search {
    display:flex;
    align-items:center;
    gap:.375rem;
    background:var(--cc-bg);
    border:1px solid var(--cc-bd);
    border-radius:.5rem;
    padding:.375rem .75rem;
}
.cc-search svg {
    width:14px;
    height:14px;
    color:var(--cc-t3);
    flex-shrink:0;
}
.cc-search input {
    background:none;
    border:none;
    outline:none;
    color:var(--cc-t1);
    font-size:.8125rem;
    font-family:inherit;
    width:200px;
}
.cc-search input::placeholder {
    color:var(--cc-t4);
}

/* Table */
.cc-table {
    width:100%;
    border-collapse:collapse;
}
.cc-table thead tr {
    border-bottom:1px solid var(--cc-bd);
}
.cc-table th {
    padding:.625rem .875rem;
    text-align:left;
    font-size:.6875rem;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--cc-t3);
    white-space:nowrap;
}
.cc-table th.th-c {
    text-align:center;
}
.cc-table th.th-r {
    text-align:right;
}
.cc-table tbody tr {
    border-bottom:1px solid var(--cc-bd);
    transition:background .12s;
    cursor:pointer;
}
.cc-table tbody tr:last-child {
    border-bottom:none;
}
.cc-table tbody tr:hover {
    background:var(--cc-bg);
}
.cc-table td {
    padding:.75rem .875rem;
    vertical-align:middle;
}

/* Cells */
.cc-id {
    font-size:.75rem;
    font-weight:700;
    color:var(--cc-t3);
}
.cc-course-cell {
    display:flex;
    align-items:center;
    gap:.625rem;
}
.cc-thumb {
    width:38px;
    height:38px;
    border-radius:.5rem;
    object-fit:cover;
    flex-shrink:0;
}
.cc-thumb-init {
    width:38px;
    height:38px;
    border-radius:.5rem;
    display:grid;
    place-items:center;
    font-size:.75rem;
    font-weight:800;
    color:#fff;
    flex-shrink:0;
}
.cc-course-title {
    font-size:.8125rem;
    font-weight:600;
    color:var(--cc-t1);
    line-height:1.3;
}
.cc-course-sub {
    font-size:.6875rem;
    color:var(--cc-t3);
    margin-top:.1rem;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    max-width:220px;
}
.cc-inst-cell {
    display:flex;
    align-items:center;
    gap:.5rem;
}
.cc-inst-avatar {
    width:24px;
    height:24px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
}
.cc-inst-name {
    font-size:.8125rem;
    color:var(--cc-t1);
    font-weight:500;
    white-space:nowrap;
}
.cc-price {
    font-size:.8125rem;
    font-weight:700;
    color:var(--cc-t1);
    white-space:nowrap;
}
.cc-status-badge {
    display:inline-flex;
    align-items:center;
    gap:.3rem;
    padding:.2rem .55rem;
    border-radius:.375rem;
    font-size:.6875rem;
    font-weight:700;
    white-space:nowrap;
}
.cc-status-dot {
    width:5px;
    height:5px;
    border-radius:50%;
    flex-shrink:0;
}
.cc-students-val {
    display:inline-flex;
    align-items:center;
    gap:.3rem;
    font-size:.8125rem;
    font-weight:600;
    color:var(--cc-t1);
}
.cc-students-val svg {
    width:13px;
    height:13px;
    color:var(--cc-t3);
}
.cc-date {
    font-size:.75rem;
    color:var(--cc-t3);
    white-space:nowrap;
}

/* Actions */
.cc-actions {
    display:flex;
    align-items:center;
    gap:.25rem;
    justify-content:flex-end;
}
.cc-act {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:28px;
    height:28px;
    border-radius:.4rem;
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    color:var(--cc-t3);
    text-decoration:none;
    transition:all .12s;
}
.cc-act:hover {
    background:var(--cc-bg);
    border-color:var(--cc-bd);
    color:var(--cc-t1);
}
.cc-act svg {
    width:14px;
    height:14px;
}

/* Empty */
.cc-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:3.5rem 1.5rem;
    gap:.625rem;
    color:var(--cc-t3);
}
.cc-empty svg {
    width:2.5rem;
    height:2.5rem;
    opacity:.3;
}
.cc-empty p {
    font-size:.8125rem;
}

/* Footer / Pagination */
.cc-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:.75rem 1.125rem;
    border-top:1px solid var(--cc-bd);
    flex-wrap:wrap;
    gap:.625rem;
}
.cc-footer-info {
    font-size:.75rem;
    color:var(--cc-t3);
}
.cc-pages {
    display:flex;
    align-items:center;
    gap:.375rem;
}
.cc-page-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:28px;
    height:28px;
    padding:0 .5rem;
    border-radius:.4rem;
    font-size:.75rem;
    font-weight:600;
    color:var(--cc-t3);
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    font-family:inherit;
    transition:all .12s;
}
.cc-page-btn:not(.cc-disabled):hover {
    background:var(--cc-bg);
    border-color:var(--cc-bd);
    color:var(--cc-t1);
}
.cc-page-btn.cc-active {
    background:var(--cc-blue);
    color:#fff;
    border-color:transparent;
}
.cc-page-btn.cc-disabled {
    opacity:.35;
    pointer-events:none;
}
.cc-per-page {
    display:flex;
    align-items:center;
    gap:.375rem;
    font-size:.75rem;
    color:var(--cc-t3);
}
.cc-per-page select {
    appearance:none;
    background:var(--cc-card);
    border:1px solid var(--cc-bd);
    border-radius:.4rem;
    padding:.2rem 1.375rem .2rem .5rem;
    font-size:.75rem;
    font-weight:600;
    color:var(--cc-t1);
    font-family:inherit;
    cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right .375rem center;
    outline:none;
}
.cc-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
</style>

<div class="cc-wrap">

    {{-- Header --}}
    <div class="cc-topbar">
        <div class="cc-topbar-left">
            @if($category->image_url ?? null)
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="cc-cat-img">
            @else
                <div class="cc-cat-icon" style="background:{{ $catBg }}">{{ $initials }}</div>
            @endif
            <div>
                <h1 class="cc-topbar-text">{{ $category->name }}</h1>
                <div class="cc-topbar-meta">
                    @if($category->description)
                        <span>{{ \Illuminate\Support\Str::limit($category->description, 55) }}</span>
                        <span>·</span>
                    @endif
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <span>{{ $category->courses_count }} {{ Str::plural('course', $category->courses_count) }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem">
            <a href="{{ $backUrl }}" wire:navigate class="cc-btn cc-btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                </svg>
                Back
            </a>
            <a href="{{ $createUrl }}" wire:navigate class="cc-btn cc-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Course
            </a>
        </div>
    </div>

    {{-- Table card --}}
    <div class="cc-card">

        {{-- Toolbar --}}
        <div class="cc-toolbar">
            <span class="cc-toolbar-info">
                @if($search) {{ $total }} {{ Str::plural('result', $total) }} for "{{ $search }}"
                @else {{ $total }} {{ Str::plural('course', $total) }} in this category
                @endif
            </span>
            <div class="cc-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search courses...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="cc-loading" wire:target="gotoPage,search,setPerPage">
        <table class="cc-table">
            <colgroup>
                <col style="width:5%">
                <col style="width:28%">
                <col style="width:18%">
                <col style="width:9%">
                <col style="width:13%">
                <col style="width:10%">
                <col style="width:10%">
                <col style="width:7%">
            </colgroup>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th class="th-c">Students</th>
                    <th>Created</th>
                    <th class="th-r">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                @php
                    $thumbUrl    = $course->thumbnail_url;
                    $thumbInitBg = '#' . substr(md5($course->title ?? ''), 0, 6);
                    $thumbInit   = strtoupper(substr($course->title ?? 'C', 0, 2));

                    $instName      = $course->instructor?->name ?? '—';
                    $instBg        = substr(md5($instName), 0, 6);
                    $instAvatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($instName)
                                   . '&background=' . $instBg . '&color=fff&bold=true&size=64';

                    $statusKey   = $course->status ?? 'draft';
                    $st          = $statusMap[$statusKey] ?? ['bg' => '#F1F5F9', 'color' => '#475569', 'dot' => '#94A3B8', 'label' => ucfirst($statusKey)];
                @endphp
                <tr onclick="Livewire.navigate('{{ $viewUrl($course) }}')">
                    <td><span class="cc-id">#{{ $course->id }}</span></td>

                    <td>
                        <div class="cc-course-cell">
                            @if($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="" class="cc-thumb">
                            @else
                                <div class="cc-thumb-init" style="background:{{ $thumbInitBg }}">{{ $thumbInit }}</div>
                            @endif
                            <div>
                                <div class="cc-course-title">{{ $course->title }}</div>
                                @if($course->short_description)
                                    <div class="cc-course-sub">{{ \Illuminate\Support\Str::limit($course->short_description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="cc-inst-cell">
                            <img src="{{ $instAvatarUrl }}" class="cc-inst-avatar" alt="">
                            <span class="cc-inst-name">{{ $instName }}</span>
                        </div>
                    </td>

                    <td><span class="cc-price">${{ number_format((float)$course->price, 2) }}</span></td>

                    <td>
                        <span class="cc-status-badge" style="background:{{ $st['bg'] }};color:{{ $st['color'] }}">
                            <span class="cc-status-dot" style="background:{{ $st['dot'] }}"></span>
                            {{ $st['label'] }}
                        </span>
                    </td>

                    <td class="th-c">
                        <div class="cc-students-val">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z"/>
                            </svg>
                            {{ number_format($course->enrollments_count) }}
                        </div>
                    </td>

                    <td><span class="cc-date">{{ $course->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="cc-actions">
                            <a href="{{ $viewUrl($course) }}" wire:navigate class="cc-act" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                </svg>
                            </a>
                            <a href="{{ $editUrl($course) }}" wire:navigate class="cc-act" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="cc-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <p>{{ $search ? 'No courses match "' . $search . '"' : 'No courses in this category yet.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="cc-footer">
            <div class="cc-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }}–{{ min($curPage * $perPage, $total) }} of {{ number_format($total) }}
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:1rem">
                <div class="cc-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="cc-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                        class="cc-page-btn {{ $curPage === 1 ? 'cc-disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                            class="cc-page-btn {{ $curPage === $p ? 'cc-active' : '' }}">{{ $p }}</button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                        class="cc-page-btn {{ $curPage === $totalPages ? 'cc-disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
</div>
