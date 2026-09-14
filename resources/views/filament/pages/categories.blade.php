@php
    $createUrl  = route('filament.admin.resources.categories.create');
    $editUrl    = fn($cat) => route('filament.admin.resources.categories.edit', ['record' => $cat->id]);
    $viewUrl    = fn($cat) => route('filament.admin.resources.categories.view', ['record' => $cat->id]);
    $coursesUrl = fn($cat) => route('filament.admin.pages.category-courses') . '?id=' . $cat->id;
    $isSuperAdmin = \App\Support\PanelAccess::isSuperAdmin();
@endphp

<div x-data="{ view: localStorage.getItem('cat-view') || 'table' }" x-init="$watch('view', v => localStorage.setItem('cat-view', v))">
<style>
/* ── Strip Filament outer padding on this page ────────────────── */
.fi-main:has(.cat-wrap) {
    padding-inline:0 !important;
}
.fi-page-header-main-ctn:has(.cat-wrap) {
    padding-block:0 !important;
    row-gap:0 !important;
}
.fi-page-content:has(.cat-wrap) {
    row-gap:0 !important;
}

/* ── Design tokens (matches dashboard db-* values) ────────────── */
:root {
    --cat-bg:#F1F5F9;
    --cat-card:#FFFFFF;
    --cat-bd:#E2E8F0;
    --cat-t1:#0F172A;
    --cat-t2:#334155;
    --cat-t3:#64748B;
    --cat-t4:#94A3B8;
    --cat-blue:#3B82F6;
    --cat-blue-l:#EFF6FF;
    --cat-green:#10B981;
    --cat-sh:0 1px 3px 0 rgb(0 0 0/.07), 0 1px 2px -1px rgb(0 0 0/.05);
}
html.dark {
    --cat-bg:#0F172A;
    --cat-card:#1E293B;
    --cat-bd:#334155;
    --cat-t1:#F1F5F9;
    --cat-t2:#CBD5E1;
    --cat-t3:#94A3B8;
    --cat-t4:#64748B;
    --cat-blue:#60A5FA;
    --cat-blue-l:#1E3A5F;
    --cat-sh:0 1px 3px 0 rgb(0 0 0/.3), 0 1px 2px -1px rgb(0 0 0/.2);
}

.cat-wrap, .cat-wrap *, .cat-wrap *::before, .cat-wrap *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cat-wrap {
    background:var(--cat-bg);
    min-height:100vh;
    padding:1rem 1.25rem;
    font-family:'Inter', system-ui, sans-serif;
    font-size:13px;
    line-height:1.5;
    color:var(--cat-t1);
    display:flex;
    flex-direction:column;
    gap:1rem;
}

/* Top bar */
.cat-topbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:.75rem;
}
.cat-topbar-left h1 {
    font-size:1.25rem;
    font-weight:700;
    color:var(--cat-t1);
}
.cat-topbar-left p {
    font-size:.8125rem;
    color:var(--cat-t3);
    margin:.1rem 0 0;
}

/* Buttons */
.cat-btn {
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
.cat-btn:hover {
    opacity:.85;
}
.cat-btn svg {
    width:13px;
    height:13px;
}
.cat-btn-primary {
    background:var(--cat-blue);
    color:#fff;
}
.cat-btn-ghost {
    background:var(--cat-card);
    color:var(--cat-t2);
    border:1px solid var(--cat-bd);
}

/* View toggle */
.cat-view-toggle {
    display:flex;
    align-items:center;
    gap:2px;
    background:var(--cat-bg);
    border:1px solid var(--cat-bd);
    border-radius:.5rem;
    padding:2px;
}
.cat-view-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    border-radius:.4rem;
    border:none;
    background:none;
    color:var(--cat-t3);
    cursor:pointer;
    transition:background .15s, color .15s;
}
.cat-view-btn.cat-view-active {
    background:var(--cat-card);
    color:var(--cat-blue);
    box-shadow:var(--cat-sh);
}
.cat-view-btn svg {
    width:15px;
    height:15px;
}

