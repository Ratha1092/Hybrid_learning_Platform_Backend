<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students — Instructor Dashboard</title>
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
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); cursor: pointer;
            transition: all .18s;
        }
        .icon-btn:hover { border-color: var(--border2); color: var(--text); }

        /* ── Student Cards ── */
        .student-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: all .18s;
        }
        .student-card:hover {
            border-color: var(--border2);
            transform: translateY(-2px);
        }
        .student-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; font-size: 18px;
        }

        /* ── Pagination ── */
        .pagination {
            display: flex; justify-content: center; gap: 8px;
            margin-top: 32px;
        }
        .page-link {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--muted);
            text-decoration: none;
            transition: all .18s;
        }
        .page-link:hover { border-color: var(--border2); color: var(--text); }
        .page-link.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }
    </style>
</head>
<body>

    {{-- ── Sidebar ── --}}
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-gem">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-semibold">HybridLearn</div>
                <div class="text-xs text-gray-400">Instructor</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Overview</div>
            <a href="{{ route('instructor.dashboard') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-section">Management</div>
            <a href="{{ route('instructor.courses.index') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                My Courses
            </a>
            <a href="{{ route('instructor.students.index') }}" class="nav-item active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Students
            </a>
            <a href="{{ route('instructor.reviews.index') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Reviews
            </a>

            <div class="nav-section">Finance</div>
            <a href="{{ route('instructor.earnings.index') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Earnings
            </a>
            <a href="{{ route('instructor.payouts.index') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                Payouts
            </a>
            <a href="{{ route('instructor.wallet.index') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                Wallet
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('instructor.settings.index') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001 1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
                Settings
            </a>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="main">
        <div class="topbar">
            <div>
                <div class="greeting-tag">Instructor Panel</div>
                <h1 class="text-2xl font-bold serif">My Students</h1>
                <p class="text-sm text-gray-400 mt-1">Students enrolled in your courses</p>
            </div>
        </div>

        {{-- Students Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($students as $enrollment)
                <div class="student-card">
                    <div class="flex items-start gap-4">
                        <div class="student-avatar">
                            {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg mb-1">{{ $enrollment->user->name }}</h3>
                            <p class="text-sm text-gray-400 mb-2">{{ $enrollment->user->email }}</p>

                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Course:</span>
                                    <span class="font-medium">{{ $enrollment->course->title }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Enrolled:</span>
                                    <span>{{ $enrollment->enrolled_at->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Progress:</span>
                                    <span class="font-medium">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Status:</span>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @if($enrollment->completed_at) bg-green-500/20 text-green-400
                                        @elseif($enrollment->progress_percentage > 0) bg-blue-500/20 text-blue-400
                                        @else bg-gray-500/20 text-gray-400 @endif">
                                        @if($enrollment->completed_at) Completed
                                        @elseif($enrollment->progress_percentage > 0) In Progress
                                        @else Not Started @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="student-card text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">No students yet</h3>
                        <p class="text-gray-400 mb-4">Students will appear here once they enroll in your courses</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($students->hasPages())
            <div class="pagination">
                {{-- Previous Page Link --}}
                @if ($students->onFirstPage())
                    <span class="page-link opacity-50 cursor-not-allowed">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15,18 9,12 15,6"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $students->previousPageUrl() }}" class="page-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15,18 9,12 15,6"/>
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                    @if ($page == $students->currentPage())
                        <span class="page-link active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($students->hasMorePages())
                    <a href="{{ $students->nextPageUrl() }}" class="page-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9,18 15,12 9,6"/>
                        </svg>
                    </a>
                @else
                    <span class="page-link opacity-50 cursor-not-allowed">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9,18 15,12 9,6"/>
                        </svg>
                    </span>
                @endif
            </div>
        @endif
    </div>

</body>
</html>