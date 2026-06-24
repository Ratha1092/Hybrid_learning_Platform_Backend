@php
    $currentCommission = $commissionValue ?? 20;
    $total    = $courseStats['total'] ?? 0;
    $atRate   = $courseStats['at_rate'] ?? 0;
    $synced   = $total > 0 ? round(($atRate / $total) * 100) : 0;
    $groupKeys = array_keys($visibleGroups ?? []);
    $firstGroup = $groupKeys[0] ?? null;

    $gatewayMeta = [
        'bakong_enabled'        => ['Bakong', '#0d9488'],
        'khqr_enabled'          => ['KHQR', '#7c3aed'],
        'paypal_enabled'        => ['PayPal', '#2563eb'],
        'stripe_enabled'        => ['Stripe', '#635bff'],
        'bank_transfer_enabled' => ['Bank Transfer', '#64748b'],
    ];
@endphp

<style>
.st,.st *,.st *::before,.st *::after{box-sizing:border-box;margin:0;padding:0}
.st{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;
    display:grid;gap:20px;
    --p1:#ffffff;--p2:#f1f5f9;--p3:#e8eef5;
    --bd:rgba(15,23,42,.13);--bd2:rgba(15,23,42,.20);
    --t1:#0f172a;--t2:#475569;--t3:#94a3b8;
    --accent:#f97316;--accent-soft:rgba(249,115,22,.14);--radius:14px;
    color:var(--t1);
}
html.dark .st{
    --p1:#0f1c2e;--p2:#0b1727;--p3:#091421;
    --bd:rgba(148,163,184,.14);--bd2:rgba(148,163,184,.22);
    --t1:#eef5ff;--t2:#91a2b8;--t3:#3a4a60;
}

@keyframes stUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.sta{opacity:0;animation:stUp .4s cubic-bezier(.16,1,.3,1) forwards}
.st1{animation-delay:.04s}.st2{animation-delay:.09s}.st3{animation-delay:.14s}

/* Header */
.st-header{padding-bottom:20px;border-bottom:1px solid var(--bd)}
.st-title{font-size:clamp(20px,2.4vw,28px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.st-subtitle{font-size:12.5px;color:var(--t2);margin-top:5px}

/* Layout */
.st-grid{display:grid;grid-template-columns:270px 1fr;gap:28px;align-items:flex-start}
@media(max-width:900px){.st-grid{grid-template-columns:1fr}}

/* Filter */
.st-filter-wrap{position:relative;margin-bottom:10px}
.st-filter-wrap svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--t2);pointer-events:none}
.st-filter{width:100%;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:9px 12px 9px 32px;font-size:12.5px;font-family:inherit;color:var(--t1);outline:none;transition:border-color .15s,box-shadow .15s}
.st-filter::placeholder{color:var(--t2)}
.st-filter:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}

/* Sidebar nav */
.st-nav{display:flex;flex-direction:column;gap:2px}
.st-nav-item{display:flex;align-items:center;gap:10px;padding:9px 10px;font-size:12.5px;font-weight:600;color:var(--t2);cursor:pointer;border-radius:10px;text-decoration:none;transition:background .15s,color .15s}
.st-nav-item.active{background:var(--accent-soft);color:var(--accent)}
.st-nav-item:not(.active):hover{background:var(--p2);color:var(--t1)}
.st-nav-icon{position:relative;width:30px;height:30px;border-radius:8px;display:grid;place-items:center;flex-shrink:0;background:var(--p2);color:var(--t2)}
.st-nav-item.active .st-nav-icon{background:rgba(249,115,22,.18);color:var(--accent)}
.st-nav-icon svg{width:14px;height:14px}
.st-nav-icon-dot{position:absolute;top:-2px;right:-2px;width:8px;height:8px;border-radius:50%;background:var(--accent);border:2px solid var(--p1)}
.st-nav-label{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.st-nav-empty{padding:10px;font-size:12px;color:var(--t2)}

/* Content card */
.st-card{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);overflow:hidden}
.st-card-header{padding:18px 22px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:12px}
.st-card-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:rgba(249,115,22,.16);color:var(--accent)}
.st-card-icon svg{width:18px;height:18px}
.st-card-title{font-size:16px;font-weight:700;color:var(--t1)}
.st-card-subtitle{font-size:12px;color:var(--t2);margin-top:2px}
.st-card-body{padding:24px}

/* Sections */
.st-section{margin-bottom:24px}
.st-section:last-child{margin-bottom:0}
.st-section-title{font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--t2);margin-bottom:6px}

