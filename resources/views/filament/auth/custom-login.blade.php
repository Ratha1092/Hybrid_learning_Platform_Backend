<x-filament-panels::page.simple>
@php
    $platformName    = filament()->getBrandName() ?: 'Hybrid Learning';
    $appVersion      = config('app.version', 'v1.0.0');
    $studentCount    = \App\Domains\Users\Models\User::role('student')->count();
    $instructorCount = \App\Domains\Users\Models\User::role('instructor')->count();
    $courseCount     = \App\Domains\Courses\Models\Course::count();
    $orderCount      = \App\Domains\Orders\Models\Order::count();

    $stats = [
        ['icon' => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
         'value' => number_format($studentCount),    'label' => 'Students',    'color' => '#60A5FA', 'rgb' => '96,165,250'],
        ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
         'value' => number_format($courseCount),     'label' => 'Courses',     'color' => '#F59E0B', 'rgb' => '245,158,11'],
        ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
         'value' => number_format($instructorCount), 'label' => 'Instructors', 'color' => '#34D399', 'rgb' => '52,211,153'],
        ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
         'value' => number_format($orderCount),      'label' => 'Orders',      'color' => '#A78BFA', 'rgb' => '167,139,250'],
    ];
@endphp

<x-slot name="heading"></x-slot>
<x-slot name="subheading"></x-slot>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

<style>
/* ── Reset ── */
html, body {
    margin:0 !important;
    padding:0 !important;
    background:#EEF1F6 !important;
    overflow:hidden !important;
    height:100% !important;
}
.fi-simple-layout {
    min-height:unset !important;
    background:transparent !important;
    padding:0 !important;
    display:block !important;
}
.fi-simple-main {
    max-width:none !important;
    width:100% !important;
    padding:0 !important;
    background:transparent !important;
    box-shadow:none !important;
    border-radius:0 !important;
    outline:none !important;
    border:none !important;
}

/* ── Root ── */
#cl-root *, #cl-root *::before, #cl-root *::after {
    box-sizing:border-box;
}
#cl-root {
    position:fixed;
    inset:0;
    z-index:9999;
    overflow-y:auto;
    background:#EEF1F6;
    font-family:'DM Sans', system-ui, sans-serif;
}
.cl-center {
    min-height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

/* ── Card wrapper ── */
#cl-root .cl-card {
    position:relative;
    z-index:1;
    width:min(1200px, calc(100% - 40px));
    display:grid;
    grid-template-columns:1.25fr 1fr;
    border-radius:28px;
    overflow:hidden;
    border:1px solid rgba(37,99,235,.18);
    box-shadow:0 0 0 1px rgba(37,99,235,.07), 0 32px 80px rgba(15,23,42,.2), 0 8px 28px rgba(15,23,42,.12);
    animation:cardIn .6s cubic-bezier(.16,1,.3,1) both;
}
@keyframes cardIn {
    from {
        opacity:0;
        transform:translateY(20px) scale(.97);
    }
    to {
        opacity:1;
        transform:translateY(0) scale(1);
    }
}

/* ═══════════════ LEFT PANEL (always dark navy) ═══════════════ */
.cl-left {
    position:relative;
    background:linear-gradient(160deg, #0d1424 0%, #080e1a 100%);
    border-right:1px solid rgba(37,99,235,.12);
    display:flex;
    flex-direction:column;
    padding:38px 38px 34px;
    overflow:hidden;
}

/* ── Ambient glows ── */
.cl-glow-1 {
    position:absolute;
    top:-80px;
    left:-80px;
    width:420px;
    height:420px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(37,99,235,.07) 0%, transparent 65%);
    pointer-events:none;
    animation:glowPulse 7s ease-in-out infinite;
}
.cl-glow-2 {
    position:absolute;
    bottom:-60px;
    right:0;
    width:340px;
    height:340px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(96,165,250,.05) 0%, transparent 65%);
    pointer-events:none;
    animation:glowPulse 9s ease-in-out infinite reverse;
}
.cl-glow-3 {
    position:absolute;
    top:45%;
    left:25%;
    width:240px;
    height:240px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(167,139,250,.025) 0%, transparent 70%);
    pointer-events:none;
}
@keyframes glowPulse {
    0%,100% {
        opacity:.6;
    }
    50% {
        opacity:1;
    }
}