/* Grid view */
.cat-grid {
    display:grid;
    grid-template-columns:repeat(5, 1fr);
    gap:1rem;
    padding:1rem;
}
@media (max-width: 1280px) {
    .cat-grid {
        grid-template-columns:repeat(4, 1fr);
    }
}
@media (max-width: 960px) {
    .cat-grid {
        grid-template-columns:repeat(3, 1fr);
    }
}
@media (max-width: 640px) {
    .cat-grid {
        grid-template-columns:repeat(2, 1fr);
    }
}
.cat-item-card {
    background:var(--cat-card);
    border:1px solid var(--cat-bd);
    border-radius:.75rem;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    cursor:pointer;
    transition:box-shadow .18s, transform .18s, border-color .18s;
}
.cat-item-card:hover {
    box-shadow:0 8px 24px rgba(0,0,0,.18);
    transform:translateY(-2px);
    border-color:var(--cat-t4);
}
.cat-item-thumb {
    position:relative;
    height:110px;
    flex-shrink:0;
    overflow:hidden;
    background:var(--cat-bg);
}
.cat-item-thumb img {
    width:100%;
    height:100%;
    object-fit:cover;
}
.cat-item-thumb-init {
    width:100%;
    height:100%;
    display:grid;
    place-items:center;
    color:#fff;
    font-size:1.5rem;
    font-weight:700;
}
.cat-item-body {
    padding:.75rem .875rem;
    flex:1;
    display:flex;
    flex-direction:column;
    gap:.375rem;
}
.cat-item-title {
    font-size:.8125rem;
    font-weight:700;
    color:var(--cat-t1);
    line-height:1.35;
    display:-webkit-box;
    -webkit-line-clamp:1;
    -webkit-box-orient:vertical;
    overflow:hidden;
}
.cat-item-desc {
    font-size:.75rem;
    color:var(--cat-t3);
    line-height:1.4;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
    min-height:2.1em;
}
.cat-item-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:.625rem .875rem;
    border-top:1px solid var(--cat-bd);
    gap:.5rem;
    margin-top:auto;
}
.cat-item-courses {
    display:flex;
    align-items:center;
    gap:.25rem;
    font-size:.75rem;
    color:var(--cat-t3);
    font-weight:600;
}
.cat-item-courses svg {
    width:13px;
    height:13px;
}
.cat-item-actions {
    display:flex;
    align-items:center;
    gap:4px;
}

/* Card */
.cat-card {
    background:var(--cat-card);
    border:1px solid var(--cat-bd);
    border-radius:.75rem;
    box-shadow:var(--cat-sh);
    overflow:hidden;
}

/* Status tabs */
.cat-tabs {
    display:flex;
    align-items:center;
    gap:.375rem;
    padding:.75rem 1.125rem 0;
}
.cat-tab {
    display:inline-flex;
    align-items:center;
    gap:.375rem;
    padding:.4375rem .75rem;
    border-radius:8px;
    border:1px solid transparent;
    background:none;
    cursor:pointer;
    font-family:inherit;
    font-size:.8125rem;
    font-weight:600;
    color:var(--cat-t3);
}
.cat-tab:hover {
    background:var(--cat-bg);
}
.cat-tab.active {
    background:var(--cat-bg);
    border-color:var(--cat-bd);
    color:var(--cat-t1);
}
.cat-tab-badge {
    font-size:.6875rem;
    font-weight:700;
    padding:.0625rem .375rem;
    border-radius:9999px;
}

/* Toolbar */
.cat-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
    padding:.875rem 1.125rem;
    border-bottom:1px solid var(--cat-bd);
    flex-wrap:wrap;
}
.cat-toolbar-info {
    font-size:.8125rem;
    color:var(--cat-t3);
    font-weight:500;
}
.cat-search {
    display:flex;
    align-items:center;
    gap:.375rem;
    background:var(--cat-bg);
    border:1px solid var(--cat-bd);
    border-radius:.5rem;
    padding:.375rem .75rem;
}
.cat-search svg {
    width:14px;
    height:14px;
    color:var(--cat-t3);
    flex-shrink:0;
}
.cat-search input {
    background:none;
    border:none;
    outline:none;
    color:var(--cat-t1);
    font-size:.8125rem;
    font-family:inherit;
    width:200px;
}
.cat-search input::placeholder {
    color:var(--cat-t4);
}