/* Flat row layout — label/description left, control right */
.st-row{display:flex;align-items:flex-start;justify-content:space-between;gap:24px;padding:16px 0;border-bottom:1px solid var(--bd)}
.st-section .st-row:last-child{border-bottom:none}
.st-row-text{flex:1 1 auto;min-width:0;max-width:60%}
.st-row-title{font-size:13px;font-weight:700;color:var(--t1)}
.st-row-desc{font-size:11.5px;color:var(--t2);margin-top:3px}
.st-row-control{flex-shrink:0;width:280px;display:flex;justify-content:flex-end}

/* Form elements */
.st-input-wrap{display:flex;align-items:center;gap:0;width:100%}
.st-input{
    flex:1;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;
    padding:8px 12px;font-size:13px;font-weight:600;color:var(--t1);font-family:inherit;
    outline:none;transition:border-color .15s,box-shadow .15s;width:100%;text-align:right;
}
.st-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft);z-index:1;position:relative}
.st-input-wrap:has(.st-input-suffix) .st-input{border-radius:9px 0 0 9px;text-align:left}
.st-input-suffix{background:var(--p2);border:1px solid var(--bd2);border-left:none;border-radius:0 9px 9px 0;padding:8px 12px;font-size:13px;font-weight:700;color:var(--t2)}
.st-range-wrap{margin-top:10px;max-width:280px}
.st-range{width:100%;accent-color:var(--accent);cursor:pointer}

/* Color row */
.st-color-row{display:flex;align-items:center;gap:8px;width:100%}
.st-color-swatch{width:36px;height:34px;border-radius:8px;border:1px solid var(--bd2);background:none;cursor:pointer;padding:2px;flex-shrink:0}

/* Stats row */
.st-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px}
.st-stat{background:var(--p2);border:1px solid var(--bd);border-radius:10px;padding:14px 16px;text-align:center}
.st-stat-val{font-size:22px;font-weight:800;color:var(--t1);letter-spacing:-.02em}
.st-stat-lbl{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-top:3px}

