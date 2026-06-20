<x-filament-panels::page.simple>
@php
    $platformName    = filament()->getBrandName() ?: 'Hybrid Learning';
    $studentCount    = \App\Domains\Users\Models\User::role('student')->count();
    $instructorCount = \App\Domains\Users\Models\User::role('instructor')->count();
    $courseCount     = \App\Domains\Courses\Models\Course::count();
    $orderCount      = \App\Domains\Orders\Models\Order::count();

    $stats = [
        ['icon' => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
         'value' => number_format($studentCount),    'label' => 'STUDENTS',    'pct' => '12.5', 'color' => '#22d3ee', 'rgb' => '34,211,238'],
        ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
         'value' => number_format($courseCount),     'label' => 'COURSES',     'pct' => '8.3',  'color' => '#a855f7', 'rgb' => '168,85,247'],
        ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
         'value' => number_format($instructorCount), 'label' => 'INSTRUCTORS', 'pct' => '5.7',  'color' => '#ec4899', 'rgb' => '236,72,153'],
        ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
         'value' => number_format($orderCount),      'label' => 'ORDERS',      'pct' => '15.4', 'color' => '#22d3ee', 'rgb' => '34,211,238'],
    ];
@endphp

<x-slot name="heading"></x-slot>
<x-slot name="subheading"></x-slot>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ── Reset ── */
html, body {
    margin: 0 !important; padding: 0 !important;
    background: #060a12 !important;
    overflow: hidden !important; height: 100% !important;
}
.fi-simple-layout {
    min-height: unset !important; background: transparent !important;
    padding: 0 !important; display: block !important;
}
.fi-simple-main {
    max-width: none !important; width: 100% !important;
    padding: 0 !important; background: transparent !important;
    box-shadow: none !important; border-radius: 0 !important;
    outline: none !important; border: none !important;
}

/* ── Root ── */
#cl-root *, #cl-root *::before, #cl-root *::after { box-sizing: border-box; }
#cl-root {
    position: fixed; inset: 0; z-index: 9999;
    overflow-y: auto;
    background: #060a12;
    font-family: 'Space Mono', monospace;
}
/* safe centering wrapper: min-height keeps flex centering usable;
   when card > viewport, #cl-root scrolls from the top */
.cl-center {
    min-height: 100%;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}

/* ── Card wrapper ── */
#cl-root .cl-card {
    position: relative; z-index: 1;
    width: min(1240px, calc(100% - 40px));
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 0 0 1px rgba(34,211,238,.12), 0 50px 120px rgba(0,0,0,.9);
    animation: cardIn .65s cubic-bezier(.16,1,.3,1) both;
}
@keyframes cardIn {
    from { opacity: 0; transform: translateY(22px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ═══════════════ LEFT PANEL ═══════════════ */
.cl-left {
    position: relative;
    background: #05090f;
    border-right: 1px solid rgba(34,211,238,.1);
    display: flex;
    overflow: hidden;
}

/* ── Line numbers ── */
.cl-lines {
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 34px;
    padding: 24px 0;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255,255,255,.04);
    background: rgba(0,0,0,.25);
    overflow: hidden;
    z-index: 1;
}
.cl-ln {
    font-family: 'Space Mono', monospace;
    font-size: 8.5px;
    color: rgba(255,255,255,.1);
    text-align: right;
    padding: 0 7px;
    line-height: 24px;
    flex-shrink: 0;
}

/* ── Main left content ── */
.cl-main {
    flex: 1;
    margin-left: 34px; /* offset for abs-positioned line numbers */
    padding: 32px 28px 26px 20px;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

/* ── Ambient glow ── */
.cl-glow-1 {
    position: absolute;
    top: -80px; left: -60px;
    width: 380px; height: 380px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,211,238,.07) 0%, transparent 70%);
    pointer-events: none;
    animation: glowPulse 6s ease-in-out infinite;
}
.cl-glow-2 {
    position: absolute;
    bottom: -60px; right: 40px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(168,85,247,.06) 0%, transparent 70%);
    pointer-events: none;
    animation: glowPulse 8s ease-in-out infinite reverse;
}
@keyframes glowPulse { 0%,100%{opacity:.6} 50%{opacity:1} }