/* ── Brand lockup ── */
.cl-brand {
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:28px;
    position:relative;
    z-index:2;
    animation:clUp .5s .04s cubic-bezier(.16,1,.3,1) both;
}
.cl-brand-icon {
    width:42px;
    height:42px;
    border-radius:11px;
    background:linear-gradient(145deg, rgba(37,99,235,.22), rgba(37,99,235,.06));
    border:1px solid rgba(37,99,235,.38);
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    box-shadow:0 0 20px rgba(37,99,235,.12), inset 0 1px 0 rgba(255,255,255,.06);
}
.cl-brand-icon svg {
    width:20px;
    height:20px;
    fill:none;
    stroke:#60A5FA;
    stroke-width:1.7;
    stroke-linecap:round;
    stroke-linejoin:round;
}
.cl-brand-text {
    display:flex;
    flex-direction:column;
    min-width:0;
}
.cl-brand-name {
    font-family:'Plus Jakarta Sans', sans-serif;
    font-size:17px;
    font-weight:700;
    color:#f1f5f9;
    line-height:1.2;
    letter-spacing:-.015em;
}
.cl-brand-sub {
    display:flex;
    align-items:center;
    gap:5px;
    font-family:'DM Sans', sans-serif;
    font-size:11px;
    font-weight:500;
    color:#7a8fa3;
    margin-top:2px;
    letter-spacing:.03em;
}
.cl-brand-dot {
    width:5px;
    height:5px;
    border-radius:50%;
    background:#36c887;
    flex-shrink:0;
    animation:dotPulse 2.4s cubic-bezier(.2,.7,.2,1) infinite;
}
@keyframes dotPulse {
    0% {
        box-shadow:0 0 0 0 rgba(54,200,135,.5);
    }
    70% {
        box-shadow:0 0 0 5px rgba(54,200,135,0);
    }
    100% {
        box-shadow:0 0 0 0 rgba(54,200,135,0);
    }
}

/* ── Platform pill ── */
.cl-platform-pill {
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:rgba(37,99,235,.07);
    border:1px solid rgba(37,99,235,.2);
    border-radius:100px;
    padding:5px 13px;
    font-family:'DM Sans', sans-serif;
    font-size:10px;
    font-weight:600;
    color:rgba(96,165,250,.8);
    letter-spacing:.08em;
    text-transform:uppercase;
    width:fit-content;
    margin-bottom:24px;
    position:relative;
    z-index:2;
    animation:clUp .5s .08s cubic-bezier(.16,1,.3,1) both;
}
.cl-platform-pill svg {
    width:10px;
    height:10px;
    stroke:#60A5FA;
    fill:none;
    stroke-width:2;
    stroke-linecap:round;
    stroke-linejoin:round;
}

