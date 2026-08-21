@php
    $catPalette = [
        ['bg' => 'rgba(167,139,250,.15)', 'color' => '#a78bfa'],
        ['bg' => 'rgba(52,211,153,.15)',  'color' => '#34d399'],
        ['bg' => 'rgba(251,191,36,.15)',  'color' => '#fbbf24'],
        ['bg' => 'rgba(248,113,113,.15)', 'color' => '#f87171'],
        ['bg' => 'rgba(96,165,250,.15)',  'color' => '#60a5fa'],
        ['bg' => 'rgba(83,211,205,.15)',  'color' => '#53d3cd'],
    ];
    $catStyle = fn($name) => $catPalette[abs(crc32($name ?? '')) % count($catPalette)];

    $statusMap = [
        'published'      => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Published'],
        'pending_review' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending Review'],
        'draft'          => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => 'Draft'],
        'rejected'       => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Rejected'],
        'archived'       => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => 'Archived'],
    ];

    $createUrl = route('filament.admin.resources.courses.create');
    $viewUrl   = fn($c) => route('filament.admin.resources.courses.view', ['record' => $c->id]);
    $editUrl   = fn($c) => route('filament.admin.resources.courses.edit', ['record' => $c->id]);
@endphp

<div x-data="{ view: localStorage.getItem('cp-view') || 'table' }" x-init="$watch('view', v => localStorage.setItem('cp-view', v))">  {{-- single Livewire root — wire:click requires exactly one root element --}}
<style>
.cp, .cp *, .cp *::before, .cp *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cp {
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
html:not(.dark) .cp {
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

@keyframes cpUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.cpa {
    opacity:0;
    animation:cpUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.cp1 {
    animation-delay:.04s;
}
.cp2 {
    animation-delay:.09s;
}

/* Page header */
.cp-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.cp-header-text h1 {
    font-size:clamp(20px, 2.2vw, 26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.15;
}
.cp-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.cp-header-btns {
    display:flex;
    align-items:center;
    gap:10px;
    flex-shrink:0;
}
.cp-btn {
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
.cp-btn:hover {
    opacity:.85;
    transform:translateY(-1px);
}
.cp-btn-primary {
    background:#6d28d9;
    color:#fff;
}
.cp-btn-gray {
    background:var(--p2);
    color:var(--t1);
    border:1px solid var(--bd2);
}

/* Table container */
.cp-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    box-shadow:var(--sh);
    min-width:0;
}

/* Tabs + search toolbar */
.cp-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.cp-tabs {
    display:flex;
    align-items:center;
    gap:4px;
    flex-wrap:wrap;
}
.cp-tab {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:6px 13px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    color:var(--t2);
    border:1px solid transparent;
    transition:background .15s, color .15s, border-color .15s;
}
.cp-tab:hover {
    background:var(--p2);
    color:var(--t1);
}
.cp-tab-badge {
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

.cp-search-box {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.cp-search-box svg {
    width:14px;
    height:14px;
    color:var(--t2);
    flex-shrink:0;
}
.cp-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.cp-search-box input::placeholder {
    color:var(--t2);
}

/* Table */
.cp-table {
    width:100%;
    min-width:920px;
    border-collapse:collapse;
}
.cp-table thead tr {
    border-bottom:1px solid var(--bd);
}
.cp-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.cp-table th.th-center {
    text-align:center;
}
.cp-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.cp-table tbody tr:last-child {
    border-bottom:none;
}
.cp-table tbody tr:hover {
    background:var(--p2);
}
.cp-row-link {
    cursor:pointer;
}
.cp-table td {
    padding:12px 12px;
    vertical-align:middle;
}

/* Cells */
.cp-id {
    font-size:11.5px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.cp-course-cell {
    display:flex;
    align-items:center;
    gap:10px;
}
.cp-thumb {
    width:40px;
    height:40px;
    border-radius:6px;
    object-fit:cover;
    flex-shrink:0;
}
.cp-course-title {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
    line-height:1.3;
    max-width:260px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.cp-course-sub {
    font-size:11px;
    color:var(--t2);
    margin-top:2px;
    max-width:260px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.cp-instructor-cell {
    display:flex;
    align-items:center;
    gap:8px;
    white-space:nowrap;
}
.cp-avatar {
    width:28px;
    height:28px;
    border-radius:50%;
    flex-shrink:0;
    object-fit:cover;
}
.cp-instructor-name {
    font-size:12.5px;
    color:var(--t1);
    font-weight:500;
}
.cp-badge {
    display:inline-flex;
    align-items:center;
    padding:3px 10px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}
.cp-status-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}
.cp-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.cp-price {
    font-size:12.5px;
    font-weight:700;
    color:var(--t1);
    white-space:nowrap;
}
.cp-price-free {
    color:#34d399;
}
.cp-students {
    text-align:center;
}
.cp-students-val {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-width:70px;
    padding:6px 10px;
    border-radius:999px;
    background:rgba(99,102,241,.08);
    border:1px solid rgba(99,102,241,.18);
    font-size:12.5px;
    font-weight:600;
    color:#4338ca;
    transition:background .15s, border-color .15s, color .15s;
}
.cp-students-val:hover {
    background:rgba(99,102,241,.14);
    color:#3730a3;
}
.cp-students-val svg {
    width:13px;
    height:13px;
    color:#6366f1;
}
.cp-date {
    font-size:12px;
    color:var(--t2);
    white-space:nowrap;
}

/* Action buttons */
.cp-actions {
    display:flex;
    align-items:center;
    gap:6px;
    justify-content:flex-end;
}
.cp-act-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:36px;
    height:36px;
    border-radius:10px;
    background:var(--p2);
    border:1px solid transparent;
    cursor:pointer;
    color:var(--t2);
    text-decoration:none;
    transition:background .15s, border-color .15s, color .15s, transform .15s;
}
.cp-act-btn:hover {
    background:var(--p1);
    border-color:var(--bd2);
    color:var(--t1);
    transform:translateY(-1px);
}
.cp-act-btn svg {
    width:16px;
    height:16px;
}
.cp-act-btn-neutral {
    background:var(--p2);
}
.cp-act-btn-success {
    background:rgba(52,211,153,.14);
    color:#16a34a;
}
.cp-act-btn-success:hover {
    background:rgba(52,211,153,.22);
    color:#166534;
}
.cp-act-btn-danger {
    background:rgba(248,113,113,.14);
    color:#dc2626;
}
.cp-act-btn-danger:hover {
    background:rgba(248,113,113,.2);
    color:#b91c1c;
}
.cp-act-more {
    position:relative;
}
.cp-act-menu {
    display:none;
    position:absolute;
    right:0;
    top:calc(100% + 6px);
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:12px;
    box-shadow:0 12px 32px rgba(0,0,0,.22);
    min-width:180px;
    z-index:50;
    overflow:hidden;
}
.cp-act-more:focus-within .cp-act-menu {
    display:block;
}
.cp-act-menu-item {
    display:flex;
    align-items:center;
    gap:10px;
    padding:11px 16px;
    font-size:13px;
    font-weight:600;
    color:var(--t1);
    cursor:pointer;
    background:none;
    border:none;
    width:100%;
    text-align:left;
    font-family:inherit;
    transition:background .12s;
    white-space:nowrap;
}
.cp-act-menu-item:hover {
    background:var(--p2);
}
.cp-act-menu-item.danger {
    color:#ef4444;
}
.cp-act-menu-item svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}

/* Empty state */
.cp-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.cp-empty svg {
    width:40px;
    height:40px;
    opacity:.35;
}
.cp-empty p {
    font-size:13px;
}

/* Pagination */
.cp-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.cp-footer-info {
    font-size:12px;
    color:var(--t2);
}
.cp-pages {
    display:flex;
    align-items:center;
    gap:6px;
}
.cp-page-btn {
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
    border:1px solid transparent;
    transition:background .15s, border-color .15s, color .15s;
}
.cp-page-btn:not(.disabled):hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.cp-page-btn.active {
    background:#6d28d9;
    color:#fff;
    border-color:transparent;
}
.cp-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
.cp-per-page {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    color:var(--t2);
}
.cp-per-page select {
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

/* Reject modal */
.cp-modal-overlay {
    display:none;
    position:fixed;
    inset:0;
    z-index:999;
    background:rgba(0,0,0,.55);
    align-items:center;
    justify-content:center;
}
.cp-modal-overlay.open {
    display:flex;
}
.cp-modal {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:14px;
    padding:24px;
    width:440px;
    max-width:95vw;
    box-shadow:0 20px 60px rgba(0,0,0,.5);
}
.cp-modal h3 {
    font-size:15px;
    font-weight:750;
    color:var(--t1);
    margin-bottom:14px;
}
.cp-modal textarea {
    width:100%;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:10px 12px;
    font-size:13px;
    color:var(--t1);
    font-family:inherit;
    resize:vertical;
    outline:none;
    transition:border-color .15s;
}
.cp-modal textarea:focus {
    border-color:#6d28d9;
}
.cp-modal-btns {
    display:flex;
    justify-content:flex-end;
    gap:8px;
    margin-top:14px;
}

/* View toggle */
.cp-view-toggle {
    display:flex;
    align-items:center;
    gap:2px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:2px;
}
.cp-view-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    border-radius:6px;
    border:none;
    background:none;
    color:var(--t2);
    cursor:pointer;
    transition:background .15s, color .15s;
}
.cp-view-btn.active {
    background:var(--p1);
    color:#6d28d9;
    box-shadow:0 1px 3px rgba(0,0,0,.15);
}
.cp-view-btn svg {
    width:15px;
    height:15px;
}

/* Card grid */
.cp-grid {
    display:grid;
    grid-template-columns:repeat(5, 1fr);
    gap:16px;
    padding:16px;
}
@media (max-width: 1280px) {
    .cp-grid {
        grid-template-columns:repeat(4, 1fr);
    }
}
@media (max-width: 960px) {
    .cp-grid {
        grid-template-columns:repeat(3, 1fr);
    }
}
.cp-course-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    transition:box-shadow .18s, transform .18s, border-color .18s;
    cursor:pointer;
}
.cp-course-card:hover {
    box-shadow:0 8px 24px rgba(0,0,0,.18);
    transform:translateY(-2px);
    border-color:var(--bd2);
}
.cp-card-thumb {
    position:relative;
    height:150px;
    overflow:hidden;
    flex-shrink:0;
    background:var(--p2);
}
.cp-card-thumb img {
    width:100%;
    height:100%;
    object-fit:cover;
}
.cp-card-thumb-placeholder {
    width:100%;
    height:100%;
    display:grid;
    place-items:center;
}
.cp-card-status {
    position:absolute;
    top:8px;
    right:8px;
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:3px 8px;
    border-radius:5px;
    font-size:10.5px;
    font-weight:700;
    white-space:nowrap;
    backdrop-filter:blur(6px);
}
.cp-card-body {
    padding:12px 14px;
    flex:1;
    display:flex;
    flex-direction:column;
    gap:8px;
}
.cp-card-title {
    font-size:13.5px;
    font-weight:700;
    color:var(--t1);
    line-height:1.35;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}
.cp-card-meta {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}
.cp-card-instructor {
    display:flex;
    align-items:center;
    gap:6px;
}
.cp-card-avatar {
    width:22px;
    height:22px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
}
.cp-card-instructor-name {
    font-size:11.5px;
    color:var(--t2);
    font-weight:500;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    max-width:120px;
}
.cp-card-cat {
    display:inline-flex;
    align-items:center;
    padding:2px 8px;
    border-radius:5px;
    font-size:11px;
    font-weight:700;
    white-space:nowrap;
    margin-left:auto;
}
.cp-card-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:10px 14px;
    border-top:1px solid var(--bd);
    gap:8px;
    margin-top:auto;
}
.cp-card-price {
    font-size:14px;
    font-weight:800;
    color:var(--t1);
}
.cp-card-price-free {
    font-size:12px;
    font-weight:700;
    color:#34d399;
}
.cp-card-students {
    display:flex;
    align-items:center;
    gap:4px;
    font-size:11.5px;
    color:var(--t2);
}
.cp-card-students svg {
    width:13px;
    height:13px;
}
.cp-card-actions {
    display:flex;
    align-items:center;
    gap:4px;
}
.cp-card-act {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:28px;
    height:28px;
    border-radius:7px;
    background:var(--p2);
    border:1px solid transparent;
    cursor:pointer;
    color:var(--t2);
    text-decoration:none;
    transition:background .15s, border-color .15s, color .15s;
}
.cp-card-act:hover {
    background:var(--p1);
    border-color:var(--bd2);
    color:var(--t1);
}
.cp-card-act svg {
    width:13px;
    height:13px;
}
.cp-card-act-success {
    background:rgba(52,211,153,.14);
    color:#16a34a;
}
.cp-card-act-success:hover {
    background:rgba(52,211,153,.22);
}
.cp-card-act-danger {
    background:rgba(248,113,113,.14);
    color:#dc2626;
}
.cp-card-act-danger:hover {
    background:rgba(248,113,113,.2);
}

/* Mobile responsive */
@media (max-width: 640px) {
    .cp-header {
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
        padding-bottom:14px;
    }
    .cp-header-btns {
        width:100%;
    }
    .cp-btn {
        flex:1;
        justify-content:center;
    }
    .cp-toolbar {
        flex-direction:column;
        align-items:stretch;
        gap:8px;
        padding:10px 12px;
    }
    /* 3 tabs per row, 2 rows = all 6 tabs always visible */
    .cp-tabs {
        flex-wrap:wrap;
        gap:4px;
    }
    .cp-tab {
        flex:0 0 calc(33.33% - 3px);
        justify-content:center;
        font-size:11px;
        padding:5px 6px;
    }
    .cp-tab-badge {
        min-width:16px;
        height:16px;
        font-size:9px;
    }
    .cp-search-box {
        width:100%;
    }
    .cp-search-box input {
        flex:1;
        width:auto;
    }
    .cp-footer {
        flex-direction:column;
        align-items:flex-start;
        gap:8px;
    }
    .cp-footer > div {
        width:100%;
        justify-content:space-between;
    }
    .cp-grid {
        grid-template-columns:1fr;
        padding:12px;
        gap:12px;
    }
}
</style>
<div class="cp" id="cp-root">

    {{-- Header --}}
    <div class="cp-header cpa cp1">
        <div class="cp-header-text">
            <h1>Courses</h1>
            <p>Review, publish, and manage all your courses in one place.</p>
        </div>
        <div class="cp-header-btns">
            <div class="cp-view-toggle">
                <button type="button" class="cp-view-btn" :class="{active: view==='table'}" @click="view='table'" title="Table view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M3 6.2A3.2 3.2 0 0 1 6.2 3h11.6A3.2 3.2 0 0 1 21 6.2v11.6a3.2 3.2 0 0 1-3.2 3.2H6.2A3.2 3.2 0 0 1 3 17.8V6.2z"/></svg>
                </button>
                <button type="button" class="cp-view-btn" :class="{active: view==='grid'}" @click="view='grid'" title="Card view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                </button>
            </div>
            <a href="{{ route('admin.export.courses', ['tab' => $tab, 'search' => $search]) }}" class="cp-btn cp-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Export
            </a>
            <a href="{{ $createUrl }}" wire:navigate class="cp-btn cp-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Course
            </a>
        </div>
    </div>

    {{-- Table card --}}
    <div class="cp-card cpa cp2">

        {{-- Toolbar: tabs + search --}}
        <div class="cp-toolbar">
            <div class="cp-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'] ?? '#6d28d9';
                    $tabStyle   = $isActive
                        ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;"
                        : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <button type="button"
                        wire:click="setTab('{{ $t['key'] }}')"
                        class="cp-tab"
                        style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="cp-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </button>
                @endforeach
            </div>

            <div class="cp-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.350ms="search"
                       placeholder="Filter courses...">
            </div>
        </div>

        {{-- Table view --}}
        <div x-show="view==='table'">
        <div style="overflow-x:auto">
        <table class="cp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th class="th-center">Students</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                @php
                    $cs    = $catStyle($course->category?->name);
                    $ss    = $statusMap[$course->status] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => ucfirst($course->status)];
                    $bgHex = substr(md5($course->instructor?->name ?? ''), 0, 6);
                    $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                @endphp
                <tr class="cp-row-link" wire:key="course-row-{{ $course->id }}" onclick="Livewire.navigate('{{ $viewUrl($course) }}')">

                    <td><span class="cp-id">{{ $course->id }}</span></td>

                    <td>
                        <div class="cp-course-cell">
                            @if($course->thumbnail)
                                <img src="{{ $course->thumbnail_url }}" class="cp-thumb" alt="">
                            @else
                                <div class="cp-thumb" style="background:{{ $cs['bg'] }};display:grid;place-items:center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="{{ $cs['color'] }}" stroke-width="1.5" style="width:18px;height:18px">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <div class="cp-course-title" title="{{ $course->title }}">{{ $course->title }}</div>
                                @if($course->short_description)
                                    <div class="cp-course-sub">{{ Str::limit($course->short_description, 45) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="cp-instructor-cell">
                            <img src="{{ $avUrl }}" class="cp-avatar" alt="">
                            <span class="cp-instructor-name">{{ $course->instructor?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td>
                        @if($course->category)
                            <span class="cp-badge" style="background:{{ $cs['bg'] }};color:{{ $cs['color'] }}">
                                {{ $course->category->name }}
                            </span>
                        @else
                            <span style="color:var(--t2)">—</span>
                        @endif
                    </td>

                    <td>
                        @if($course->price > 0)
                            <span class="cp-price">${{ number_format($course->price, 2) }}</span>
                        @else
                            <span class="cp-price cp-price-free">Free</span>
                        @endif
                    </td>

                    <td>
                        <span class="cp-status-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="cp-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td class="cp-students" onclick="event.stopPropagation()">
                        <a href="{{ url('/admin/courses/' . $course->id . '/students') }}" wire:navigate onclick="event.stopPropagation()" class="cp-students-val" title="View enrolled students" style="text-decoration:none;cursor:pointer;transition:color .15s" onmouseover="this.style.color='#7c3aed'" onmouseout="this.style.color=''">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            {{ number_format($course->enrollments_count) }}
                        </a>
                    </td>

                    <td onclick="event.stopPropagation()">
                        <div class="cp-actions">
                            @if(!$course->trashed() && $course->isPendingReview())
                                <button type="button" wire:click="approveCourse({{ $course->id }})" class="cp-act-btn cp-act-btn-success" title="Approve">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                </button>
                                <button type="button" class="cp-act-btn cp-act-btn-danger" title="Reject" wire:click="openRejectModal({{ $course->id }})" onclick="event.stopPropagation()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                </button>
                            @endif

                            <div class="cp-act-more" tabindex="0">
                                <button class="cp-act-btn cp-act-btn-neutral" title="More actions">
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                                        <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                                    </svg>
                                </button>
                                <div class="cp-act-menu">
                                    @if($course->trashed())
                                        <a href="{{ $viewUrl($course) }}" wire:navigate class="cp-act-menu-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/></svg>
                                            View
                                        </a>
                                        <button type="button"
                                            wire:click="restoreCourse({{ $course->id }})"
                                            wire:confirm="Restore &quot;{{ $course->title }}&quot;?"
                                            class="cp-act-menu-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                            Restore
                                        </button>
                                    @else
                                        <a href="{{ $viewUrl($course) }}" wire:navigate class="cp-act-menu-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/></svg>
                                            View
                                        </a>
                                        @unless($course->isPendingReview())
                                        <a href="{{ $editUrl($course) }}" wire:navigate class="cp-act-menu-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                                            Edit
                                        </a>
                                        @endunless
                                        @if($course->isPublished())
                                            <button type="button" wire:click="archiveCourse({{ $course->id }})" class="cp-act-menu-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4"/></svg>
                                                Archive
                                            </button>
                                        @endif
                                        @if($course->isArchived())
                                            <button type="button" wire:click="unarchiveCourse({{ $course->id }})" class="cp-act-menu-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                                Restore to Published
                                            </button>
                                        @endif
                                        @if(!$course->isDraft())
                                            <button type="button" wire:click="returnToDraft({{ $course->id }})" class="cp-act-menu-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                                Return to Draft
                                            </button>
                                        @endif
                                        <button type="button"
                                            wire:click="deleteCourse({{ $course->id }})"
                                            wire:confirm="Delete &quot;{{ $course->title }}&quot;? This will soft-delete the course."
                                            class="cp-act-menu-item danger">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="cp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <p>No courses found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        </div>{{-- end table view --}}

        {{-- Card / grid view --}}
        <div x-show="view==='grid'">
        @if($courses->isEmpty())
            <div class="cp-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                </svg>
                <p>No courses found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
            </div>
        @else
        <div class="cp-grid">
            @foreach ($courses as $course)
            @php
                $cs    = $catStyle($course->category?->name);
                $ss    = $statusMap[$course->status] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => ucfirst($course->status)];
                $bgHex = substr(md5($course->instructor?->name ?? ''), 0, 6);
                $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
            @endphp
            <div class="cp-course-card" wire:key="course-card-{{ $course->id }}" onclick="Livewire.navigate('{{ $viewUrl($course) }}')">

                {{-- Thumbnail --}}
                <div class="cp-card-thumb">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}">
                    @else
                        <div class="cp-card-thumb-placeholder" style="background:{{ $cs['bg'] }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $cs['color'] }}" stroke-width="1.3" style="width:38px;height:38px;opacity:.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                    @endif
                    <span class="cp-card-status" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $ss['color'] }};flex-shrink:0;display:inline-block;"></span>
                        {{ $ss['label'] }}
                    </span>
                </div>

                {{-- Body --}}
                <div class="cp-card-body">
                    <div class="cp-card-title" title="{{ $course->title }}">{{ $course->title }}</div>
                    <div class="cp-card-meta">
                        <div class="cp-card-instructor">
                            <img src="{{ $avUrl }}" class="cp-card-avatar" alt="">
                            <span class="cp-card-instructor-name">{{ $course->instructor?->name ?? '—' }}</span>
                        </div>
                        @if($course->category)
                        <span class="cp-card-cat" style="background:{{ $cs['bg'] }};color:{{ $cs['color'] }}">{{ $course->category->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="cp-card-footer" onclick="event.stopPropagation()">
                    <div>
                        @if($course->price > 0)
                            <span class="cp-card-price">${{ number_format($course->price, 2) }}</span>
                        @else
                            <span class="cp-card-price-free">Free</span>
                        @endif
                        <div class="cp-card-students" style="margin-top:2px">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                            {{ number_format($course->enrollments_count) }} students
                        </div>
                    </div>
                    <div class="cp-card-actions">
                        @if(!$course->trashed() && $course->isPendingReview())
                            <button type="button" wire:click="approveCourse({{ $course->id }})" class="cp-card-act cp-card-act-success" title="Approve" onclick="event.stopPropagation()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                            </button>
                            <button type="button" wire:click="openRejectModal({{ $course->id }})" class="cp-card-act cp-card-act-danger" title="Reject" onclick="event.stopPropagation()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                            </button>
                        @endif
                        <a href="{{ $viewUrl($course) }}" wire:navigate class="cp-card-act" title="View" onclick="event.stopPropagation()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/></svg>
                        </a>
                        @if($course->trashed())
                        <button type="button" wire:click="restoreCourse({{ $course->id }})" wire:confirm="Restore &quot;{{ $course->title }}&quot;?" class="cp-card-act" title="Restore" onclick="event.stopPropagation()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                        </button>
                        @elseif(!$course->isPendingReview())
                        <a href="{{ $editUrl($course) }}" wire:navigate class="cp-card-act" title="Edit" onclick="event.stopPropagation()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        </div>{{-- end grid view --}}

        {{-- Pagination --}}
        <div class="cp-footer">
            <div class="cp-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} courses
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="cp-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="cp-pages">
                    <button type="button" wire:click="setPage({{ max(1, $curPage - 1) }})"
                            class="cp-page-btn {{ $curPage === 1 ? 'disabled' : '' }}"
                            {{ $curPage === 1 ? 'disabled' : '' }}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="setPage({{ $p }})"
                                class="cp-page-btn {{ $curPage === $p ? 'active' : '' }}">{{ $p }}</button>
                    @endfor
                    <button type="button" wire:click="setPage({{ min($totalPages, $curPage + 1) }})"
                            class="cp-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}"
                            {{ $curPage === $totalPages ? 'disabled' : '' }}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Reject modal — fully Livewire-driven, no raw JS needed --}}
<div class="cp-modal-overlay"
     x-data="{ open: false }"
     x-show="open"
     x-on:open-reject-modal.window="open = true"
     x-on:close-reject-modal.window="open = false"
     x-on:click.self="$wire.closeRejectModal()"
     style="display:none">
    <div class="cp-modal">
        <h3>Reject Course</h3>
        @if($rejectingCourseTitle)
            <p style="font-size:12px;color:var(--t2);margin-bottom:12px">"{{ $rejectingCourseTitle }}"</p>
        @endif
        <textarea wire:model="rejectReason" rows="4" placeholder="Explain why this course is being rejected…"></textarea>
        <div class="cp-modal-btns">
            <button class="cp-btn cp-btn-gray" wire:click="closeRejectModal()">Cancel</button>
            <button class="cp-btn" style="background:#dc2626;color:#fff"
                    wire:click="submitRejectModal()"
                    wire:loading.attr="disabled">
                Reject Course
            </button>
        </div>
    </div>
</div>

<script>
    window.addEventListener('download-csv', function(e) {
        const a = document.createElement('a');
        a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(e.detail.content);
        a.download = e.detail.filename;
        a.click();
    });
</script>
</div>
