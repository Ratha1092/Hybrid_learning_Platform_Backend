@php
    $url        = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $createUrl  = route('filament.admin.resources.categories.create');
    $editUrl    = fn($cat) => route('filament.admin.resources.categories.edit', ['record' => $cat->id]);

    $accent = '#0d9488';
@endphp

<style>
.lp, .lp *, .lp *::before, .lp *::after { box-sizing: border-box; margin: 0; padding: 0; }
.lp {
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size: 13px; line-height: 1.5;
    padding-bottom: 48px;
    display: grid; gap: 20px;
    --accent: #0d9488;
    --bg:  #0f172a; --p1: #1e293b; --p2: #263245;
    --bd:  rgba(255,255,255,.07); --bd2: rgba(255,255,255,.13);
    --t1:  #e2e8f0; --t2: #64748b; --t3: #334155;
    --sh:  0 4px 24px rgba(0,0,0,.3);
    color: var(--t1);
}
html:not(.dark) .lp {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.08); --bd2:rgba(15,23,42,.14);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}

@keyframes lpUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.lpa { opacity:0; animation:lpUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.lp1{animation-delay:.04s} .lp2{animation-delay:.09s}

.lp-header {
    display:flex; align-items:flex-start; justify-content:space-between;
    gap:16px; flex-wrap:wrap;
    padding-bottom:20px; border-bottom:1px solid var(--bd);
}
.lp-header-text h1 {
    font-size:clamp(20px,2.2vw,26px); font-weight:780;
    letter-spacing:-.018em; color:var(--t1); line-height:1.15;
}
.lp-header-text p { font-size:12px; color:var(--t2); margin-top:5px; }
.lp-header-btns { display:flex; align-items:center; gap:10px; flex-shrink:0; }
.lp-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px; border-radius:8px; font-size:12px; font-weight:700;
    letter-spacing:.02em; cursor:pointer; text-decoration:none;
    transition:opacity .18s, transform .15s; white-space:nowrap; border:none;
    font-family:inherit;
}
.lp-btn:hover { opacity:.85; transform:translateY(-1px); }
.lp-btn-primary { background:var(--accent); color:#fff; }
.lp-btn-gray    { background:var(--p2); color:var(--t1); border:1px solid var(--bd2); }

.lp-card {
    background:var(--p1); border:1px solid var(--bd);
    border-radius:12px; overflow:hidden; box-shadow:var(--sh);
}

.lp-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; padding:14px 16px;
    border-bottom:1px solid var(--bd); flex-wrap:wrap;
}
.lp-search-box {
    display:flex; align-items:center; gap:6px;
    background:var(--p2); border:1px solid var(--bd2); border-radius:8px;
    padding:6px 12px;
}
.lp-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.lp-search-box input {
    background:none; border:none; outline:none; color:var(--t1);
    font-size:12px; font-family:inherit; width:200px;
}
.lp-search-box input::placeholder { color:var(--t2); }

.lp-table { width:100%; border-collapse:collapse; }
.lp-table thead tr { border-bottom:1px solid var(--bd); }
.lp-table th {
    padding:10px 12px; text-align:left;
    font-size:10.5px; font-weight:800; letter-spacing:.06em;
    text-transform:uppercase; color:var(--t2); white-space:nowrap;
}
.lp-table th.th-center { text-align:center; }
.lp-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.lp-table tbody tr:last-child { border-bottom:none; }
.lp-table tbody tr:hover { background:var(--p2); }
.lp-table td { padding:12px 12px; vertical-align:middle; }

