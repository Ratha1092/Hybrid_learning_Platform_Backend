<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - Profile</title>
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
        }
        h1, h2, h3, .brand { font-family: 'Syne', sans-serif; }
        .profile-header {
            background: linear-gradient(135deg, var(--ink) 0%, #1a1d25 100%);
            color: white;
            padding: 3rem 0;
        }
        .avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--accent);
            object-fit: cover;
        }
        .stat-card {
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        .stat-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(232, 81, 42, 0.1);
        }
        .stat-number {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--accent);
            font-family: 'Syne', sans-serif;
        }
        .stat-label {
            font-size: 0.875rem;
            color: var(--muted);
            margin-top: 0.5rem;
        }
        .info-section {
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            margin: 1.5rem 0;
        }
        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--accent-light);
            color: var(--accent);
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .social-link:hover {
            background: var(--accent);
            color: white;
        }
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--accent);
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-badge {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--accent);
            color: white;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Syne', sans-serif;
        }
        .btn-primary {
            padding: 0.75rem 1.5rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            background: #d0441e;
        }
        .btn-secondary {
            padding: 0.75rem 1.5rem;
            background: white;
            color: var(--ink);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
    </style>
</head>
<body>
    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:var(--accent)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </div>
                        <span style="font-family: 'Syne', sans-serif; font-weight: bold;">HybridLearn</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-sm" style="color: var(--muted)">← Back to Dashboard</a>
                    @if(auth()->check() && auth()->user()->id !== $user->id)
                        <a href="{{ route('profile.show') }}" class="text-sm btn-secondary">My Profile</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Profile Header --}}
    <div class="profile-header">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-6">
                @if($user->oauth_avatar || ($profile && $profile->avatar))
                    <img src="{{ $user->oauth_avatar ?? $profile->avatar }}" alt="{{ $user->name }}" class="avatar-large">
                @else
                    <div class="avatar-large flex items-center justify-center" style="background: var(--accent)">
                        <span style="font-size: 3rem; font-weight: bold; color: white;">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>
                    <p class="text-lg mb-3" style="color: rgba(255,255,255,0.8)">{{ $user->email }}</p>
                    <div class="flex items-center gap-3">
                        <span class="role-badge">{{ ucfirst($user->role) }}</span>
                        @if($isOwnProfile)
                            <span class="badge" style="background: rgba(255,255,255,0.2)">Your Profile</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        {{-- Bio Section --}}
        @if($profile && $profile->bio)
            <div class="info-section">
                <h2 class="text-xl font-bold mb-3">About</h2>
                <p style="color: var(--muted); line-height: 1.6;">{{ $profile->bio }}</p>
            </div>
        @endif

        {{-- Stats Section --}}
        @if($stats)
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Statistics</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($stats as $key => $value)
                        <div class="stat-card">
                            <div class="stat-number">
                                @if(is_numeric($value))
                                    @if($key === 'average_rating' || $key === 'average_progress')
                                        {{ number_format($value, 1) }}{{ $key === 'average_progress' ? '%' : '' }}
                                    @else
                                        {{ $value }}
                                    @endif
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                            <div class="stat-label">
                                {{ str_replace('_', ' ', ucfirst($key)) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Social Links Section --}}
        @if($profile)
            <div class="info-section">
                <h2 class="text-xl font-bold mb-4">Connect</h2>
                <div class="flex flex-wrap gap-3">
                    {{-- Student Profile Social Links --}}
                    @if($user->role === 'student')
                        @if($profile->github)
                            <a href="https://github.com/{{ $profile->github }}" target="_blank" rel="noopener" class="social-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
                                GitHub
                            </a>
                        @endif
                        @if($profile->linkedin)
                            <a href="https://linkedin.com/in/{{ $profile->linkedin }}" target="_blank" rel="noopener" class="social-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                LinkedIn
                            </a>
                        @endif
                    @endif

                    {{-- Instructor Profile Social Links --}}
                    @if($user->role === 'instructor')
                        @if($profile->website)
                            <a href="{{ $profile->website }}" target="_blank" rel="noopener" class="social-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                Website
                            </a>
                        @endif
                        @if($profile->twitter)
                            <a href="https://twitter.com/{{ $profile->twitter }}" target="_blank" rel="noopener" class="social-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7a10.6 10.6 0 01-9.5 5"/></svg>
                                Twitter
                            </a>
                        @endif
                        @if($profile->linkedin)
                            <a href="https://linkedin.com/in/{{ $profile->linkedin }}" target="_blank" rel="noopener" class="social-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                LinkedIn
                            </a>
                        @endif
                        @if($profile->youtube)
                            <a href="https://youtube.com/@{{ $profile->youtube }}" target="_blank" rel="noopener" class="social-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                YouTube
                            </a>
                        @endif
                    @endif

                    @if(!$profile || (!$profile->github && !$profile->linkedin && !$profile->website && !$profile->twitter && !$profile->youtube))
                        <p style="color: var(--muted); font-style: italic;">No social links added yet</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="info-section flex gap-3">
            @if($isOwnProfile)
                <a href="{{ route('instructor.profile.index') }}" class="btn-primary">Edit Profile</a>
            @endif
            <a href="{{ route('home') }}" class="btn-secondary">Back to Dashboard</a>
        </div>

    </div>
</body>
</html>
