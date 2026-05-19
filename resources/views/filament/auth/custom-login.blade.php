<x-filament-panels::page.simple>

{{-- Suppress Filament's default heading & subheading --}}
<x-slot name="heading"></x-slot>
<x-slot name="subheading"></x-slot>

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/*  Full-screen takeover  */
.lh-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: #030812;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    font-family: 'Sora', system-ui, sans-serif;
}

/* Ambient background blobs */
.lh-ambient {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(ellipse 60% 50% at 20% 60%, rgba(212,160,23,0.07) 0%, transparent 70%),
        radial-gradient(ellipse 40% 60% at 80% 30%, rgba(180,60,10,0.05) 0%, transparent 70%);
}

/* Main two-column card */
.lh-card {
    position: relative;
    z-index: 2;
    width: min(1080px, 100%);
    min-height: min(660px, 90dvh);
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-radius: 26px;
    overflow: hidden;
    box-shadow:
        0 0 0 1px rgba(212,160,23,0.11),
        0 40px 100px rgba(0,0,0,0.85),
        0 0 80px rgba(212,160,23,0.04);
}

/*  LEFT PANEL  */
.lh-left {
    position: relative;
    background: #080f1a;
    border-right: 1px solid rgba(212,160,23,0.11);
    padding: 52px 48px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}

.lh-grid {
    position: absolute; inset: 0; pointer-events: none;
    background-image:
        linear-gradient(rgba(212,160,23,0.038) 1px, transparent 1px),
        linear-gradient(90deg, rgba(212,160,23,0.038) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 100%);
}

.lh-orb {
    position: absolute; border-radius: 50%;
    filter: blur(64px); pointer-events: none;
}
.lh-orb-1 {
    width: 340px; height: 340px;
    background: radial-gradient(circle, rgba(212,160,23,0.13), transparent 70%);
    top: -80px; left: -90px;
}
.lh-orb-2 {
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(180,60,10,0.11), transparent 70%);
    bottom: 20px; right: -50px;
}

.lh-geo {
    position: absolute; pointer-events: none;
    border: 1px solid rgba(212,160,23,0.15); border-radius: 4px;
}
.lh-geo-1 { width:58px; height:58px; top:22%; right:13%; transform:rotate(22deg); animation:lhFloat1 9s ease-in-out infinite; }
.lh-geo-2 { width:28px; height:28px; top:56%; left:9%;  transform:rotate(45deg); border-color:rgba(212,160,23,0.07); animation:lhFloat2 7s ease-in-out infinite; }
.lh-geo-3 { width:72px; height:72px; bottom:17%; right:19%; border-radius:18px; border-color:rgba(255,255,255,0.04); animation:lhFloat1 11s ease-in-out infinite reverse; }
@keyframes lhFloat1 { 0%,100%{transform:translateY(0) rotate(22deg)} 50%{transform:translateY(-13px) rotate(26deg)} }
@keyframes lhFloat2 { 0%,100%{transform:translateY(0) rotate(45deg)} 50%{transform:translateY(-7px)  rotate(49deg)} }