/* ── Background code snippet ── */
.cl-bg-code {
    position: absolute;
    top: 130px; right: 14px;
    font-family: 'Space Mono', monospace;
    font-size: 9px;
    line-height: 1.75;
    color: rgba(255,255,255,.055);
    white-space: pre;
    pointer-events: none;
    z-index: 0;
}

/* ── Logo ── */
.cl-logo {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 18px;
    position: relative; z-index: 2;
    animation: clUp .55s .04s cubic-bezier(.16,1,.3,1) both;
}

.cl-badge {
    width: 52px; height: 52px;
    border-radius: 12px;
    background: linear-gradient(145deg, #3b0f8c 0%, #6d28d9 50%, #2563eb 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 0 0 1px rgba(109,40,217,.35), 0 0 18px rgba(109,40,217,.4), 0 4px 12px rgba(0,0,0,.5);
    gap: 0;
}
.cl-badge-code {
    font-family: 'Space Mono', monospace;
    font-size: 6px; font-weight: 700;
    color: rgba(255,255,255,.5);
    line-height: 1.3;
}
.cl-badge-hl {
    font-family: 'Space Mono', monospace;
    font-size: 16px; font-weight: 700;
    color: #fff; line-height: 1;
}

.cl-brand-name {
    font-family: 'Space Mono', monospace;
    font-size: 18px; font-weight: 700;
    color: #fff; line-height: 1.2;
    letter-spacing: -.01em;
}
.cl-brand-sub {
    font-family: 'Space Mono', monospace;
    font-size: 9.5px; font-weight: 700;
    color: #22d3ee; letter-spacing: .14em;
    margin-top: 3px;
}
.cl-cursor-blink::after {
    content: '_';
    animation: blinkAnim 1.1s step-end infinite;
}
@keyframes blinkAnim { 0%,100%{opacity:1} 50%{opacity:0} }

/* ── Eyebrow pill ── */
.cl-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(34,211,238,.06);
    border: 1px solid rgba(34,211,238,.2);
    border-radius: 100px; padding: 5px 14px;
    font-size: 9px; font-weight: 700;
    color: rgba(34,211,238,.65);
    letter-spacing: .14em;
    margin-bottom: 18px;
    position: relative; z-index: 2;
    width: fit-content;
    animation: clUp .55s .09s cubic-bezier(.16,1,.3,1) both;
}
.cl-eyebrow svg {
    width: 10px; height: 10px;
    stroke: #22d3ee; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
}

/* ── Code + icons section ── */
.cl-code-section {
    position: relative;
    flex: 1;
    min-height: 200px;
    margin-bottom: 18px;
    z-index: 1;
    animation: clUp .55s .14s cubic-bezier(.16,1,.3,1) both;
}

.cl-functions { position: relative; z-index: 3; }