/* Progress bar */
.st-progress-wrap{margin:16px 0}
.st-progress-label{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--t2);margin-bottom:6px}
.st-progress-bar{height:6px;background:var(--bd);border-radius:3px;overflow:hidden}
.st-progress-fill{height:100%;background:linear-gradient(90deg,var(--accent),#fb923c);border-radius:3px;transition:width .5s ease}

/* Checkbox / toggle */
.st-checkbox-row{display:flex;align-items:flex-start;gap:10px;padding:14px 16px;background:var(--accent-soft);border:1px solid rgba(249,115,22,.25);border-radius:10px;margin-top:4px;cursor:pointer}
.st-checkbox-row input[type=checkbox]{width:16px;height:16px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;margin-top:2px}
.st-checkbox-title{font-size:13px;font-weight:700;color:var(--t1)}
.st-checkbox-desc{font-size:11.5px;color:var(--t2);margin-top:2px}

.st-switch{position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0}
.st-switch input{opacity:0;width:0;height:0}
.st-switch-track{position:absolute;inset:0;background:var(--bd2);border-radius:999px;transition:background .15s}
.st-switch-track::before{content:'';position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:transform .15s;box-shadow:0 1px 3px rgba(0,0,0,.3)}
.st-switch input:checked + .st-switch-track{background:var(--accent)}
.st-switch input:checked + .st-switch-track::before{transform:translateX(18px)}

/* Gateway cards (payment_gateway group) */
.st-gateway-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:10px}
.st-gateway-card{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:13px 14px;border:1px solid var(--bd2);border-radius:11px;background:var(--p2);transition:border-color .15s}
.st-gateway-card:has(input:checked){border-color:var(--accent)}
.st-gateway-info{display:flex;align-items:center;gap:10px;min-width:0}
.st-gateway-icon{width:32px;height:32px;border-radius:8px;display:grid;place-items:center;flex-shrink:0}
.st-gateway-icon svg{width:15px;height:15px}
.st-gateway-name{font-size:12.5px;font-weight:700;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* Buttons */
.st-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;border:1px solid transparent;font-family:inherit;transition:all .15s;white-space:nowrap}
.st-btn svg{width:14px;height:14px}
.st-btn-primary{background:var(--accent);color:#fff}
.st-btn-primary:hover:not(:disabled){filter:brightness(1.05)}
.st-btn-ghost{background:transparent;border-color:var(--bd2);color:var(--t1)}
.st-btn-ghost:hover:not(:disabled){background:var(--p2)}
.st-btn:disabled{opacity:.45;cursor:not-allowed}

/* Toast alerts */
.st-alert{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
.st-alert svg{width:18px;height:18px;flex-shrink:0;margin-top:1px}
.st-alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#22c55e}
.st-alert-error{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:#f87171}

/* Read-only notice */
.st-readonly{font-size:11.5px;color:var(--t2);background:var(--p2);border:1px solid var(--bd);border-radius:8px;padding:10px 14px}

/* Sticky save bar */
.st-footer{position:sticky;bottom:0;z-index:5;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 22px;margin-top:4px;background:var(--p1);border:1px solid var(--bd);border-radius:12px;backdrop-filter:blur(12px)}
.st-footer-status{display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:var(--t2)}
.st-footer-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0;transition:background .15s}
.st-footer-actions{display:flex;align-items:center;gap:10px}
</style>

<div class="st">

    {{-- Header --}}
    <div class="st-header sta st1">
        <h1 class="st-title">Settings</h1>
        <p class="st-subtitle">Platform configuration and defaults</p>
    </div>

    {{-- Alerts --}}
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

    @if(empty($visibleGroups))
    <div class="st-card sta st2"><div class="st-card-body">You don't have access to any settings groups.</div></div>
    @else
    {{-- Layout --}}
    <div class="st-grid sta st2">

        {{-- Sidebar nav --}}
        <div>
            <div class="st-filter-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input type="search" id="st-filter" class="st-filter" placeholder="Filter settings…" autocomplete="off">
            </div>

            <div class="st-nav" id="st-nav">
                @foreach ($visibleGroups as $key => $groupData)
                <a href="#{{ $key }}" class="st-nav-item {{ $key === $firstGroup ? 'active' : '' }}" data-group="{{ $key }}" data-label="{{ \Illuminate\Support\Str::lower($groupData['label']) }}">
                    <span class="st-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $groupData['icon'] }}"/></svg>
                        @if(! $groupData['canUpdate'])
                            <span class="st-nav-icon-dot" title="View-only access"></span>
                        @endif
                    </span>
                    <span class="st-nav-label">{{ $groupData['label'] }}</span>
                </a>
                @endforeach
                <div class="st-nav-empty" id="st-nav-empty" style="display:none">No settings match your filter.</div>
            </div>
        </div>

        {{-- Content panels --}}
        <div style="display:grid;gap:20px">

            @foreach ($visibleGroups as $key => $groupData)
            <div class="st-card st-panel" id="{{ $key }}" data-group="{{ $key }}" style="{{ $key === $firstGroup ? '' : 'display:none' }}">
                <div class="st-card-header">
                    <div class="st-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $groupData['icon'] }}"/></svg>
                    </div>
                    <div>
                        <div class="st-card-title">{{ $groupData['label'] }}</div>
                        <div class="st-card-subtitle">{{ $groupData['description'] }}</div>
                    </div>
                </div>

                <div class="st-card-body">
                    <form method="POST" action="{{ route('admin.settings.update', ['group' => $key]) }}" id="form-{{ $key }}" data-can-update="{{ $groupData['canUpdate'] ? '1' : '0' }}">
                        @csrf

                        @if($key === 'finance')
                            <div class="st-section">
                                <div class="st-section-title">Commission</div>

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
                                        <div class="st-stat-val" style="color:{{ $synced >= 100 ? '#22c55e' : ($synced > 50 ? '#f59e0b' : '#f87171') }}">{{ $synced }}%</div>
                                        <div class="st-stat-lbl">Synced</div>
                                    </div>
                                </div>

                                <div class="st-progress-wrap">
                                    <div class="st-progress-label">
                                        <span>Courses at current rate</span>
                                        <span style="font-weight:700;color:var(--t1)">{{ number_format($atRate) }} / {{ number_format($total) }}</span>
                                    </div>
                                    <div class="st-progress-bar">
                                        <div class="st-progress-fill" style="width:{{ $synced }}%"></div>
                                    </div>
                                </div>

                                <div style="margin-top:16px">
                                    <label class="st-row-title" style="display:block">Default Commission Rate</label>
                                    <p class="st-row-desc" style="margin-bottom:8px">Platform percentage deducted from each course sale. Instructors receive the remainder.</p>
                                    <div class="st-input-wrap" style="max-width:280px">
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

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:16px">
                                    <div style="background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.2);border-radius:10px;padding:14px 16px;text-align:center">
                                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#60a5fa;margin-bottom:4px">Platform Gets</div>
                                        <div style="font-size:20px;font-weight:800;color:#60a5fa" id="platform_pct">{{ number_format($currentCommission, 0) }}%</div>
                                        <div style="font-size:11.5px;color:var(--t2);margin-top:2px">of each sale</div>
                                    </div>
                                    <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:14px 16px;text-align:center">
                                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4ade80;margin-bottom:4px">Instructor Gets</div>
                                        <div style="font-size:20px;font-weight:800;color:#4ade80" id="instructor_pct">{{ number_format(100 - $currentCommission, 0) }}%</div>
                                        <div style="font-size:11.5px;color:var(--t2);margin-top:2px">of each sale</div>
                                    </div>
                                </div>

                                <label class="st-checkbox-row">
                                    <input type="checkbox" name="apply_to_all_courses" value="1">
                                    <div>
                                        <div class="st-checkbox-title">Apply commission to all existing courses</div>
                                        <div class="st-checkbox-desc">Update the commission on all {{ number_format($total) }} courses immediately. Otherwise only new courses will use this rate.</div>
                                    </div>
                                </label>
                            </div>
                        @endif

                        {{-- Sectioned fields --}}
                        @foreach ($groupData['sections'] as $section)
                        <div class="st-section">
                            <div class="st-section-title">{{ $section['label'] }}</div>

                            @if($key === 'payment_gateway' && $section['label'] === 'Checkout Methods')
                                <div class="st-gateway-grid">
                                    @foreach ($section['settings'] as $setting)
                                        @php [$gwName, $gwColor] = $gatewayMeta[$setting->key] ?? [\Illuminate\Support\Str::headline(str_replace('_enabled', '', $setting->key)), '#64748b']; @endphp
                                        <label class="st-gateway-card">
                                            <span class="st-gateway-info">
                                                <span class="st-gateway-icon" style="background:{{ $gwColor }}1a;color:{{ $gwColor }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                                                </span>
                                                <span class="st-gateway-name">{{ $gwName }}</span>
                                            </span>
                                            <span class="st-switch">
                                                <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value === 'true' ? 'checked' : '' }}>
                                                <span class="st-switch-track"></span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                @foreach ($section['settings'] as $setting)
                                    @if($setting->type === 'boolean')
                                        <label class="st-row" style="cursor:pointer">
                                            <span class="st-row-text">
                                                <span class="st-row-title">{{ \Illuminate\Support\Str::headline($setting->key) }}</span>
                                                @if($setting->description)<span class="st-row-desc" style="display:block">{{ $setting->description }}</span>@endif
                                            </span>
                                            <span class="st-row-control">
                                                <span class="st-switch">
                                                    <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value === 'true' ? 'checked' : '' }}>
                                                    <span class="st-switch-track"></span>
                                                </span>
                                            </span>
                                        </label>
                                    @elseif(str_ends_with($setting->key, '_color'))
                                        <div class="st-row">
                                            <span class="st-row-text">
                                                <span class="st-row-title">{{ \Illuminate\Support\Str::headline($setting->key) }}</span>
                                                @if($setting->description)<span class="st-row-desc" style="display:block">{{ $setting->description }}</span>@endif
                                            </span>
                                            <span class="st-row-control">
                                                <span class="st-color-row">
                                                    <input type="color" class="st-color-swatch" value="{{ $setting->value ?: '#f97316' }}" oninput="this.nextElementSibling.value=this.value">
                                                    <input type="text" name="{{ $setting->key }}" class="st-input" value="{{ $setting->value }}" oninput="this.previousElementSibling.value=this.value">
                                                </span>
                                            </span>
                                        </div>
                                    @else
                                        <div class="st-row">
                                            <span class="st-row-text">
                                                <span class="st-row-title">{{ \Illuminate\Support\Str::headline($setting->key) }}</span>
                                                @if($setting->description)<span class="st-row-desc" style="display:block">{{ $setting->description }}</span>@endif
                                            </span>
                                            <span class="st-row-control">
                                                <span class="st-input-wrap">
                                                    <input
                                                        type="{{ in_array($setting->type, ['integer', 'decimal']) ? 'number' : 'text' }}"
                                                        name="{{ $setting->key }}"
                                                        class="st-input"
                                                        value="{{ $setting->value }}"
                                                        @if($setting->type === 'decimal') step="0.01" @elseif($setting->type === 'integer') step="1" @endif
                                                    >
                                                </span>
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        @endforeach
                    </form>
                </div>
            </div>
            @endforeach

            {{-- Sticky save bar — submits/discards whichever group is currently active --}}
            <div class="st-footer" id="st-footer">
                <div class="st-footer-status">
                    <span class="st-footer-dot" id="st-footer-dot"></span>
                    <span id="st-footer-status-text">All changes saved</span>
                </div>
                <div class="st-footer-actions">
                    <span class="st-readonly" id="st-readonly-note" style="display:none">You have view-only access to this group.</span>
                    <button type="button" class="st-btn st-btn-ghost" id="st-discard-btn" disabled>Discard</button>
                    <button type="button" class="st-btn st-btn-primary" id="st-save-btn" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        Save changes
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>