.lh-logo-mark {
    width:50px; height:50px; border-radius:13px; flex-shrink:0;
    background: linear-gradient(135deg, #e8c44a, #b8860b);
    display:flex; align-items:center; justify-content:center;
    font-family:'Cormorant Garamond',Georgia,serif;
    font-size:24px; font-weight:700; color:#030812;
    box-shadow: 0 0 28px rgba(212,160,23,0.32), 0 4px 14px rgba(0,0,0,0.4);
}

.lh-shimmer {
    background: linear-gradient(90deg,
        rgba(255,255,255,0.5) 0%,
        rgba(212,160,23,0.92) 30%,
        rgba(255,255,255,0.92) 50%,
        rgba(212,160,23,0.92) 70%,
        rgba(255,255,255,0.5) 100%
    );
    background-size: 200% auto;
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: lhShimmer 4s linear infinite;
}
@keyframes lhShimmer { 0%{background-position:200% center} 100%{background-position:-200% center} }

.lh-stat {
    flex:1;
    background: rgba(212,160,23,0.04);
    border: 1px solid rgba(212,160,23,0.12);
    border-radius:14px; padding:15px 16px;
    transition: border-color .3s, background .3s;
}
.lh-stat:hover { border-color:rgba(212,160,23,0.28); background:rgba(212,160,23,0.07); }

.lh-a1 { opacity:0; animation: lhUp .7s  .05s cubic-bezier(.16,1,.3,1) forwards; }
.lh-a2 { opacity:0; animation: lhUp .7s  .18s cubic-bezier(.16,1,.3,1) forwards; }
.lh-a3 { opacity:0; animation: lhUp .7s  .30s cubic-bezier(.16,1,.3,1) forwards; }
.lh-a4 { opacity:0; animation: lhUp .7s  .42s cubic-bezier(.16,1,.3,1) forwards; }
@keyframes lhUp { 0%{opacity:0;transform:translateY(20px)} 100%{opacity:1;transform:translateY(0)} }

/*  RIGHT PANEL  */
.lh-right {
    background: #03060f;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 52px 48px;
}

.lh-form-wrap {
    width: 100%; max-width: 360px;
    opacity: 0;
    animation: lhUp .9s .22s cubic-bezier(.16,1,.3,1) forwards;
}

.lh-form-icon {
    width:64px; height:64px; border-radius:18px; margin:0 auto 26px;
    background: linear-gradient(135deg, #e8c44a, #b8860b);
    display:flex; align-items:center; justify-content:center;
    font-family:'Cormorant Garamond',Georgia,serif;
    font-size:30px; font-weight:700; color:#030812;
    box-shadow: 0 0 36px rgba(212,160,23,0.22), 0 8px 22px rgba(0,0,0,0.5);
}

.lh-field { margin-bottom: 16px; }
.lh-label {
    display:block; margin-bottom:7px;
    font-size:10.5px; font-weight:600;
    letter-spacing:.11em; text-transform:uppercase;
    color:rgba(255,255,255,0.32);
}
.lh-input {
    width:100%;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius:11px; padding:12px 15px;
    font-family:'Sora',system-ui,sans-serif;
    font-size:14px; color:rgba(255,255,255,0.9);
    outline:none; caret-color:#d4a017;
    transition: border-color .22s, background .22s, box-shadow .22s;
}
.lh-input::placeholder { color:rgba(255,255,255,0.2); }
.lh-input:hover { border-color:rgba(212,160,23,0.22); background:rgba(255,255,255,0.055); }
.lh-input:focus {
    border-color:rgba(212,160,23,0.52);
    background:rgba(212,160,23,0.035);
    box-shadow: 0 0 0 4px rgba(212,160,23,0.08);
}

.lh-check { width:16px; height:16px; flex-shrink:0; accent-color:#d4a017; cursor:pointer; }

.lh-btn {
    width:100%; padding:13px; border:none; border-radius:11px;
    cursor:pointer; font-family:'Sora',system-ui,sans-serif;
    font-size:13.5px; font-weight:600; letter-spacing:.03em; color:#030812;
    background: linear-gradient(135deg, #eacc5a 0%, #d4a017 45%, #b8860b 100%);
    background-size:200% 100%; background-position:100% 0;
    box-shadow: 0 4px 22px rgba(212,160,23,0.28);
    transition: background-position .4s, box-shadow .3s, transform .14s;
}
.lh-btn:hover { background-position:0% 0; box-shadow:0 6px 30px rgba(212,160,23,0.42); transform:translateY(-1px); }
.lh-btn:active { transform:translateY(0); }

.lh-divider { display:flex; align-items:center; gap:12px; margin:18px 0; }
.lh-div-line { flex:1; height:1px; background:rgba(255,255,255,0.055); }
.lh-div-txt { font-size:10px; color:rgba(255,255,255,0.18); letter-spacing:.09em; }

@media (max-width: 720px) {
    .lh-card { grid-template-columns:1fr; min-height:auto; }
    .lh-left { display:none; }
    .lh-right { padding:40px 28px; }
}
</style>
<div class="lh-overlay">
    <div class="lh-ambient" aria-hidden="true"></div>

    <div class="lh-card">

        {{-- ══ LEFT PANEL ══ --}}
        <div class="lh-left">
            <div class="lh-grid"  aria-hidden="true"></div>
            <div class="lh-orb lh-orb-1" aria-hidden="true"></div>
            <div class="lh-orb lh-orb-2" aria-hidden="true"></div>
            <div class="lh-geo lh-geo-1" aria-hidden="true"></div>
            <div class="lh-geo lh-geo-2" aria-hidden="true"></div>
            <div class="lh-geo lh-geo-3" aria-hidden="true"></div>

            {{-- Brand --}}
            <div class="lh-a1" style="position:relative;z-index:2;">
                <div style="display:flex;align-items:center;gap:15px;">
                    <div class="lh-logo-mark">HLP</div>
                    <div>
                        <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:21px;font-weight:700;color:rgba(255,255,255,0.94);line-height:1.1;letter-spacing:-.01em;">Hybrid Learning Platform</p>
                        <p style="font-size:10px;font-weight:500;letter-spacing:.13em;text-transform:uppercase;color:rgba(212,160,23,0.68);margin-top:3px;">Admin Console</p>
                    </div>
                </div>
            </div>

            {{-- Headline --}}
            <div class="lh-a2" style="position:relative;z-index:2;">
                <p style="font-size:10px;font-weight:600;letter-spacing:.15em;text-transform:uppercase;color:rgba(212,160,23,0.58);margin-bottom:16px;">Hybrid Learning Marketplace</p>
                <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(34px,3.6vw,50px);font-weight:700;line-height:1.08;color:rgba(255,255,255,0.92);">
                    <span class="lh-shimmer">Manage.</span><br>
                    <span style="color:rgba(255,255,255,0.48);font-weight:500;">Analys.</span><br>
                    <span style="font-style:italic;color:rgba(212,160,23,0.82);">Grow.</span>
                </h2>
                <p style="margin-top:16px;font-size:12.5px;line-height:1.7;color:rgba(255,255,255,0.32);max-width:270px;">
                    Your all-in-one admin dashboard for managing Hybrid Learning Platform.
                </p>
            </div>

            {{-- Stats --}}
            <div class="lh-a3" style="position:relative;z-index:2;">
                <div style="display:flex;gap:9px;">
                    <div class="lh-stat">
                        <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:28px;font-weight:700;color:rgba(255,255,255,0.88);line-height:1;">12K+</p>
                        <p style="font-size:10px;color:rgba(212,160,23,0.62);font-weight:500;letter-spacing:.07em;text-transform:uppercase;margin-top:5px;">Students</p>
                    </div>
                    <div class="lh-stat">
                        <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:28px;font-weight:700;color:rgba(255,255,255,0.88);line-height:1;">240+</p>
                        <p style="font-size:10px;color:rgba(212,160,23,0.62);font-weight:500;letter-spacing:.07em;text-transform:uppercase;margin-top:5px;">Courses</p>
                    </div>
                    <div class="lh-stat">
                        <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:28px;font-weight:700;color:rgba(255,255,255,0.88);line-height:1;">90+</p>
                        <p style="font-size:10px;color:rgba(212,160,23,0.62);font-weight:500;letter-spacing:.07em;text-transform:uppercase;margin-top:5px;">Instructors</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="lh-a4" style="position:relative;z-index:2;">
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:6px;height:6px;border-radius:50%;background:#4ade80;box-shadow:0 0 7px #4ade80;flex-shrink:0;"></div>
                    <p style="font-size:10.5px;color:rgba(255,255,255,0.22);">All systems operational &nbsp;·&nbsp; © {{ now()->year }} Hybrid LMS</p>
                </div>
            </div>
        </div>

        {{-- ══ RIGHT PANEL ══ --}}
        <div class="lh-right">
            <div class="lh-form-wrap">

                <div class="lh-form-icon" aria-hidden="true">HLP</div>

                <div style="text-align:center;margin-bottom:30px;">
                    <h1 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:28px;font-weight:700;color:rgba(255,255,255,0.92);letter-spacing:-.01em;line-height:1.1;">Welcome back</h1>
                    <p style="font-size:12.5px;color:rgba(255,255,255,0.28);margin-top:6px;">Sign in to your admin account</p>
                </div>

                <form wire:submit.prevent="authenticate">
                    @csrf

                    <div class="lh-field">
                        <label class="lh-label" for="lh-email">Email address</label>
                        <input class="lh-input" type="email" id="lh-email"
                            wire:model="data.email"
                            name="email" placeholder="Only Admin Only"
                            autocomplete="email" required />
                    </div>

                    <div class="lh-field" style="margin-bottom:20px;">
                        <label class="lh-label" for="lh-password">Password</label>
                        <input class="lh-input" type="password" id="lh-password"
                            wire:model="data.password"
                            name="password" placeholder="••••••••••••"
                            autocomplete="current-password" required />
                    </div>

                    <div style="margin-bottom:22px;">
                        <label style="display:flex;align-items:center;gap:9px;cursor:pointer;">
                            <input type="checkbox" class="lh-check"
                                wire:model="data.remember" name="remember" />
                            <span style="font-size:12px;color:rgba(255,255,255,0.36);">Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="lh-btn">
                        Sign in to Dashboard
                    </button>

                    <div class="lh-divider">
                        <div class="lh-div-line"></div>
                        <span class="lh-div-txt">SECURED</span>
                        <div class="lh-div-line"></div>
                    </div>

                    <p style="text-align:center;font-size:10.5px;color:rgba(255,255,255,0.16);line-height:1.65;">
                        Protected by 256-bit SSL encryption.<br>
                        Unauthorized access is strictly prohibited.
                    </p>
                </form>

            </div>
        </div>

    </div>
</div>

</x-filament-panels::page.simple>