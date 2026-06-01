@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));

    $stars = fn(int $rating) => str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

    $ratingColor = fn(int $r) => match(true) {
        $r >= 4 => '#34d399',
        $r >= 3 => '#fbbf24',
        default  => '#f87171',
    };
@endphp

<style>
.lp, .lp *, .lp *::before, .lp *::after { box-sizing:border-box; margin:0; padding:0; }
.lp {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px; line-height:1.5;
    padding-bottom:48px;
    display:grid; gap:20px;
    --accent:#d97706;
    --bg:#0f172a; --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b; --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
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

.lp-card {
    background:var(--p1); border:1px solid var(--bd);
    border-radius:12px; overflow:hidden; box-shadow:var(--sh);
}
.lp-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; padding:14px 16px;
    border-bottom:1px solid var(--bd); flex-wrap:wrap;
}
.lp-tabs { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.lp-tab {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 13px; border-radius:8px; font-size:12px; font-weight:600;
    cursor:pointer; text-decoration:none; color:var(--t2);
    border:1px solid transparent;
    transition:background .15s, color .15s, border-color .15s;
}
.lp-tab:hover { background:var(--p2); color:var(--t1); }
.lp-tab-badge {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:18px; height:18px; padding:0 5px;
    border-radius:5px; font-size:10px; font-weight:800;
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
.lp-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.lp-table tbody tr:last-child { border-bottom:none; }
.lp-table tbody tr:hover { background:var(--p2); }
.lp-table td { padding:12px 12px; vertical-align:middle; }

.lp-id         { font-size:11.5px; font-weight:700; color:var(--t2); white-space:nowrap; }
.lp-course     { font-size:12.5px; font-weight:650; color:var(--t1); max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }
.lp-user-cell  { display:flex; align-items:center; gap:8px; }
.lp-user-name  { font-size:12.5px; color:var(--t1); font-weight:500; }
.lp-stars      { font-size:14px; letter-spacing:1px; }
.lp-comment    { font-size:12px; color:var(--t2); max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }
.lp-date       { font-size:12px; color:var(--t2); white-space:nowrap; }

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
<div class="lp" id="lp-reviews">

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Reviews</h1>
            <p>Monitor student feedback and ratings across all courses.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar: tabs + search --}}
        <div class="lp-toolbar">
            <div class="lp-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <a href="{{ $url(['tab' => $t['key'], 'page' => 1]) }}" class="lp-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="lp-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </a>
                @endforeach
            </div>

            <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:0">
                @foreach(request()->except(['search', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <div class="lp-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Filter reviews...">
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Student</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                @php
                    $rc    = $ratingColor($review->rating);
                    $bgHex = substr(md5($review->user?->name ?? ''), 0, 6);
                    $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($review->user?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                @endphp
                <tr>
                    <td><span class="lp-id">{{ $review->id }}</span></td>

                    <td>
                        <span class="lp-course">{{ \Illuminate\Support\Str::limit($review->course?->title ?? '—', 40) }}</span>
                    </td>

                    <td>
                        <div class="lp-user-cell">
                            <img src="{{ $avUrl }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">
                            <span class="lp-user-name">{{ $review->user?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="lp-stars" style="color:{{ $rc }}">{{ $stars($review->rating) }}</span>
                    </td>

                    <td>
                        <span class="lp-comment">{{ $review->comment ? \Illuminate\Support\Str::limit($review->comment, 60) : '—' }}</span>
                    </td>

                    <td><span class="lp-date">{{ $review->created_at?->format('M d, Y') }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5z"/>
                            </svg>
                            <p>No reviews found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
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
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} reviews
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
