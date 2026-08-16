@php
    /** @var \App\Domains\Courses\Models\Lesson $record */
    $les = $record;

    $typeStyle = match($les->type) {
        'video'   => ['bg'=>'rgba(59,130,246,.1)',  'border'=>'rgba(59,130,246,.25)',  'color'=>'#3b82f6', 'label'=>'Video',          'icon'=>'M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z'],
        'article' => ['bg'=>'rgba(16,185,129,.1)',  'border'=>'rgba(16,185,129,.25)',  'color'=>'#10b981', 'label'=>'Article',        'icon'=>'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25'],
        'file'    => ['bg'=>'rgba(239,68,68,.1)',   'border'=>'rgba(239,68,68,.25)',   'color'=>'#ef4444', 'label'=>'File / Document', 'icon'=>'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z'],
        default   => ['bg'=>'rgba(100,116,139,.08)','border'=>'rgba(100,116,139,.2)',  'color'=>'#64748b', 'label'=>ucfirst($les->type ?? '—'), 'icon'=>''],
    };
@endphp

<div class="le">

<style>
.le,.le *,.le *::before,.le *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.le {
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:56px;
    display:grid;
    gap:20px;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#8b5cf6;
    --accent2:#7c3aed;
    color:var(--t1);
}
html:not(.dark) .le {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes leUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.lea {
    opacity:0;
    animation:leUp .38s cubic-bezier(.16,1,.3,1) forwards;
}
.le1 {
    animation-delay:.04s;
}
.le2 {
    animation-delay:.09s;
}
.le3 {
    animation-delay:.14s;
}
.le4 {
    animation-delay:.19s;
}

.le-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.le-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.le-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
}
.le-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:9px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:none;
    font-family:inherit;
    transition:all .15s;
    white-space:nowrap;
}
.le-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.le-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.le-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.le-btn-violet {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.le-btn-violet:hover {
    background:var(--accent2);
}
.le-btn-outline {
    background:transparent;
    border:1px solid var(--bd2);
    color:var(--t2);
}
.le-btn-outline:hover {
    border-color:#ef4444;
    color:#ef4444;
}
.le-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}

