<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard — HybridLearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,700;1,9..144,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:       #0f1117;
            --surface:  #181c27;
            --surface2: #1e2332;
            --border:   rgba(255,255,255,0.07);
            --border2:  rgba(255,255,255,0.12);
            --text:     #e8e6f0;
            --muted:    #7a7d8e;
            --accent:   #6c63ff;
            --accent2:  #a78bfa;
            --gold:     #f4b94a;
            --green:    #34d399;
            --red:      #f87171;
            --teal:     #22d3ee;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .serif { font-family: 'Fraunces', serif; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 240px; height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            z-index: 20; overflow-y: auto;
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: 10px;
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }
        .logo-gem {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-section {
            font-size: .6rem; font-weight: 700;
            letter-spacing: .14em; text-transform: uppercase;
            color: var(--muted); padding: 0 10px 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: 8px;
            font-size: .84rem; font-weight: 500;
            color: var(--muted);
            text-decoration: none; margin-bottom: 2px;
            transition: all .18s;
        }
        .nav-item:hover { background: rgba(255,255,255,.05); color: var(--text); }
        .nav-item.active {
            background: rgba(108,99,255,.15);
            color: var(--accent2);
            border: 1px solid rgba(108,99,255,.2);
        }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }

        /* ── Main ── */
        .main { margin-left: 240px; padding: 28px 32px; min-height: 100vh; }

        /* ── Topbar ── */
        .topbar { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 32px; }
        .greeting-tag {
            display: inline-block;
            font-size: .68rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
            color: var(--accent2);
            background: rgba(108,99,255,.12);
            border: 1px solid rgba(108,99,255,.2);
            padding: 3px 10px; border-radius: 99px;
            margin-bottom: 8px;
        }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .icon-btn {
            width: 38px; height: 38px; border-radius: 10px;
            border: 1px solid var(--border2);
            background: var(--surface);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: border-color .15s, background .15s;
            text-decoration: none; position: relative;
        }
        .icon-btn:hover { background: var(--surface2); border-color: rgba(255,255,255,.2); }
        .notif-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); position: absolute; top: 7px; right: 7px; border: 1.5px solid var(--surface); }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 10px;
            background: var(--accent); color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .84rem; font-weight: 600;
            border: none; cursor: pointer;
            transition: opacity .15s, transform .15s;
            text-decoration: none;
        }
        .btn-primary:hover { opacity: .88; transform: translateY(-1px); }

        /* ── Wallet hero ── */
        .wallet-hero {
            background: linear-gradient(135deg, #1a1040 0%, #0f1117 50%, #0f1a2e 100%);
            border: 1px solid rgba(108,99,255,.25);
            border-radius: 18px;
            padding: 28px 32px;
            margin-bottom: 24px;
            position: relative; overflow: hidden;
        }
        .wallet-hero::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(108,99,255,.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .wallet-hero::after {
            content: '';
            position: absolute; bottom: -40px; left: 200px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(244,185,74,.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Stat cards ── */
        .stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 20px;
            transition: border-color .2s;
        }
        .stat-card:hover { border-color: var(--border2); }
        .stat-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .stat-label { font-size: .74rem; color: var(--muted); margin-bottom: 5px; }
        .stat-value { font-size: 1.5rem; font-weight: 700; letter-spacing: -.02em; line-height: 1; }
        .trend-badge {
            display: inline-flex; align-items: center; gap: 4px;
            margin-top: 8px; font-size: .68rem; font-weight: 600;
            padding: 3px 8px; border-radius: 99px;
        }
        .trend-up   { background: rgba(52,211,153,.12); color: var(--green); }
        .trend-down { background: rgba(248,113,113,.12); color: var(--red); }
        .trend-flat { background: rgba(122,125,142,.12); color: var(--muted); }

        /* ── Panels ── */
        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 22px;
        }
        .panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .panel-title { font-size: .9rem; font-weight: 700; }
        .view-link { font-size: .74rem; color: var(--accent2); font-weight: 600; text-decoration: none; }
        .view-link:hover { text-decoration: underline; }

        .chart-row   { display: grid; grid-template-columns: 1fr 320px; gap: 16px; margin-bottom: 24px; }
        .mid-row     { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .bottom-row  { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px; }

        /* ── Course table ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            font-size: .68rem; font-weight: 600; color: var(--muted);
            text-transform: uppercase; letter-spacing: .07em;
            padding: 0 0 10px; text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .data-table td { padding: 11px 0; border-bottom: 1px solid var(--border); font-size: .82rem; }
        .data-table tr:last-child td { border-bottom: none; }

        /* ── Pills ── */
        .pill { padding: 3px 9px; border-radius: 99px; font-size: .67rem; font-weight: 600; }
        .pill.published { background: rgba(52,211,153,.12); color: var(--green); }
        .pill.draft     { background: rgba(122,125,142,.1);  color: var(--muted); }
        .pill.pending   { background: rgba(244,185,74,.12);  color: var(--gold); }
        .pill.active    { background: rgba(52,211,153,.12);  color: var(--green); }
        .pill.completed { background: rgba(34,211,238,.12);  color: var(--teal); }
        .pill.credit    { background: rgba(52,211,153,.12);  color: var(--green); }
        .pill.debit     { background: rgba(248,113,113,.12); color: var(--red); }
        .pill.beginner      { background: rgba(52,211,153,.12); color: var(--green); }
        .pill.intermediate  { background: rgba(244,185,74,.12);  color: var(--gold); }
        .pill.advanced      { background: rgba(248,113,113,.12); color: var(--red); }

        /* ── Stars ── */
        .stars { display: inline-flex; gap: 2px; }
        .star  { font-size: 11px; }
        .star.filled  { color: var(--gold); }
        .star.empty   { color: rgba(255,255,255,.2); }

        /* ── Activity feed ── */
        .feed-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); }
        .feed-item:last-child { border-bottom: none; }
        .feed-avatar { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .65rem; font-weight: 700; color: #fff; flex-shrink: 0; }

        /* ── Rating bar ── */
        .rating-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .rating-bar-wrap { flex: 1; height: 6px; background: rgba(255,255,255,.06); border-radius: 99px; overflow: hidden; }
        .rating-bar-fill { height: 100%; border-radius: 99px; background: var(--gold); transition: width .5s; }

        /* ── Progress ring ── */
        .progress-ring { transform: rotate(-90deg); }

        /* ── Section label ── */
        .section-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--muted); margin-bottom: 14px; }

        /* ── Scroll ── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

        /* ── Animations ── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .45s ease both; }
        .d1 { animation-delay: .05s; }
        .d2 { animation-delay: .1s; }
        .d3 { animation-delay: .15s; }
        .d4 { animation-delay: .2s; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════
     SIDEBAR
══════════════════════════════════ --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-gem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
            </svg>
        </div>
        <span class="serif" style="color:var(--text);font-size:1rem;font-weight:500;letter-spacing:-.01em">HybridLearn</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section mt-2 mb-2">Instructor</div>
        <a href="{{ route('instructor.dashboard') }}" class="nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            My Courses
        </a>
        <a href="{{ route('instructor.students.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Students
        </a>
        <a href="{{ route('instructor.reviews.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Reviews
        </a>

        <div class="nav-section mt-5 mb-2">Earnings</div>
        <a href="{{ route('instructor.earnings.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Earnings
        </a>
        <a href="{{ route('instructor.payouts.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            Payouts
        </a>
        <a href="{{ route('instructor.wallet.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Wallet
        </a>

        <div class="nav-section mt-5 mb-2">Account</div>
        <a href="{{ route('instructor.profile.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profile
        </a>
        <a href="{{ route('instructor.settings.index') }}" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#fff;flex-shrink:0">
                {{ strtoupper(substr($instructor->name, 0, 2)) }}
            </div>
            <div style="min-width:0">
                <p style="font-size:.82rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $instructor->name }}</p>
                <p style="font-size:.7rem;color:var(--muted)">Instructor</p>
            </div>
        </div>
    </div>
</aside>


{{-- ══════════════════════════════════
     MAIN
══════════════════════════════════ --}}
<main class="main">

    {{-- TOPBAR --}}
    <div class="topbar fade-up">
        <div>
            <div class="greeting-tag">Instructor Portal</div>
            <h1 class="serif" style="font-size:1.55rem;font-weight:500;letter-spacing:-.02em;line-height:1.2">
                Welcome back, {{ explode(' ', $instructor->name)[0] }}
            </h1>
            <p style="font-size:.82rem;color:var(--muted);margin-top:4px">{{ now()->format('l, F j Y') }}</p>
        </div>
        <div class="topbar-actions">
            <div class="icon-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <div class="notif-dot"></div>
            </div>
            <a href="{{ route('instructor.courses.create') }}" class="btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Course
            </a>
            <a href="{{ route('logout') }}" class="icon-btn"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
        </div>
    </div>

    {{-- INSTRUCTOR VERIFICATION STATUS ALERT --}}
    @if(auth()->user()->role === 'instructor')
        @if(auth()->user()->hasVerificationPending())
        <div class="fade-up" style="background:rgba(244,185,74,.1);border:1px solid rgba(244,185,74,.2);border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:14px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div style="flex:1">
                <div style="font-size:.84rem;font-weight:600;color:var(--gold);margin-bottom:2px">⏳ Verification Pending</div>
                <p style="font-size:.78rem;color:var(--muted);margin:0">Your instructor verification is being reviewed by our admin team. You'll receive an email when approved. In the meantime, you cannot create courses.</p>
            </div>
        </div>
        @elseif(auth()->user()->hasVerificationRejected())
        <div class="fade-up" style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.2);border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:14px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <div style="flex:1">
                <div style="font-size:.84rem;font-weight:600;color:var(--red);margin-bottom:2px">✗ Verification Rejected</div>
                <p style="font-size:.78rem;color:var(--muted);margin:0">
                    Your instructor verification was rejected. 
                    @if(auth()->user()->instructorVerification?->rejection_reason)
                        Reason: {{ auth()->user()->instructorVerification->rejection_reason }}
                    @endif
                </p>
            </div>
        </div>
        @elseif(!auth()->user()->isVerifiedInstructor())
        <div class="fade-up" style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:14px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <div style="flex:1">
                <div style="font-size:.84rem;font-weight:600;color:var(--green);margin-bottom:2px">✓ Verified Instructor</div>
                <p style="font-size:.78rem;color:var(--muted);margin:0">You are verified! You can now create and publish courses.</p>
            </div>
        </div>
        @endif
    @endif

    {{-- ── WALLET HERO ────────────────────────────────────────────────────
         Source: instructor_wallets (balance, pending_balance)
         + revenue_shares (instructor_amount) for total earned
    ── --}}
    <div class="wallet-hero fade-up d1">
        <div style="display:grid;grid-template-columns:1fr auto;gap:24px;align-items:center">
            <div>
                <div style="font-size:.72rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--accent2);margin-bottom:10px">Wallet Balance</div>
                <div class="serif" style="font-size:2.8rem;font-weight:300;letter-spacing:-.03em;line-height:1">
                    ${{ number_format($wallet->balance, 2) }}
                    <span style="font-size:1rem;color:var(--muted);font-family:'Plus Jakarta Sans',sans-serif;font-weight:400">{{ $wallet->currency }}</span>
                </div>
                <div style="display:flex;gap:24px;margin-top:16px;flex-wrap:wrap">
                    <div>
                        <div style="font-size:.7rem;color:var(--muted);margin-bottom:3px">Pending</div>
                        <div style="font-size:1rem;font-weight:600;color:var(--gold)">${{ number_format($wallet->pending_balance, 2) }}</div>
                    </div>
                    <div style="width:1px;background:var(--border)"></div>
                    <div>
                        <div style="font-size:.7rem;color:var(--muted);margin-bottom:3px">Total Earned</div>
                        <div style="font-size:1rem;font-weight:600;color:var(--green)">${{ number_format($totalEarned, 2) }}</div>
                    </div>
                    <div style="width:1px;background:var(--border)"></div>
                    <div>
                        <div style="font-size:.7rem;color:var(--muted);margin-bottom:3px">This Month</div>
                        <div style="font-size:1rem;font-weight:600;color:var(--text)">${{ number_format($earningsThisMonth, 2) }}</div>
                    </div>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;align-items:flex-end">
                @if($pendingPayout)
                <div style="background:rgba(244,185,74,.1);border:1px solid rgba(244,185,74,.2);border-radius:10px;padding:10px 14px;font-size:.78rem;color:var(--gold);text-align:right">
                    Payout pending<br>
                    <strong>${{ number_format($pendingPayout->amount, 2) }}</strong>
                </div>
                @endif
                <a href="{{ route('instructor.payouts.index') }}" class="btn-primary" style="background:rgba(108,99,255,.2);border:1px solid rgba(108,99,255,.3);color:var(--accent2)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/></svg>
                    Request Payout
                </a>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ─────────────────────────────────────────────────────
         courses count, students, avg rating, completion rate
    ── --}}
    <p class="section-label">At a glance</p>
    <div class="stat-grid">

        <div class="stat-card fade-up d1">
            <div class="stat-icon" style="background:rgba(108,99,255,.12)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent2)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div class="stat-label">My Courses</div>
            <div class="stat-value">{{ $totalCourses }}</div>
            <div style="margin-top:8px;display:flex;gap:8px;font-size:.7rem">
                <span style="color:var(--green)">{{ $publishedCourses }} live</span>
                <span style="color:var(--muted)">·</span>
                <span style="color:var(--muted)">{{ $draftCourses }} draft</span>
            </div>
        </div>

        <div class="stat-card fade-up d2">
            <div class="stat-icon" style="background:rgba(52,211,153,.1)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ number_format($totalStudents) }}</div>
            <span class="trend-badge {{ $enrollTrendPct >= 0 ? 'trend-up' : 'trend-down' }}">
                {{ $enrollTrendPct >= 0 ? '↑' : '↓' }} {{ abs($enrollTrendPct) }}% this month
            </span>
        </div>

        <div class="stat-card fade-up d3">
            <div class="stat-icon" style="background:rgba(244,185,74,.1)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="stat-label">Avg Rating</div>
            <div class="stat-value">{{ number_format($reviewStats->avg_rating ?? 0, 1) }}</div>
            <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
                <div class="stars">
                    @for($s = 1; $s <= 5; $s++)
                        <span class="star {{ $s <= round($reviewStats->avg_rating ?? 0) ? 'filled' : 'empty' }}">★</span>
                    @endfor
                </div>
                <span style="font-size:.7rem;color:var(--muted)">({{ number_format($reviewStats->total_reviews ?? 0) }})</span>
            </div>
        </div>

        <div class="stat-card fade-up d4">
            <div class="stat-icon" style="background:rgba(34,211,238,.1)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="stat-label">Completion Rate</div>
            <div class="stat-value">{{ $completionRate }}%</div>
            <div style="height:4px;background:rgba(255,255,255,.06);border-radius:99px;overflow:hidden;margin-top:10px">
                <div style="height:100%;width:{{ $completionRate }}%;background:var(--teal);border-radius:99px;transition:width .5s"></div>
            </div>
        </div>
    </div>

    {{-- ── EARNINGS CHART + RATING BREAKDOWN ──────────────────────────────
         earningsTrend: revenue_shares grouped by month
         ratingBreakdown: reviews grouped by rating
    ── --}}
    <div class="chart-row fade-up">

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Earnings Trend</div>
                    <div style="font-size:.74rem;color:var(--muted);margin-top:2px">Monthly from <code style="font-size:.7rem;color:var(--accent2)">revenue_shares</code></div>
                </div>
                <a href="{{ route('instructor.earnings.index') }}" class="view-link">Full report →</a>
            </div>
            <div style="position:relative;height:220px">
                <canvas id="earningsChart" role="img" aria-label="Monthly earnings bar chart"></canvas>
            </div>
        </div>

        {{-- Rating breakdown from reviews table --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Rating Breakdown</div>
                <a href="{{ route('instructor.reviews.index') }}" class="view-link">View →</a>
            </div>
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px">
                <div style="text-align:center">
                    <div class="serif" style="font-size:3rem;font-weight:300;line-height:1;color:var(--gold)">
                        {{ number_format($reviewStats->avg_rating ?? 0, 1) }}
                    </div>
                    <div class="stars" style="margin-top:4px">
                        @for($s = 1; $s <= 5; $s++)
                            <span class="star {{ $s <= round($reviewStats->avg_rating ?? 0) ? 'filled' : 'empty' }}">★</span>
                        @endfor
                    </div>
                    <div style="font-size:.7rem;color:var(--muted);margin-top:4px">{{ number_format($reviewStats->total_reviews ?? 0) }} reviews</div>
                </div>
                <div style="flex:1">
                    @php $maxReviews = max($ratingBreakdown->max() ?? 1, 1); @endphp
                    @foreach([5,4,3,2,1] as $star)
                    <div class="rating-row">
                        <span style="font-size:.72rem;color:var(--muted);width:12px;text-align:right">{{ $star }}</span>
                        <span style="font-size:10px;color:var(--gold)">★</span>
                        <div class="rating-bar-wrap">
                            <div class="rating-bar-fill" style="width:{{ $ratingBreakdown->get($star,0) / $maxReviews * 100 }}%"></div>
                        </div>
                        <span style="font-size:.7rem;color:var(--muted);width:24px;text-align:right">{{ $ratingBreakdown->get($star, 0) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── MY COURSES TABLE ────────────────────────────────────────────────
         Source: courses + enrollments_count + reviews_avg_rating
         + revenue_shares SUM(instructor_amount)
    ── --}}
    <div class="panel fade-up" style="margin-bottom:24px">
        <div class="panel-header">
            <div class="panel-title">My Courses</div>
            <div style="display:flex;gap:10px;align-items:center">
                <a href="{{ route('instructor.courses.create') }}" class="btn-primary" style="font-size:.78rem;padding:7px 14px">
                    + New Course
                </a>
                <a href="{{ route('instructor.courses.index') }}" class="view-link">Manage all →</a>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Students</th>
                    <th>Rating</th>
                    <th style="text-align:right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myCourses as $course)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:var(--surface2);flex-shrink:0;overflow:hidden">
                                @if($course->thumbnail)
                                <img src="{{ $course->thumbnail }}" alt="" style="width:100%;height:100%;object-fit:cover">
                                @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:var(--accent2)">
                                    {{ strtoupper(substr($course->title, 0, 2)) }}
                                </div>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.83rem">{{ Str::limit($course->title, 30) }}</div>
                                <div style="font-size:.7rem;color:var(--muted)">{{ $course->category?->name ?? '—' }} · <span class="pill {{ $course->level }}">{{ ucfirst($course->level) }}</span></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="pill {{ $course->is_published ? 'published' : 'draft' }}">
                            {{ $course->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600">{{ number_format($course->enrollments_count) }}</div>
                        <div style="font-size:.7rem;color:var(--muted)">enrolled</div>
                    </td>
                    <td>
                        @if($course->reviews_avg_rating)
                        <div style="display:flex;align-items:center;gap:5px">
                            <span style="font-size:11px;color:var(--gold)">★</span>
                            <span style="font-weight:600">{{ number_format($course->reviews_avg_rating, 1) }}</span>
                        </div>
                        @else
                        <span style="color:var(--muted);font-size:.78rem">No reviews</span>
                        @endif
                    </td>
                    <td style="text-align:right">
                        <div style="font-weight:700;color:var(--green)">${{ number_format($course->total_revenue ?? 0, 2) }}</div>
                        <div style="font-size:.7rem;color:var(--muted)">earned</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="color:var(--muted);padding:24px 0;text-align:center">
                        No courses yet. <a href="{{ route('instructor.courses.create') }}" style="color:var(--accent2)">Create your first one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── BOTTOM ROW ──────────────────────────────────────────────────────
         Recent enrollments, recent reviews, wallet transactions
    ── --}}
    <div class="bottom-row fade-up">

        {{-- Recent enrollments — enrollments.enrolled_at, user.name, course.title, status --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">New Enrollments</div>
                <a href="{{ route('instructor.students.index') }}" class="view-link">View all →</a>
            </div>
            @php $avatarColors=['#6c63ff','#34d399','#f4b94a','#22d3ee','#f87171','#a78bfa']; @endphp
            <div>
                @forelse($recentEnrollments as $i => $enroll)
                <div class="feed-item">
                    <div class="feed-avatar" style="background:{{ $avatarColors[$i%6] }}">
                        {{ strtoupper(substr($enroll->user?->name ?? '?', 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:.82rem;font-weight:600">{{ $enroll->user?->name ?? 'Unknown' }}</p>
                        <p style="font-size:.75rem;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ Str::limit($enroll->course?->title ?? '—', 30) }}
                        </p>
                        <p style="font-size:.7rem;color:var(--muted);margin-top:2px">{{ $enroll->enrolled_at?->diffForHumans() }}</p>
                    </div>
                    <span class="pill {{ $enroll->status }}">{{ ucfirst($enroll->status) }}</span>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.82rem;padding:12px 0">No enrollments yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent reviews — reviews.rating, comment, user.name, course.title --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Recent Reviews</div>
                <a href="{{ route('instructor.reviews.index') }}" class="view-link">View all →</a>
            </div>
            <div>
                @forelse($recentReviews as $review)
                <div class="feed-item">
                    <div class="feed-avatar" style="background:#6c63ff;font-size:.65rem">
                        {{ strtoupper(substr($review->user?->name ?? '?', 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px">
                            <span style="font-size:.82rem;font-weight:600">{{ $review->user?->name ?? 'Anonymous' }}</span>
                            <div class="stars">
                                @for($s = 1; $s <= 5; $s++)
                                    <span style="font-size:9px;color:{{ $s <= $review->rating ? 'var(--gold)' : 'rgba(255,255,255,.15)' }}">★</span>
                                @endfor
                            </div>
                        </div>
                        <p style="font-size:.76rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ Str::limit($review->comment ?? $review->title ?? '—', 42) }}
                        </p>
                        <p style="font-size:.7rem;color:rgba(255,255,255,.2);margin-top:2px">{{ Str::limit($review->course?->title ?? '—', 26) }}</p>
                    </div>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.82rem;padding:12px 0">No reviews yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Wallet transactions — wallet_transactions (type, amount, description, created_at) --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Wallet Activity</div>
                <a href="{{ route('instructor.wallet.index') }}" class="view-link">View all →</a>
            </div>
            <div>
                @forelse($recentTransactions as $tx)
                <div class="feed-item">
                    <div style="width:30px;height:30px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:{{ $tx->type === 'credit' ? 'rgba(52,211,153,.12)' : 'rgba(248,113,113,.12)' }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $tx->type === 'credit' ? 'var(--green)' : 'var(--red)' }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            @if($tx->type === 'credit')
                                <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                            @else
                                <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
                            @endif
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:.82rem;font-weight:600;color:{{ $tx->type === 'credit' ? 'var(--green)' : 'var(--red)' }}">
                            {{ $tx->type === 'credit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                        </p>
                        <p style="font-size:.74rem;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ Str::limit($tx->description ?? ucfirst($tx->type), 34) }}
                        </p>
                        <p style="font-size:.7rem;color:rgba(255,255,255,.2);margin-top:2px">{{ $tx->created_at?->diffForHumans() }}</p>
                    </div>
                    <span class="pill {{ $tx->type }}">{{ ucfirst($tx->type) }}</span>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.82rem;padding:12px 0">No transactions yet.</p>
                @endforelse
            </div>
        </div>
    </div>

</main>

{{-- ══════════════════════════════════
     CHARTS
══════════════════════════════════ --}}
<script>
    // Earnings bar chart — from revenue_shares grouped by month
    const months   = @json($earningsTrend->pluck('month'));
    const earnings = @json($earningsTrend->pluck('total'));

    // Format month labels e.g. "2026-01" → "Jan"
    const monthLabels = months.map(m => {
        const d = new Date(m + '-01');
        return d.toLocaleString('en-US', { month: 'short' });
    });

    new Chart(document.getElementById('earningsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Earnings ($)',
                data: earnings,
                backgroundColor: earnings.map((_, i) =>
                    i === earnings.length - 1
                        ? 'rgba(108,99,255,0.9)'
                        : 'rgba(108,99,255,0.3)'
                ),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` $${Number(ctx.parsed.y).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#7a7d8e' },
                    border: { display: false }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        color: '#7a7d8e',
                        callback: v => '$' + (v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v)
                    },
                    border: { display: false }
                }
            }
        }
    });
</script>
</body>
</html>