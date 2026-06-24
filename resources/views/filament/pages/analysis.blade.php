{{-- resources/views/filament/pages/analysis.blade.php --}}

@php
    // ── Student Trend SVG math ────────────────────────────────────
    $sPts    = collect($studentTrend)->values();
    $sMax    = max((int)$sPts->max('count'), 1);
    $sPtCnt  = max($sPts->count(), 1);
    $SW = 616; $SH = 150;

    $sLineCoords = $sPts->map(function($p, $i) use ($sPtCnt, $SW, $SH, $sMax) {
        $x = $sPtCnt === 1 ? 32 : 32 + ($i / ($sPtCnt - 1)) * ($SW - 32);
        $y = ($SH - 20) - ((int)$p['count'] / $sMax) * ($SH - 44);
        return round($x, 1) . ',' . round($y, 1);
    })->implode(' ');
    $sAreaCoords = '32,' . ($SH - 20) . ' ' . $sLineCoords . ' ' . $SW . ',' . ($SH - 20);

    // ── Revenue Trend bar math ────────────────────────────────────
    $rPts   = collect($revenueTrend)->values();
    $rMax   = max((float)$rPts->max('revenue'), 1);
    $oMax   = max((int)$rPts->max('orders'), 1);
    $rPtCnt = max($rPts->count(), 1);

    // ── Payment status donut ──────────────────────────────────────
    $statusColors = ['Completed' => '#21c77a', 'Pending' => '#e3b83f', 'Failed' => '#ef5b63', 'Refunded' => '#8b5cf6'];
    $sR = 52; $sCirc = 2 * M_PI * $sR;
    $sDoff = 0;
    $statusSegs = collect($paymentStatusBreakdown)->map(function($cnt, $label) use (&$sDoff, $statusColors, $sCirc, $sR, $statusTotal) {
        $pct  = $cnt / $statusTotal;
        $dash = $pct * $sCirc;
        $seg  = ['label' => $label, 'count' => $cnt, 'color' => $statusColors[$label] ?? '#2f8cff',
                 'pct' => round($pct * 100), 'dash' => round($dash, 2), 'gap' => round($sCirc - $dash, 2), 'off' => round($sDoff, 2)];
        $sDoff += $dash;
        return $seg;
    })->values();

    // ── Gateway bar widths ────────────────────────────────────────
    $gwColors = ['#2f8cff', '#53d3cd', '#8b5cf6', '#21c77a', '#e3b83f'];

    $hour     = now()->hour;
    $userName = auth()->user()->name ?? 'Admin';
@endphp

<style>
.an, .an *, .an *::before, .an *::after { box-sizing: border-box; margin: 0; padding: 0; }
.an {
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size: 13px; line-height: 1.5;
    padding: 0 0 44px;
    display: grid; gap: 16px;

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
html:not(.dark) .an {
    --bg:#edf1f8; --p1:#ffffff; --p2:#f5f8fc; --p3:#eef2f9;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0e1e34; --t2:#5070a0; --t3:#9ab0cc;
    --sh:0 4px 18px rgba(15,23,42,.1);
    color: var(--t1);
}

/* ── Animations ── */
@keyframes anFadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:none} }
.ana { opacity:0; animation:anFadeUp .52s cubic-bezier(.16,1,.3,1) forwards; }
.an1{animation-delay:.03s} .an2{animation-delay:.08s} .an3{animation-delay:.13s}
.an4{animation-delay:.18s} .an5{animation-delay:.23s} .an6{animation-delay:.28s}
.an7{animation-delay:.33s} .an8{animation-delay:.38s} .an9{animation-delay:.43s}
.an10{animation-delay:.48s}

