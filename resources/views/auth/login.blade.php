<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — HybridLearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0d0f14;
            --paper: #f5f2eb;
            --accent: #e8512a;
            --accent-light: #fde8e0;
            --muted: #8a8a8a;
            --border: #d8d3c8;
            --card: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--paper);
            color: var(--ink);
            min-height: 100vh;
        }
        h1, h2, h3, .brand { font-family: 'Syne', sans-serif; }

        /* Grid dots background */
        .bg-grid {
            background-image: radial-gradient(circle, #c5bfb4 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Decorative diagonal stripe */
        .stripe-panel {
            background-color: var(--ink);
            position: relative;
            overflow: hidden;
        }
        .stripe-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                -55deg,
                transparent,
                transparent 18px,
                rgba(255,255,255,0.03) 18px,
                rgba(255,255,255,0.03) 36px
            );
        }
        .accent-tag {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 2px;
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--ink);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-field:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232,81,42,0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 13px;
            background: var(--accent);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-primary:hover {
            background: #d0441e;
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 0.78rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Floating course cards on the dark panel */
        .course-card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 14px 18px;
            backdrop-filter: blur(4px);
        }
        .progress-bar {
            height: 4px;
            background: rgba(255,255,255,0.12);
            border-radius: 99px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 99px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-green { background: rgba(52,211,153,0.15); color: #34d399; }
        .badge-blue  { background: rgba(96,165,250,0.15);  color: #60a5fa; }
        .badge-amber { background: rgba(251,191,36,0.15);  color: #fbbf24; }

        .stat-box {
            text-align: center;
            padding: 16px;
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.55s ease both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        label { font-size: 0.82rem; font-weight: 500; color: #3a3a3a; }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--ink);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        .social-btn:hover { border-color: #aaa; background: #fafafa; }
    </style>
</head>
<body class="bg-grid min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl flex rounded-2xl overflow-hidden shadow-2xl" style="min-height: 600px;">

        {{-- ── LEFT: Dark visual panel ── --}}
        <div class="stripe-panel hidden lg:flex flex-col justify-between w-[46%] p-10 text-white">

            {{-- Brand --}}
            <div class="fade-up">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--accent)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <span class="brand text-lg font-bold tracking-tight">HybridLearn</span>
                </div>
                <span class="accent-tag">Web-based Platform</span>
                <p class="mt-4 text-sm leading-relaxed" style="color:rgba(255,255,255,0.55)">
                    Your integrated space for online lessons, live sessions, and collaborative learning — anywhere, anytime.
                </p>
            </div>

            {{-- Live activity cards --}}
            <div class="space-y-3 fade-up delay-1">
                <p class="text-xs font-semibold tracking-widest uppercase" style="color:rgba(255,255,255,0.35)">Your Activity</p>

                <div class="course-card">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-sm font-semibold">Introduction to UX Design</p>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">Module 4 of 8</p>
                        </div>
                        <span class="badge badge-green">● Live</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:52%"></div>
                    </div>
                    <p class="text-xs mt-1.5" style="color:rgba(255,255,255,0.35)">52% completed</p>
                </div>

                <div class="course-card">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-sm font-semibold">Data Structures & Algorithms</p>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">Module 2 of 10</p>
                        </div>
                        <span class="badge badge-blue">Async</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:20%"></div>
                    </div>
                    <p class="text-xs mt-1.5" style="color:rgba(255,255,255,0.35)">20% completed</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-3 fade-up delay-2">
                <div class="stat-box">
                    <p class="text-xl font-bold" style="font-family:'Syne',sans-serif">12k+</p>
                    <p class="text-xs mt-1" style="color:rgba(255,255,255,0.4)">Learners</p>
                </div>
                <div class="stat-box">
                    <p class="text-xl font-bold" style="font-family:'Syne',sans-serif">340</p>
                    <p class="text-xs mt-1" style="color:rgba(255,255,255,0.4)">Courses</p>
                </div>
                <div class="stat-box">
                    <p class="text-xl font-bold" style="font-family:'Syne',sans-serif">98%</p>
                    <p class="text-xs mt-1" style="color:rgba(255,255,255,0.4)">Satisfaction</p>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Login form ── --}}
        <div class="flex-1 bg-white flex flex-col justify-center px-8 py-10 md:px-12">

            <div class="max-w-sm mx-auto w-full fade-up">

                {{-- Heading --}}
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight" style="font-family:'Syne',sans-serif">Welcome back</h2>
                        <p class="text-sm mt-1" style="color:var(--muted)">Sign in to continue your learning journey.</p>
                    </div>
                    <a href="{{ route('home') }}" class="text-xs px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition" style="color:var(--muted)">← Back</a>
                </div>

                {{-- Laravel session errors --}}
                @if ($errors->any())
                    <div class="mb-5 p-3 rounded-lg text-sm" style="background:var(--accent-light); color:var(--accent)">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Google / GitHub OAuth --}}
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <a href="{{ route('oauth.redirect', 'google') }}" class="social-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Google
                    </a>
                    <a href="{{ route('oauth.redirect', 'github') }}" class="social-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        GitHub
                    </a>
                </div>

                <div class="divider mb-5">or sign in with email</div>

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block mb-1.5">Email address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            required
                            autocomplete="email"
                            class="input-field"
                        >
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs" style="color:var(--accent)">Forgot password?</a>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            class="input-field"
                        >
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded accent-orange-500">
                        <label for="remember" class="cursor-pointer">Remember me for 30 days</label>
                    </div>

                    <button type="submit" class="btn-primary mt-2">
                        Sign In →
                    </button>
                </form>

                <p class="text-center text-sm mt-6" style="color:var(--muted)">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold" style="color:var(--ink)">Create one free</a>
                </p>

            </div>
        </div>
    </div>

</body>
</html>