.lp-id { font-size:11.5px; font-weight:700; color:var(--t2); white-space:nowrap; }
.lp-name-cell { display:flex; align-items:center; gap:10px; }
.lp-avatar-img { width:32px; height:32px; border-radius:50%; object-fit:cover; flex-shrink:0; }
.lp-avatar-init {
    width:32px; height:32px; border-radius:50%; flex-shrink:0;
    display:grid; place-items:center;
    font-size:12px; font-weight:800; color:#fff;
}
.lp-name-text { font-size:13px; font-weight:650; color:var(--t1); }
.lp-desc { font-size:12px; color:var(--t2); max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.lp-count-cell { text-align:center; }
.lp-count-val { display:inline-flex; align-items:center; gap:5px; font-size:12.5px; font-weight:600; color:var(--t1); }
.lp-count-val svg { width:13px; height:13px; color:var(--t2); }
.lp-date { font-size:12px; color:var(--t2); white-space:nowrap; }

.lp-actions { display:flex; align-items:center; gap:4px; justify-content:flex-end; }
.lp-act-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:30px; height:30px; border-radius:7px;
    background:none; border:1px solid transparent;
    cursor:pointer; color:var(--t2); text-decoration:none;
    transition:background .15s, border-color .15s, color .15s;
}
.lp-act-btn:hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.lp-act-btn svg { width:14px; height:14px; }

.lp-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:56px 24px; gap:10px; color:var(--t2);
}
.lp-empty svg { width:40px; height:40px; opacity:.35; }
.lp-empty p { font-size:13px; }

.lp-footer {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 16px; border-top:1px solid var(--bd);
    flex-wrap:wrap; gap:10px;
}
.lp-footer-info { font-size:12px; color:var(--t2); }
.lp-pages { display:flex; align-items:center; gap:6px; }
.lp-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:30px; height:30px; padding:0 8px;
    border-radius:7px; font-size:12px; font-weight:700;
    text-decoration:none; color:var(--t2);
    background:none; border:1px solid transparent;
    transition:background .15s, border-color .15s, color .15s;
}
.lp-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.lp-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.lp-page-btn.disabled { opacity:.35; pointer-events:none; }
.lp-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.lp-per-page select {
    appearance:none; background:var(--p2); border:1px solid var(--bd2);
    border-radius:7px; padding:4px 22px 4px 9px; font-size:12px;
    font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 6px center; outline:none;
}
</style>

<div>
<div class="lp" id="lp-categories">

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Categories</h1>
            <p>Manage course categories and their assignments.</p>
        </div>
        <div class="lp-header-btns">
            <a href="{{ $createUrl }}" class="lp-btn lp-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Category
            </a>
        </div>
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar: search --}}
        <div class="lp-toolbar">
            <div></div>
            <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:0">
                @foreach(request()->except(['search', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <div class="lp-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Filter categories...">
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="th-center">Courses</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                @php
                    $initials = strtoupper(mb_substr($cat->name, 0, 2));
                    $hue      = abs(crc32($cat->name)) % 360;
                    $bgColor  = "hsl({$hue},55%,40%)";
                @endphp
                <tr>
                    <td><span class="lp-id">{{ $cat->id }}</span></td>

                    <td>
                        <div class="lp-name-cell">
                            @if($cat->image)
                                <img src="{{ $cat->image_url }}" class="lp-avatar-img" alt="">
                            @else
                                <div class="lp-avatar-init" style="background:{{ $bgColor }}">{{ $initials }}</div>
                            @endif
                            <span class="lp-name-text">{{ $cat->name }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="lp-desc">{{ $cat->description ? \Illuminate\Support\Str::limit($cat->description, 50) : '—' }}</span>
                    </td>

                    <td class="lp-count-cell">
                        <span class="lp-count-val">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                            {{ number_format($cat->courses_count) }}
                        </span>
                    </td>

                    <td><span class="lp-date">{{ $cat->created_at?->format('M d, Y') }}</span></td>

                    <td>
                        <div class="lp-actions">
                            <a href="{{ $editUrl($cat) }}" class="lp-act-btn" title="Edit">
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
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
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
        <div class="lp-footer">
            <div class="lp-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} categories
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="lp-per-page">
                    Per page
                    <select onchange="window.location.href='{{ $url([]) }}&per_page=' + this.value + '&page=1'">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="lp-pages">
                    <a href="{{ $url(['page' => max(1, $curPage - 1)]) }}"
                       class="lp-page-btn {{ $curPage === 1 ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <a href="{{ $url(['page' => $p]) }}"
                           class="lp-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </a>
                    @endfor
                    <a href="{{ $url(['page' => min($totalPages, $curPage + 1)]) }}"
                       class="lp-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
</div>
