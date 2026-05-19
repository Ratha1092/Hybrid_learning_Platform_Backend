{{-- resources/views/filament/pages/dashboard.blade.php --}}

@php
    $paidPayments        = max((int)  $completedPayments,  0);
    $pendingCount        = max((int)  $pendingPayments,     0);
    $failedCount         = max((int)  $failedPayments,      0);
    $allPayments         = max($paidPayments + $pendingCount + $failedCount, 1);
    $successRate         = round(($paidPayments / $allPayments) * 100);

    $gtTotal  = max(collect($paymentGatewayBreakdown)->sum(), 1);
    $gwRows   = collect($paymentGatewayBreakdown)
        ->map(fn($v,$k) => [
            'name'    => str($k)->headline()->toString(),
            'amount'  => (float) $v,
            'percent' => round(((float)$v / $gtTotal) * 100, 1),
        ])
        ->sortByDesc('amount')->values();

    $pts     = collect($revenueTrend)->values();
    $maxRev  = max((float)$pts->max('revenue'), 1);
    $maxOrd  = max((int)$pts->max('orders'),    1);
    $ptCount = max($pts->count(), 1);

    // SVG helpers
    $W = 616; $H = 160;
    $lineCoords = $pts->map(function($p,$i) use ($ptCount,$W,$H,$maxRev) {
        $x = $ptCount===1 ? 32 : 32 + ($i/($ptCount-1))*($W-32);
        $y = ($H-20) - ((float)$p['revenue']/$maxRev)*($H-44);
        return round($x,1).','.round($y,1);
    })->implode(' ');
    $areaCoords = '32,'.($H-20).' '.$lineCoords.' '.($W).','.($H-20);

    $latestTx = $recentPayments->take(6);

    // Donut
    $donutColors = ['#2f8cff','#53d3cd','#8b5cf6','#21c77a','#e3b83f'];
    $r = 52; $circ = 2 * M_PI * $r;
    $dOffset = 0;
    $donutSegs = $gwRows->map(function($row,$i) use (&$dOffset,$donutColors,$circ,$r) {
        $dash = ($row['percent']/100) * $circ;
        $seg  = ['color'=>$donutColors[$i%5],'dash'=>round($dash,2),'gap'=>round($circ-$dash,2),'off'=>round($dOffset,2)];
        $dOffset += $dash;
        return $seg;
    });

    $hour = now()->hour;
    $greeting = $hour < 12 ? 'morning' : ($hour < 18 ? 'afternoon' : 'evening');
    $userName = auth()->user()->name ?? 'Admin';
@endphp