/* ── Headline ── */
.cl-headline {
    position:relative;
    z-index:2;
    font-family:'Plus Jakarta Sans', sans-serif;
    line-height:1.1;
    letter-spacing:-.022em;
    margin-bottom:20px;
    animation:clUp .5s .12s cubic-bezier(.16,1,.3,1) both;
}
.cl-hl-1 {
    display:block;
    font-size:clamp(32px, 3.4vw, 52px);
    font-weight:800;
    color:#f1f5f9;
}
.cl-hl-2 {
    display:block;
    font-size:clamp(30px, 3.2vw, 48px);
    font-weight:800;
    background:linear-gradient(90deg, #F59E0B 0%, #D97706 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
}
.cl-hl-3 {
    display:block;
    font-size:clamp(30px, 3.2vw, 48px);
    font-weight:800;
    background:linear-gradient(90deg, #60A5FA 15%, #818CF8 80%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
}

/* ── Description ── */
.cl-desc {
    font-family:'DM Sans', sans-serif;
    font-size:13.5px;
    line-height:1.7;
    color:#7a8fa3;
    max-width:400px;
    margin-bottom:22px;
    position:relative;
    z-index:2;
    animation:clUp .5s .16s cubic-bezier(.16,1,.3,1) both;
}

/* ── Feature chips ── */
.cl-chips {
    display:flex;
    flex-wrap:wrap;
    gap:7px;
    margin-bottom:28px;
    position:relative;
    z-index:2;
    animation:clUp .5s .19s cubic-bezier(.16,1,.3,1) both;
}
.cl-chip {
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.1);
    border-radius:8px;
    padding:7px 12px;
    font-family:'DM Sans', sans-serif;
    font-size:11.5px;
    font-weight:500;
    color:rgba(241,245,249,.68);
    transition:border-color .2s, background .2s;
}
.cl-chip:hover {
    border-color:rgba(37,99,235,.28);
    background:rgba(37,99,235,.06);
}
.cl-chip svg {
    width:13px;
    height:13px;
    fill:none;
    stroke-width:1.8;
    stroke-linecap:round;
    stroke-linejoin:round;
    flex-shrink:0;
}

/* ── Stats grid ── */
.cl-stats {
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:8px;
    position:relative;
    z-index:2;
    animation:clUp .5s .22s cubic-bezier(.16,1,.3,1) both;
}
.cl-stat {
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px;
    padding:13px 10px 10px;
    transition:border-color .25s, background .25s;
}
.cl-stat:hover {
    border-color:rgba(37,99,235,.25);
    background:rgba(37,99,235,.04);
}
.cl-stat-icon {
    width:27px;
    height:27px;
    border-radius:7px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:10px;
}
.cl-stat-icon svg {
    width:12px;
    height:12px;
    fill:none;
    stroke-width:1.8;
    stroke-linecap:round;
    stroke-linejoin:round;
}
.cl-stat-val {
    font-family:'Plus Jakarta Sans', sans-serif;
    font-size:21px;
    font-weight:700;
    color:#f1f5f9;
    line-height:1;
    margin-bottom:3px;
    letter-spacing:-.02em;
}
.cl-stat-lbl {
    font-family:'DM Sans', sans-serif;
    font-size:9px;
    font-weight:600;
    letter-spacing:.07em;
    text-transform:uppercase;
    color:rgba(148,163,184,.65);
    margin-bottom:7px;
}
.cl-spark {
    width:100%;
    height:20px;
    display:block;
    overflow:visible;
}

/* ═══════════════ RIGHT PANEL — white by default ═══════════════ */
.cl-right {
    position:relative;
    background:#ffffff;
    border-left:1px solid rgba(37,99,235,.16);
    display:flex;
    flex-direction:column;
}

.cl-right::before {
    content:'';
    position:absolute;
    inset:0;
    background-image:radial-gradient(circle, rgba(37,99,235,.1) 1px, transparent 1px);
    background-size:28px 28px;
    pointer-events:none;
    z-index:0;
    mask-image:radial-gradient(ellipse at 70% 40%, black 20%, transparent 68%);
}

/* ── Form area ── */
.cl-form-area {
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:48px 52px;
    position:relative;
    z-index:1;
}
.cl-form-wrap {
    width:100%;
    max-width:380px;
    animation:clUp .5s .1s cubic-bezier(.16,1,.3,1) both;
}

/* ── Admin badge ── */
.cl-admin-badge {
    display:inline-flex;
    align-items:center;
    gap:7px;
    border:1px solid rgba(37,99,235,.35);
    border-radius:8px;
    padding:7px 14px;
    font-family:'DM Sans', sans-serif;
    font-size:10.5px;
    font-weight:700;
    color:#1d4ed8;
    letter-spacing:.1em;
    text-transform:uppercase;
    margin-bottom:28px;
    background:rgba(37,99,235,.08);
}
.cl-admin-badge svg {
    width:11px;
    height:11px;
    stroke:#2563eb;
    fill:none;
    stroke-width:2;
    stroke-linecap:round;
    stroke-linejoin:round;
}

/* ── Title ── */
.cl-title {
    font-family:'Plus Jakarta Sans', sans-serif;
    font-size:clamp(22px, 2.6vw, 30px);
    font-weight:800;
    color:#0f172a;
    line-height:1.2;
    letter-spacing:-.022em;
    margin:0 0 6px;
}
.cl-title-accent {
    background:linear-gradient(90deg, #2563eb 0%, #7c3aed 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
}
.cl-subtitle {
    font-family:'DM Sans', sans-serif;
    font-size:14px;
    color:#64748b;
    margin:0 0 32px;
    font-weight:400;
}

/* ═══ FILAMENT FORM OVERRIDES ═══ */
#cl-root label, #cl-root label *, #cl-root .fi-fo-field-wrp-label, #cl-root .fi-fo-field-wrp-label * {
    font-family:'DM Sans', sans-serif !important;
    font-size:13px !important;
    font-weight:600 !important;
    color:rgba(15,23,42,.82) !important;
    letter-spacing:0 !important;
    background:transparent !important;
    border:none !important;
    box-shadow:none !important;
}

#cl-root label sup, #cl-root [class*="required-mark"], #cl-root [class*="required-indicator"], #cl-root label [class*="danger"] {
    color:#2563eb !important;
}