/* ── Header ── */
.an-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
    padding-bottom: 20px; border-bottom: 1px solid var(--bd);
}
.an-header-text h1 {
    font-size: clamp(20px,2.4vw,28px); font-weight: 780;
    letter-spacing: -.015em; color: var(--t1); line-height: 1.1;
}
.an-header-text h1 em {
    font-style: normal;
    background: linear-gradient(100deg,#5bc8ff,#2f8cff);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.an-header-text p { font-size:12px; color:var(--t2); margin-top:4px; }

/* ── Filters ── */
.an-filters {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.an-flbl {
    font-size: 10px; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; color: var(--t2); white-space: nowrap;
}
.an-sel {
    appearance: none; -webkit-appearance: none;
    background-color: var(--p1);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%235a7a9a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 7px center;
    border: 1px solid var(--bd2); border-radius: 7px;
    padding: 5px 24px 5px 9px; font-size: 11px; font-weight: 700;
    color: var(--t1); cursor: pointer; outline: none;
    transition: border-color .18s, box-shadow .18s; font-family: inherit;
}
.an-sel:hover { border-color: rgba(47,140,255,.4); }
.an-sel:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(47,140,255,.12); }
html:not(.dark) .an-sel { background-color:#fff; color:#0e1e34; }
.an-sel option { background:#172540; color:#cee0f4; }
html:not(.dark) .an-sel option { background:#fff; color:#0e1e34; }
.an-div { width:1px; height:18px; background:var(--bd2); flex-shrink:0; margin:0 2px; }
.an-apply {
    background: var(--blue); color:#fff; border:none; border-radius:7px;
    padding:6px 14px; font-size:11px; font-weight:800; letter-spacing:.04em;
    cursor:pointer; transition:opacity .18s,transform .15s; white-space:nowrap; font-family:inherit;
}
.an-apply:hover { opacity:.85; transform:translateY(-1px); }

/* ── KPI grid ── */
.an-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
@media(max-width:1080px){.an-kpis{grid-template-columns:repeat(2,1fr);}}
@media(max-width:520px){.an-kpis{grid-template-columns:1fr;}}

.an-kpi {
    position:relative; overflow:hidden;
    background:var(--p1); border:1px solid var(--bd);
    border-radius:10px; padding:18px 18px 16px;
    box-shadow:var(--sh);
    transition:transform .2s,border-color .25s;
    --ic:var(--blue);
}
.an-kpi:hover { transform:translateY(-2px); border-color:var(--bd2); }
.an-kpi-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.an-kpi-val { font-size:22px; font-weight:780; letter-spacing:-.01em; line-height:1; color:var(--t1); }
.an-kpi-lbl { font-size:11.5px; font-weight:600; color:var(--t2); margin-top:7px; }
.an-kpi-ico {
    width:52px; height:52px; border-radius:50%; flex-shrink:0;
    display:grid; place-items:center;
    color:var(--ic);
    background:color-mix(in srgb, var(--ic) 15%, transparent);
}
.an-kpi-ico svg { width:22px; height:22px; }
.an-kpi::after {
    content:''; position:absolute; right:-18px; bottom:-18px;
    width:88px; height:88px; border-radius:50%; pointer-events:none;
    background:color-mix(in srgb, var(--ic) 18%, transparent);
    filter:blur(30px); opacity:.4;
}
.an-tag {
    display:inline-flex; align-items:center; gap:4px;
    margin-top:13px; border-radius:5px; padding:3px 8px;
    font-size:10px; font-weight:800;
    background:rgba(33,199,122,.14); color:var(--green);
}
.an-tag.warn  { background:rgba(239,91,99,.14);  color:var(--red); }
.an-tag.amber { background:rgba(227,184,63,.12);  color:var(--amber); }
.an-tag.blue  { background:rgba(47,140,255,.13);  color:var(--blue); }
.an-tag.purple{ background:rgba(139,92,246,.13);  color:var(--purple); }

/* ── 2-col grids ── */
.an-g2  { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.an-g2b { display:grid; grid-template-columns:1.55fr 1fr; gap:14px; }
.an-g3  { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.an-g3b { display:grid; grid-template-columns:repeat(6,1fr); gap:14px; }
@media(max-width:1080px){.an-g2,.an-g2b{grid-template-columns:1fr;}.an-g3{grid-template-columns:repeat(2,1fr);}.an-g3b{grid-template-columns:repeat(3,1fr);}}
@media(max-width:520px){.an-g3,.an-g3b{grid-template-columns:1fr 1fr;}}

/* ── Panel ── */
.an-panel {
    background:var(--p1); border:1px solid var(--bd);
    border-radius:10px; overflow:hidden; box-shadow:var(--sh);
}
.an-ph {
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; padding:12px 16px 11px; border-bottom:1px solid var(--bd); min-height:46px;
}
.an-ph-title { font-size:13px; font-weight:750; color:var(--t1); }
.an-ph-sub   { font-size:11px; color:var(--t2); }

/* ── SVG charts ── */
.an-chart-wrap { padding:14px 14px 2px; }
.an-chart-wrap svg { display:block; width:100%; }
.an-months {
    display:flex; justify-content:space-between;
    padding:6px 16px 12px; font-size:10.5px; font-weight:700; color:var(--t3);
}

/* ── Revenue bars ── */
.an-bars-wrap {
    display:flex; align-items:flex-end; justify-content:space-around;
    padding:20px 16px 6px; min-height:155px;
}
.an-bar-col  { display:flex; flex-direction:column; align-items:center; gap:8px; flex:1; }
.an-bar-pair { display:flex; align-items:flex-end; gap:5px; }
.an-b        { width:10px; border-radius:999px; background:var(--purple); box-shadow:0 0 14px rgba(139,92,246,.2); }
.an-b.alt    { background:var(--cyan); box-shadow:0 0 14px rgba(83,211,205,.18); }
.an-bar-lbl  { font-size:10px; font-weight:700; color:var(--t3); }
.an-bar-legend { display:flex; gap:16px; padding:10px 16px 14px; border-top:1px solid var(--bd); }
.an-leg { display:flex; align-items:center; gap:6px; }
.an-leg-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.an-leg-lbl { font-size:10.5px; font-weight:600; color:var(--t2); }

/* ── Donut ── */
.an-donut-wrap {
    display:flex; flex-direction:column; align-items:center; padding:18px 16px 8px;
}
.an-donut-c { position:relative; display:grid; place-items:center; }
.an-donut-lbl { position:absolute; text-align:center; pointer-events:none; }
.an-donut-lbl span   { display:block; font-size:11px; font-weight:700; color:var(--t2); }
.an-donut-lbl strong { display:block; font-size:20px; font-weight:800; color:var(--t1); margin-top:2px; }
.an-legend-list { width:100%; padding:0 16px 14px; }
.an-leg-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:7px 0; border-top:1px solid var(--bd);
    font-size:11px; font-weight:700;
}
.an-leg-row:first-child { border-top:none; }
.an-leg-left  { display:flex; align-items:center; gap:7px; color:var(--t2); }
.an-leg-pct   { color:var(--t1); }
.an-leg-cnt   { font-size:10.5px; color:var(--t3); }

/* ── Gateway bars ── */
.an-gw-list { padding:12px 16px 8px; display:flex; flex-direction:column; gap:11px; }
.an-gw-row  { display:flex; flex-direction:column; gap:5px; }
.an-gw-top  { display:flex; justify-content:space-between; font-size:11.5px; font-weight:700; color:var(--t2); }
.an-gw-amt  { color:var(--t1); }
.an-gw-bar-bg { height:6px; border-radius:999px; background:rgba(255,255,255,.07); overflow:hidden; }
.an-gw-bar-fill { height:100%; border-radius:999px; transition:width .5s cubic-bezier(.16,1,.3,1); }

/* ── Content stat mini cards ── */
.an-mini {
    background:var(--p1); border:1px solid var(--bd);
    border-radius:10px; padding:16px; box-shadow:var(--sh);
    display:flex; flex-direction:column; gap:6px;
}
.an-mini-val { font-size:24px; font-weight:780; line-height:1; color:var(--t1); }
.an-mini-lbl { font-size:11px; font-weight:600; color:var(--t2); }
.an-mini-sub { font-size:10.5px; color:var(--t3); }

/* ── Section label ── */
.an-section-lbl {
    font-size:10.5px; font-weight:800; letter-spacing:.1em;
    text-transform:uppercase; color:var(--t3);
    padding-bottom:4px; border-bottom:1px solid var(--bd);
}

/* ── Responsive filter ── */
@media(max-width:900px){.an-header{flex-direction:column;align-items:flex-start;}.an-filters{width:100%;}}
@media(max-width:600px){.an-div{display:none;}.an-filters{gap:8px;}}
</style>

<div class="an">

    {{-- HEADER --}}
    <div class="an-header ana an1">
        <div class="an-header-text">
            <h1>Platform <em>Analysis</em></h1>
            <p>Deep-dive metrics for revenue, engagement, and content performance.</p>
        </div>
        <div class="an-filters">
            <span class="an-flbl">Period</span>
            <select class="an-sel" id="anf-preset">
                @foreach([
                    'today'        => 'Today',
                    'yesterday'    => 'Yesterday',
                    'this_week'    => 'This Week',
                    'last_week'    => 'Last Week',
                    'last_30'      => 'Last 30 Days',
                    'last_6m'      => 'Last 6 Months',
                    'this_month'   => 'This Month',
                    'last_month'   => 'Last Month',
                    'this_quarter' => 'This Quarter',
                    'this_year'    => 'This Year',
                    'all_time'     => 'All Time',
                ] as $val => $label)
                    <option value="{{ $val }}" @selected(($activePreset ?? 'last_6m') === $val)>{{ $label }}</option>
                @endforeach
            </select>

            <div class="an-div"></div>
            <span class="an-flbl">Gateway</span>
            <select class="an-sel" id="anf-gateway">
                <option value="all" @selected(($activeGateway ?? 'all') === 'all')>All Gateways</option>
                @foreach(\App\Domains\Payments\Enums\PaymentGateway::cases() as $gw)
                    <option value="{{ $gw->value }}" @selected(($activeGateway ?? 'all') === $gw->value)>
                        {{ str($gw->value)->headline() }}
                    </option>
                @endforeach
            </select>

            <button class="an-apply" onclick="anApplyFilters()">Apply</button>
        </div>
    </div>

    {{-- PLATFORM CONTENT STATS --}}
    <p class="an-section-lbl ana an7">Platform Content (All-Time)</p>

    <div class="an-g3b ana an8">

        <div class="an-mini">
            <div class="an-mini-val" style="color:var(--blue);">{{ number_format($totalCourses) }}</div>
            <div class="an-mini-lbl">Total Courses</div>
            <div class="an-mini-sub">{{ number_format($publishedCourses) }} published</div>
        </div>

        <div class="an-mini">
            <div class="an-mini-val" style="color:var(--purple);">{{ number_format($totalSections) }}</div>
            <div class="an-mini-lbl">Sections</div>
            <div class="an-mini-sub">Across all courses</div>
        </div>

        <div class="an-mini">
            <div class="an-mini-val" style="color:var(--cyan);">{{ number_format($totalLessons) }}</div>
            <div class="an-mini-lbl">Lessons</div>
            <div class="an-mini-sub">Total content units</div>
        </div>

        <div class="an-mini">
            <div class="an-mini-val" style="color:var(--green);">{{ number_format($totalInstructors) }}</div>
            <div class="an-mini-lbl">Instructors</div>
            <div class="an-mini-sub">
                @if($pendingVerifications > 0)
                    <span style="color:var(--amber);">{{ $pendingVerifications }} pending</span>
                @else
                    All verified
                @endif
            </div>
        </div>

        <div class="an-mini">
            <div class="an-mini-val" style="color:{{ $pendingCourseReviews > 0 ? 'var(--amber)' : 'var(--green)' }};">{{ number_format($pendingCourseReviews) }}</div>
            <div class="an-mini-lbl">Pending Reviews</div>
            <div class="an-mini-sub">Courses awaiting approval</div>
        </div>

        <div class="an-mini">
            <div class="an-mini-val" style="color:var(--amber);">${{ number_format($totalInstructorBalance, 0) }}</div>
            <div class="an-mini-lbl">Instructor Balance</div>
            <div class="an-mini-sub">${{ number_format($totalPendingBalance, 0) }} pending</div>
        </div>

    </div>
    
    {{-- KPI CARDS --}}
    <div class="an-kpis ana an2">

        <div class="an-kpi" style="--ic:var(--cyan)">
            <div class="an-kpi-row">
                <div>
                    <div class="an-kpi-val">${{ number_format($totalRevenue, 0) }}</div>
                    <div class="an-kpi-lbl">Total Revenue</div>
                </div>
                <div class="an-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="an-tag blue">{{ $successRate }}% success rate</div>
        </div>

        <div class="an-kpi" style="--ic:var(--blue)">
            <div class="an-kpi-row">
                <div>
                    <div class="an-kpi-val">{{ number_format($totalOrders) }}</div>
                    <div class="an-kpi-lbl">Total Orders</div>
                </div>
                <div class="an-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
            </div>
            <div class="an-tag blue">{{ number_format($completedPayments) }} completed</div>
        </div>

        <div class="an-kpi" style="--ic:var(--green)">
            <div class="an-kpi-row">
                <div>
                    <div class="an-kpi-val">{{ number_format($totalStudents) }}</div>
                    <div class="an-kpi-lbl">Students (Period)</div>
                </div>
                <div class="an-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="an-tag">{{ number_format($totalInstructors) }} instructors</div>
        </div>

        <div class="an-kpi" style="--ic:var(--{{ $failedPayments > 0 ? 'red' : 'green' }})">
            <div class="an-kpi-row">
                <div>
                    <div class="an-kpi-val">{{ $successRate }}%</div>
                    <div class="an-kpi-lbl">Payment Success</div>
                </div>
                <div class="an-kpi-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            @if($failedPayments > 0)
                <div class="an-tag warn">{{ number_format($failedPayments) }} failed</div>
            @else
                <div class="an-tag">✓ No failures</div>
            @endif
        </div>

    </div>

    {{-- TREND CHARTS --}}
    <p class="an-section-lbl ana an3">6-Month Trends</p>

    <div class="an-g2 ana an4">

        {{-- Student Growth Chart --}}
        <div class="an-panel">
            <div class="an-ph">
                <div class="an-ph-title">Student Growth</div>
                <span class="an-ph-sub">New registrations per month</span>
            </div>
            <div class="an-chart-wrap">
                <svg viewBox="0 0 648 155" style="height:155px;">
                    <defs>
                        <linearGradient id="sg" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%"   stop-color="#21c77a" stop-opacity=".32"/>
                            <stop offset="100%" stop-color="#21c77a" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    @for($i = 0; $i < 4; $i++)
                    @php $gy = 18 + $i * 28; @endphp
                    <line x1="30" y1="{{ $gy }}" x2="640" y2="{{ $gy }}" stroke="rgba(148,163,184,.08)" stroke-dasharray="3 7"/>
                    @endfor
                    <polygon points="{{ $sAreaCoords }}" fill="url(#sg)"/>
                    <polyline points="{{ $sLineCoords }}" fill="none" stroke="#21c77a" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    @foreach($sPts as $i => $p)
                    @php
                        $cx = $sPtCnt === 1 ? 32 : 32 + ($i / ($sPtCnt - 1)) * 606;
                        $cy = ($SH - 20) - ((int)$p['count'] / $sMax) * ($SH - 44);
                    @endphp
                    <circle cx="{{ round($cx,1) }}" cy="{{ round($cy,1) }}" r="4" fill="#21c77a" stroke="var(--p1)" stroke-width="2"/>
                    <text x="{{ round($cx,1) }}" y="{{ round($cy - 10, 1) }}" fill="#5a7a9a" font-size="9" text-anchor="middle">{{ $p['count'] }}</text>
                    @endforeach
                </svg>
            </div>
            <div class="an-months">
                @foreach($sPts as $p)<span>{{ $p['month'] }}</span>@endforeach
            </div>
        </div>

        {{-- Revenue vs Orders Bars --}}
        <div class="an-panel">
            <div class="an-ph">
                <div class="an-ph-title">Revenue vs Orders</div>
                <span class="an-ph-sub">Monthly comparison</span>
            </div>
            <div class="an-bars-wrap">
                @foreach($rPts as $p)
                @php
                    $rh = max(14, round(((float)$p['revenue'] / $rMax) * 118));
                    $oh = max(10, round(((int)$p['orders']  / $oMax)  * 90));
                @endphp
                <div class="an-bar-col">
                    <div class="an-bar-pair">
                        <div class="an-b"     style="height:{{ $rh }}px;" title="${{ number_format($p['revenue'],0) }}"></div>
                        <div class="an-b alt" style="height:{{ $oh }}px;" title="{{ $p['orders'] }} orders"></div>
                    </div>
                    <span class="an-bar-lbl">{{ $p['month'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="an-bar-legend">
                <div class="an-leg"><div class="an-leg-dot" style="background:var(--purple);"></div><span class="an-leg-lbl">Revenue</span></div>
                <div class="an-leg"><div class="an-leg-dot" style="background:var(--cyan);"></div><span class="an-leg-lbl">Orders</span></div>
            </div>
        </div>

    </div>

    {{-- PAYMENT ANALYSIS --}}
    <p class="an-section-lbl ana an5">Payment Analysis</p>

    <div class="an-g2 ana an6">

        {{-- Payment Status Donut --}}
        <div class="an-panel">
            <div class="an-ph">
                <div class="an-ph-title">Payment Status Breakdown</div>
                <span class="an-ph-sub">{{ number_format($statusTotal) }} total</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;align-items:center;gap:0;">
                <div class="an-donut-wrap">
                    <div class="an-donut-c">
                        <svg width="150" height="150" viewBox="0 0 150 150" role="img" aria-label="Payment status distribution">
                            <circle cx="75" cy="75" r="52" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="24"/>
                            @foreach($statusSegs as $seg)
                            <circle cx="75" cy="75" r="52" fill="none"
                                stroke="{{ $seg['color'] }}" stroke-width="24"
                                stroke-dasharray="{{ $seg['dash'] }} {{ $seg['gap'] }}"
                                stroke-dashoffset="{{ -$seg['off'] }}"
                                transform="rotate(-90 75 75)">
                                <title>{{ $seg['label'] }}: {{ $seg['count'] }} ({{ $seg['pct'] }}%)</title>
                            </circle>
                            @endforeach
                            <circle cx="75" cy="75" r="42" fill="var(--p1)"/>
                        </svg>
                        <div class="an-donut-lbl">
                            <span>Total</span>
                            <strong>{{ number_format($statusTotal) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="an-legend-list">
                    @foreach($statusSegs as $seg)
                    <div class="an-leg-row">
                        <div class="an-leg-left">
                            <span style="width:9px;height:9px;border-radius:50%;background:{{ $seg['color'] }};display:inline-block;flex-shrink:0;"></span>
                            {{ $seg['label'] }}
                        </div>
                        <div style="text-align:right;">
                            <div class="an-leg-pct">{{ $seg['pct'] }}%</div>
                            <div class="an-leg-cnt">{{ number_format($seg['count']) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Gateway Revenue Bars --}}
        <div class="an-panel">
            <div class="an-ph">
                <div class="an-ph-title">Revenue by Gateway</div>
                <span class="an-ph-sub">${{ number_format($gtTotal, 0) }} total</span>
            </div>
            <div class="an-gw-list">
                @forelse($paymentGatewayBreakdown as $name => $amount)
                @php
                    $gwPct = $gtTotal > 0 ? round(($amount / $gtTotal) * 100, 1) : 0;
                    $gwIdx = $loop->index;
                @endphp
                <div class="an-gw-row">
                    <div class="an-gw-top">
                        <span>{{ $name }}</span>
                        <span class="an-gw-amt">${{ number_format($amount, 0) }} <span style="color:var(--t3);font-weight:600;">({{ $gwPct }}%)</span></span>
                    </div>
                    <div class="an-gw-bar-bg">
                        <div class="an-gw-bar-fill" style="width:{{ $gwPct }}%;background:{{ $gwColors[$gwIdx % 5] }};"></div>
                    </div>
                </div>
                @empty
                <p style="color:var(--t2);font-size:12px;padding:16px 0;">No gateway data for this period.</p>
                @endforelse
            </div>

            {{-- Payment health summary --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:8px 16px 16px;">
                <div style="background:rgba(33,199,122,.08);border:1px solid rgba(33,199,122,.15);border-radius:8px;padding:11px;">
                    <div style="font-size:10.5px;color:var(--t2);font-weight:700;margin-bottom:3px;">Completed</div>
                    <div style="font-size:16px;font-weight:780;color:var(--green);">{{ number_format($completedPayments) }}</div>
                </div>
                <div style="background:rgba(239,91,99,.08);border:1px solid rgba(239,91,99,.15);border-radius:8px;padding:11px;">
                    <div style="font-size:10.5px;color:var(--t2);font-weight:700;margin-bottom:3px;">Failed / Pending</div>
                    <div style="font-size:16px;font-weight:780;color:var(--red);">{{ number_format($failedPayments + $pendingPayments) }}</div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
function anApplyFilters() {
    const p = new URLSearchParams(window.location.search);
    p.set('preset',  document.getElementById('anf-preset').value);
    p.set('gateway', document.getElementById('anf-gateway').value);
    window.location.search = p.toString();
}
</script>