.le-hero {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    padding:24px 28px;
    display:flex;
    align-items:center;
    gap:20px;
    flex-wrap:wrap;
}
.le-hero-icon {
    width:64px;
    height:64px;
    border-radius:16px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(139,92,246,.12);
    color:var(--accent);
}
.le-hero-icon svg {
    width:28px;
    height:28px;
}
.le-hero-info {
    flex:1;
    min-width:0;
}
.le-hero-title {
    font-size:20px;
    font-weight:780;
    color:var(--t1);
    letter-spacing:-.015em;
    line-height:1.2;
}
.le-hero-meta {
    font-size:12px;
    color:var(--t2);
    margin-top:3px;
}
.le-hero-pills {
    display:flex;
    align-items:center;
    gap:6px;
    margin-top:10px;
    flex-wrap:wrap;
}
.le-pill {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    border:1px solid;
}
.le-hero-stats {
    display:flex;
    gap:0;
    flex-shrink:0;
}
.le-stat {
    text-align:center;
    padding:0 20px;
    border-left:1px solid var(--bd);
}
.le-stat-val {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.le-stat-label {
    font-size:10px;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    margin-top:2px;
}

/* ── Filament form override ── */
.le-form-wrap .fi-section {
    background:var(--p1)!important;
    border:1px solid var(--bd)!important;
    border-radius:14px!important;
    box-shadow:var(--sh)!important;
    overflow:hidden!important;
}
.le-form-wrap .fi-section + .fi-section {
    margin-top:20px;
}
.le-form-wrap .fi-section-header {
    padding:18px 22px!important;
    border-bottom:1px solid var(--bd)!important;
    background:transparent!important;
}
.le-form-wrap .fi-section-header-heading {
    font-size:13px!important;
    font-weight:750!important;
    color:var(--t1)!important;
}
.le-form-wrap .fi-section-header-description {
    font-size:11.5px!important;
    color:var(--t2)!important;
}
.le-form-wrap .fi-section-content-ctn {
    padding:22px!important;
}
.le-form-wrap .fi-section-content {
    gap:18px!important;
}
.le-form-wrap .fi-fo-field-wrp-label .fi-fo-field-wrp-label-content {
    font-size:11px!important;
    font-weight:700!important;
    text-transform:uppercase!important;
    letter-spacing:.05em!important;
    color:var(--t2)!important;
}
.le-form-wrap .fi-input,.le-form-wrap .fi-select-input,.le-form-wrap .fi-textarea {
    border-radius:9px!important;
    background:var(--p2)!important;
    border-color:var(--bd2)!important;
    color:var(--t1)!important;
}
.le-form-wrap .fi-input:focus,.le-form-wrap .fi-select-input:focus,.le-form-wrap .fi-textarea:focus {
    border-color:var(--accent)!important;
    box-shadow:0 0 0 3px rgba(139,92,246,.12)!important;
}
.le-form-wrap .fi-input-wrp {
    border-radius:9px!important;
    background:var(--p2)!important;
    border-color:var(--bd2)!important;
}
.le-form-wrap .fi-repeater-item {
    border-radius:10px!important;
    border-color:var(--bd2)!important;
}
.le-form-wrap .fi-form-actions {
    display:none!important;
}

.le-save-bar {
    display:flex;
    align-items:center;
    gap:10px;
    padding-top:20px;
    border-top:1px solid var(--bd);
}
@keyframes spin {
    from {
        transform:rotate(0deg);
    }
    to {
        transform:rotate(360deg);
    }
}
</style>

{{-- ── Header ── --}}
<div class="le-header lea le1">
    <h1 class="le-page-title">Edit Lesson</h1>
    <div class="le-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="le-btn le-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to lessons
        </a>
        <button type="button" wire:click="mountAction('delete')" class="le-btn le-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            Delete
        </button>
    </div>
</div>

{{-- ── Hero ── --}}
<div class="le-hero lea le2">
    <div class="le-hero-icon">
        @if($typeStyle['icon'])
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeStyle['icon'] }}"/>
            </svg>
        @endif
    </div>
    <div class="le-hero-info">
        <div class="le-hero-title">{{ $les->title }}</div>
        <div class="le-hero-meta">{{ $sectionTitle }} &middot; {{ $courseTitle }}</div>
        <div class="le-hero-pills">
            <span class="le-pill" style="background:{{ $typeStyle['bg'] }};border-color:{{ $typeStyle['border'] }};color:{{ $typeStyle['color'] }}">
                {{ $typeStyle['label'] }}
            </span>
            @if($les->is_preview)
                <span class="le-pill" style="background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.25);color:#d97706">Free Preview</span>
            @endif
        </div>
    </div>
    <div class="le-hero-stats">
        @if($les->duration)
        <div class="le-stat">
            <div class="le-stat-val">{{ $les->duration }}</div>
            <div class="le-stat-label">Minutes</div>
        </div>
        @endif
        <div class="le-stat">
            <div class="le-stat-val">{{ $les->order ?? 1 }}</div>
            <div class="le-stat-label">Order</div>
        </div>
        <div class="le-stat">
            <div class="le-stat-val">{{ $progressCount }}</div>
            <div class="le-stat-label">Completions</div>
        </div>
    </div>
</div>

{{-- ── Filament form (all conditional sections) ── --}}
<div class="le-form-wrap lea le3">
    {{ $this->form }}
</div>

{{-- ── Save bar ── --}}
<div class="le-save-bar lea le4">
    <button type="button" wire:click="save" wire:loading.attr="disabled" class="le-btn le-btn-violet">
        <span wire:loading.remove wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </span>
        <span wire:loading wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Save changes
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="le-btn le-btn-gray">Cancel</a>
</div>

</div>