#cl-root .fi-input-wrp {
    background:#f8fafc !important;
    border:1px solid rgba(15,23,42,.22) !important;
    border-radius:10px !important;
    box-shadow:none !important;
    transition:border-color .18s, box-shadow .18s, background .18s !important;
}

/* Email icon prefix */
#cl-root .fi-input-wrp:has(input[type=email]) {
    position:relative !important;
}
#cl-root .fi-input-wrp:has(input[type=email])::before {
    content:'' !important;
    position:absolute !important;
    left:13px !important;
    top:50% !important;
    transform:translateY(-50%) !important;
    width:14px !important;
    height:14px !important;
    z-index:2 !important;
    pointer-events:none !important;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z'/%3E%3Cpolyline points='22,6 12,13 2,6'/%3E%3C/svg%3E") !important;
    background-size:contain !important;
    background-repeat:no-repeat !important;
    opacity:.6 !important;
}
#cl-root .fi-input-wrp:has(input[type=email]) input {
    padding-left:36px !important;
}

/* Password icon prefix */
#cl-root .fi-input-wrp:has(input[type=password]) {
    position:relative !important;
}
#cl-root .fi-input-wrp:has(input[type=password])::before {
    content:'' !important;
    position:absolute !important;
    left:13px !important;
    top:50% !important;
    transform:translateY(-50%) !important;
    width:14px !important;
    height:14px !important;
    z-index:2 !important;
    pointer-events:none !important;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='11' width='18' height='11' rx='2'/%3E%3Cpath d='M7 11V7a5 5 0 0110 0v4'/%3E%3C/svg%3E") !important;
    background-size:contain !important;
    background-repeat:no-repeat !important;
    opacity:.6 !important;
}
#cl-root .fi-input-wrp:has(input[type=password]) input {
    padding-left:36px !important;
}

#cl-root .fi-input-wrp:focus-within {
    background:rgba(37,99,235,.04) !important;
    border-color:rgba(37,99,235,.5) !important;
    box-shadow:0 0 0 3px rgba(37,99,235,.1), 0 0 14px rgba(37,99,235,.06) !important;
}

#cl-root .fi-input, #cl-root .fi-input-wrp input {
    background:transparent !important;
    border:none !important;
    box-shadow:none !important;
    color:#0f172a !important;
    font-family:'DM Sans', sans-serif !important;
    font-size:13.5px !important;
    font-weight:400 !important;
    caret-color:#2563eb !important;
}
#cl-root .fi-input::placeholder, #cl-root .fi-input-wrp input::placeholder {
    color:rgba(100,116,139,.65) !important;
    font-size:13px !important;
    font-family:'DM Sans', sans-serif !important;
}

#cl-root input:-webkit-autofill, #cl-root input:-webkit-autofill:focus {
    -webkit-box-shadow:0 0 0 100px #f8fafc inset !important;
    -webkit-text-fill-color:#0f172a !important;
    caret-color:#2563eb !important;
}

#cl-root .fi-input-wrp button {
    background:transparent !important;
    border:none !important;
    box-shadow:none !important;
    color:rgba(100,116,139,.6) !important;
    transition:color .18s !important;
}
#cl-root .fi-input-wrp button:hover {
    color:#2563eb !important;
    background:transparent !important;
}

#cl-root .fi-checkbox-input {
    background:#eef2ff !important;
    border:1.5px solid rgba(37,99,235,.55) !important;
    border-radius:4px !important;
    box-shadow:none !important;
    appearance:none !important;
    -webkit-appearance:none !important;
    width:15px !important;
    height:15px !important;
    flex-shrink:0 !important;
}
#cl-root .fi-checkbox-input:checked {
    background:#2563eb !important;
    border-color:#2563eb !important;
}
#cl-root .fi-fo-checkbox label, #cl-root .fi-fo-checkbox label *, #cl-root .fi-checkbox label, #cl-root .fi-checkbox label * {
    color:rgba(51,65,85,.8) !important;
    font-size:13px !important;
    font-family:'DM Sans', sans-serif !important;
}