/* Table */
.cat-table {
    width:100%;
    border-collapse:collapse;
}
.cat-table thead tr {
    border-bottom:1px solid var(--cat-bd);
}
.cat-table th {
    padding:.625rem .875rem;
    text-align:left;
    font-size:.6875rem;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--cat-t3);
    white-space:nowrap;
}
.cat-table th.th-c {
    text-align:center;
}
.cat-table th.th-r {
    text-align:right;
}
.cat-table tbody tr {
    border-bottom:1px solid var(--cat-bd);
    transition:background .12s;
    cursor:pointer;
}
.cat-table tbody tr:last-child {
    border-bottom:none;
}
.cat-table tbody tr:hover {
    background:var(--cat-bg);
}
.cat-table td {
    padding:.75rem .875rem;
    vertical-align:middle;
}

/* Cells */
.cat-id {
    font-size:.75rem;
    font-weight:700;
    color:var(--cat-t3);
}
.cat-name-cell {
    display:flex;
    align-items:center;
    gap:.625rem;
}
.cat-avatar {
    width:32px;
    height:32px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
}
.cat-avatar-init {
    width:32px;
    height:32px;
    border-radius:.5rem;
    display:grid;
    place-items:center;
    font-size:.75rem;
    font-weight:800;
    color:#fff;
    flex-shrink:0;
}
.cat-name {
    font-size:.8125rem;
    font-weight:600;
    color:var(--cat-t1);
}
.cat-desc {
    font-size:.75rem;
    color:var(--cat-t3);
    max-width:280px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.cat-courses-link {
    display:inline-flex;
    align-items:center;
    gap:.3rem;
    font-size:.8125rem;
    font-weight:600;
    color:var(--cat-blue);
    text-decoration:none;
    padding:.2rem .5rem;
    border-radius:.375rem;
    transition:background .12s;
}
.cat-courses-link:hover {
    background:var(--cat-blue-l);
}
.cat-courses-link svg {
    width:13px;
    height:13px;
}
.cat-date {
    font-size:.75rem;
    color:var(--cat-t3);
    white-space:nowrap;
}

/* Action buttons */
.cat-actions {
    display:flex;
    align-items:center;
    gap:.25rem;
    justify-content:flex-end;
}
.cat-act {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:28px;
    height:28px;
    border-radius:.4rem;
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    color:var(--cat-t3);
    text-decoration:none;
    transition:all .12s;
}
.cat-act:hover {
    background:var(--cat-bg);
    border-color:var(--cat-bd);
    color:var(--cat-t1);
}
.cat-act svg {
    width:14px;
    height:14px;
}
.cat-act-danger:hover {
    background:rgba(220,38,38,.12);
    border-color:rgba(220,38,38,.35);
    color:#dc2626;
}

/* Empty */
.cat-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:3.5rem 1.5rem;
    gap:.625rem;
    color:var(--cat-t3);
}
.cat-empty svg {
    width:2.5rem;
    height:2.5rem;
    opacity:.3;
}
.cat-empty p {
    font-size:.8125rem;
}

/* Footer / Pagination */
.cat-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:.75rem 1.125rem;
    border-top:1px solid var(--cat-bd);
    flex-wrap:wrap;
    gap:.625rem;
}
.cat-footer-info {
    font-size:.75rem;
    color:var(--cat-t3);
}
.cat-pages {
    display:flex;
    align-items:center;
    gap:.375rem;
}
.cat-page-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:28px;
    height:28px;
    padding:0 .5rem;
    border-radius:.4rem;
    font-size:.75rem;
    font-weight:600;
    color:var(--cat-t3);
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    font-family:inherit;
    transition:all .12s;
}
.cat-page-btn:not(.cat-disabled):hover {
    background:var(--cat-bg);
    border-color:var(--cat-bd);
    color:var(--cat-t1);
}
.cat-page-btn.cat-active {
    background:var(--cat-blue);
    color:#fff;
    border-color:transparent;
}
.cat-page-btn.cat-disabled {
    opacity:.35;
    pointer-events:none;
}
.cat-per-page {
    display:flex;
    align-items:center;
    gap:.375rem;
    font-size:.75rem;
    color:var(--cat-t3);
}
.cat-per-page select {
    appearance:none;
    background:var(--cat-card);
    border:1px solid var(--cat-bd);
    border-radius:.4rem;
    padding:.2rem 1.375rem .2rem .5rem;
    font-size:.75rem;
    font-weight:600;
    color:var(--cat-t1);
    font-family:inherit;
    cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right .375rem center;
    outline:none;
}
.cat-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
</style>