.cl-fn {
    font-family: 'Space Mono', monospace;
    font-size: clamp(24px, 3vw, 42px);
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -.01em;
}
.cl-fn.c-cyan  { color: #22d3ee; text-shadow: 0 0 24px rgba(34,211,238,.4); }
.cl-fn.c-purple{ color: #a855f7; text-shadow: 0 0 24px rgba(168,85,247,.4); }
.cl-fn.c-pink  { color: #ec4899; text-shadow: 0 0 24px rgba(236,72,153,.4); }
.cl-fn.c-mixed {
    background: linear-gradient(90deg, #22d3ee 20%, #a855f7 80%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}

/* ── Floating icons ── */
.cl-icons {
    position: absolute;
    top: 0; right: -8px;
    width: 230px; height: 100%;
    pointer-events: none;
}

.cl-ic {
    position: absolute;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid rgba(255,255,255,.1);
    backdrop-filter: blur(4px);
}
.cl-ic svg { fill: none; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; }

.cl-ic-1 { /* grad cap — top */
    top: 2%; left: 50%; transform: translateX(-40%);
    width: 64px; height: 64px;
    background: linear-gradient(135deg, rgba(34,211,238,.14), rgba(59,130,246,.2));
    box-shadow: 0 0 20px rgba(34,211,238,.18);
    animation: flt1 4s ease-in-out infinite;
}
.cl-ic-1 svg { width: 23px; height: 23px; stroke: #22d3ee; }

.cl-ic-2 { /* book — left mid */
    top: 42%; left: 2%;
    width: 46px; height: 46px;
    background: linear-gradient(135deg, rgba(168,85,247,.14), rgba(139,92,246,.2));
    box-shadow: 0 0 18px rgba(168,85,247,.18);
    animation: flt2 5s ease-in-out infinite;
}
.cl-ic-2 svg { width: 19px; height: 19px; stroke: #a855f7; }

.cl-ic-3 { /* cart — center (largest) */
    top: 22%; left: 50%; transform: translateX(-50%);
    width: 84px; height: 84px;
    background: linear-gradient(135deg, rgba(34,211,238,.16), rgba(59,130,246,.22));
    border-color: rgba(34,211,238,.2);
    box-shadow: 0 0 28px rgba(34,211,238,.25), inset 0 1px 0 rgba(255,255,255,.08);
    animation: flt1 3.6s ease-in-out infinite;
}
.cl-ic-3 svg { width: 28px; height: 28px; stroke: #22d3ee; }

.cl-ic-4 { /* box — right */
    top: 42%; right: 0%;
    width: 46px; height: 46px;
    background: linear-gradient(135deg, rgba(236,72,153,.14), rgba(168,85,247,.18));
    box-shadow: 0 0 18px rgba(236,72,153,.18);
    animation: flt3 4.8s ease-in-out infinite;
}
.cl-ic-4 svg { width: 19px; height: 19px; stroke: #ec4899; }

.cl-ic-5 { /* card — bottom */
    bottom: 2%; left: 32%;
    width: 46px; height: 46px;
    background: linear-gradient(135deg, rgba(52,211,153,.14), rgba(16,185,129,.18));
    box-shadow: 0 0 18px rgba(52,211,153,.18);
    animation: flt2 5.5s ease-in-out infinite;
}
.cl-ic-5 svg { width: 19px; height: 19px; stroke: #34d399; }

/* connector dots */
.cl-dot-c {
    position: absolute; border-radius: 50%;
    background: rgba(34,211,238,.55);
    box-shadow: 0 0 6px rgba(34,211,238,.65);
}
.cl-dc-1 { width: 5px; height: 5px; top: 27%; left: 51%; }
.cl-dc-2 { width: 4px; height: 4px; top: 46%; left: 34%; }
.cl-dc-3 { width: 4px; height: 4px; top: 67%; left: 63%; }
.cl-dc-4 { width: 3px; height: 3px; top: 14%; left: 68%; }
.cl-dc-5 { width: 3px; height: 3px; top: 54%; left: 20%; background: rgba(168,85,247,.6); box-shadow: 0 0 6px rgba(168,85,247,.6); }

@keyframes flt1 { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(-9px)} }
@keyframes flt2 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-11px)} }
@keyframes flt3 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(9px)} }

/* ── Description ── */
.cl-desc {
    font-family: 'Space Mono', monospace;
    font-size: 10.5px; line-height: 1.8;
    color: rgba(255,255,255,.28);
    max-width: 360px;
    margin-bottom: 18px;
    position: relative; z-index: 2;
    font-weight: 400;
    animation: clUp .55s .2s cubic-bezier(.16,1,.3,1) both;
}

/* ── Stats row ── */
.cl-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    position: relative; z-index: 2;
    animation: clUp .55s .26s cubic-bezier(.16,1,.3,1) both;
}

.cl-stat {
    background: rgba(255,255,255,.035);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 11px;
    padding: 12px 10px 8px;
    position: relative; overflow: hidden;
    transition: border-color .3s, background .3s;
    cursor: default;
}
.cl-stat:hover { border-color: rgba(34,211,238,.2); background: rgba(34,211,238,.03); }

.cl-stat-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    margin-bottom: 7px;
}
.cl-stat-icon {
    width: 24px; height: 24px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.cl-stat-icon svg {
    width: 12px; height: 12px; fill: none;
    stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.cl-stat-arr { font-size: 11px; color: rgba(255,255,255,.18); line-height: 1; }

.cl-stat-val {
    font-family: 'Space Mono', monospace;
    font-size: 20px; font-weight: 700;
    color: #fff; line-height: 1;
    letter-spacing: -.02em; margin-bottom: 3px;
}
.cl-stat-lbl {
    font-family: 'Space Mono', monospace;
    font-size: 7px; font-weight: 700;
    letter-spacing: .12em;
    color: rgba(255,255,255,.25);
    margin-bottom: 8px;
}
.cl-stat-pct {
    font-family: 'Space Mono', monospace;
    font-size: 8px; font-weight: 700;
    color: #34d399; letter-spacing: .04em;
    margin-bottom: 3px;
}
.cl-spark { width: 100%; height: 24px; overflow: visible; display: block; }

/* ═══════════════ RIGHT PANEL ═══════════════ */
.cl-right {
    position: relative;
    background: #070b14;
    display: flex; flex-direction: column;
    border-left: 1px solid rgba(34,211,238,.07);
}

/* ── Form area ── */
.cl-form-area {
    flex: 1; display: flex;
    align-items: center; justify-content: center;
    padding: 44px 52px;
}

.cl-form-wrap {
    width: 100%; max-width: 400px;
    animation: clUp .55s .1s cubic-bezier(.16,1,.3,1) both;
}

/* ── Access badge ── */
.cl-access-badge {
    display: inline-flex; align-items: center; gap: 8px;
    border: 1px solid rgba(34,211,238,.3);
    border-radius: 7px; padding: 8px 16px;
    font-family: 'Space Mono', monospace;
    font-size: 10px; font-weight: 700;
    color: #22d3ee; letter-spacing: .14em;
    margin-bottom: 26px;
    background: rgba(34,211,238,.05);
}
.cl-access-badge svg {
    width: 11px; height: 11px;
    stroke: #22d3ee; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* ── Title ── */
.cl-title {
    font-family: 'Space Mono', monospace;
    font-size: clamp(20px, 2.2vw, 28px); font-weight: 700;
    color: #fff; line-height: 1.2;
    letter-spacing: -.015em;
    margin: 0 0 8px;
}
.cl-title-cursor::after {
    content: '_';
    color: #22d3ee;
    animation: blinkAnim 1.1s step-end infinite;
}
.cl-subtitle {
    font-family: 'Space Mono', monospace;
    font-size: 12px; color: rgba(255,255,255,.28);
    margin: 0 0 30px; font-weight: 400;
}

/* ═══ FILAMENT FORM OVERRIDES ═══ */
#cl-root label,
#cl-root label *,
#cl-root .fi-fo-field-wrp-label,
#cl-root .fi-fo-field-wrp-label * {
    font-family: 'Space Mono', monospace !important;
    font-size: 10.5px !important; font-weight: 700 !important;
    color: rgba(255,255,255,.65) !important;
    letter-spacing: .06em !important;
    text-transform: none !important;
    background: transparent !important;
    border: none !important; box-shadow: none !important;
}

#cl-root label sup,
#cl-root [class*="required-mark"],
#cl-root [class*="required-indicator"],
#cl-root label [class*="danger"] {
    color: #22d3ee !important;
}

#cl-root .fi-input-wrp {
    background: rgba(255,255,255,.03) !important;
    border: 1px solid rgba(34,211,238,.18) !important;
    border-radius: 8px !important; box-shadow: none !important;
    transition: border-color .2s, box-shadow .2s, background .2s !important;
}

/* Email icon prefix via :has() */
#cl-root .fi-input-wrp:has(input[type=email]) {
    position: relative !important;
}
#cl-root .fi-input-wrp:has(input[type=email])::before {
    content: '' !important;
    position: absolute !important; left: 13px !important; top: 50% !important;
    transform: translateY(-50%) !important;
    width: 14px !important; height: 14px !important; z-index: 2 !important;
    pointer-events: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2322d3ee' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z'/%3E%3Cpolyline points='22,6 12,13 2,6'/%3E%3C/svg%3E") !important;
    background-size: contain !important; background-repeat: no-repeat !important;
    opacity: .55 !important;
}
#cl-root .fi-input-wrp:has(input[type=email]) input {
    padding-left: 36px !important;
}

