<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard — HybridLearn')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #edf2ff;
            --paper: #09101d;
            --accent: #5d8dff;
            --accent-soft: rgba(93,141,255,.18);
            --accent-strong: #83b7ff;
            --shadow: rgba(0,0,0,.24);
            --sidebar: #0b1526;
            --sidebar-soft: #142034;
            --sidebar-border: rgba(93,141,255,.14);
            --panel: #121d31;
            --panel-border: rgba(255,255,255,.08);
            --text: #e2ebff;
            --text-muted: #8fa6c2;
            --text-soft: #c7d4eb;
            --muted: #6c86a7;
            --card: #121f34;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(180deg, #07101d 0%, #09121c 100%);
            color: var(--ink);
            min-height: 100vh;
        }

        h1, h2, h3 {
            font-family: 'Syne', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 248px;
            height: 100vh;
            padding: 22px 18px 18px;
            background: linear-gradient(180deg, #0c1629 0%, #0a1427 100%);
            border-right: 1px solid rgba(255,255,255,.06);
            box-shadow: 18px 0 50px rgba(0,0,0,.28);
            display: flex;
            flex-direction: column;
            z-index: 10;
            overflow-y: auto;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, rgba(93,141,255,.48), transparent 45%);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 18px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4b77ff, #40d6ff);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 12px 28px rgba(69,113,255,.18);
        }

        .logo-text {
            color: var(--text);
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .logo-text small {
            display: block;
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 2px;
            font-weight: 500;
        }

        .sidebar-nav {
            padding: 8px 0 10px;
            flex: 1;
        }

        .nav-group-label {
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255,255,255,.36);
            padding: 0 10px 10px;
            margin-top: 14px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 0.94rem;
            font-weight: 600;
            color: rgba(255,255,255,.72);
            text-decoration: none;
            margin-bottom: 8px;
            transition: background .22s ease, transform .18s ease, color .18s ease;
        }

        .nav-item:hover {
            background: rgba(93,141,255,.12);
            color: #f8fbff;
            transform: translateX(1px);
        }

        .nav-item.active {
            background: rgba(69,113,255,.18);
            border-left: 4px solid #5d8dff;
            color: #f8fbff;
            box-shadow: inset 4px 0 0 rgba(79,143,255,.25), 0 14px 30px rgba(0,0,0,.16);
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            color: #9ab9de;
            flex-shrink: 0;
            transition: color .18s ease;
        }

        .nav-item.active svg {
            color: #a4d8ff;
        }

        .sidebar-footer {
            padding: 16px 14px 14px;
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .user-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .u-avatar {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4b77ff, #40d6ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: #0b1120;
            flex-shrink: 0;
            box-shadow: 0 14px 28px rgba(74,138,255,.16);
        }

        .user-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #f8fbff;
        }

        .user-role {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .sidebar-footer .footer-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 14px;
            padding: 10px 12px;
            color: #e6f3ff;
            background: rgba(93,141,255,.14);
            border-radius: 12px;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .main {
            margin-left: 248px;
            min-height: 100vh;
            padding: 28px 36px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .topbar-search {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 14px;
            background: rgba(255,255,255,.04);
            width: 280px;
            color: var(--text-soft);
            font-size: 0.9rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.05);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color .15s ease, transform .15s ease;
            position: relative;
            text-decoration: none;
            color: inherit;
        }

        .icon-btn:hover {
            border-color: rgba(93,141,255,.3);
            transform: translateY(-1px);
        }

        .notif-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ad7ff;
            position: absolute;
            top: 8px;
            right: 8px;
            border: 2px solid rgba(255,255,255,.1);
        }

        .section-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 14px;
        }

        .panel,
        .today-card,
        .stat-card {
            background: var(--panel);
            border-radius: 20px;
            border: 1px solid var(--panel-border);
        }

        .panel {
            padding: 22px 24px;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 16px;
        }

        .panel-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #f8fbff;
        }

        .stat-grid,
        .today-grid {
            display: grid;
            gap: 16px;
        }

        .stat-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 24px;
        }

        .today-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 64px;
            height: 64px;
            border-radius: 0 0 0 64px;
            opacity: .08;
        }

        .stat-card.c-orange::before { background: #e89060; }
        .stat-card.c-blue::before { background: #3b82f6; }
        .stat-card.c-green::before { background: #10b981; }
        .stat-card.c-purple::before { background: #8b5cf6; }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.05;
            color: #f8fbff;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 999px;
        }

        .stat-badge.up {
            background: rgba(16,185,129,.12);
            color: #7dd3fc;
        }

        .stat-badge.down {
            background: rgba(239,68,68,.12);
            color: #fecdd3;
        }

        .today-card {
            padding: 20px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .today-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            text-align: left;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,.08);
            font-size: 0.86rem;
        }

        .data-table th {
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.09em;
        }

        .data-table td {
            color: var(--text-soft);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .pill.paid,
        .pill.active { background: rgba(16,185,129,.12); color: #7dd3fc; }
        .pill.pending { background: rgba(251,191,36,.13); color: #fcd34d; }
        .pill.failed { background: rgba(239,68,68,.12); color: #fecdd3; }
        .pill.refunded { background: rgba(139,92,246,.12); color: #c4b5fd; }
        .pill.completed { background: rgba(59,130,246,.1); color: #93c5fd; }
        .pill.beginner { background: rgba(16,185,129,.12); color: #a7f3d0; }
        .pill.intermediate { background: rgba(251,191,36,.13); color: #fde68a; }
        .pill.advanced { background: rgba(239,68,68,.12); color: #fecaca; }

        .course-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .activity-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .activity-item:last-child { border-bottom: none; }

        .act-avatar {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .split-bar {
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
            display: flex;
            margin: 8px 0;
        }

        .split-fill {
            height: 100%;
            transition: width .4s ease;
        }

        .table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            gap: 12px;
        }

        .btn-secondary {
            padding: .75rem 1rem;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 14px;
            background: rgba(255,255,255,.05);
            color: #eef4ff;
            font-weight: 700;
            text-decoration: none;
        }

        .empty-state {
            padding: 24px 18px;
            color: var(--muted);
            font-size: .92rem;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.12);
            border-radius: 999px;
        }
    </style>
    @stack('head')
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div>
            <span class="logo-text">HybridLearn</span>
            <small>Marketplace admin</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-group-label mt-2 mb-1">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="nav-item {{ Request::routeIs('admin.users') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Users
        </a>
        <a href="{{ route('admin.courses') }}" class="nav-item {{ Request::routeIs('admin.courses') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Courses
        </a>
        <a href="{{ route('admin.enrollments') }}" class="nav-item {{ Request::routeIs('admin.enrollments') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Enrollments
        </a>
        <a href="{{ route('admin.reviews') }}" class="nav-item {{ Request::routeIs('admin.reviews') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Reviews
        </a>
        <div class="nav-group-label mt-5 mb-1">Finance</div>
        <a href="{{ route('admin.orders') }}" class="nav-item {{ Request::routeIs('admin.orders') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Orders
        </a>
        <a href="{{ route('admin.payments') }}" class="nav-item {{ Request::routeIs('admin.payments') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Payments
        </a>
        <a href="{{ route('admin.payouts') }}" class="nav-item {{ Request::routeIs('admin.payouts') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            Payouts
        </a>
        <div class="nav-group-label mt-5 mb-1">System</div>
        <a href="{{ route('admin.settings') }}" class="nav-item {{ Request::routeIs('admin.settings') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Settings
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-meta">
            <div class="u-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <p class="user-name">{{ auth()->user()->name }}</p>
                <p class="user-role">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>
        <a href="{{ route('admin.settings') }}" class="footer-cta">Open settings</a>
    </div>
</aside>
<main class="main">
    <div class="topbar">
        <div>
            <h1 style="font-size:1.3rem;font-weight:700">@yield('page-heading', 'Admin')</h1>
            <p style="font-size:.8rem;color:var(--muted);margin-top:2px">{{ now()->format('l, F j Y') }}</p>
        </div>
        <div class="topbar-actions">
            <div class="topbar-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>Search...</span>
            </div>
            <div class="icon-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <div class="notif-dot"></div>
            </div>
            <a href="{{ route('logout') }}" class="icon-btn" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
        </div>
    </div>
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