#cl-root a, #cl-root .fi-link {
    color:#2563eb !important;
    text-decoration:none !important;
    transition:opacity .18s !important;
}
#cl-root a:hover, #cl-root .fi-link:hover {
    opacity:.75 !important;
}

/* ── Submit button ── */
#cl-root .fi-btn {
    width:100% !important;
    padding:14px 20px !important;
    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;
    cursor:pointer !important;
    background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    color:#fff !important;
    border:none !important;
    border-radius:10px !important;
    font-family:'Plus Jakarta Sans', sans-serif !important;
    font-weight:700 !important;
    font-size:14px !important;
    letter-spacing:-.01em !important;
    box-shadow:0 4px 16px rgba(37,99,235,.32), 0 2px 6px rgba(0,0,0,.1) !important;
    transition:filter .18s, transform .12s, box-shadow .18s !important;
    position:relative !important;
    overflow:hidden !important;
}
#cl-root .fi-btn::after {
    content:'' !important;
    position:absolute !important;
    inset:0 !important;
    background:linear-gradient(180deg, rgba(255,255,255,.14) 0%, transparent 55%) !important;
    pointer-events:none !important;
}
#cl-root .fi-btn:hover {
    filter:brightness(1.06) !important;
    transform:translateY(-1px) !important;
    box-shadow:0 6px 24px rgba(37,99,235,.42), 0 3px 10px rgba(0,0,0,.12) !important;
}
#cl-root .fi-btn:active {
    transform:translateY(0) scale(.99) !important;
}
#cl-root .fi-btn * {
    color:#fff !important;
    fill:#fff !important;
}

#cl-root .cl-submit-wrap {
    margin-top:22px;
}

#cl-root .fi-fo-field-wrp-error-message, #cl-root p[data-error] {
    color:#EF4444 !important;
    font-family:'DM Sans', sans-serif !important;
    font-size:12px !important;
    background:transparent !important;
}
#cl-root .fi-form-component-ctn {
    margin-bottom:16px !important;
}
#cl-root .fi-form-component-ctn:last-child {
    margin-bottom:0 !important;
}

/* ── Divider ── */
.cl-divider {
    display:flex;
    align-items:center;
    gap:10px;
    margin:22px 0 16px;
}
.cl-div-line {
    flex:1;
    height:1px;
    background:rgba(15,23,42,.18);
}
.cl-div-text {
    font-family:'DM Sans', sans-serif;
    font-size:10px;
    font-weight:600;
    color:#64748b;
    letter-spacing:.12em;
    white-space:nowrap;
    text-transform:uppercase;
}

/* ── Security row ── */
.cl-security {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:12px;
    margin-bottom:10px;
}
.cl-sec-item {
    display:flex;
    align-items:center;
    gap:5px;
    font-family:'DM Sans', sans-serif;
    font-size:10px;
    font-weight:500;
    color:#64748b;
    letter-spacing:.04em;
}
.cl-sec-item svg {
    width:11px;
    height:11px;
    stroke:rgba(37,99,235,.7);
    fill:none;
    stroke-width:2;
    stroke-linecap:round;
    stroke-linejoin:round;
}
.cl-sec-sep {
    width:1px;
    height:12px;
    background:rgba(15,23,42,.2);
}

/* ── Version ── */
.cl-version {
    text-align:center;
    font-family:'DM Sans', sans-serif;
    font-size:10px;
    font-weight:500;
    color:rgba(37,99,235,.7);
    letter-spacing:.06em;
}

/* ── Entry animation ── */
@keyframes clUp {
    from {
        opacity:0;
        transform:translateY(14px);
    }
    to {
        opacity:1;
        transform:translateY(0);
    }
}

/* ── Theme transition ── */
#cl-root, #cl-root .cl-right, #cl-root .cl-card, #cl-root .cl-stat, #cl-root .cl-chip, #cl-root .cl-title, #cl-root .cl-subtitle {
    transition:background .28s ease, color .28s ease, border-color .28s ease, box-shadow .28s ease;
}