/* Password icon prefix */
#cl-root .fi-input-wrp:has(input[type=password]) {
    position: relative !important;
}
#cl-root .fi-input-wrp:has(input[type=password])::before {
    content: '' !important;
    position: absolute !important; left: 13px !important; top: 50% !important;
    transform: translateY(-50%) !important;
    width: 14px !important; height: 14px !important; z-index: 2 !important;
    pointer-events: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2322d3ee' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='11' width='18' height='11' rx='2'/%3E%3Cpath d='M7 11V7a5 5 0 0110 0v4'/%3E%3C/svg%3E") !important;
    background-size: contain !important; background-repeat: no-repeat !important;
    opacity: .55 !important;
}
#cl-root .fi-input-wrp:has(input[type=password]) input {
    padding-left: 36px !important;
}

#cl-root .fi-input-wrp:focus-within {
    background: rgba(34,211,238,.03) !important;
    border-color: rgba(34,211,238,.55) !important;
    box-shadow: 0 0 0 3px rgba(34,211,238,.07), 0 0 14px rgba(34,211,238,.08) !important;
}

#cl-root .fi-input,
#cl-root .fi-input-wrp input {
    background: transparent !important; border: none !important; box-shadow: none !important;
    color: #fff !important;
    font-family: 'Space Mono', monospace !important;
    font-size: 12px !important; font-weight: 400 !important;
    caret-color: #22d3ee !important;
}