<div class="cat-wrap">

    {{-- Header --}}
    <div class="cat-topbar">
        <div class="cat-topbar-left">
            <h1>Categories</h1>
            <p>Manage course categories and their assignments.</p>
        </div>
        <div style="display:flex;align-items:center;gap:.625rem">
            <div class="cat-view-toggle">
                <button type="button" class="cat-view-btn" :class="{'cat-view-active': view==='table'}" @click="view='table'" title="Table view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M3 6.2A3.2 3.2 0 0 1 6.2 3h11.6A3.2 3.2 0 0 1 21 6.2v11.6a3.2 3.2 0 0 1-3.2 3.2H6.2A3.2 3.2 0 0 1 3 17.8V6.2z"/></svg>
                </button>
                <button type="button" class="cat-view-btn" :class="{'cat-view-active': view==='grid'}" @click="view='grid'" title="Card view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                </button>
            </div>
            <a href="{{ $createUrl }}" wire:navigate class="cat-btn cat-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Category
            </a>
        </div>
    </div>

    <div class="cat-tabs">
        @foreach ($tabs as $t)
        <button type="button" wire:click="selectStatus('{{ $t['key'] }}')" class="cat-tab {{ $status === $t['key'] ? 'active' : '' }}">
            {{ $t['label'] }}
            <span class="cat-tab-badge" style="background:{{ $t['color'] }}20;color:{{ $t['color'] }}">{{ $t['count'] }}</span>
        </button>
        @endforeach
    </div>

    {{-- Table card --}}
    <div class="cat-card">

        {{-- Toolbar --}}
        <div class="cat-toolbar">
            <span class="cat-toolbar-info">{{ number_format($total) }} {{ Str::plural('category', $total) }}</span>
            <div class="cat-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search categories...">
            </div>
        </div>

        {{-- Table view --}}
        <div x-show="view==='table'">
        <div style="overflow-x:auto" wire:loading.class="cat-loading" wire:target="gotoPage,search,setPerPage">
        <table class="cat-table">
            <colgroup>
                <col style="width:6%">
                <col style="width:24%">
                <col style="width:38%">
                <col style="width:12%">
                <col style="width:13%">
                <col style="width:7%">
            </colgroup>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="th-c">Courses</th>
                    <th>Created</th>
                    <th class="th-r">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                @php
                    $initials = strtoupper(mb_substr($cat->name, 0, 2));
                    $hue      = abs(crc32($cat->name)) % 360;
                    $bgColor  = "hsl({$hue},55%,42%)";
                @endphp
                <tr wire:key="category-row-{{ $cat->id }}" onclick="Livewire.navigate('{{ $viewUrl($cat) }}')">
                    <td><span class="cat-id">{{ $cat->id }}</span></td>

                    <td>
                        <div class="cat-name-cell">
                            @if($cat->image)
                                <img src="{{ $cat->image_url }}" class="cat-avatar" alt="">
                            @else
                                <div class="cat-avatar-init" style="background:{{ $bgColor }}">{{ $initials }}</div>
                            @endif
                            <span class="cat-name">{{ $cat->name }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="cat-desc">{{ $cat->description ? \Illuminate\Support\Str::limit($cat->description, 65) : '—' }}</span>
                    </td>

                    <td class="th-c" onclick="event.stopPropagation()">
                        <a href="{{ $coursesUrl($cat) }}"
                           wire:navigate class="cat-courses-link"
                           style="{{ $cat->courses_count === 0 ? 'opacity:.4;pointer-events:none' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                            {{ number_format($cat->courses_count) }}
                        </a>
                    </td>

                    <td><span class="cat-date">{{ $cat->created_at?->setTimezone(config('app.timezone'))->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="cat-actions">
                            @if($cat->trashed())
                                @if($isSuperAdmin)
                                <button type="button" class="cat-act" title="Restore"
                                    wire:click="restoreCategory({{ $cat->id }})"
                                    wire:confirm="Restore the &quot;{{ addslashes($cat->name) }}&quot; category?">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                                    </svg>
                                </button>
                                @if($cat->courses_count === 0)
                                <button type="button" class="cat-act cat-act-danger" title="Delete permanently"
                                    wire:click="forceDeleteCategory({{ $cat->id }})"
                                    wire:confirm="Permanently delete the &quot;{{ addslashes($cat->name) }}&quot; category? This cannot be undone.">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                                @endif
                                @endif
                            @else
                                <a href="{{ $editUrl($cat) }}" wire:navigate class="cat-act" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/>
                                    </svg>
                                </a>
                                @if($isSuperAdmin && $cat->courses_count === 0)
                                <button type="button" class="cat-act cat-act-danger" title="Delete"
                                    wire:click="deleteCategory({{ $cat->id }})"
                                    wire:confirm="Delete the &quot;{{ addslashes($cat->name) }}&quot; category? This cannot be undone.">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                                @elseif($isSuperAdmin && $cat->courses_count > 0)
                                <span class="cat-act" title="Move or delete its courses first to enable deletion" style="cursor:default;opacity:.4">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="cat-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                            </svg>
                            <p>No categories found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        </div>{{-- end table view --}}

        {{-- Grid view --}}
        <div x-show="view==='grid'">
        @if($categories->isEmpty())
            <div class="cat-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                </svg>
                <p>No categories found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
            </div>
        @else
        <div class="cat-grid">
            @foreach ($categories as $cat)
            @php
                $initials = strtoupper(mb_substr($cat->name, 0, 2));
                $hue      = abs(crc32($cat->name)) % 360;
                $bgColor  = "hsl({$hue},55%,42%)";
            @endphp
            <div class="cat-item-card" onclick="Livewire.navigate('{{ $viewUrl($cat) }}')">
                <div class="cat-item-thumb">
                    @if($cat->image)
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}">
                    @else
                        <div class="cat-item-thumb-init" style="background:{{ $bgColor }}">{{ $initials }}</div>
                    @endif
                </div>
                <div class="cat-item-body">
                    <div class="cat-item-title" title="{{ $cat->name }}">{{ $cat->name }}</div>
                    <div class="cat-item-desc">{{ $cat->description ?: '—' }}</div>
                </div>
                <div class="cat-item-footer" onclick="event.stopPropagation()">
                    <a href="{{ $coursesUrl($cat) }}" wire:navigate class="cat-item-courses"
                       style="{{ $cat->courses_count === 0 ? 'opacity:.5;pointer-events:none' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                        </svg>
                        {{ number_format($cat->courses_count) }}
                    </a>
                    <div class="cat-item-actions">
                        @if($cat->trashed())
                            @if($isSuperAdmin)
                            <button type="button" class="cat-act" title="Restore"
                                wire:click="restoreCategory({{ $cat->id }})"
                                wire:confirm="Restore the &quot;{{ addslashes($cat->name) }}&quot; category?">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                                </svg>
                            </button>
                            @if($cat->courses_count === 0)
                            <button type="button" class="cat-act cat-act-danger" title="Delete permanently"
                                wire:click="forceDeleteCategory({{ $cat->id }})"
                                wire:confirm="Permanently delete the &quot;{{ addslashes($cat->name) }}&quot; category? This cannot be undone.">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                            </button>
                            @endif
                        @endif
                        @else
                        <a href="{{ $editUrl($cat) }}" wire:navigate class="cat-act" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/>
                            </svg>
                        </a>
                        @if($isSuperAdmin && $cat->courses_count === 0)
                        <button type="button" class="cat-act cat-act-danger" title="Delete"
                            wire:click="deleteCategory({{ $cat->id }})"
                            wire:confirm="Delete the &quot;{{ addslashes($cat->name) }}&quot; category? This cannot be undone.">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                        @elseif($isSuperAdmin && $cat->courses_count > 0)
                        <span class="cat-act" title="Move or delete its courses first to enable deletion" style="cursor:default;opacity:.4">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </span>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        </div>{{-- end grid view --}}

        {{-- Pagination --}}
        <div class="cat-footer">
            <div class="cat-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }}–{{ min($curPage * $perPage, $total) }} of {{ number_format($total) }}
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:1rem">
                <div class="cat-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="cat-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                        class="cat-page-btn {{ $curPage === 1 ? 'cat-disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                            class="cat-page-btn {{ $curPage === $p ? 'cat-active' : '' }}">{{ $p }}</button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                        class="cat-page-btn {{ $curPage === $totalPages ? 'cat-disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
</div>
