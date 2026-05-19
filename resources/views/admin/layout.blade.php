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
            --ink:#0d0f14; --paper:#f5f2eb; --accent:#e8512a;
            --accent-light:#fde8e0; --muted:#8a8a8a;
            --border:#e2ddd5; --card:#ffffff; --sidebar:#0d0f14;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'DM Sans',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;}
        h1,h2,h3{font-family:'Syne',sans-serif;}
        .sidebar{position:fixed;top:0;left:0;width:230px;height:100vh;background:var(--sidebar);display:flex;flex-direction:column;z-index:10;overflow-y:auto;}
        .sidebar-logo{display:flex;align-items:center;gap:10px;padding:22px 22px 18px;border-bottom:1px solid rgba(255,255,255,.07);}
        .logo-icon{width:34px;height:34px;border-radius:8px;background:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .sidebar-nav{padding:16px 12px;flex:1;}
        .nav-group-label{font-size:.6rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.28);padding:0 10px 8px;}
        .nav-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:.84rem;font-weight:500;color:rgba(255,255,255,.5);cursor:pointer;transition:all .18s;text-decoration:none;margin-bottom:2px;}
        .nav-item:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.85);}
        .nav-item.active{background:var(--accent);color:#fff;}
        .nav-item svg{width:16px;height:16px;flex-shrink:0;}
        .sidebar-footer{padding:14px 16px;border-top:1px solid rgba(255,255,255,.07);}
        .u-avatar{width:32px;height:32px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#fff;flex-shrink:0;}
        .main{margin-left:230px;min-height:100vh;padding:28px 32px;}
        .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap;}
        .topbar-search{display:flex;align-items:center;gap:8px;padding:9px 14px;border:1.5px solid var(--border);border-radius:10px;background:#fff;width:240px;font-size:.84rem;color:var(--muted);}
        .topbar-actions{display:flex;align-items:center;gap:10px;}
        .icon-btn{width:36px;height:36px;border-radius:8px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:border-color .15s;position:relative;text-decoration:none;color:inherit;}
        .icon-btn:hover{border-color:#aaa;}
        .notif-dot{width:7px;height:7px;border-radius:50%;background:var(--accent);position:absolute;top:6px;right:6px;border:1.5px solid #fff;}
        .section-label{font-size:.76rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;}
        .panel{background:var(--card);border-radius:14px;border:1.5px solid var(--border);padding:20px 22px;}
        .panel-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:12px;}
        .panel-title{font-size:.9rem;font-weight:700;}
        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
        .stat-card{background:var(--card);border-radius:14px;border:1.5px solid var(--border);padding:18px 20px;position:relative;overflow:hidden;}
        .stat-card::before{content:'';position:absolute;top:0;right:0;width:60px;height:60px;border-radius:0 0 0 60px;opacity:.06;}
        .stat-card.c-orange::before{background:var(--accent);}
        .stat-card.c-blue::before{background:#3b82f6;}
        .stat-card.c-green::before{background:#10b981;}
        .stat-card.c-purple::before{background:#8b5cf6;}
        .stat-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
        .stat-icon.c-orange{background:var(--accent-light);}
        .stat-icon.c-blue{background:#eff6ff;}
        .stat-icon.c-green{background:#ecfdf5;}
        .stat-icon.c-purple{background:#f5f3ff;}
        .stat-label{font-size:.76rem;font-weight:500;color:var(--muted);margin-bottom:4px;}
        .stat-value{font-size:1.55rem;font-weight:700;letter-spacing:-.02em;line-height:1;}
        .stat-badge{display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:.7rem;font-weight:600;padding:3px 8px;border-radius:99px;}
        .stat-badge.up{background:#ecfdf5;color:#059669;}
        .stat-badge.down{background:#fff1f0;color:#e03d2d;}
        .today-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
        .today-card{background:var(--card);border-radius:14px;border:1.5px solid var(--border);padding:16px 18px;display:flex;align-items:center;gap:14px;}
        .today-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .data-table{width:100%;border-collapse:collapse;}
        .data-table th{font-size:.7rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;padding:0 0 10px;text-align:left;border-bottom:1px solid var(--border);}
        .data-table td{padding:10px 0;border-bottom:1px solid var(--border);font-size:.82rem;}
        .data-table tr:last-child td{border-bottom:none;}
        .pill{padding:3px 9px;border-radius:99px;font-size:.68rem;font-weight:600;}
        .pill.paid,.pill.active{background:#ecfdf5;color:#059669;}
        .pill.pending{background:#fef9c3;color:#92400e;}
        .pill.failed{background:#fff1f0;color:#e03d2d;}
        .pill.refunded{background:#f5f3ff;color:#7c3aed;}
        .pill.completed{background:#eff6ff;color:#2563eb;}
        .pill.beginner{background:#ecfdf5;color:#059669;}
        .pill.intermediate{background:#fef9c3;color:#92400e;}
        .pill.advanced{background:#fff1f0;color:#e03d2d;}
        .course-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
        .activity-item{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);}
        .activity-item:last-child{border-bottom:none;}
        .act-avatar{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#fff;flex-shrink:0;}
        .split-bar{height:8px;border-radius:99px;overflow:hidden;display:flex;margin:8px 0;}
        .split-fill{height:100%;transition:width .4s;}
        .table-heading{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;}
        .btn-secondary{padding:.7rem 1rem;border:1px solid var(--border);border-radius:12px;background:#fff;color:var(--ink);font-weight:600;text-decoration:none;}
        .empty-state{padding:24px 18px;color:var(--muted);font-size:.92rem;}
        ::-webkit-scrollbar{width:5px;}
        ::-webkit-scrollbar-thumb{background:#d8d3c8;border-radius:99px;}
    </style>
    @stack('head')
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <span style="font-family:'Syne',sans-serif;color:#fff;font-size:1rem;font-weight:700">HybridLearn</span>
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
        <div style="display:flex;align-items:center;gap:10px">
            <div class="u-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <p style="font-size:.8rem;font-weight:600;color:#fff">{{ auth()->user()->name }}</p>
                <p style="font-size:.7rem;color:rgba(255,255,255,.4)">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>
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