<script>
(function() {
    const num   = document.getElementById('commission_input');
    const range = document.getElementById('range_input');
    const prev  = document.getElementById('preview_val');
    const plat  = document.getElementById('platform_pct');
    const inst  = document.getElementById('instructor_pct');

    function updateCommission(v) {
        const val = Math.min(100, Math.max(0, parseFloat(v) || 0));
        if (prev)  prev.textContent  = Math.round(val) + '%';
        if (plat)  plat.textContent  = Math.round(val) + '%';
        if (inst)  inst.textContent  = Math.round(100 - val) + '%';
        if (num)   num.value         = val;
        if (range) range.value       = val;
    }

    if (num)   num.addEventListener('input',   e => updateCommission(e.target.value));
    if (range) range.addEventListener('input', e => updateCommission(e.target.value));

    // --- Filter ---
    const filterInput = document.getElementById('st-filter');
    const navEmpty = document.getElementById('st-nav-empty');
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visibleCount = 0;
            document.querySelectorAll('.st-nav-item').forEach(function (item) {
                const matches = item.dataset.label.includes(q);
                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });
            if (navEmpty) navEmpty.style.display = visibleCount === 0 ? '' : 'none';
        });
    }

    // --- Sticky save bar / dirty tracking ---
    const footerDot    = document.getElementById('st-footer-dot');
    const footerText   = document.getElementById('st-footer-status-text');
    const discardBtn   = document.getElementById('st-discard-btn');
    const saveBtn      = document.getElementById('st-save-btn');
    const readonlyNote = document.getElementById('st-readonly-note');

    let activeForm = null;
    let dirty = false;

    function updateFooter() {
        if (!activeForm) return;
        const canUpdate = activeForm.dataset.canUpdate === '1';

        footerText.textContent = dirty ? 'Unsaved changes' : 'All changes saved';
        footerDot.style.background = dirty ? '#f59e0b' : '#22c55e';

        discardBtn.style.display = canUpdate ? '' : 'none';
        saveBtn.style.display = canUpdate ? '' : 'none';
        readonlyNote.style.display = canUpdate ? 'none' : '';

        discardBtn.disabled = !dirty;
        saveBtn.disabled = !dirty;
    }

    function setActiveForm(key) {
        activeForm = document.getElementById('form-' + key);
        dirty = false;
        updateFooter();
    }

    document.addEventListener('input', function (e) {
        if (activeForm && activeForm.contains(e.target)) {
            dirty = true;
            updateFooter();
        }
    });
    document.addEventListener('change', function (e) {
        if (activeForm && activeForm.contains(e.target)) {
            dirty = true;
            updateFooter();
        }
    });

    discardBtn.addEventListener('click', function () {
        if (!activeForm) return;
        activeForm.reset();
        updateCommission(num ? num.defaultValue : 0);
        dirty = false;
        updateFooter();
    });

    saveBtn.addEventListener('click', function () {
        if (activeForm) activeForm.requestSubmit();
    });

    function activateGroup(key) {
        document.querySelectorAll('.st-nav-item').forEach(el => el.classList.toggle('active', el.dataset.group === key));
        document.querySelectorAll('.st-panel').forEach(el => el.style.display = (el.dataset.group === key) ? '' : 'none');
        setActiveForm(key);
    }

    function groupFromHash() {
        const h = (location.hash || '').replace('#', '');
        return document.querySelector('.st-nav-item[data-group="' + h + '"]') ? h : null;
    }

    window.addEventListener('hashchange', function () {
        const g = groupFromHash();
        if (g) activateGroup(g);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const g = groupFromHash();
        const activeItem = document.querySelector('.st-nav-item.active');
        setActiveForm(g || (activeItem ? activeItem.dataset.group : null));
        if (g) activateGroup(g);
    });
})();
</script>