{{-- SIDEBAR OVERRIDES (Filament 3 classes) --}}
<style>
.fi-sidebar { background: #0d1526 !important; border-right: 1px solid rgba(255,255,255,.07) !important; }
.fi-sidebar-inner { background: #0d1526 !important; }
.fi-sidebar-header {
    background: #0d1526 !important;
    border-bottom: 1px solid rgba(255,255,255,.07) !important;
    padding: 14px 16px !important;
}
.fi-logo { color: #ffffff !important; font-weight: 700 !important; }
.fi-sidebar-group-label {
    font-size: 9.5px !important; font-weight: 700 !important; letter-spacing: .13em !important;
    text-transform: uppercase !important; color: #2e4a68 !important; padding: 8px 14px 4px !important;
}
.fi-sidebar-item-button {
    color: #5a7a9a !important; border-radius: 8px !important; margin: 1px 6px !important;
    padding: 8px 10px !important; font-size: 13px !important; font-weight: 500 !important;
    transition: background .18s, color .18s !important;
}
.fi-sidebar-item-button:hover { background: rgba(47,140,255,.1) !important; color: #a8c8e8 !important; }
.fi-sidebar-item-button.fi-active,
.fi-sidebar-item-button[aria-current="page"],
.fi-sidebar-item-button[data-active="true"] {
    background: rgba(47,140,255,.16) !important; color: #4fa8ff !important; font-weight: 600 !important;
}
.fi-sidebar-item-button svg { color: inherit !important; opacity: .9; }
.fi-sidebar-group-items .fi-sidebar-item-button { color: #4a6a8a !important; padding-left: 20px !important; font-size: 12.5px !important; }
.fi-sidebar-group-items .fi-sidebar-item-button:hover { color: #a8c8e8 !important; }
.fi-topbar { background: #0d1526 !important; border-bottom: 1px solid rgba(255,255,255,.07) !important; }
.fi-topbar-nav { background: #0d1526 !important; }
.fi-main, .fi-simple-main, .fi-body, .fi-main-ctn { background: #111d32 !important; }

/* Hide the old Filament page header / filter bar */
.fi-page > .fi-page-header { display: none !important; }

html:not(.dark) .fi-sidebar,
html:not(.dark) .fi-sidebar-inner,
html:not(.dark) .fi-sidebar-header { background: #1a2540 !important; }
html:not(.dark) .fi-sidebar-item-button { color: #8aaccc !important; }
html:not(.dark) .fi-sidebar-item-button:hover { background:rgba(47,140,255,.12) !important; color:#c0daee !important; }
html:not(.dark) .fi-sidebar-item-button.fi-active { background:rgba(47,140,255,.18) !important; color:#5bb8ff !important; }
html:not(.dark) .fi-sidebar-group-label { color: #3a5878 !important; }
html:not(.dark) .fi-topbar,
html:not(.dark) .fi-topbar-nav { background: #1a2540 !important; }
html:not(.dark) .fi-main,
html:not(.dark) .fi-main-ctn { background: #edf1f8 !important; }
</style>

{{-- DASHBOARD CSS --}}
<style>
.rb,
.rb *, .rb *::before, .rb *::after { box-sizing: border-box; margin: 0; padding: 0; }
.rb {
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size: 13px;
    line-height: 1.5;
    padding: 0 0 44px;
    display: grid;
    gap: 16px;

    --bg:     #111d32;
    --p1:     #172540;
    --p2:     #1c2d4c;
    --p3:     #1f3358;
    --bd:     rgba(255,255,255,.07);
    --bd2:    rgba(255,255,255,.13);
    --t1:     #cee0f4;
    --t2:     #5a7a9a;
    --t3:     #2e4a68;
    --blue:   #2f8cff;
    --cyan:   #53d3cd;
    --green:  #21c77a;
    --amber:  #e3b83f;
    --red:    #ef5b63;
    --purple: #8b5cf6;
    --sh:     0 8px 28px rgba(0,0,0,.28);
    color: var(--t1);
}
html:not(.dark) .rb {
    --bg: #edf1f8; --p1: #ffffff; --p2: #f5f8fc; --p3: #eef2f9;
    --bd: rgba(15,23,42,.08); --bd2: rgba(15,23,42,.14);
    --t1: #0e1e34; --t2: #5070a0; --t3: #9ab0cc;
    --sh: 0 4px 18px rgba(15,23,42,.1);
    color: var(--t1);
}

/* ── animations ── */
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:none} }
@keyframes blink  { 0%,100%{opacity:1} 50%{opacity:.3} }
.a  { opacity:0; animation:fadeUp .55s cubic-bezier(.16,1,.3,1) forwards; }
.d1{animation-delay:.03s} .d2{animation-delay:.08s} .d3{animation-delay:.13s}
.d4{animation-delay:.18s} .d5{animation-delay:.23s} .d6{animation-delay:.28s}
.d7{animation-delay:.33s} .d8{animation-delay:.38s} .d9{animation-delay:.43s}
.d10{animation-delay:.48s}

/* ────────── GREETING HEADER ────────── */
.rb-greet {
    display: flex; align-items: flex-end; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
    padding-bottom: 20px; border-bottom: 1px solid var(--bd);
}
.rb-greet-text h1 {
    font-size: clamp(22px,2.8vw,32px); font-weight: 780;
    letter-spacing: -.015em; color: var(--t1); line-height: 1.1;
}
.rb-greet-text h1 em {
    font-style: normal;
    background: linear-gradient(100deg,#5bc8ff,#2f8cff);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.rb-greet-text p { font-size:12px; color:var(--t2); margin-top:5px; font-weight:400; }
.rb-live {
    display: flex; align-items: center; gap: 8px;
    background: var(--p1); border: 1px solid var(--bd2);
    border-radius: 100px; padding: 7px 16px 7px 11px;
    font-size: 11.5px; font-weight: 600; color: var(--blue);
    letter-spacing: .03em; white-space: nowrap;
}
.rb-live-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--green); box-shadow: 0 0 8px var(--green);
    animation: blink 2.2s ease-in-out infinite; flex-shrink: 0;
}

/* ────────── SUBHEAD + INLINE FILTER ROW ────────── */
.rb-subhead {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.rb-title {
    font-size: 20px;
    font-weight: 750;
    letter-spacing: -.01em;
    color: var(--t1);
    white-space: nowrap;
}
.rb-filters {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.rb-filter-group {
    display: flex;
    align-items: center;
    gap: 5px;
}
.rb-filter-lbl {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--t2);
    white-space: nowrap;
}
.rb-select {
    appearance: none;
    -webkit-appearance: none;
    background-color: var(--p1);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%235a7a9a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 7px center;
    border: 1px solid var(--bd2);
    border-radius: 7px;
    padding: 5px 24px 5px 9px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t1);
    cursor: pointer;
    outline: none;
    transition: border-color .18s, box-shadow .18s;
    font-family: inherit;
}
.rb-select:hover  { border-color: rgba(47,140,255,.4); }
.rb-select:focus  { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(47,140,255,.12); }
html:not(.dark) .rb-select { background-color: #fff; color: #0e1e34; }
.rb-select option { background: #172540; color: #cee0f4; }
html:not(.dark) .rb-select option { background: #fff; color: #0e1e34; }

.rb-fdivider {
    width: 1px; height: 18px;
    background: var(--bd2);
    flex-shrink: 0;
    margin: 0 2px;
}
.rb-apply-btn {
    background: var(--blue);
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 6px 14px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .04em;
    cursor: pointer;
    transition: opacity .18s, transform .15s;
    white-space: nowrap;
    font-family: inherit;
}
.rb-apply-btn:hover  { opacity: .85; transform: translateY(-1px); }
.rb-apply-btn:active { transform: translateY(0); opacity: 1; }

/* ────────── KPI CARDS ────────── */
.rb-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
@media(max-width:1080px){.rb-kpis{grid-template-columns:repeat(2,1fr);}}
@media(max-width:520px) {.rb-kpis{grid-template-columns:1fr;}}

.rb-kpi {
    position: relative; overflow: hidden;
    background: var(--p1); border: 1px solid var(--bd);
    border-radius: 10px; padding: 18px 18px 16px;
    box-shadow: var(--sh);
    transition: transform .2s, border-color .25s;
    --ic: var(--blue);
}
.rb-kpi:hover { transform: translateY(-2px); border-color: var(--bd2); }
.rb-kpi-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.rb-kpi-val { font-size:22px; font-weight:780; letter-spacing:-.01em; line-height:1; color:var(--t1); }
.rb-kpi-lbl { font-size:11.5px; font-weight:600; color:var(--t2); margin-top:7px; }
.rb-kpi-ico {
    width:52px; height:52px; border-radius:50%; flex-shrink:0;
    display:grid; place-items:center;
    color: var(--ic);
    background: color-mix(in srgb, var(--ic) 15%, transparent);
}
.rb-kpi-ico svg { width:22px; height:22px; }
.rb-kpi::after {
    content:''; position:absolute; right:-18px; bottom:-18px;
    width:88px; height:88px; border-radius:50%; pointer-events:none;
    background: color-mix(in srgb, var(--ic) 18%, transparent);
    filter: blur(30px); opacity:.4;
}
.rb-tag {
    display:inline-flex; align-items:center; gap:4px;
    margin-top:13px; border-radius:5px; padding:3px 8px;
    font-size:10px; font-weight:800;
    background:rgba(33,199,122,.14); color:var(--green);
}
.rb-tag.warn { background:rgba(239,91,99,.14);  color:var(--red); }
.rb-tag.neu  { background:rgba(47,140,255,.13);  color:var(--blue); }
.rb-tag.mute { background:rgba(227,184,63,.12);  color:var(--amber); }

/* ────────── GRIDS ────────── */
.rb-g2  { display:grid; grid-template-columns:1fr .86fr; gap:14px; }
.rb-g2b { display:grid; grid-template-columns:1.45fr .7fr;  gap:14px; }
@media(max-width:1080px){.rb-g2,.rb-g2b{grid-template-columns:1fr;}}

/* ────────── PANEL ────────── */
.rb-panel { background:var(--p1); border:1px solid var(--bd); border-radius:10px; overflow:hidden; box-shadow:var(--sh); }
.rb-ph {
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; padding:12px 16px 11px; border-bottom:1px solid var(--bd); min-height:46px;
}
.rb-ph-title { font-size:13px; font-weight:750; color:var(--t1); }
.rb-tabs { display:flex; gap:4px; }
.rb-tab {
    border:1px solid var(--bd); border-radius:5px; padding:4px 9px;
    font-size:10.5px; font-weight:700; color:var(--t2); background:transparent; cursor:pointer;
}
.rb-tab.on { color:var(--blue); background:rgba(47,140,255,.14); border-color:rgba(47,140,255,.22); }
.rb-ph-link {
    font-size:11px; font-weight:700; color:var(--blue); text-decoration:none;
    background:rgba(47,140,255,.12); border:1px solid rgba(47,140,255,.2);
    border-radius:5px; padding:4px 10px; transition:opacity .18s;
}
.rb-ph-link:hover { opacity:.75; }

/* ────────── CHARTS ────────── */
.rb-chart-wrap { padding:14px 14px 2px; }
.rb-chart-wrap svg { display:block; width:100%; }
.rb-months {
    display:flex; justify-content:space-between;
    padding:6px 16px 12px; font-size:10.5px; font-weight:700; color:var(--t3);
}
.rb-bars-wrap { display:flex; align-items:flex-end; justify-content:space-around; padding:20px 16px 6px; min-height:185px; }
.rb-bar-col { display:flex; flex-direction:column; align-items:center; gap:8px; flex:1; }
.rb-bar-pair { display:flex; align-items:flex-end; gap:5px; }
.rb-b { width:9px; border-radius:999px; background:var(--blue); box-shadow:0 0 14px rgba(47,140,255,.2); }
.rb-b.alt { background:var(--cyan); box-shadow:0 0 14px rgba(83,211,205,.18); }
.rb-bar-lbl { font-size:10px; font-weight:700; color:var(--t3); }
.rb-bar-legend { display:flex; gap:16px; padding:10px 16px 14px; border-top:1px solid var(--bd); }
.rb-leg { display:flex; align-items:center; gap:6px; }
.rb-leg-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.rb-leg-lbl { font-size:10.5px; font-weight:600; color:var(--t2); }

/* ────────── TABLE ────────── */
.rb-tscroll { overflow-x:auto; }
.rb-table { width:100%; border-collapse:collapse; }
.rb-table th { background:var(--p2); padding:11px 14px; font-size:11px; font-weight:750; color:var(--t2); text-align:left; white-space:nowrap; }
.rb-table td { padding:11px 14px; border-top:1px solid var(--bd); font-size:11.5px; font-weight:600; color:var(--t2); vertical-align:middle; }
.rb-table tr:hover td { background:rgba(47,140,255,.04); }
.rb-person { display:flex; align-items:center; gap:10px; }
.rb-av { width:30px; height:30px; border-radius:50%; flex-shrink:0; display:grid; place-items:center; background:linear-gradient(135deg,#46d2c8,#2f8cff); font-size:11px; font-weight:900; color:#fff; }
.rb-name { font-size:12px; font-weight:750; color:var(--t1); }
.rb-meta { font-size:10.5px; color:var(--t2); }
.rb-st { display:inline-flex; align-items:center; border-radius:5px; padding:3px 8px; font-size:10px; font-weight:850; white-space:nowrap; }
.rb-st.ok  { color:var(--green);  background:rgba(33,199,122,.13); }
.rb-st.pnd { color:var(--amber);  background:rgba(227,184,63,.13); }
.rb-st.err { color:var(--red);    background:rgba(239,91,99,.13); }
.rb-empty  { padding:24px 16px; color:var(--t2); font-size:12px; }

/* ────────── DONUT ────────── */
.rb-donut-wrap { display:flex; flex-direction:column; align-items:center; padding:18px 16px 8px; }
.rb-donut-c { position:relative; display:grid; place-items:center; }
.rb-donut-lbl { position:absolute; text-align:center; pointer-events:none; }
.rb-donut-lbl span { display:block; font-size:11px; font-weight:700; color:var(--t2); }
.rb-donut-lbl strong { display:block; font-size:18px; font-weight:800; color:var(--t1); margin-top:2px; }
.rb-src-table { width:100%; border-collapse:collapse; }
.rb-src-table th,.rb-src-table td { padding:8px 16px; border-top:1px solid var(--bd); font-size:11px; font-weight:700; text-align:left; color:var(--t2); }
.rb-src-table th { background:var(--p2); border-top:none; font-size:10.5px; color:var(--t3); }

/* ────────── VERIFY LIST ────────── */
.rb-vrow { display:flex; align-items:center; gap:11px; padding:12px 16px; border-top:1px solid var(--bd); transition:background .18s; }
.rb-vrow:hover { background:rgba(47,140,255,.04); }

/* ────────── RESPONSIVE FILTER ────────── */
@media(max-width:900px) {
    .rb-subhead { flex-direction: column; align-items: flex-start; }
    .rb-filters { width: 100%; }
}
@media(max-width:600px) {
    .rb-filters { gap: 8px; }
    .rb-fdivider { display: none; }
    .rb-filter-group { flex: 1; min-width: 0; }
    .rb-select { width: 100%; }
}
</style>

<div class="rb">

    {{-- GREETING --}}
    <div class="rb-greet a d1">
        <div class="rb-greet-text">
            <h1>Good {{ $greeting }}, <em>{{ $userName }}</em> 👋</h1>
            <p>Here's what's happening on your platform today.</p>
        </div>
        <div class="rb-live">
            <div class="rb-live-dot"></div>
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    {{-- ADMIN DASHBOARD TITLE + INLINE FILTERS --}}
    <div class="rb-subhead a d2">
        <h2 class="rb-title">Admin Dashboard</h2>

        <div class="rb-filters">

            {{-- Period --}}
            <div class="rb-filter-group">
                <span class="rb-filter-lbl">Period</span>
                <select class="rb-select" id="rbf-preset">
                    @foreach([
                        'today'        => 'Today',
                        'yesterday'    => 'Yesterday',
                        'this_week'    => 'This Week',
                        'last_week'    => 'Last Week',
                        'last_30'      => 'Last 30d',
                        'this_month'   => 'This Month',
                        'last_month'   => 'Last Month',
                        'this_quarter' => 'Quarter',
                        'this_year'    => 'This Year',
                        'all_time'     => 'All Time',
                    ] as $val => $label)
                        <option value="{{ $val }}" @selected(($activePreset ?? 'this_month') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="rb-fdivider"></div>

            {{-- Gateway --}}
            <div class="rb-filter-group">
                <span class="rb-filter-lbl">Gateway</span>
                <select class="rb-select" id="rbf-gateway">
                    <option value="all" @selected(($activeGateway ?? 'all') === 'all')>All Gateways</option>
                    @foreach(\App\Domains\Payments\Enums\PaymentGateway::cases() as $gw)
                        <option value="{{ $gw->value }}" @selected(($activeGateway ?? 'all') === $gw->value)>
                            {{ str($gw->value)->headline() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="rb-fdivider"></div>

            {{-- Status --}}
            <div class="rb-filter-group">
                <span class="rb-filter-lbl">Status</span>
                <select class="rb-select" id="rbf-status">
                    <option value="all" @selected(($activeStatus ?? 'all') === 'all')>All Statuses</option>
                    @foreach(\App\Domains\Payments\Enums\PaymentStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected(($activeStatus ?? 'all') === $st->value)>
                            {{ str($st->value)->headline() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="rb-fdivider"></div>

            {{-- Courses --}}
            <div class="rb-filter-group">
                <span class="rb-filter-lbl">Courses</span>
                <select class="rb-select" id="rbf-course">
                    <option value="all"         @selected(($activeCourseStatus ?? 'all') === 'all')>All Courses</option>
                    <option value="published"   @selected(($activeCourseStatus ?? 'all') === 'published')>Published</option>
                    <option value="unpublished" @selected(($activeCourseStatus ?? 'all') === 'unpublished')>Unpublished</option>
                </select>
            </div>

            <button class="rb-apply-btn" onclick="rbApplyFilters()">Apply</button>

        </div>
    </div>

    {{-- TOP KPI CARDS --}}
    <div class="rb-kpis">

        <div class="rb-kpi a d3" style="--ic:var(--cyan)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">${{ number_format($totalRevenue, 1) }}</div>
                    <div class="rb-kpi-lbl">Wallet Balance</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/><path d="M16 11h4v4h-4a2 2 0 0 1 0-4Z"/><path d="M18 7V5a2 2 0 0 0-2-2H5"/></svg>
                </div>
            </div>
            <div class="rb-tag">{{ $successRate }}% success rate</div>
        </div>

        <div class="rb-kpi a d4" style="--ic:var(--blue)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">${{ number_format($revenueThisMonth, 1) }}</div>
                    <div class="rb-kpi-lbl">Total Income</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4 6 4v14"/><path d="M9 9h1"/><path d="M14 9h1"/><path d="M9 13h1"/><path d="M14 13h1"/></svg>
                </div>
            </div>
            <div class="rb-tag neu">{{ number_format($ordersThisMonth) }} orders this month</div>
        </div>

        <div class="rb-kpi a d5" style="--ic:var(--green)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">{{ number_format($totalStudents) }}</div>
                    <div class="rb-kpi-lbl">Total Students</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="rb-tag">↑ {{ number_format($newStudentsThisMonth) }} new this month</div>
        </div>

        <div class="rb-kpi a d6" style="--ic:var(--amber)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">{{ number_format($totalCourses) }}</div>
                    <div class="rb-kpi-lbl">Courses</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
            </div>
            <div class="rb-tag {{ $pendingVerifications > 0 ? 'warn' : '' }}">
                {{ $pendingVerifications > 0 ? '⚠ '.$pendingVerifications.' pending reviews' : '✓ All reviewed' }}
            </div>
        </div>

    </div>

    {{-- REVENUE CHART + ACTIVITY BARS --}}
    <div class="rb-g2 a d6">

        <div class="rb-panel">
            <div class="rb-ph">
                <div class="rb-ph-title">Revenue</div>
                <div class="rb-tabs">
                    <span class="rb-tab">ALL</span><span class="rb-tab">1M</span>
                    <span class="rb-tab">6M</span><span class="rb-tab on">1Y</span>
                </div>
            </div>
            <div class="rb-chart-wrap">
                <svg viewBox="0 0 648 170" style="height:170px;">
                    <defs>
                        <linearGradient id="rg" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%"   stop-color="#8b5cf6" stop-opacity=".36"/>
                            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    @for($i=0;$i<5;$i++)
                    @php $gy = 18 + $i*30; @endphp
                    <line x1="30" y1="{{ $gy }}" x2="640" y2="{{ $gy }}" stroke="rgba(148,163,184,.09)" stroke-dasharray="3 7"/>
                    <text x="0" y="{{ $gy+4 }}" fill="#2e4a68" font-size="9">{{ 80-$i*20 }}k</text>
                    @endfor
                    @foreach($pts->values() as $i => $p)
                    @php
                        $x1o = $ptCount===1 ? 32 : 32+($i/($ptCount-1))*606;
                        $y1o = 148 - max(8, round(((int)$p['orders']/$maxOrd)*78));
                    @endphp
                    @if($i < $ptCount-1)
                    @php
                        $x2o = 32+(($i+1)/($ptCount-1))*606;
                        $y2o = 148 - max(8, round(((int)$pts[$i+1]['orders']/$maxOrd)*78));
                    @endphp
                    <line x1="{{ round($x1o,1) }}" y1="{{ round($y1o,1) }}" x2="{{ round($x2o,1) }}" y2="{{ round($y2o,1) }}"
                          stroke="#21c77a" stroke-width="2" stroke-dasharray="5 4" stroke-linecap="round"/>
                    @endif
                    @endforeach
                    <polygon points="{{ $areaCoords }}" fill="url(#rg)"/>
                    <polyline points="{{ $lineCoords }}" fill="none" stroke="#8b5cf6" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
                    @foreach($pts->values() as $i => $p)
                    @php $cx2 = $ptCount===1?32:32+($i/($ptCount-1))*606; $cy2 = 148-((float)$p['revenue']/$maxRev)*130; @endphp
                    <circle cx="{{ round($cx2,1) }}" cy="{{ round($cy2,1) }}" r="3" fill="#8b5cf6" stroke="var(--p1)" stroke-width="2"/>
                    @endforeach
                </svg>
            </div>
            <div class="rb-months">
                @foreach($pts as $p)<span>{{ $p['month'] }}</span>@endforeach
            </div>
        </div>

        <div class="rb-panel">
            <div class="rb-ph">
                <div class="rb-ph-title">Activity</div>
                <div class="rb-tabs">
                    <span class="rb-tab">ALL</span><span class="rb-tab">1M</span>
                    <span class="rb-tab">6M</span><span class="rb-tab on">1Y</span>
                </div>
            </div>
            <div class="rb-bars-wrap">
                @foreach($pts as $p)
                @php
                    $oh = max(22, round(((int)$p['orders']/$maxOrd)*128));
                    $rh = max(14, round(((float)$p['revenue']/$maxRev)*100));
                @endphp
                <div class="rb-bar-col">
                    <div class="rb-bar-pair">
                        <div class="rb-b"     style="height:{{ $oh }}px;"></div>
                        <div class="rb-b alt" style="height:{{ $rh }}px;"></div>
                    </div>
                    <span class="rb-bar-lbl">{{ $p['month'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="rb-bar-legend">
                <div class="rb-leg"><div class="rb-leg-dot" style="background:var(--blue);"></div><span class="rb-leg-lbl">Orders</span></div>
                <div class="rb-leg"><div class="rb-leg-dot" style="background:var(--cyan);"></div><span class="rb-leg-lbl">Revenue</span></div>
            </div>
        </div>

    </div>

    {{-- TRANSACTIONS + REVENUE SOURCES --}}
    <div class="rb-g2b a d7">

        <div class="rb-panel">
            <div class="rb-ph">
                <div class="rb-ph-title">Transactions</div>
                <div class="rb-tabs"><span class="rb-tab on">All</span></div>
            </div>
            <div class="rb-tscroll">
                <table class="rb-table">
                    <thead>
                        <tr><th>Name</th><th>Description</th><th>Amount</th><th>Timestamp</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @forelse($latestTx as $pay)
                    @php
                        $st  = $pay->status?->value ?? $pay->status ?? 'pending';
                        $gw  = $pay->payment_gateway?->value ?? $pay->payment_gateway ?? 'payment';
                        $nm  = $pay->order?->user?->name ?? $pay->order?->customer_name ?? 'Customer';
                        $cls = in_array($st,['paid','completed']) ? 'ok' : ($st==='pending' ? 'pnd' : 'err');
                    @endphp
                    <tr>
                        <td>
                            <div class="rb-person">
                                <div class="rb-av">{{ strtoupper(substr($nm,0,1)) }}</div>
                                <div>
                                    <div class="rb-name">{{ $nm }}</div>
                                    <div class="rb-meta">{{ $pay->order?->order_number ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ str($gw)->headline() }} payment</td>
                        <td style="white-space:nowrap;">${{ number_format((float)$pay->amount,2) }}</td>
                        <td style="white-space:nowrap;">{{ $pay->created_at?->format('d M, y H:i') }}</td>
                        <td><span class="rb-st {{ $cls }}">{{ str($st)->headline() }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="rb-empty">No recent transactions.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rb-panel">
            <div class="rb-ph">
                <div class="rb-ph-title">Revenue Sources</div>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--t2)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            </div>
            <div class="rb-donut-wrap">
                <div class="rb-donut-c">
                    <svg width="140" height="140" viewBox="0 0 140 140">
                        <circle cx="70" cy="70" r="52" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="22"/>
                        @foreach($donutSegs as $seg)
                        <circle cx="70" cy="70" r="52" fill="none"
                            stroke="{{ $seg['color'] }}" stroke-width="22"
                            stroke-dasharray="{{ $seg['dash'] }} {{ $seg['gap'] }}"
                            stroke-dashoffset="{{ -$seg['off'] }}"
                            transform="rotate(-90 70 70)"/>
                        @endforeach
                        <circle cx="70" cy="70" r="41" fill="var(--p1)"/>
                    </svg>
                    <div class="rb-donut-lbl">
                        <span>Total</span>
                        <strong>{{ number_format($gtTotal, 0) }}</strong>
                    </div>
                </div>
            </div>
            <table class="rb-src-table">
                <thead><tr><th>Source</th><th>Revenue</th><th>%</th></tr></thead>
                <tbody>
                @forelse($gwRows as $i => $row)
                <tr>
                    <td style="display:flex;align-items:center;gap:7px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $donutColors[$i%5] }};display:inline-block;flex-shrink:0;"></span>
                        {{ $row['name'] }}
                    </td>
                    <td>${{ number_format($row['amount'],0) }}</td>
                    <td>{{ $row['percent'] }}%</td>
                </tr>
                @empty
                <tr><td colspan="3" class="rb-empty">No data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- SECOND KPI ROW --}}
    <div class="rb-kpis a d8">

        <div class="rb-kpi" style="--ic:var(--blue)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">{{ number_format($totalInstructors) }}</div>
                    <div class="rb-kpi-lbl">Instructors</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
            </div>
            <div class="rb-tag {{ $pendingVerifications > 0 ? 'warn' : '' }}">{{ $pendingVerifications }} pending</div>
        </div>

        <div class="rb-kpi" style="--ic:var(--purple)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">{{ number_format($totalLessons) }}</div>
                    <div class="rb-kpi-lbl">Lessons</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m10 8 6 4-6 4V8z"/></svg>
                </div>
            </div>
            <div class="rb-tag mute">{{ number_format($totalSections) }} sections</div>
        </div>

        <div class="rb-kpi" style="--ic:var(--green)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">${{ number_format($totalInstructorBalance, 0) }}</div>
                    <div class="rb-kpi-lbl">Instructor Balance</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="rb-tag mute">${{ number_format($totalPendingBalance, 0) }} pending</div>
        </div>

        <div class="rb-kpi" style="--ic:var(--red)">
            <div class="rb-kpi-row">
                <div>
                    <div class="rb-kpi-val">{{ number_format($failedPayments) }}</div>
                    <div class="rb-kpi-lbl">Failed Payments</div>
                </div>
                <div class="rb-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                </div>
            </div>
            <div class="rb-tag {{ $failedPayments > 0 ? 'warn' : '' }}">{{ $failedPayments > 0 ? 'Needs review' : '✓ Clear' }}</div>
        </div>

    </div>

    {{-- RECENT COURSES + PENDING VERIFICATIONS --}}
    <div class="rb-g2b a d9">

        <div class="rb-panel">
            <div class="rb-ph">
                <div class="rb-ph-title">Recent Courses</div>
                <a href="{{ route('filament.admin.resources.courses.index') }}" class="rb-ph-link">View all</a>
            </div>
            <div class="rb-tscroll">
                <table class="rb-table">
                    <thead>
                        <tr><th>Course</th><th>Instructor</th><th>Category</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @forelse($recentCourses->take(6) as $course)
                    @php $cs = $course->is_published ? 'ok' : ($course->status==='pending_review' ? 'pnd' : 'err'); @endphp
                    <tr>
                        <td>
                            <div class="rb-person">
                                <div class="rb-av">{{ strtoupper(substr($course->title ?? 'C',0,1)) }}</div>
                                <div>
                                    <div class="rb-name">{{ str($course->title ?? 'Untitled')->limit(32) }}</div>
                                    <div class="rb-meta">{{ $course->created_at?->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $course->instructor?->name ?? 'Unassigned' }}</td>
                        <td>{{ $course->category?->name ?? 'General' }}</td>
                        <td><span class="rb-st {{ $cs }}">{{ str($course->status ?? 'Draft')->headline() }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="rb-empty">No courses found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rb-panel">
            <div class="rb-ph">
                <div class="rb-ph-title">Pending Verifications</div>
                <a href="{{ route('filament.admin.resources.instructor-verifications.index') }}" class="rb-ph-link">Review</a>
            </div>
            @forelse($pendingInstructors as $inst)
            <div class="rb-vrow">
                <div class="rb-av">{{ strtoupper(substr($inst->name ?? 'I',0,1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="rb-name">{{ $inst->name }}</div>
                    <div class="rb-meta">{{ $inst->email }}</div>
                </div>
                <span class="rb-st pnd">Pending</span>
            </div>
            @empty
            <div class="rb-empty" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:32px 16px;text-align:center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="opacity:.55;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="color:var(--t2);font-size:12px;">All instructors verified</span>
            </div>
            @endforelse
        </div>

    </div>

</div>

<script>
function rbApplyFilters() {
    const p = new URLSearchParams(window.location.search);
    p.set('preset',        document.getElementById('rbf-preset').value);
    p.set('gateway',       document.getElementById('rbf-gateway').value);
    p.set('status',        document.getElementById('rbf-status').value);
    p.set('course_status', document.getElementById('rbf-course').value);
    window.location.search = p.toString();
}
</script>