/* ── Theme toggle button ── */
#cl-theme-toggle {
    position:absolute;
    top:18px;
    right:18px;
    z-index:20;
    width:36px;
    height:36px;
    border-radius:10px;
    background:rgba(37,99,235,.08);
    border:1px solid rgba(37,99,235,.32);
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    outline:none;
    color:#2563eb;
    transition:background .2s, border-color .2s, transform .15s, color .2s;
}
#cl-theme-toggle svg {
    width:16px;
    height:16px;
    flex-shrink:0;
}
#cl-theme-toggle:hover {
    background:rgba(37,99,235,.14);
    border-color:rgba(37,99,235,.38);
    color:#2563eb;
    transform:scale(1.06);
}
#cl-theme-toggle:active {
    transform:scale(.96);
}

/* ═══════════════ DARK MODE OVERRIDES ═══════════════ */
html.cl-dark, html.cl-dark body {
    background:#080b10 !important;
}

html.cl-dark #cl-root {
    background:#080b10;
}

html.cl-dark #cl-root .cl-card {
    border-color:rgba(37,99,235,.22);
    box-shadow:0 0 0 1px rgba(37,99,235,.1), 0 40px 100px rgba(0,0,0,.85), 0 0 60px rgba(37,99,235,.08);
}

/* Right panel — dark */
html.cl-dark #cl-root .cl-right {
    background:#080f18;
    border-left-color:rgba(37,99,235,.22);
}
html.cl-dark #cl-root .cl-right::before {
    background-image:linear-gradient(rgba(255,255,255,.028) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.028) 1px, transparent 1px);
    background-size:32px 32px;
    mask-image:radial-gradient(ellipse at 50% 50%, black 30%, transparent 75%);
}

/* Toggle — dark */
html.cl-dark #cl-root #cl-theme-toggle {
    background:rgba(37,99,235,.14);
    border-color:rgba(37,99,235,.4);
    color:#60A5FA;
}
html.cl-dark #cl-root #cl-theme-toggle:hover {
    background:rgba(37,99,235,.2);
    color:#60A5FA;
}

/* Admin badge — dark */
html.cl-dark #cl-root .cl-admin-badge {
    background:rgba(37,99,235,.15);
    border-color:rgba(37,99,235,.45);
    color:#93c5fd;
}
html.cl-dark #cl-root .cl-admin-badge svg {
    stroke:#93c5fd;
}

/* Title — dark */
html.cl-dark #cl-root .cl-title {
    color:#f1f5f9;
}
html.cl-dark #cl-root .cl-subtitle {
    color:#94a3b8;
}
html.cl-dark #cl-root .cl-title-accent {
    background:linear-gradient(90deg, #60A5FA 0%, #818CF8 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
}

/* Form labels — dark */
html.cl-dark #cl-root label, html.cl-dark #cl-root label *, html.cl-dark #cl-root .fi-fo-field-wrp-label, html.cl-dark #cl-root .fi-fo-field-wrp-label * {
    color:rgba(241,245,249,.82) !important;
}

/* Inputs — dark */
html.cl-dark #cl-root .fi-input-wrp {
    background:rgba(255,255,255,.07) !important;
    border-color:rgba(148,163,184,.28) !important;
}
html.cl-dark #cl-root .fi-input-wrp:focus-within {
    background:rgba(37,99,235,.05) !important;
    border-color:rgba(37,99,235,.55) !important;
    box-shadow:0 0 0 3px rgba(37,99,235,.1), 0 0 14px rgba(37,99,235,.07) !important;
}
html.cl-dark #cl-root .fi-input, html.cl-dark #cl-root .fi-input-wrp input {
    color:#f1f5f9 !important;
}
html.cl-dark #cl-root .fi-input::placeholder, html.cl-dark #cl-root .fi-input-wrp input::placeholder {
    color:rgba(148,163,184,.55) !important;
}
html.cl-dark #cl-root input:-webkit-autofill, html.cl-dark #cl-root input:-webkit-autofill:focus {
    -webkit-box-shadow:0 0 0 100px #080f18 inset !important;
    -webkit-text-fill-color:#f1f5f9 !important;
}
html.cl-dark #cl-root .fi-input-wrp button {
    color:rgba(148,163,184,.6) !important;
}
html.cl-dark #cl-root .fi-input-wrp button:hover {
    color:#60A5FA !important;
}

