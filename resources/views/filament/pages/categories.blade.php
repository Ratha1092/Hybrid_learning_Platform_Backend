@php
    $createUrl = route('filament.admin.resources.categories.create');
    $editUrl   = fn($cat) => route('filament.admin.resources.categories.edit', ['record' => $cat->id]);
@endphp

<div>
<style>
/* ── Strip Filament outer padding on this page ────────────────── */
.fi-main:has(.cat-wrap)                  { padding-inline: 0 !important; }
.fi-page-header-main-ctn:has(.cat-wrap) { padding-block: 0 !important; row-gap: 0 !important; }
.fi-page-content:has(.cat-wrap)          { row-gap: 0 !important; }

/* ── Design tokens (matches dashboard db-* values) ────────────── */
:root {
    --cat-bg:     #F1F5F9;
    --cat-card:   #FFFFFF;
    --cat-bd:     #E2E8F0;
    --cat-t1:     #0F172A;
    --cat-t2:     #334155;
    --cat-t3:     #64748B;
    --cat-t4:     #94A3B8;
    --cat-blue:   #3B82F6;
    --cat-blue-l: #EFF6FF;
    --cat-green:  #10B981;
    --cat-sh:     0 1px 3px 0 rgb(0 0 0/.07), 0 1px 2px -1px rgb(0 0 0/.05);
}
html.dark {
    --cat-bg:     #0F172A;
    --cat-card:   #1E293B;
    --cat-bd:     #334155;
    --cat-t1:     #F1F5F9;
    --cat-t2:     #CBD5E1;
    --cat-t3:     #94A3B8;
    --cat-t4:     #64748B;
    --cat-blue:   #60A5FA;
    --cat-blue-l: #1E3A5F;
    --cat-sh:     0 1px 3px 0 rgb(0 0 0/.3), 0 1px 2px -1px rgb(0 0 0/.2);
}

.cat-wrap, .cat-wrap *, .cat-wrap *::before, .cat-wrap *::after { box-sizing: border-box; margin: 0; padding: 0; }
.cat-wrap {
    background: var(--cat-bg);
    min-height: 100vh;
    padding: 1rem 1.25rem;
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 13px;
    line-height: 1.5;
    color: var(--cat-t1);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Top bar */
.cat-topbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; }
.cat-topbar-left h1 { font-size:1.25rem; font-weight:700; color:var(--cat-t1); }
.cat-topbar-left p  { font-size:.8125rem; color:var(--cat-t3); margin:.1rem 0 0; }

/* Buttons */
.cat-btn {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.4rem .875rem; border-radius:.5rem;
    font-size:.8125rem; font-weight:600; cursor:pointer;
    text-decoration:none; border:none; font-family:inherit;
    transition:opacity .15s; white-space:nowrap;
}
.cat-btn:hover { opacity:.85; }
.cat-btn svg { width:13px; height:13px; }
.cat-btn-primary { background:var(--cat-blue); color:#fff; }
.cat-btn-ghost   { background:var(--cat-card); color:var(--cat-t2); border:1px solid var(--cat-bd); }

/* Card */
.cat-card { background:var(--cat-card); border:1px solid var(--cat-bd); border-radius:.75rem; box-shadow:var(--cat-sh); overflow:hidden; }

/* Toolbar */
.cat-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    gap:.75rem; padding:.875rem 1.125rem;
    border-bottom:1px solid var(--cat-bd); flex-wrap:wrap;
}
.cat-toolbar-info { font-size:.8125rem; color:var(--cat-t3); font-weight:500; }
.cat-search {
    display:flex; align-items:center; gap:.375rem;
    background:var(--cat-bg); border:1px solid var(--cat-bd);
    border-radius:.5rem; padding:.375rem .75rem;
}
.cat-search svg { width:14px; height:14px; color:var(--cat-t3); flex-shrink:0; }
.cat-search input {
    background:none; border:none; outline:none;
    color:var(--cat-t1); font-size:.8125rem; font-family:inherit; width:200px;
}
.cat-search input::placeholder { color:var(--cat-t4); }

/* Table */
.cat-table { width:100%; border-collapse:collapse; }
.cat-table thead tr { border-bottom:1px solid var(--cat-bd); }
.cat-table th {
    padding:.625rem .875rem; text-align:left;
    font-size:.6875rem; font-weight:700; letter-spacing:.06em;
    text-transform:uppercase; color:var(--cat-t3); white-space:nowrap;
}
.cat-table th.th-c { text-align:center; }
.cat-table th.th-r { text-align:right; }
.cat-table tbody tr { border-bottom:1px solid var(--cat-bd); transition:background .12s; cursor:pointer; }
.cat-table tbody tr:last-child { border-bottom:none; }
.cat-table tbody tr:hover { background:var(--cat-bg); }
.cat-table td { padding:.75rem .875rem; vertical-align:middle; }