#cl-root .fi-input::placeholder,
#cl-root .fi-input-wrp input::placeholder {
    color: rgba(255,255,255,.18) !important;
    font-size: 11px !important;
    font-family: 'Space Mono', monospace !important;
}

#cl-root input:-webkit-autofill,
#cl-root input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 100px #070b14 inset !important;
    -webkit-text-fill-color: #fff !important;
    caret-color: #22d3ee !important;
}

#cl-root .fi-input-wrp button {
    background: transparent !important; border: none !important; box-shadow: none !important;
    color: rgba(255,255,255,.25) !important; transition: color .2s !important;
}
#cl-root .fi-input-wrp button:hover { color: #22d3ee !important; background: transparent !important; }

#cl-root .fi-checkbox-input {
    background: rgba(255,255,255,.05) !important;
    border-color: rgba(34,211,238,.3) !important;
    border-radius: 4px !important; box-shadow: none !important;
}
#cl-root .fi-checkbox-input:checked {
    background: #22d3ee !important; border-color: #22d3ee !important;
}
#cl-root .fi-fo-checkbox label,
#cl-root .fi-fo-checkbox label *,
#cl-root .fi-checkbox label,
#cl-root .fi-checkbox label * {
    color: rgba(255,255,255,.4) !important;
    font-size: 11px !important;
    font-family: 'Space Mono', monospace !important;
}

#cl-root a, #cl-root .fi-link {
    color: #22d3ee !important; text-decoration: none !important; transition: opacity .2s !important;
}
#cl-root a:hover, #cl-root .fi-link:hover { opacity: .7 !important; }

/* ── Submit button ── */
#cl-root .fi-btn {
    width: 100% !important; padding: 16px 20px !important;
    display: inline-flex !important; align-items: center !important;
    cursor: pointer !important;
    background: linear-gradient(90deg, #0d9488 0%, #7c3aed 100%) !important;
    color: #fff !important; border: none !important; border-radius: 8px !important;
    font-family: 'Space Mono', monospace !important;
    font-weight: 700 !important; font-size: 13px !important;
    letter-spacing: .1em !important;
    box-shadow: 0 0 28px rgba(13,148,136,.22), 0 0 44px rgba(124,58,237,.14) !important;
    transition: filter .2s, transform .15s, box-shadow .2s !important;
    position: relative !important; overflow: hidden !important;
    justify-content: space-between !important;
}
#cl-root .fi-btn::after {
    content: '' !important;
    position: absolute !important; inset: 0 !important;
    background: linear-gradient(180deg, rgba(255,255,255,.1) 0%, transparent 60%) !important;
    pointer-events: none !important;
}
#cl-root .fi-btn:hover {
    filter: brightness(1.1) !important; transform: translateY(-1px) !important;
    box-shadow: 0 0 36px rgba(13,148,136,.32), 0 0 52px rgba(124,58,237,.22) !important;
    background: linear-gradient(90deg, #0d9488 0%, #7c3aed 100%) !important;
    color: #fff !important;
}
#cl-root .fi-btn:active { transform: translateY(0) scale(.99) !important; }
#cl-root .fi-btn * { color: #fff !important; fill: #fff !important; }
#cl-root .fi-btn .cl-btn-prefix { color: rgba(255,255,255,.65) !important; font-size: 11px !important; }
#cl-root .fi-btn .cl-btn-arrow  { font-size: 16px !important; }