/* Checkbox — dark */
html.cl-dark #cl-root .fi-checkbox-input {
    background:rgba(37,99,235,.14) !important;
    border:1.5px solid rgba(96,165,250,.6) !important;
}
html.cl-dark #cl-root .fi-fo-checkbox label, html.cl-dark #cl-root .fi-fo-checkbox label *, html.cl-dark #cl-root .fi-checkbox label, html.cl-dark #cl-root .fi-checkbox label * {
    color:rgba(148,163,184,.78) !important;
}

/* Links — dark */
html.cl-dark #cl-root a, html.cl-dark #cl-root .fi-link {
    color:#60A5FA !important;
}

/* Button — dark (same gradient, enhanced shadow) */
html.cl-dark #cl-root .fi-btn {
    box-shadow:0 0 24px rgba(37,99,235,.28), 0 4px 14px rgba(0,0,0,.4) !important;
}
html.cl-dark #cl-root .fi-btn:hover {
    box-shadow:0 0 36px rgba(37,99,235,.4), 0 6px 20px rgba(0,0,0,.45) !important;
}

/* Divider + security + version — dark */
html.cl-dark #cl-root .cl-div-line {
    background:rgba(148,163,184,.2);
}
html.cl-dark #cl-root .cl-div-text {
    color:rgba(148,163,184,.75);
}
html.cl-dark #cl-root .cl-sec-item {
    color:rgba(148,163,184,.75);
}
html.cl-dark #cl-root .cl-sec-item svg {
    stroke:rgba(96,165,250,.7);
}
html.cl-dark #cl-root .cl-sec-sep {
    background:rgba(148,163,184,.25);
}
html.cl-dark #cl-root .cl-version {
    color:rgba(96,165,250,.65);
}

/* ── Responsive ── */
@media (max-width: 800px) {
    .cl-center {
        padding:0;
    }
    #cl-root .cl-card {
        grid-template-columns:1fr;
        border-radius:0;
        min-height:100dvh;
    }
    .cl-left {
        display:none;
    }
    .cl-form-area {
        padding:44px 28px;
        align-items:flex-start;
        padding-top:60px;
    }
    html, body {
        overflow:auto !important;
    }
    #cl-theme-toggle {
        top:14px;
        right:14px;
    }
}

@media (prefers-reduced-motion: reduce) {
    * {
        animation:none !important;
        transition:none !important;
    }
}
</style>

{{-- Flash-free theme init: reads cl-theme → Filament 'theme' → system, in priority order --}}
<script>
(function(){
    var cl = localStorage.getItem('cl-theme');
    var fi = localStorage.getItem('theme');
    var sysDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    var dark = cl === 'dark'
        || (!cl && (fi === 'dark' || (fi === 'system' && sysDark) || (!fi && sysDark)));
    if (dark) document.documentElement.classList.add('cl-dark');
    window.__clInitTheme = dark ? 'dark' : 'light';
})();
</script>

