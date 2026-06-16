@php
    $currentCommission = $commissionValue ?? 20;
    $total    = $courseStats['total'] ?? 0;
    $atRate   = $courseStats['at_rate'] ?? 0;
    $synced   = $total > 0 ? round(($atRate / $total) * 100) : 0;
@endphp

<style>
.st,.st *,.st *::before,.st *::after{box-sizing:border-box;margin:0;padding:0}
.st{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;
    padding-bottom:48px;display:grid;gap:20px;
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 1px 4px rgba(15,23,42,.06),0 4px 16px rgba(15,23,42,.06);
    --accent:#7c3aed;--radius:14px;
    color:var(--t1);
}
html.dark .st{
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
}

@keyframes stUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.sta{opacity:0;animation:stUp .4s cubic-bezier(.16,1,.3,1) forwards}
.st1{animation-delay:.04s}.st2{animation-delay:.09s}.st3{animation-delay:.14s}

/* Header */
.st-header{padding-bottom:20px;border-bottom:1px solid var(--bd)}
.st-title{font-size:clamp(20px,2.4vw,28px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.st-subtitle{font-size:12.5px;color:var(--t2);margin-top:5px}

/* Layout */
.st-grid{display:grid;grid-template-columns:260px 1fr;gap:24px;align-items:flex-start}
@media(max-width:900px){.st-grid{grid-template-columns:1fr}}

/* Sidebar nav */
.st-nav{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);overflow:hidden;box-shadow:var(--sh)}
.st-nav-item{display:flex;align-items:center;gap:10px;padding:12px 16px;font-size:12.5px;font-weight:600;color:var(--t2);cursor:pointer;border-bottom:1px solid var(--bd);text-decoration:none;transition:background .15s,color .15s}
.st-nav-item:last-child{border-bottom:none}
.st-nav-item.active{background:rgba(124,58,237,.08);color:var(--accent);border-left:3px solid var(--accent)}
.st-nav-item:not(.active):hover{background:var(--p2);color:var(--t1)}
.st-nav-item svg{width:16px;height:16px;flex-shrink:0}
.st-nav-section{padding:8px 16px 4px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--t3)}

/* Cards */
.st-card{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);overflow:hidden;box-shadow:var(--sh)}
.st-card-header{padding:16px 20px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:10px}
.st-card-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.st-card-icon svg{width:17px;height:17px}
.st-card-title{font-size:14px;font-weight:700;color:var(--t1)}
.st-card-subtitle{font-size:12px;color:var(--t2);margin-top:2px}
.st-card-body{padding:24px}

/* Form elements */
.st-field{margin-bottom:20px}
.st-field:last-child{margin-bottom:0}
.st-label{display:block;font-size:12px;font-weight:700;color:var(--t1);margin-bottom:6px}
.st-sublabel{font-size:11.5px;color:var(--t2);margin-bottom:8px;margin-top:-4px}
.st-input-wrap{display:flex;align-items:center;gap:0;max-width:280px}
.st-input{
    flex:1;background:var(--p2);border:1px solid var(--bd2);border-radius:9px 0 0 9px;
    padding:9px 14px;font-size:14px;font-weight:600;color:var(--t1);font-family:inherit;
    outline:none;transition:border-color .15s,box-shadow .15s;
}
.st-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(124,58,237,.12);z-index:1;position:relative}
.st-input-suffix{background:var(--p2);border:1px solid var(--bd2);border-left:none;border-radius:0 9px 9px 0;padding:9px 14px;font-size:14px;font-weight:700;color:var(--t2)}
.st-range-wrap{margin-top:10px;max-width:280px}
.st-range{width:100%;accent-color:var(--accent);cursor:pointer}

/* Stats row */
.st-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px}
.st-stat{background:var(--p2);border:1px solid var(--bd);border-radius:10px;padding:14px 16px;text-align:center}
.st-stat-val{font-size:22px;font-weight:800;color:var(--t1);letter-spacing:-.02em}
.st-stat-lbl{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-top:3px}