#cl-root .cl-submit-wrap { margin-top: 24px; }

#cl-root .fi-fo-field-wrp-error-message,
#cl-root p[data-error] {
    color: #f87171 !important;
    font-family: 'Space Mono', monospace !important;
    font-size: 10px !important; background: transparent !important;
}
#cl-root .fi-form-component-ctn { margin-bottom: 15px !important; }
#cl-root .fi-form-component-ctn:last-child { margin-bottom: 0 !important; }

/* ── Divider ── */
.cl-divider {
    display: flex; align-items: center; gap: 10px;
    margin: 24px 0 16px;
}
.cl-div-line {
    flex: 1; height: 1px;
    background: repeating-linear-gradient(
        90deg,
        rgba(255,255,255,.07) 0, rgba(255,255,255,.07) 3px,
        transparent 3px, transparent 7px
    );
}
.cl-div-text {
    font-family: 'Space Mono', monospace;
    font-size: 7.5px; font-weight: 700;
    color: rgba(255,255,255,.15); letter-spacing: .2em; white-space: nowrap;
}

/* ── Security row ── */
.cl-security {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    margin-bottom: 10px;
}
.cl-sec-item {
    display: flex; align-items: center; gap: 5px;
    font-family: 'Space Mono', monospace;
    font-size: 9px; font-weight: 700;
    color: rgba(255,255,255,.2); letter-spacing: .07em;
}
.cl-sec-item svg {
    width: 11px; height: 11px;
    stroke: rgba(34,211,238,.4); fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.cl-sec-sep { width: 1px; height: 13px; background: rgba(255,255,255,.1); }

/* ── Version ── */
.cl-version {
    text-align: center;
    font-family: 'Space Mono', monospace;
    font-size: 8.5px; font-weight: 700;
    color: rgba(34,211,238,.2); letter-spacing: .1em;
}

/* ── Entry animation ── */
@keyframes clUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .cl-center { padding: 0; }
    #cl-root .cl-card { grid-template-columns: 1fr; border-radius: 0; min-height: 100dvh; }
    .cl-left { display: none; }
    .cl-form-area { padding: 40px 28px; align-items: flex-start; padding-top: 56px; }
    html, body { overflow: auto !important; }
}
</style>

<div id="cl-root">
<div class="cl-center">
<div class="cl-card">

    {{-- ═══ LEFT PANEL ═══ --}}
    <div class="cl-left">

        {{-- Line numbers --}}
        <div class="cl-lines">
            @for ($i = 1; $i <= 38; $i++)
                <div class="cl-ln">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</div>
            @endfor
        </div>

        {{-- Main content --}}
        <div class="cl-main">
            <div class="cl-glow-1"></div>
            <div class="cl-glow-2"></div>

            {{-- Background code --}}
            <div class="cl-bg-code">import { HybridLearning } from 'platform';

const platform = {
    learn: true,
    sell: true,
    scale: true,
    ecommerce: true
};

