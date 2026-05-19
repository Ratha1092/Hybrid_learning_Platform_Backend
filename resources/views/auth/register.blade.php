<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — HybridLearn</title>
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

        .bg-grid {
            background-image: radial-gradient(circle, #c5bfb4 1px, transparent 1px);
            background-size: 28px 28px;
        }
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
            padding: 11px 16px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
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
        .btn-primary:hover { background: #d0441e; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        label { font-size: 0.82rem; font-weight: 500; color: #3a3a3a; }

        /* Password strength meter */
        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 6px;
        }
        .strength-segment {
            flex: 1;
            height: 3px;
            border-radius: 99px;
            background: var(--border);
            transition: background 0.3s;
        }
        .strength-segment.active-weak   { background: #ef4444; }
        .strength-segment.active-fair   { background: #f97316; }
        .strength-segment.active-good   { background: #eab308; }
        .strength-segment.active-strong { background: #22c55e; }

        /* Feature list on dark panel */
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }
        .feature-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Role selector */
        .role-option {
            display: none;
        }
        .role-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.84rem;
            font-weight: 500;
            background: #fff;
        }
        .role-option:checked + .role-label {
            border-color: var(--accent);
            background: var(--accent-light);
            color: var(--accent);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.55s ease both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }

        .avatar-stack { display: flex; }
        .avatar-stack img,
        .avatar-item {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid var(--ink);
            margin-left: -8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: #fff;
        }
        .avatar-stack .avatar-item:first-child { margin-left: 0; }

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

        /* Scrollable form panel */
        .form-scroll {
            overflow-y: auto;
            max-height: 100vh;
        }
    </style>
</head>
<body class="bg-grid min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl flex rounded-2xl overflow-hidden shadow-2xl">

        {{-- ── LEFT: Dark visual panel ── --}}
        <div class="stripe-panel hidden lg:flex flex-col justify-between w-[44%] p-10 text-white" style="min-height:700px">

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
                <span class="accent-tag">Web-based Hybrid Platform</span>
                <h3 class="text-xl font-bold mt-4 leading-tight" style="font-family:'Syne',sans-serif">
                    Join thousands of<br>learners today.
                </h3>

                {{-- Social proof --}}
                <div class="flex items-center gap-3 mt-4">
                    <div class="avatar-stack">
                        <div class="avatar-item" style="background:#e8512a">S</div>
                        <div class="avatar-item" style="background:#3b82f6">M</div>
                        <div class="avatar-item" style="background:#8b5cf6">A</div>
                        <div class="avatar-item" style="background:#10b981">K</div>
                    </div>
                    <p class="text-xs" style="color:rgba(255,255,255,0.5)">+12,400 learners enrolled</p>
                </div>
            </div>

            {{-- Feature list --}}
            <div class="space-y-3 fade-up delay-1">
                <p class="text-xs font-semibold tracking-widest uppercase mb-4" style="color:rgba(255,255,255,0.35)">What you get</p>

                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(232,81,42,0.2)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e8512a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Live & Async Sessions</p>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">Join real-time classes or learn at your own pace.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(96,165,250,0.15)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Collaborative Workspaces</p>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">Study groups, shared notes, and peer feedback.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(52,211,153,0.15)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Progress Analytics</p>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">Detailed insights on your learning performance.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(251,191,36,0.15)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Certificates & Badges</p>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45)">Earn verifiable credentials for every course.</p>
                    </div>
                </div>
            </div>

            <p class="text-xs fade-up delay-2" style="color:rgba(255,255,255,0.25)">
                © 2025 HybridLearn. Free to join, always.
            </p>
        </div>

        {{-- ── RIGHT: Register form ── --}}
        <div class="flex-1 bg-white form-scroll">
            <div class="px-8 py-9 md:px-12 max-w-sm mx-auto w-full fade-up">

                {{-- Heading --}}
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight" style="font-family:'Syne',sans-serif">Create your account</h2>
                        <p class="text-sm mt-1" style="color:var(--muted)">Start learning in under 60 seconds. No credit card needed.</p>
                    </div>
                    <a href="{{ route('home') }}" class="text-xs px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition" style="color:var(--muted)">← Back</a>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="mb-5 p-3 rounded-lg text-sm" style="background:var(--accent-light); color:var(--accent)">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Social sign-up --}}
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

                <div class="divider mb-5">or register with email</div>

                {{-- Register Form --}}
                <form method="POST" action="{{ route('register.post') }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf

                    {{-- Role --}}
                    <div>
                        <label class="block mb-2">I want to join as</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="role-label">
                                <input type="radio" name="role" value="student" id="role-student"
                                    {{ old('role', 'student') === 'student' ? 'checked' : '' }}>
                                Student
                            </label>

                            <label class="role-label">
                                <input type="radio" name="role" value="instructor" id="role-instructor"
                                    {{ old('role') === 'instructor' ? 'checked' : '' }}>
                                Instructor
                            </label>
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label>Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Your full name"
                            required
                            class="input-field"
                        >
                    </div>

                    {{-- Email --}}
                    <div>
                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            required
                            class="input-field"
                        >
                    </div>

                    {{-- Password --}}
                    <div>
                        <label>Password</label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="input-field"
                        >
                    </div>

                    {{-- Confirm --}}
                    <div>
                        <label>Confirm Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            class="input-field"
                        >
                    </div>

                    {{-- INSTRUCTOR VERIFICATION FIELDS (Shown when instructor is selected) --}}
                    <div id="instructor-fields" style="display: {{ old('role') === 'instructor' ? 'block' : 'none' }}; border-top: 2px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                        
                        <p style="font-size: 0.82rem; color: var(--muted); margin-bottom: 1rem;">
                            ⓘ Please provide evidence of your teaching qualification to verify your instructor status
                        </p>

                        {{-- Bio --}}
                        <div class="mb-4">
                            <label>Professional Bio</label>
                            <textarea
                                name="bio"
                                placeholder="Tell us about your teaching experience and expertise (max 1000 characters)"
                                maxlength="1000"
                                class="input-field"
                                rows="3"
                                style="resize: vertical;"
                            >{{ old('bio') }}</textarea>
                        </div>

                        {{-- Experience --}}
                        <div class="mb-4">
                            <label>Teaching Experience</label>
                            <textarea
                                name="experience"
                                placeholder="Describe your years of experience, subjects taught, and achievements (max 2000 characters)"
                                maxlength="2000"
                                class="input-field"
                                rows="3"
                                style="resize: vertical;"
                            >{{ old('experience') }}</textarea>
                        </div>

                        {{-- Qualification Type --}}
                        <div class="mb-4">
                            <label>Qualification Type</label>
                            <select name="qualification_type" class="input-field">
                                <option value="">-- Select a qualification --</option>
                                <option value="degree" {{ old('qualification_type') === 'degree' ? 'selected' : '' }}>Bachelor's/Master's Degree</option>
                                <option value="certification" {{ old('qualification_type') === 'certification' ? 'selected' : '' }}>Professional Certification</option>
                                <option value="professional_experience" {{ old('qualification_type') === 'professional_experience' ? 'selected' : '' }}>Professional Experience</option>
                            </select>
                        </div>

                        {{-- Institution --}}
                        <div class="mb-4">
                            <label>Institution / Organization</label>
                            <input
                                type="text"
                                name="institution"
                                placeholder="University or organization name"
                                value="{{ old('institution') }}"
                                class="input-field"
                            >
                        </div>

                        {{-- Completion Year --}}
                        <div class="mb-4">
                            <label>Year of Completion</label>
                            <input
                                type="number"
                                name="completion_year"
                                placeholder="2020"
                                min="1990"
                                max="{{ date('Y') }}"
                                value="{{ old('completion_year') }}"
                                class="input-field"
                            >
                        </div>

                        {{-- Portfolio URL --}}
                        <div class="mb-4">
                            <label>Portfolio / Website (Optional)</label>
                            <input
                                type="url"
                                name="portfolio_url"
                                placeholder="https://yourportfolio.com"
                                value="{{ old('portfolio_url') }}"
                                class="input-field"
                            >
                        </div>

                        {{-- Certificate Upload --}}
                        <div class="mb-4">
                            <label>Certificate / Credential Document</label>
                            <div style="border: 2px dashed var(--border); border-radius: 8px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s;" 
                                onclick="document.getElementById('certificate_file').click()"
                                id="cert-drop-zone">
                                <input 
                                    type="file" 
                                    name="certificate_file" 
                                    id="certificate_file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    style="display: none;"
                                >
                                <p style="margin: 0; font-size: 0.85rem; color: var(--muted);">
                                    📄 Click to upload or drag & drop
                                </p>
                                <p style="margin: 0.5rem 0 0 0; font-size: 0.75rem; color: #999;">
                                    PDF, JPG, or PNG (Max 5MB)
                                </p>
                                <p id="cert-file-name" style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: var(--accent); font-weight: 500;"></p>
                            </div>
                        </div>

                        {{-- ID Proof Upload --}}
                        <div class="mb-4">
                            <label>ID Proof / Document</label>
                            <div style="border: 2px dashed var(--border); border-radius: 8px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s;" 
                                onclick="document.getElementById('identity_file').click()"
                                id="id-drop-zone">
                                <input 
                                    type="file" 
                                    name="identity_file" 
                                    id="identity_file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    style="display: none;"
                                >
                                <p style="margin: 0; font-size: 0.85rem; color: var(--muted);">
                                    🪪 Click to upload or drag & drop
                                </p>
                                <p style="margin: 0.5rem 0 0 0; font-size: 0.75rem; color: #999;">
                                    PDF, JPG, or PNG (Max 5MB)
                                </p>
                                <p id="id-file-name" style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: var(--accent); font-weight: 500;"></p>
                            </div>
                        </div>

                        <div style="background: var(--accent-light); border-radius: 8px; padding: 0.875rem; font-size: 0.8rem; color: var(--accent); margin-bottom: 1rem;">
                            ✓ Your verification documents will be reviewed by our admin team within 24-48 hours. You'll receive an email notification when approved.
                        </div>
                    </div>

                    {{-- Terms --}}
                    <div class="flex items-start gap-2">
                        <input type="checkbox" name="terms" required>
                        <label>I agree to terms</label>
                    </div>

                    <button class="btn-primary">
                        Create Account →
                    </button>
                </form>

                <p class="text-center text-sm mt-5 pb-2" style="color:var(--muted)">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold" style="color:var(--ink)">Sign in</a>
                </p>

            </div>
        </div>
    </div>

    <script>
        // Role toggle for instructor fields
        const roleStudent = document.getElementById('role-student');
        const roleInstructor = document.getElementById('role-instructor');
        const instructorFields = document.getElementById('instructor-fields');

        function toggleInstructorFields() {
            if (roleInstructor.checked) {
                instructorFields.style.display = 'block';
            } else {
                instructorFields.style.display = 'none';
            }
        }

        roleStudent.addEventListener('change', toggleInstructorFields);
        roleInstructor.addEventListener('change', toggleInstructorFields);

        // File upload handlers
        function setupFileUpload(inputId, dropZoneId, displayId) {
            const input = document.getElementById(inputId);
            const dropZone = document.getElementById(dropZoneId);
            const fileNameDisplay = document.getElementById(displayId);

            // File selection
            input.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    fileNameDisplay.textContent = '✓ ' + e.target.files[0].name;
                    dropZone.style.borderColor = 'var(--accent)';
                    dropZone.style.background = 'var(--accent-light)';
                }
            });

            // Drag and drop
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = 'var(--accent)';
                dropZone.style.background = 'var(--accent-light)';
            });

            dropZone.addEventListener('dragleave', () => {
                if (!input.files.length) {
                    dropZone.style.borderColor = 'var(--border)';
                    dropZone.style.background = 'transparent';
                }
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            });
        }

        setupFileUpload('certificate_file', 'cert-drop-zone', 'cert-file-name');
        setupFileUpload('identity_file', 'id-drop-zone', 'id-file-name');

        // Password strength meter
        function checkStrength(val) {
            const segs = [document.getElementById('seg1'), document.getElementById('seg2'), document.getElementById('seg3'), document.getElementById('seg4')];
            const label = document.getElementById('strength-label');
            const classes = ['active-weak', 'active-fair', 'active-good', 'active-strong'];

            segs.forEach(s => s.className = 'strength-segment');
            if (!val) { label.textContent = 'Enter a password'; label.style.color = 'var(--muted)'; return; }

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const labels = ['Weak', 'Fair', 'Good', 'Strong'];
            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];

            for (let i = 0; i < score; i++) segs[i].classList.add(classes[score - 1]);
            label.textContent = labels[score - 1] || 'Too short';
            label.style.color = colors[score - 1] || '#ef4444';
        }
    </script>
</body>
</html>