/* Progress bar */
.st-progress-wrap{margin:16px 0}
.st-progress-label{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--t2);margin-bottom:6px}
.st-progress-bar{height:6px;background:var(--bd);border-radius:3px;overflow:hidden}
.st-progress-fill{height:100%;background:linear-gradient(90deg,var(--accent),#2563eb);border-radius:3px;transition:width .5s ease}

/* Checkbox toggle */
.st-checkbox-row{display:flex;align-items:flex-start;gap:10px;padding:14px 16px;background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);border-radius:10px;margin-top:16px;cursor:pointer}
.st-checkbox-row input[type=checkbox]{width:16px;height:16px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;margin-top:2px}
.st-checkbox-text{}
.st-checkbox-title{font-size:13px;font-weight:700;color:var(--t1)}
.st-checkbox-desc{font-size:11.5px;color:var(--t2);margin-top:2px}

/* Buttons */
.st-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.st-btn svg{width:14px;height:14px}
.st-btn-primary{background:var(--accent);color:#fff}
.st-btn-primary:hover{opacity:.88}

/* Toast alerts */
.st-alert{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
.st-alert svg{width:18px;height:18px;flex-shrink:0;margin-top:1px}
.st-alert-success{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.25);color:#059669}
.st-alert-error{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:#dc2626}

/* Divider */
.st-divider{height:1px;background:var(--bd);margin:20px 0}
</style>

<div class="st">

    {{-- ── Header ── --}}
    <div class="st-header sta st1">
        <h1 class="st-title">Settings</h1>
        <p class="st-subtitle">Platform configuration and defaults</p>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('settings_success'))
    <div class="st-alert st-alert-success sta st1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        {{ session('settings_success') }}
    </div>
    @endif
    @if(session('settings_error'))
    <div class="st-alert st-alert-error sta st1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        {{ session('settings_error') }}
    </div>
    @endif

    {{-- ── Layout ── --}}
    <div class="st-grid sta st2">

        {{-- Sidebar nav --}}
        <div class="st-nav">
            <div class="st-nav-section">Finance</div>
            <a href="#commission" class="st-nav-item active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Commission
            </a>
            {{-- Future settings can be added here --}}
        </div>

        {{-- Content panels --}}
        <div style="display:grid;gap:20px">

            {{-- Commission card --}}
            <div class="st-card" id="commission">
                <div class="st-card-header">
                    <div class="st-card-icon" style="background:rgba(124,58,237,.1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    </div>
                    <div>
                        <div class="st-card-title">Commission Percentage</div>
                        <div class="st-card-subtitle">Platform cut from each course sale</div>
                    </div>
                </div>

                <div class="st-card-body">

                    {{-- Stats --}}
                    <div class="st-stats">
                        <div class="st-stat">
                            <div class="st-stat-val">{{ number_format($currentCommission, 0) }}%</div>
                            <div class="st-stat-lbl">Current Rate</div>
                        </div>
                        <div class="st-stat">
                            <div class="st-stat-val">{{ number_format($total) }}</div>
                            <div class="st-stat-lbl">Total Courses</div>
                        </div>
                        <div class="st-stat">
                            <div class="st-stat-val" style="color:{{ $synced >= 100 ? '#059669' : ($synced > 50 ? '#d97706' : '#dc2626') }}">{{ $synced }}%</div>
                            <div class="st-stat-lbl">Synced</div>
                        </div>
                    </div>

                    {{-- Sync progress --}}
                    <div class="st-progress-wrap">
                        <div class="st-progress-label">
                            <span>Courses at current rate</span>
                            <span style="font-weight:700;color:var(--t1)">{{ number_format($atRate) }} / {{ number_format($total) }}</span>
                        </div>
                        <div class="st-progress-bar">
                            <div class="st-progress-fill" style="width:{{ $synced }}%"></div>
                        </div>
                    </div>

                    <div class="st-divider"></div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('admin.settings.commission') }}">
                        @csrf

                        <div class="st-field">
                            <label class="st-label">Default Commission Rate</label>
                            <p class="st-sublabel">Platform percentage deducted from each course sale. Instructors receive the remainder.</p>
                            <div class="st-input-wrap">
                                <input
                                    type="number"
                                    name="default_commission_percentage"
                                    id="commission_input"
                                    class="st-input"
                                    value="{{ $currentCommission }}"
                                    min="0" max="100" step="0.01"
                                    oninput="document.getElementById('range_input').value=this.value;document.getElementById('preview_val').textContent=parseFloat(this.value||0).toFixed(0)+'%'"
                                >
                                <span class="st-input-suffix">%</span>
                            </div>
                            <div class="st-range-wrap">
                                <input
                                    type="range"
                                    id="range_input"
                                    class="st-range"
                                    min="0" max="100" step="1"
                                    value="{{ $currentCommission }}"
                                    oninput="document.getElementById('commission_input').value=this.value;document.getElementById('preview_val').textContent=this.value+'%'"
                                >
                                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--t2);margin-top:4px">
                                    <span>0%</span>
                                    <span style="font-weight:700;color:var(--accent)" id="preview_val">{{ number_format($currentCommission, 0) }}%</span>
                                    <span>100%</span>
                                </div>
                            </div>
                        </div>

                        {{-- Preview split --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px">
                            <div style="background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.15);border-radius:10px;padding:14px 16px;text-align:center">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#2563eb;margin-bottom:4px">Platform Gets</div>
                                <div style="font-size:20px;font-weight:800;color:#2563eb" id="platform_pct">{{ number_format($currentCommission, 0) }}%</div>
                                <div style="font-size:11.5px;color:var(--t2);margin-top:2px">of each sale</div>
                            </div>
                            <div style="background:rgba(5,150,105,.06);border:1px solid rgba(5,150,105,.15);border-radius:10px;padding:14px 16px;text-align:center">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#059669;margin-bottom:4px">Instructor Gets</div>
                                <div style="font-size:20px;font-weight:800;color:#059669" id="instructor_pct">{{ number_format(100 - $currentCommission, 0) }}%</div>
                                <div style="font-size:11.5px;color:var(--t2);margin-top:2px">of each sale</div>
                            </div>
                        </div>

                        {{-- Apply to all courses toggle --}}
                        <label class="st-checkbox-row">
                            <input type="checkbox" name="apply_to_all_courses" value="1">
                            <div class="st-checkbox-text">
                                <div class="st-checkbox-title">Apply to all existing courses</div>
                                <div class="st-checkbox-desc">Update the commission on all {{ number_format($total) }} courses immediately. Otherwise only new courses will use this rate.</div>
                            </div>
                        </label>

                        <div style="margin-top:20px">
                            <button type="submit" class="st-btn st-btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                Save Commission Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
(function() {
    const num   = document.getElementById('commission_input');
    const range = document.getElementById('range_input');
    const prev  = document.getElementById('preview_val');
    const plat  = document.getElementById('platform_pct');
    const inst  = document.getElementById('instructor_pct');

    function update(v) {
        const val = Math.min(100, Math.max(0, parseFloat(v) || 0));
        if (prev)  prev.textContent  = Math.round(val) + '%';
        if (plat)  plat.textContent  = Math.round(val) + '%';
        if (inst)  inst.textContent  = Math.round(100 - val) + '%';
        if (num)   num.value         = val;
        if (range) range.value       = val;
    }

    if (num)   num.addEventListener('input',   e => update(e.target.value));
    if (range) range.addEventListener('input', e => update(e.target.value));
})();
</script>