platform.connect();
// Inspire. Educate. Earn.</div>

            {{-- Logo --}}
            <div class="cl-logo">
                <div class="cl-badge">
                    <span class="cl-badge-code">&lt;/&gt;</span>
                    <span class="cl-badge-hl">HL</span>
                    <span class="cl-badge-code">&lt;/&gt;</span>
                </div>
                <div>
                    <div class="cl-brand-name">{{ $platformName }}</div>
                    <div class="cl-brand-sub cl-cursor-blink">ADMIN CONSOLE</div>
                </div>
            </div>

            {{-- Eyebrow --}}
            <div class="cl-eyebrow">
                <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                LEARNING &bull; E-COMMERCE PLATFORM
            </div>

            {{-- Functions + floating icons --}}
            <div class="cl-code-section">
                <div class="cl-functions">
                    <div class="cl-fn c-cyan">learn();</div>
                    <div class="cl-fn c-purple">sell();</div>
                    <div class="cl-fn c-pink">scale();</div>
                    <div class="cl-fn c-mixed">e_commerce();</div>
                </div>

                <div class="cl-icons">
                    {{-- Connector dots --}}
                    <div class="cl-dot-c cl-dc-1"></div>
                    <div class="cl-dot-c cl-dc-2"></div>
                    <div class="cl-dot-c cl-dc-3"></div>
                    <div class="cl-dot-c cl-dc-4"></div>
                    <div class="cl-dot-c cl-dc-5"></div>

                    {{-- Graduation cap --}}
                    <div class="cl-ic cl-ic-1">
                        <svg viewBox="0 0 24 24" width="23" height="23" stroke="#22d3ee"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>

                    {{-- Book --}}
                    <div class="cl-ic cl-ic-2">
                        <svg viewBox="0 0 24 24" width="19" height="19" stroke="#a855f7"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                    </div>

                    {{-- Cart (center, largest) --}}
                    <div class="cl-ic cl-ic-3">
                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="#22d3ee"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    </div>

                    {{-- Box --}}
                    <div class="cl-ic cl-ic-4">
                        <svg viewBox="0 0 24 24" width="19" height="19" stroke="#ec4899"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>

                    {{-- Credit card --}}
                    <div class="cl-ic cl-ic-5">
                        <svg viewBox="0 0 24 24" width="19" height="19" stroke="#34d399"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="cl-desc">Your unified admin hub for managing courses, instructors, marketplace &amp; e-commerce operations.</div>

            {{-- Stats --}}
            <div class="cl-stats">
                @foreach($stats as $s)
                <div class="cl-stat">
                    <div class="cl-stat-top">
                        <div class="cl-stat-icon" style="background:rgba({{ $s['rgb'] }},.12);">
                            <svg viewBox="0 0 24 24" style="stroke:{{ $s['color'] }};"><path d="{{ $s['icon'] }}"/></svg>
                        </div>
                        <span class="cl-stat-arr">›</span>
                    </div>
                    <div class="cl-stat-val">{{ $s['value'] }}</div>
                    <div class="cl-stat-lbl">{{ $s['label'] }}</div>
                    <div class="cl-stat-pct">↑ {{ $s['pct'] }}%</div>
                    <svg class="cl-spark" viewBox="0 0 70 22" preserveAspectRatio="none">
                        <polyline points="0,20 10,17 20,15 30,12 40,9 50,6 60,4 70,1"
                            fill="none" stroke="{{ $s['color'] }}" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" opacity=".55"/>
                    </svg>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ RIGHT PANEL ═══ --}}
    <div class="cl-right">
        <div class="cl-form-area">
            <div class="cl-form-wrap">

                {{-- Access badge --}}
                <div class="cl-access-badge">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    ADMIN ACCESS
                </div>

                {{-- Heading --}}
                <h2 class="cl-title">Welcome back, <span class="cl-title-cursor">Admin</span></h2>
                <p class="cl-subtitle">Sign in to your admin account</p>

                {{-- Filament form --}}
                <form wire:submit="authenticate">
                    {{ $this->form }}

                    <div class="cl-submit-wrap">
                        <button type="submit" class="fi-btn">
                            <span class="cl-btn-prefix">&gt;_</span>
                            <span wire:loading.remove wire:target="authenticate">SIGN IN</span>
                            <span wire:loading wire:target="authenticate">SIGNING IN…</span>
                            <span class="cl-btn-arrow">→</span>
                        </button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="cl-divider">
                    <div class="cl-div-line"></div>
                    <span class="cl-div-text">SECURED CONNECTION</span>
                    <div class="cl-div-line"></div>
                </div>

                {{-- Security --}}
                <div class="cl-security">
                    <div class="cl-sec-item">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        256-bit SSL
                    </div>
                    <div class="cl-sec-sep"></div>
                    <div class="cl-sec-item">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Authorized Only
                    </div>
                </div>

                {{-- Version --}}
                <div class="cl-version">&lt;/&gt; {{ $platformName }} Admin Console v2.0.0</div>

            </div>
        </div>

    </div>

</div>{{-- .cl-card --}}
</div>{{-- .cl-center --}}
</div>{{-- #cl-root --}}
</x-filament-panels::page.simple>
