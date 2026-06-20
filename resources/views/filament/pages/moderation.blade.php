@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $accent = '#ea580c';

    $typeStyle = fn($type) => match($type) {
        'course'       => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171'],
        'verification' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24'],
        default        => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8'],
    };
@endphp

<style>
.md, .md *, .md *::before, .md *::after { box-sizing:border-box; margin:0; padding:0; }
.md {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px; line-height:1.5;
    padding-bottom:48px;
    display:grid; gap:20px;
    --bg:#0f172a; --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b; --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .md {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.08); --bd2:rgba(15,23,42,.14);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes mdUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.mda { opacity:0; animation:mdUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.md1{animation-delay:.04s} .md2{animation-delay:.09s}

.md-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.md-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.md-header-text p { font-size:12px; color:var(--t2); margin-top:5px; }

.md-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.md-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bd); flex-wrap:wrap; }
.md-tabs { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.md-tab { display:inline-flex; align-items:center; gap:6px; padding:6px 13px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; color:var(--t2); border:1px solid transparent; transition:background .15s, color .15s, border-color .15s; }
.md-tab:hover { background:var(--p2); color:var(--t1); }
.md-tab-badge { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; border-radius:5px; font-size:10px; font-weight:800; }
.md-search-box { display:flex; align-items:center; gap:6px; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 12px; }
.md-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.md-search-box input { background:none; border:none; outline:none; color:var(--t1); font-size:12px; font-family:inherit; width:200px; }
.md-search-box input::placeholder { color:var(--t2); }

.md-table { width:100%; border-collapse:collapse; }
.md-table thead tr { border-bottom:1px solid var(--bd); }
.md-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.md-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; cursor:pointer; }
.md-table tbody tr:last-child { border-bottom:none; }
.md-table tbody tr:hover { background:var(--p2); }
.md-table td { padding:12px 12px; vertical-align:middle; }

.md-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:700; white-space:nowrap; }
.md-title { font-size:12.5px; color:var(--t1); font-weight:500; }
.md-subject { font-size:12px; color:var(--t2); }
.md-reason { font-size:12px; color:var(--t2); max-width:320px; }
.md-date { font-size:12px; color:var(--t2); white-space:nowrap; }

.md-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 24px; gap:10px; color:var(--t2); }
.md-empty svg { width:40px; height:40px; opacity:.35; }
.md-empty p { font-size:13px; }

.md-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--bd); flex-wrap:wrap; gap:10px; }
.md-footer-info { font-size:12px; color:var(--t2); }
.md-pages { display:flex; align-items:center; gap:6px; }
.md-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; font-size:12px; font-weight:700; text-decoration:none; color:var(--t2); background:none; border:1px solid transparent; transition:background .15s, border-color .15s, color .15s; }
.md-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.md-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.md-page-btn.disabled { opacity:.35; pointer-events:none; }
.md-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.md-per-page select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:7px; padding:4px 22px 4px 9px; font-size:12px; font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 6px center; outline:none; }
</style>

<div class="md" id="md-moderation" style="--accent:{{ $accent }};">

    {{-- Header --}}
    <div class="md-header mda md1">
        <div class="md-header-text">
            <h1>Moderation Log</h1>
            <p>A unified history of rejected courses and instructor verifications. Take action on the original Courses / Verifications pages.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="md-card mda md2">

        {{-- Toolbar --}}
        <div class="md-toolbar">
            <div class="md-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <a href="{{ $url(['tab' => $t['key'], 'page' => 1]) }}" class="md-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="md-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </a>
                @endforeach
            </div>

            <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:0">
                @foreach(request()->except(['search', 'page']) as $k => $v)
                    @if(is_scalar($v))
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="md-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search title or person...">
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
        <table class="md-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Person</th>
                    <th>Reason</th>
                    <th>Reviewed</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $entry)
                @php $ts = $typeStyle($entry['type']); @endphp
                <tr onclick="window.location='{{ $entry['url'] }}'">
                    <td>
                        <span class="md-badge" style="background:{{ $ts['bg'] }};color:{{ $ts['color'] }}">
                            {{ $entry['type_label'] }}
                        </span>
                    </td>
                    <td><span class="md-title">{{ $entry['title'] }}</span></td>
                    <td><span class="md-subject">{{ $entry['subject'] }}</span></td>
                    <td><span class="md-reason">{{ $entry['reason'] ?? '—' }}</span></td>
                    <td><span class="md-date">{{ $entry['date']?->format('M d, Y') ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="md-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <p>No moderation activity{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="md-footer">
            <div class="md-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} entries
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="md-per-page">
                    Per page
                    <select onchange="window.location.href='{{ $url([]) }}&per_page=' + this.value + '&page=1'">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="md-pages">
                    <a href="{{ $url(['page' => max(1, $curPage - 1)]) }}"
                       class="md-page-btn {{ $curPage === 1 ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <a href="{{ $url(['page' => $p]) }}"
                           class="md-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </a>
                    @endfor
                    <a href="{{ $url(['page' => min($totalPages, $curPage + 1)]) }}"
                       class="md-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