<div id="cl-root">
<div class="cl-center">
<div class="cl-card">

    {{-- ═══ LEFT PANEL (always dark navy) ═══ --}}
    <div class="cl-left">
        <div class="cl-glow-1"></div>
        <div class="cl-glow-2"></div>
        <div class="cl-glow-3"></div>

        <div class="cl-brand">
            <div class="cl-brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="cl-brand-text">
                <span class="cl-brand-name">{{ $platformName }}</span>
                <span class="cl-brand-sub">
                    <span class="cl-brand-dot"></span>
                    admin console · local
                </span>
            </div>
        </div>

        <div class="cl-platform-pill">
            <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
            Learning · E-Commerce Platform
        </div>

        <div class="cl-headline">
            <span class="cl-hl-1">Inspire.</span>
            <span class="cl-hl-2">Educate.</span>
            <span class="cl-hl-3">Earn.</span>
        </div>

        <div class="cl-desc">Your unified admin hub for managing courses, instructors, marketplace &amp; e-commerce operations.</div>

        <div class="cl-chips">
            <div class="cl-chip">
                <svg stroke="#2563eb" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Role-based access
            </div>
            <div class="cl-chip">
                <svg stroke="#60A5FA" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Live analytics
            </div>
            <div class="cl-chip">
                <svg stroke="#34D399" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payments &amp; payouts
            </div>
            <div class="cl-chip">
                <svg stroke="#A78BFA" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/></svg>
                Smart reporting
            </div>
        </div>

        <div class="cl-stats">
            @foreach($stats as $s)
            <div class="cl-stat">
                <div class="cl-stat-icon" style="background:rgba({{ $s['rgb'] }},.12);">
                    <svg viewBox="0 0 24 24" style="stroke:{{ $s['color'] }};"><path d="{{ $s['icon'] }}"/></svg>
                </div>
                <div class="cl-stat-val">{{ $s['value'] }}</div>
                <div class="cl-stat-lbl">{{ $s['label'] }}</div>
                <svg class="cl-spark" viewBox="0 0 70 20" preserveAspectRatio="none">
                    <polyline points="0,18 10,15 20,13 30,10 40,7 50,4 60,3 70,1"
                        fill="none" stroke="{{ $s['color'] }}" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" opacity=".45"/>
                </svg>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ RIGHT PANEL (white / light default) ═══ --}}
    <div class="cl-right">

        {{-- Theme toggle — shows sun in light mode (default), moon in dark --}}
        <button id="cl-theme-toggle" onclick="clToggleTheme()" title="Toggle light / dark mode" type="button">
            <svg id="cl-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            <svg id="cl-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>

        <div class="cl-form-area">
            <div class="cl-form-wrap">

                <div class="cl-admin-badge">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Admin Panel
                </div>

                <h2 class="cl-title">Welcome back, <span class="cl-title-accent">Admin</span></h2>
                <p class="cl-subtitle">Sign in to your admin console</p>

                <form wire:submit="authenticate">
                    {{ $this->form }}

                    <div class="cl-submit-wrap">
                        <button type="submit" class="fi-btn">
                            <span wire:loading.remove wire:target="authenticate">Sign In &nbsp;→</span>
                            <span wire:loading wire:target="authenticate">Signing in…</span>
                        </button>
                    </div>
                </form>

                <div class="cl-divider">
                    <div class="cl-div-line"></div>
                    <span class="cl-div-text">Secured Connection</span>
                    <div class="cl-div-line"></div>
                </div>

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

                <div class="cl-version">{{ $platformName }} Admin Console · {{ $appVersion }}</div>

            </div>
        </div>
    </div>

</div>{{-- .cl-card --}}
</div>{{-- .cl-center --}}
</div>{{-- #cl-root --}}

<script>
(function () {
    var mq = window.matchMedia('(prefers-color-scheme: dark)');

    function resolveTheme() {
        var cl = localStorage.getItem('cl-theme');
        if (cl) return cl;
        var fi = localStorage.getItem('theme');
        if (fi === 'dark' || fi === 'light') return fi;
        if (fi === 'system') return mq.matches ? 'dark' : 'light';
        return mq.matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        var root = document.getElementById('cl-root');
        if (root) root.setAttribute('data-theme', theme);

        if (theme === 'dark') {
            document.documentElement.classList.add('cl-dark');
        } else {
            document.documentElement.classList.remove('cl-dark');
        }

        var moon = document.getElementById('cl-icon-moon');
        var sun  = document.getElementById('cl-icon-sun');
        if (moon) moon.style.display = theme === 'dark' ? '' : 'none';
        if (sun)  sun.style.display  = theme === 'light' ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(resolveTheme());
    });

    // Follow OS theme changes when no explicit preference is stored
    mq.addEventListener('change', function () {
        if (!localStorage.getItem('cl-theme') && localStorage.getItem('theme') !== 'dark' && localStorage.getItem('theme') !== 'light') {
            applyTheme(resolveTheme());
        }
    });

    window.clToggleTheme = function () {
        var root    = document.getElementById('cl-root');
        var current = root ? root.getAttribute('data-theme') : resolveTheme();
        var next    = current === 'light' ? 'dark' : 'light';
        // Write to both keys so the admin panel picks it up on next load
        localStorage.setItem('cl-theme', next);
        localStorage.setItem('theme', next);
        applyTheme(next);
    };
})();
</script>
</x-filament-panels::page.simple>