/* Cells */
.cat-id { font-size:.75rem; font-weight:700; color:var(--cat-t3); }
.cat-name-cell { display:flex; align-items:center; gap:.625rem; }
.cat-avatar     { width:32px; height:32px; border-radius:50%; object-fit:cover; flex-shrink:0; }
.cat-avatar-init {
    width:32px; height:32px; border-radius:.5rem;
    display:grid; place-items:center;
    font-size:.75rem; font-weight:800; color:#fff; flex-shrink:0;
}
.cat-name { font-size:.8125rem; font-weight:600; color:var(--cat-t1); }
.cat-desc { font-size:.75rem; color:var(--cat-t3); max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.cat-courses-link {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.8125rem; font-weight:600; color:var(--cat-blue);
    text-decoration:none; padding:.2rem .5rem; border-radius:.375rem;
    transition:background .12s;
}
.cat-courses-link:hover { background:var(--cat-blue-l); }
.cat-courses-link svg { width:13px; height:13px; }
.cat-date { font-size:.75rem; color:var(--cat-t3); white-space:nowrap; }

/* Action buttons */
.cat-actions { display:flex; align-items:center; gap:.25rem; justify-content:flex-end; }
.cat-act {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:.4rem;
    background:none; border:1px solid transparent;
    cursor:pointer; color:var(--cat-t3); text-decoration:none; transition:all .12s;
}
.cat-act:hover { background:var(--cat-bg); border-color:var(--cat-bd); color:var(--cat-t1); }
.cat-act svg { width:14px; height:14px; }

/* Empty */
.cat-empty {
    display:flex; flex-direction:column; align-items:center;
    justify-content:center; padding:3.5rem 1.5rem; gap:.625rem; color:var(--cat-t3);
}
.cat-empty svg { width:2.5rem; height:2.5rem; opacity:.3; }
.cat-empty p { font-size:.8125rem; }

/* Footer / Pagination */
.cat-footer {
    display:flex; align-items:center; justify-content:space-between;
    padding:.75rem 1.125rem; border-top:1px solid var(--cat-bd); flex-wrap:wrap; gap:.625rem;
}
.cat-footer-info { font-size:.75rem; color:var(--cat-t3); }
.cat-pages { display:flex; align-items:center; gap:.375rem; }
.cat-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:28px; height:28px; padding:0 .5rem; border-radius:.4rem;
    font-size:.75rem; font-weight:600; color:var(--cat-t3);
    background:none; border:1px solid transparent; cursor:pointer; font-family:inherit; transition:all .12s;
}
.cat-page-btn:not(.cat-disabled):hover { background:var(--cat-bg); border-color:var(--cat-bd); color:var(--cat-t1); }
.cat-page-btn.cat-active { background:var(--cat-blue); color:#fff; border-color:transparent; }
.cat-page-btn.cat-disabled { opacity:.35; pointer-events:none; }
.cat-per-page { display:flex; align-items:center; gap:.375rem; font-size:.75rem; color:var(--cat-t3); }
.cat-per-page select {
    appearance:none; background:var(--cat-card); border:1px solid var(--cat-bd);
    border-radius:.4rem; padding:.2rem 1.375rem .2rem .5rem;
    font-size:.75rem; font-weight:600; color:var(--cat-t1); font-family:inherit; cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right .375rem center; outline:none;
}
.cat-loading { opacity:.45; pointer-events:none; transition:opacity .1s; }
</style>

<div class="cat-wrap">

    {{-- Header --}}
    <div class="cat-topbar">
        <div class="cat-topbar-left">
            <h1>Categories</h1>
            <p>Manage course categories and their assignments.</p>
        </div>
        <a href="{{ $createUrl }}" wire:navigate class="cat-btn cat-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New Category
        </a>
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

        {{-- Table --}}
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
                <tr onclick="Livewire.navigate('{{ $editUrl($cat) }}')">
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
                        <a href="{{ route('filament.admin.pages.category-courses') }}?id={{ $cat->id }}"
                           wire:navigate class="cat-courses-link"
                           style="{{ $cat->courses_count === 0 ? 'opacity:.4;pointer-events:none' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                            {{ number_format($cat->courses_count) }}
                        </a>
                    </td>

                    <td><span class="cat-date">{{ $cat->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="cat-actions">
                            <a href="{{ $editUrl($cat) }}" wire:navigate class="cat-act" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/>
                                </svg>
                            </a>
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
