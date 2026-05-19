<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HybridLearn — Learn Without Limits</title>
    <meta name="description" content="Web-based hybrid learning platform. Study live, async, or blended — your schedule, your pace.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,700;1,9..144,300;1,9..144,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink:     #0d0f14;
            --paper:   #f5f2eb;
            --cream:   #faf8f3;
            --accent:  #e8512a;
            --accent2: #ff7a52;
            --gold:    #f0b429;
            --muted:   #7a7870;
            --border:  #e2ddd5;
            --dark:    #111318;
            --dark2:   #181c27;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); overflow-x: hidden; }
        .serif { font-family: 'Fraunces', serif; }

        /* ── Noise texture overlay ── */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* ══════════════════ NAVBAR ══════════════════ */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 0 5%;
            height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            transition: background .3s, box-shadow .3s, border-color .3s, color .3s;
            border-bottom: 1px solid transparent;
            background: transparent;
            color: #f0ede6;
        }
        .navbar.scrolled {
            background: rgba(245,242,235,0.96);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-color: var(--border);
            box-shadow: 0 1px 0 rgba(0,0,0,.04);
            color: var(--ink);
        }
        .navbar-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .navbar-logo span { color: inherit; }
        .navbar.scrolled .navbar-logo span { color: var(--ink); }
        .nav-link { color: inherit; }
        .navbar.scrolled .nav-link { color: var(--ink); }
        .nav-actions .btn-ghost {
            color: #f0ede6;
            border-color: rgba(255,255,255,.15);
        }
        .navbar.scrolled .nav-actions .btn-ghost {
            color: var(--ink);
            border-color: var(--border);
            background: transparent;
        }
        .navbar.scrolled .btn-accent { background: var(--accent); }
        .logo-mark {
            width: 36px; height: 36px; border-radius: 9px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .nav-links { display: flex; align-items: center; gap: 6px; }
        .nav-link {
            padding: 8px 14px; border-radius: 8px;
            font-size: .88rem; font-weight: 500;
            color: inherit; text-decoration: none;
            transition: background .15s;
        }
        .nav-link:hover { background: rgba(255,255,255,.1); }
        .navbar.scrolled .nav-link:hover { background: rgba(0,0,0,.05); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn-ghost {
            padding: 9px 18px; border-radius: 9px;
            font-family: 'DM Sans', sans-serif; font-size: .88rem; font-weight: 500;
            color: inherit; background: transparent;
            border: 1.5px solid rgba(255,255,255,.15);
            cursor: pointer; text-decoration: none;
            transition: border-color .15s, background .15s;
        }
        .navbar.scrolled .btn-ghost {
            color: var(--ink);
            border-color: var(--border);
        }
        .btn-ghost:hover { border-color: #aaa; background: rgba(0,0,0,.03); }
        .btn-accent {
            padding: 9px 22px; border-radius: 9px;
            font-family: 'Fraunces', serif; font-size: .88rem; font-weight: 500;
            color: #fff; background: var(--accent);
            border: none; cursor: pointer; text-decoration: none;
            letter-spacing: -.01em;
            transition: background .15s, transform .15s;
            display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-accent:hover { background: #d0441e; transform: translateY(-1px); }
        .btn-accent:active { transform: translateY(0); }

        /* ══════════════════ HERO ══════════════════ */
        .hero {
            min-height: 100vh;
            background: var(--dark);
            position: relative;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 120px 5% 80px;
            overflow: hidden;
        }

        /* Diagonal grid pattern */
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 60px 60px;
            transform: skewY(-8deg) scaleY(1.4);
            transform-origin: top left;
        }

        /* Warm glow blobs */
        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
        }
        .blob-1 { width:500px; height:500px; background:rgba(232,81,42,.12); top:-100px; right:-80px; }
        .blob-2 { width:400px; height:400px; background:rgba(240,180,41,.07); bottom:0; left:10%; }
        .blob-3 { width:300px; height:300px; background:rgba(108,99,255,.08); top:30%; left:-5%; }

        .hero-inner { position: relative; z-index: 2; max-width: 900px; text-align: center; }

        .hero-tag {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(232,81,42,.12);
            border: 1px solid rgba(232,81,42,.25);
            color: var(--accent2);
            padding: 6px 14px; border-radius: 99px;
            font-size: .76rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            margin-bottom: 28px;
        }
        .hero-tag-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.5)} }

        .hero-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(3rem, 7vw, 5.5rem);
            font-weight: 300;
            line-height: 1.05;
            letter-spacing: -.03em;
            color: #f0ede6;
            margin-bottom: 24px;
        }
        .hero-title em { font-style: italic; color: var(--accent2); }
        .hero-title .line-accent {
            display: inline-block;
            background: linear-gradient(90deg, var(--accent), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: 1.1rem; line-height: 1.7;
            color: rgba(240,237,230,.5);
            max-width: 560px; margin: 0 auto 36px;
        }

        .hero-cta-row { display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary {
            padding: 14px 32px; border-radius: 12px;
            font-family: 'Fraunces', serif; font-size: 1rem; font-weight: 500;
            color: #fff; background: var(--accent);
            border: none; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            letter-spacing: -.01em;
            transition: background .15s, transform .15s, box-shadow .15s;
            box-shadow: 0 8px 32px rgba(232,81,42,.3);
        }
        .btn-hero-primary:hover { background: #d0441e; transform: translateY(-2px); box-shadow: 0 12px 40px rgba(232,81,42,.4); }
        .btn-hero-ghost {
            padding: 14px 28px; border-radius: 12px;
            font-size: .92rem; font-weight: 500;
            color: rgba(240,237,230,.7);
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            transition: background .15s, border-color .15s;
        }
        .btn-hero-ghost:hover { background: rgba(255,255,255,.09); border-color: rgba(255,255,255,.2); color: #f0ede6; }

        /* Floating course card */
        .hero-card-float {
            position: absolute; right: 5%; top: 50%;
            transform: translateY(-50%);
            z-index: 3;
            animation: floatY 4s ease-in-out infinite;
        }
        @keyframes floatY { 0%,100%{transform:translateY(-50%)} 50%{transform:translateY(calc(-50% - 12px))} }
        .float-card {
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 16px;
            padding: 18px;
            width: 240px;
            box-shadow: 0 24px 60px rgba(0,0,0,.4);
        }
        .float-card-thumb {
            width: 100%; height: 110px; border-radius: 10px;
            background: linear-gradient(135deg, rgba(232,81,42,.3), rgba(240,180,41,.2));
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px; overflow: hidden;
        }
        .progress-mini { height: 4px; background: rgba(255,255,255,.1); border-radius: 99px; overflow: hidden; margin-top: 8px; }
        .progress-mini-fill { height: 100%; border-radius: 99px; background: var(--accent); }
        .avatar-xs { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .55rem; font-weight: 700; color: #fff; flex-shrink: 0; }

        /* ── Stats bar ── */
        .stats-bar {
            background: var(--paper);
            border-top: 1px solid var(--border);
            padding: 32px 5%;
            display: grid; grid-template-columns: repeat(4,1fr);
            gap: 0;
        }
        .stat-item {
            text-align: center;
            padding: 0 20px;
            border-right: 1px solid var(--border);
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-family: 'Fraunces', serif;
            font-size: 2.4rem; font-weight: 300;
            letter-spacing: -.03em; line-height: 1;
            color: var(--ink);
        }
        .stat-label-sm { font-size: .78rem; color: var(--muted); margin-top: 4px; }

        /* ══════════════════ SECTIONS ══════════════════ */
        section { position: relative; }

        .section-tag {
            display: inline-block;
            font-size: .68rem; font-weight: 700;
            letter-spacing: .14em; text-transform: uppercase;
            color: var(--accent); margin-bottom: 12px;
        }
        .section-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 300; letter-spacing: -.025em;
            line-height: 1.15; color: var(--ink);
            margin-bottom: 14px;
        }
        .section-title em { font-style: italic; color: var(--accent); }
        .section-sub { font-size: 1rem; color: var(--muted); line-height: 1.7; max-width: 520px; }

        /* ══════════════════ HOW IT WORKS ══════════════════ */
        .how-section { padding: 100px 5%; background: var(--cream); }
        .how-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-top: 60px; }
        .how-card {
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 18px;
            padding: 32px 28px;
            position: relative;
            transition: border-color .2s, transform .2s;
        }
        .how-card:hover { border-color: var(--accent); transform: translateY(-4px); }
        .how-num {
            font-family: 'Fraunces', serif;
            font-size: 3.5rem; font-weight: 300;
            line-height: 1; letter-spacing: -.04em;
            color: var(--accent);
            opacity: .25; position: absolute; top: 20px; right: 24px;
        }
        .how-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .how-card h3 { font-family: 'Fraunces', serif; font-size: 1.25rem; font-weight: 500; margin-bottom: 10px; letter-spacing: -.01em; }
        .how-card p { font-size: .88rem; color: var(--muted); line-height: 1.7; }

        /* Mode badges */
        .mode-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 99px;
            font-size: .72rem; font-weight: 600;
            margin-top: 16px;
        }
        .mode-live    { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .mode-async   { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .mode-hybrid  { background: #fde8e0; color: var(--accent); border: 1px solid #fca68a; }

        /* ══════════════════ CATEGORIES ══════════════════ */
        .categories-section { padding: 100px 5%; background: var(--paper); }
        .cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-top: 48px; }
        .cat-card {
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 22px 20px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            transition: all .2s;
        }
        .cat-card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.06); }
        .cat-emoji { font-size: 1.8rem; margin-bottom: 10px; display: block; }
        .cat-name { font-size: .9rem; font-weight: 600; color: var(--ink); margin-bottom: 4px; }
        .cat-count { font-size: .76rem; color: var(--muted); }

        /* ══════════════════ FEATURED COURSES ══════════════════ */
        .courses-section { padding: 100px 5%; background: var(--dark); position: relative; overflow: hidden; }
        .courses-section::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.015) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .courses-section .section-title { color: #f0ede6; }
        .courses-section .section-tag { color: var(--accent2); }
        .courses-section .section-sub { color: rgba(240,237,230,.5); }

        .courses-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
        .course-card {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px; overflow: hidden;
            transition: border-color .2s, transform .2s;
            text-decoration: none; color: inherit;
            display: block;
        }
        .course-card:hover { border-color: rgba(232,81,42,.4); transform: translateY(-4px); }
        .course-thumb {
            width: 100%; height: 160px;
            background: linear-gradient(135deg, rgba(232,81,42,.2), rgba(240,180,41,.1));
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; position: relative;
        }
        .course-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .course-level-badge {
            position: absolute; top: 10px; right: 10px;
            padding: 3px 9px; border-radius: 99px;
            font-size: .65rem; font-weight: 700;
            backdrop-filter: blur(8px);
            background: rgba(0,0,0,.35); color: #fff;
        }
        .course-body { padding: 18px; }
        .course-cat { font-size: .7rem; color: var(--accent2); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
        .course-title { font-family: 'Fraunces', serif; font-size: 1.02rem; font-weight: 500; line-height: 1.35; color: #f0ede6; margin-bottom: 10px; letter-spacing: -.01em; }
        .course-instructor { display: flex; align-items: center; gap: 7px; margin-bottom: 14px; }
        .inst-avatar { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .55rem; font-weight: 700; color: #fff; flex-shrink: 0; }
        .inst-name { font-size: .76rem; color: rgba(240,237,230,.5); }
        .course-meta { display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid rgba(255,255,255,.06); }
        .course-rating { display: flex; align-items: center; gap: 4px; font-size: .78rem; font-weight: 600; color: var(--gold); }
        .course-students { font-size: .75rem; color: rgba(240,237,230,.4); }
        .course-price { font-family: 'Fraunces', serif; font-size: 1.1rem; font-weight: 500; color: #f0ede6; }
        .course-price.free { color: var(--gold); }

        /* ══════════════════ TESTIMONIALS ══════════════════ */
        .testimonials-section { padding: 100px 5%; background: var(--cream); overflow: hidden; }
        .testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
        .testi-card {
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 16px; padding: 26px;
            transition: border-color .2s;
        }
        .testi-card:hover { border-color: var(--accent); }
        .testi-stars { display: flex; gap: 3px; margin-bottom: 14px; }
        .testi-star { font-size: 13px; }
        .testi-comment { font-size: .88rem; line-height: 1.75; color: var(--ink); margin-bottom: 18px; font-style: italic; }
        .testi-author { display: flex; align-items: center; gap: 10px; }
        .testi-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700; color: #fff; flex-shrink: 0; }
        .testi-course { font-size: .72rem; color: var(--muted); margin-top: 2px; }

        /* ══════════════════ INSTRUCTOR CTA ══════════════════ */
        .instructor-section {
            padding: 100px 5%;
            background: var(--paper);
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 80px; align-items: center;
        }
        .instructor-list { list-style: none; margin-top: 28px; display: flex; flex-direction: column; gap: 14px; }
        .instructor-list li { display: flex; align-items: flex-start; gap: 10px; font-size: .9rem; color: var(--muted); line-height: 1.5; }
        .check-icon { width: 20px; height: 20px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .instructor-cards-demo { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .inst-demo-card {
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 12px; padding: 16px;
            text-align: center;
        }
        .inst-demo-avatar { width: 48px; height: 48px; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: .9rem; font-weight: 700; color: #fff; }
        .inst-demo-name { font-size: .82rem; font-weight: 600; margin-bottom: 3px; }
        .inst-demo-stat { font-size: .74rem; color: var(--muted); }

        /* ══════════════════ FINAL CTA ══════════════════ */
        .final-cta {
            padding: 120px 5%;
            background: var(--dark);
            text-align: center;
            position: relative; overflow: hidden;
        }
        .final-cta::before {
            content: '';
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            width: 700px; height: 400px; border-radius: 50%;
            background: radial-gradient(ellipse, rgba(232,81,42,.12) 0%, transparent 70%);
        }
        .final-cta .section-title { color: #f0ede6; font-size: clamp(2.2rem, 5vw, 3.8rem); }
        .final-cta .section-sub { color: rgba(240,237,230,.45); margin: 0 auto 40px; }

        /* ══════════════════ FOOTER ══════════════════ */
        .footer {
            background: var(--ink);
            color: rgba(240,237,230,.5);
            padding: 60px 5% 32px;
        }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand p { font-size: .84rem; line-height: 1.7; margin-top: 14px; max-width: 240px; }
        .footer-col h4 { font-size: .78rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: rgba(240,237,230,.3); margin-bottom: 16px; }
        .footer-col a { display: block; font-size: .85rem; color: rgba(240,237,230,.5); text-decoration: none; margin-bottom: 9px; transition: color .15s; }
        .footer-col a:hover { color: rgba(240,237,230,.9); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.07); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; }
        .footer-bottom p { font-size: .8rem; }

        /* ══════════════════ ANIMATIONS ══════════════════ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease both; }
        .d1 { animation-delay: .1s; } .d2 { animation-delay: .2s; }
        .d3 { animation-delay: .3s; } .d4 { animation-delay: .4s; }
        .d5 { animation-delay: .5s; }

        /* Scroll reveal */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ══════════════════ MISC ══════════════════ */
        .divider-line { width: 40px; height: 2px; background: var(--accent); border-radius: 99px; margin-bottom: 20px; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════ --}}
<nav class="navbar" id="navbar">
    <a href="{{ route('home') }}" class="navbar-logo">
        <div class="logo-mark">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
            </svg>
        </div>
        <span class="serif" style="font-size:1.05rem;font-weight:500;letter-spacing:-.01em">HybridLearn</span>
    </a>

    <div class="nav-links" id="navLinks">
        <a href="{{ route('home') }}" class="nav-link">Home</a>
        <a href="{{ route('courses.index') }}" class="nav-link">Courses</a>
        <a href="#categories" class="nav-link">Categories</a>
        <a href="#how-it-works" class="nav-link">How it works</a>
        <a href="{{ route('instructor.landing') }}" class="nav-link">Become Instructor</a>
    </div>

    <div class="nav-actions">
        @auth
            {{-- Profile Dropdown (GitHub Style) --}}
            <div style="position: relative; display: inline-block;" class="profile-dropdown-wrapper">
                <button onclick="toggleProfileDropdown()" class="profile-btn" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 6px 8px;
                    border-radius: 20px;
                    border: 1.5px solid transparent;
                    background: transparent;
                    cursor: pointer;
                    transition: all 0.2s;
                    color: inherit;
                    font-size: 0.9rem;
                    font-weight: 500;
                ">
                    @if(auth()->user()->oauth_avatar)
                        <img src="{{ auth()->user()->oauth_avatar }}" alt="{{ auth()->user()->name }}" style="
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            object-fit: cover;
                            border: 2px solid var(--accent);
                        ">
                    @else
                        <div style="
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            background: var(--accent);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-weight: 700;
                            font-size: 0.85rem;
                            border: 2px solid var(--accent);
                        ">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    @endif
                    <span>{{ auth()->user()->name }}</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div id="profileDropdown" style="
                    position: absolute;
                    top: 100%;
                    right: 0;
                    margin-top: 8px;
                    background: white;
                    border: 1.5px solid var(--border);
                    border-radius: 12px;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                    min-width: 220px;
                    display: none;
                    z-index: 1000;
                    overflow: hidden;
                " class="navbar.scrolled ? 'scrolled' : ''">
                    {{-- Profile Header --}}
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--border); background: var(--cream);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @if(auth()->user()->oauth_avatar)
                                <img src="{{ auth()->user()->oauth_avatar }}" alt="{{ auth()->user()->name }}" style="
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    object-fit: cover;
                                    border: 2px solid var(--accent);
                                ">
                            @else
                                <div style="
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    background: var(--accent);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-weight: 700;
                                    font-size: 1rem;
                                    border: 2px solid var(--accent);
                                ">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            @endif
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.9rem; color: var(--ink); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</div>
                                <div style="font-size: 0.8rem; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <a href="{{ route('profile.show') }}" style="
                        display: block;
                        padding: 12px 16px;
                        color: var(--ink);
                        text-decoration: none;
                        font-size: 0.9rem;
                        transition: background 0.15s;
                    " onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                        👤 Your Profile
                    </a>

                    @if(auth()->user()->role === 'instructor')
                        <a href="{{ route('instructor.dashboard') }}" style="
                            display: block;
                            padding: 12px 16px;
                            color: var(--ink);
                            text-decoration: none;
                            font-size: 0.9rem;
                            transition: background 0.15s;
                        " onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                            📊 Dashboard
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" style="
                            display: block;
                            padding: 12px 16px;
                            color: var(--ink);
                            text-decoration: none;
                            font-size: 0.9rem;
                            transition: background 0.15s;
                        " onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                            ⚙️ Admin Panel
                        </a>
                    <!-- @else
                        <a href="{{ route('dashboard') }}" style="
                            display: block;
                            padding: 12px 16px;
                            color: var(--ink);
                            text-decoration: none;
                            font-size: 0.9rem;
                            transition: background 0.15s;
                        " onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                            📚 My Learning
                        </a> -->
                    @endif

                    <div style="padding: 8px 0; border-top: 1px solid var(--border);">
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="
                                width: 100%;
                                padding: 12px 16px;
                                color: #dc2626;
                                background: transparent;
                                border: none;
                                text-align: left;
                                font-size: 0.9rem;
                                cursor: pointer;
                                transition: background 0.15s;
                            " onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                                🚪 Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
            <a href="{{ route('register') }}" class="btn-accent">Get Started</a>
        @endauth
    </div>
</nav>


{{-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-blob blob-1"></div>
    <div class="hero-blob blob-2"></div>
    <div class="hero-blob blob-3"></div>

    <div class="hero-inner">
        <div class="hero-tag fade-up">
            <span class="hero-tag-dot"></span>
            Web-based Hybrid Learning
        </div>

        <h1 class="hero-title fade-up d1">
            Learn <em>anything</em>, <br>
            <span class="line-accent">your way.</span>
        </h1>

        <p class="hero-sub fade-up d2">
            Join thousands of learners in live sessions, self-paced courses, and hybrid classrooms — all in one place built for the modern student.
        </p>

        <div class="hero-cta-row fade-up d3">
            <a href="{{ route('register') }}" class="btn-hero-primary">
                Start for Free
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="{{ route('courses.index') }}" class="btn-hero-ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Browse Courses
            </a>
        </div>

        {{-- Trust avatars --}}
        <div class="fade-up d4" style="display:flex;align-items:center;justify-content:center;gap:12px;margin-top:36px">
            <div style="display:flex">
                @php $avatarColors=['#e8512a','#3b82f6','#10b981','#8b5cf6']; @endphp
                @foreach($avatarColors as $i => $c)
                <div style="width:28px;height:28px;border-radius:50%;background:{{ $c }};border:2px solid var(--dark);display:flex;align-items:center;justify-content:center;font-size:.55rem;font-weight:700;color:#fff;margin-left:{{ $i>0?'-8px':'0' }}">
                    {{ chr(65+$i) }}
                </div>
                @endforeach
            </div>
            <span style="font-size:.8rem;color:rgba(240,237,230,.45)">
                <strong style="color:rgba(240,237,230,.8)">{{ number_format($stats['students']) }}+</strong> students already enrolled
            </span>
        </div>
    </div>

    {{-- Floating course preview card --}}
    <div class="hero-card-float fade-up d5" style="display:none" id="heroFloatCard">
        <div class="float-card">
            <div class="float-card-thumb">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="rgba(232,81,42,.7)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
            </div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.5);margin-bottom:4px">Currently learning</div>
            <div style="font-size:.84rem;font-weight:600;color:#f0ede6;line-height:1.3">UX Design Fundamentals</div>
            <div class="progress-mini" style="margin-top:10px">
                <div class="progress-mini-fill" style="width:64%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:.7rem;color:rgba(255,255,255,.35)">
                <span>Module 5 / 8</span><span>64%</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.07)">
                <div class="avatar-xs" style="background:#e8512a">SK</div>
                <span style="font-size:.72rem;color:rgba(255,255,255,.4)">Live session in 2h</span>
                <span style="margin-left:auto;width:7px;height:7px;border-radius:50%;background:#34d399;flex-shrink:0"></span>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════
     STATS BAR
══════════════════════════════════════════ --}}
<div class="stats-bar">
    <div class="stat-item reveal">
        <div class="stat-num">{{ number_format($stats['students']) }}+</div>
        <div class="stat-label-sm">Active Students</div>
    </div>
    <div class="stat-item reveal">
        <div class="stat-num">{{ number_format($stats['courses']) }}+</div>
        <div class="stat-label-sm">Published Courses</div>
    </div>
    <div class="stat-item reveal">
        <div class="stat-num">{{ number_format($stats['instructors']) }}+</div>
        <div class="stat-label-sm">Expert Instructors</div>
    </div>
    <div class="stat-item reveal">
        <div class="stat-num">{{ number_format($stats['enrollments']) }}+</div>
        <div class="stat-label-sm">Enrollments</div>
    </div>
</div>


{{-- ══════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════ --}}
<section class="how-section" id="how-it-works">
    <div style="max-width:600px">
        <div class="section-tag reveal">How it works</div>
        <div class="divider-line reveal"></div>
        <h2 class="section-title reveal">Three ways to <em>learn</em></h2>
        <p class="section-sub reveal">HybridLearn supports every learning style — attend live, go at your own pace, or blend both. One platform, total flexibility.</p>
    </div>

    <div class="how-grid">
        <div class="how-card reveal">
            <div class="how-num">01</div>
            <div class="how-icon" style="background:#ecfdf5">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
            </div>
            <h3>Live Sessions</h3>
            <p>Join real-time virtual classrooms with your instructor and classmates. Ask questions, get instant feedback, and collaborate in the moment.</p>
            <span class="mode-badge mode-live">● Live</span>
        </div>

        <div class="how-card reveal">
            <div class="how-num">02</div>
            <div class="how-icon" style="background:#eff6ff">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3>Self-Paced (Async)</h3>
            <p>Learn on your own schedule. Watch pre-recorded lectures, complete assignments, and progress through modules at the speed that works for you.</p>
            <span class="mode-badge mode-async">⏱ Async</span>
        </div>

        <div class="how-card reveal">
            <div class="how-num">03</div>
            <div class="how-icon" style="background:#fde8e0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
            </div>
            <h3>Hybrid Blend</h3>
            <p>The best of both worlds. Combine live interaction with flexible async content — structured enough to stay on track, flexible enough for real life.</p>
            <span class="mode-badge mode-hybrid">⚡ Hybrid</span>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════
     CATEGORIES
══════════════════════════════════════════ --}}
<section class="categories-section" id="categories">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:20px">
        <div>
            <div class="section-tag reveal">Browse by topic</div>
            <div class="divider-line reveal"></div>
            <h2 class="section-title reveal">Find your <em>focus</em></h2>
        </div>
        <a href="{{ route('courses.index') }}" style="font-size:.88rem;font-weight:600;color:var(--accent);text-decoration:none;white-space:nowrap">See all courses →</a>
    </div>

    @php
        $catEmojis = ['💻','🎨','📊','🔬','🎵','📸','✍️','🌐','🧠','📱','🏛️','⚙️'];
        $catDefault = ['Technology','Design','Business','Science','Music','Photography','Writing','Marketing','Psychology','Mobile Dev','Architecture','Engineering'];
    @endphp
    <div class="cat-grid">
        @forelse($categories as $i => $cat)
        <a href="{{ route('courses.index', ['category' => $cat->slug]) }}" class="cat-card reveal">
            <span class="cat-emoji">{{ $catEmojis[$i % 12] }}</span>
            <div class="cat-name">{{ $cat->name }}</div>
            <div class="cat-count">{{ number_format($cat->courses_count) }} {{ Str::plural('course', $cat->courses_count) }}</div>
        </a>
        @empty
        @foreach(array_slice($catDefault, 0, 8) as $i => $name)
        <div class="cat-card reveal">
            <span class="cat-emoji">{{ $catEmojis[$i] }}</span>
            <div class="cat-name">{{ $name }}</div>
            <div class="cat-count">Coming soon</div>
        </div>
        @endforeach
        @endforelse
    </div>
</section>


{{-- ══════════════════════════════════════════
     FEATURED COURSES
══════════════════════════════════════════ --}}
<section class="courses-section" id="courses">
    <div style="position:relative;z-index:2">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:20px;margin-bottom:0">
            <div>
                <div class="section-tag reveal">Most popular</div>
                <div style="width:40px;height:2px;background:var(--accent);border-radius:99px;margin-bottom:20px" class="reveal"></div>
                <h2 class="section-title reveal">Top-rated <em>courses</em></h2>
                <p class="section-sub reveal">Hand-picked by learners. Courses with the highest enrollment and satisfaction ratings.</p>
            </div>
            <a href="{{ route('courses.index') }}" style="font-size:.88rem;font-weight:600;color:var(--accent2);text-decoration:none;white-space:nowrap;z-index:2">Browse all →</a>
        </div>

        <div class="courses-grid">
            @forelse($featuredCourses as $course)
            @php
                $instColors = ['#e8512a','#3b82f6','#10b981','#8b5cf6','#f59e0b','#ec4899'];
                $ci = $loop->index % 6;
            @endphp
            <a href="{{ route('courses.show', $course->slug) }}" class="course-card reveal">
                <div class="course-thumb">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}">
                    @else
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(232,81,42,.4)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    @endif
                    <span class="course-level-badge">{{ ucfirst($course->level) }}</span>
                </div>
                <div class="course-body">
                    <div class="course-cat">{{ $course->category?->name ?? 'General' }}</div>
                    <div class="course-title">{{ Str::limit($course->title, 52) }}</div>
                    <div class="course-instructor">
                        <div class="inst-avatar" style="background:{{ $instColors[$ci] }}">
                            {{ strtoupper(substr($course->instructor?->name ?? '?', 0, 2)) }}
                        </div>
                        <span class="inst-name">{{ $course->instructor?->name ?? 'Instructor' }}</span>
                    </div>
                    <div class="course-meta">
                        <div>
                            <div class="course-rating">
                                ★ {{ number_format($course->reviews_avg_rating ?? 0, 1) }}
                                <span style="font-weight:400;color:rgba(240,237,230,.3);margin-left:3px">({{ number_format($course->reviews_count ?? 0) }})</span>
                            </div>
                            <div class="course-students">{{ number_format($course->enrollments_count) }} students</div>
                        </div>
                        <div class="course-price {{ $course->price == 0 ? 'free' : '' }}">
                            {{ $course->price == 0 ? 'Free' : '$'.number_format($course->price, 2) }}
                        </div>
                    </div>
                </div>
            </a>
            @empty
            {{-- Placeholder cards if no courses yet --}}
            @foreach(range(1,6) as $n)
            <div class="course-card reveal" style="cursor:default">
                <div class="course-thumb"></div>
                <div class="course-body">
                    <div class="course-cat" style="background:rgba(255,255,255,.05);height:10px;border-radius:4px;width:60px;margin-bottom:10px"></div>
                    <div style="background:rgba(255,255,255,.05);height:14px;border-radius:4px;margin-bottom:8px"></div>
                    <div style="background:rgba(255,255,255,.03);height:10px;border-radius:4px;width:70%"></div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════
     TESTIMONIALS
══════════════════════════════════════════ --}}
<section class="testimonials-section">
    <div style="text-align:center;max-width:600px;margin:0 auto">
        <div class="section-tag reveal">Student stories</div>
        <div style="width:40px;height:2px;background:var(--accent);border-radius:99px;margin:0 auto 20px" class="reveal"></div>
        <h2 class="section-title reveal">What our <em>learners</em> say</h2>
        <p class="section-sub reveal" style="margin:0 auto">Real feedback from students who've transformed their skills with HybridLearn.</p>
    </div>

    @php
        $testiColors = ['#e8512a','#3b82f6','#10b981','#8b5cf6','#f59e0b','#ec4899'];
        $fallbackTestimonials = [
            ['name'=>'Sokha Meng','comment'=>'The hybrid format is exactly what I needed. I can join live when I have time, and catch up async when life gets busy.','rating'=>5,'course'=>'Full-Stack Web Development'],
            ['name'=>'Chan Virak','comment'=>'Best investment I\'ve made. The instructors are world-class and the platform is beautifully designed.','rating'=>5,'course'=>'UX Design Fundamentals'],
            ['name'=>'Dara Lim','comment'=>'I\'ve tried other platforms but HybridLearn\'s live sessions make a huge difference. Feels like a real classroom.','rating'=>5,'course'=>'Data Science Basics'],
            ['name'=>'Phal Sophea','comment'=>'Completed 3 courses and got a new job! The certificates are respected by employers here.','rating'=>5,'course'=>'Digital Marketing'],
            ['name'=>'Rathana Kong','comment'=>'The progress tracking keeps me motivated. Love seeing my completion rate go up every week.','rating'=>4,'course'=>'Machine Learning'],
            ['name'=>'Bopha Srun','comment'=>'Incredibly well structured courses. The instructors respond quickly and the community is supportive.','rating'=>5,'course'=>'Photography Masterclass'],
        ];
    @endphp

    <div class="testimonials-grid">
        @forelse($testimonials as $i => $review)
        <div class="testi-card reveal">
            <div class="testi-stars">
                @for($s = 1; $s <= 5; $s++)
                    <span class="testi-star" style="color:{{ $s <= $review->rating ? '#f0b429' : '#e2ddd5' }}">★</span>
                @endfor
            </div>
            <p class="testi-comment">"{{ Str::limit($review->comment ?? $review->title, 120) }}"</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:{{ $testiColors[$i % 6] }}">
                    {{ strtoupper(substr($review->user?->name ?? '?', 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:.85rem;font-weight:600">{{ $review->user?->name ?? 'Student' }}</div>
                    <div class="testi-course">{{ Str::limit($review->course?->title ?? '—', 30) }}</div>
                </div>
            </div>
        </div>
        @empty
        @foreach($fallbackTestimonials as $i => $t)
        <div class="testi-card reveal">
            <div class="testi-stars">
                @for($s = 1; $s <= 5; $s++)
                    <span class="testi-star" style="color:{{ $s <= $t['rating'] ? '#f0b429' : '#e2ddd5' }}">★</span>
                @endfor
            </div>
            <p class="testi-comment">"{{ $t['comment'] }}"</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:{{ $testiColors[$i % 6] }}">
                    {{ strtoupper(substr($t['name'], 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:.85rem;font-weight:600">{{ $t['name'] }}</div>
                    <div class="testi-course">{{ $t['course'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
        @endforelse
    </div>
</section>


{{-- ══════════════════════════════════════════
     BECOME AN INSTRUCTOR
══════════════════════════════════════════ --}}
<section class="instructor-section">
    <div class="reveal">
        <div class="section-tag">For educators</div>
        <div class="divider-line"></div>
        <h2 class="section-title">Share your knowledge,<br><em>earn on your terms.</em></h2>
        <p class="section-sub">Create live, async, or hybrid courses. Reach thousands of eager learners and build a sustainable income from teaching what you love.</p>

        <ul class="instructor-list">
            @foreach(['Keep up to 80% of every sale', 'Full control over pricing and curriculum', 'Live session tools built right in', 'Analytics dashboard to track your growth', 'Dedicated instructor support team'] as $benefit)
            <li>
                <div class="check-icon">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                {{ $benefit }}
            </li>
            @endforeach
        </ul>

        <div style="margin-top:32px;display:flex;gap:12px;flex-wrap:wrap">
            <a href="{{ route('instructor.landing') }}" class="btn-accent" style="font-size:.92rem;padding:12px 26px">
                Start Teaching
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="{{ route('instructor.landing') }}#learn-more" style="font-size:.88rem;font-weight:500;color:var(--muted);text-decoration:none;display:flex;align-items:center;gap:6px;padding:12px 0">
                Learn more →
            </a>
        </div>
    </div>

    {{-- Instructor demo cards --}}
    <div class="reveal">
        @php $icols=['#e8512a','#3b82f6','#10b981','#8b5cf6']; @endphp
        <div class="instructor-cards-demo">
            @forelse($topInstructors as $i => $inst)
            <div class="inst-demo-card">
                <div class="inst-demo-avatar" style="background:{{ $icols[$i % 4] }}">
                    {{ strtoupper(substr($inst->name, 0, 2)) }}
                </div>
                <div class="inst-demo-name">{{ Str::limit($inst->name, 16) }}</div>
                <div class="inst-demo-stat">{{ $inst->courses_count }} {{ Str::plural('course', $inst->courses_count) }}</div>
            </div>
            @empty
            @foreach([['name'=>'Sokha Chan','courses'=>12],['name'=>'Virak Phan','courses'=>8],['name'=>'Lina Ros','courses'=>15],['name'=>'Dara Kong','courses'=>6]] as $i => $inst)
            <div class="inst-demo-card">
                <div class="inst-demo-avatar" style="background:{{ $icols[$i] }}">
                    {{ strtoupper(substr($inst['name'], 0, 2)) }}
                </div>
                <div class="inst-demo-name">{{ $inst['name'] }}</div>
                <div class="inst-demo-stat">{{ $inst['courses'] }} courses</div>
            </div>
            @endforeach
            @endforelse
        </div>

        {{-- Big earnings highlight --}}
        <div style="background:linear-gradient(135deg,#1a0f08,#0d0f14);border:1.5px solid rgba(232,81,42,.2);border-radius:16px;padding:28px;margin-top:14px;text-align:center">
            <div style="font-size:.72rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:rgba(232,81,42,.7);margin-bottom:12px">Instructors collectively earned</div>
            <div class="serif" style="font-size:2.8rem;font-weight:300;letter-spacing:-.03em;color:#f0ede6;line-height:1">$240,000+</div>
            <div style="font-size:.8rem;color:rgba(240,237,230,.35);margin-top:8px">paid out to our educators this year</div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════
     FINAL CTA
══════════════════════════════════════════ --}}
<section class="final-cta">
    <div style="position:relative;z-index:2">
        <div class="section-tag" style="color:var(--accent2)">Ready?</div>
        <h2 class="section-title serif reveal" style="color:#f0ede6;margin-bottom:16px">
            Your next chapter<br><em>starts today.</em>
        </h2>
        <p class="section-sub reveal">Join {{ number_format($stats['students']) }}+ learners already building their future on HybridLearn. Free to join — always.</p>

        <div style="display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap" class="reveal">
            <a href="{{ route('register') }}" class="btn-hero-primary" style="font-size:1rem;padding:15px 36px">
                Create Free Account
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="{{ route('courses.index') }}" class="btn-hero-ghost">
                Browse Courses
            </a>
        </div>

        <p style="font-size:.78rem;color:rgba(240,237,230,.25);margin-top:18px" class="reveal">No credit card required · Cancel anytime</p>
    </div>
</section>


{{-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ --}}
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
                <div class="logo-mark" style="width:30px;height:30px;border-radius:7px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <span class="serif" style="color:#f0ede6;font-size:.95rem;font-weight:500">HybridLearn</span>
            </div>
            <p>Web-based hybrid learning platform. Learn live, async, or blended — your schedule, your pace.</p>
            <div style="display:flex;gap:12px;margin-top:16px">
                @foreach(['Twitter','LinkedIn','YouTube','Facebook'] as $social)
                <a href="#" style="width:32px;height:32px;border-radius:8px;border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:rgba(240,237,230,.4);text-decoration:none;transition:border-color .15s" onmouseover="this.style.borderColor='rgba(255,255,255,.25)'" onmouseout="this.style.borderColor='rgba(255,255,255,.1)'">
                    {{ substr($social, 0, 2) }}
                </a>
                @endforeach
            </div>
        </div>

        <div class="footer-col">
            <h4>Learn</h4>
            <a href="{{ route('courses.index') }}">Browse Courses</a>
            <a href="#categories">Categories</a>
            <a href="#">Learning Paths</a>
            <a href="#">Live Schedule</a>
            <a href="#">Certificates</a>
        </div>

        <div class="footer-col">
            <h4>Teach</h4>
            <a href="{{ route('instructor.landing') }}">Become an Instructor</a>
            <a href="#">Instructor Handbook</a>
            <a href="#">Pricing & Payouts</a>
            <a href="#">Creator Resources</a>
        </div>

        <div class="footer-col">
            <h4>Company</h4>
            <a href="#">About Us</a>
            <a href="#">Careers</a>
            <a href="#">Blog</a>
            <a href="#">Help Center</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} HybridLearn. All rights reserved.</p>
        <p>Made with ❤ for learners everywhere.</p>
    </div>
</footer>


{{-- ══════════════════════════════════════════
     JS
══════════════════════════════════════════ --}}
<script>
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });

    // Profile dropdown toggle
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const wrapper = document.querySelector('.profile-dropdown-wrapper');
        const dropdown = document.getElementById('profileDropdown');
        if (wrapper && !wrapper.contains(e.target) && dropdown) {
            dropdown.style.display = 'none';
        }
    });

    // Show float card on desktop
    if (window.innerWidth > 1100) {
        document.getElementById('heroFloatCard').style.display = 'block';
    }

    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(el => observer.observe(el));

    // Stagger children inside grids
    document.querySelectorAll('.how-grid .how-card, .cat-grid .cat-card, .courses-grid .course-card, .testimonials-grid .testi-card, .instructor-cards-demo .inst-demo-card').forEach((el, i) => {
        el.style.transitionDelay = (i % 3) * 0.08 + 's';
    });
</script>
</body>
</html>