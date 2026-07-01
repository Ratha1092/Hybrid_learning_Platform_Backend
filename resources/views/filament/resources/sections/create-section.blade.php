@php
    $backUrl = $backUrl ?? route('filament.admin.pages.sections');
@endphp

<div class="cs">

<style>
.cs,.cs *,.cs *::before,.cs *::after{box-sizing:border-box;margin:0;padding:0}
.cs{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:56px;display:grid;gap:20px;
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#6366f1;--accent2:#4f46e5;
    color:var(--t1);
}
html:not(.dark) .cs{
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes csUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.csa{opacity:0;animation:csUp .38s cubic-bezier(.16,1,.3,1) forwards}
.cs1{animation-delay:.04s}.cs2{animation-delay:.09s}.cs3{animation-delay:.14s}.cs4{animation-delay:.19s}

/* ── Header ── */
.cs-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.cs-page-title{font-size:clamp(22px,2.6vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.cs-header-actions{display:flex;align-items:center;gap:8px}
.cs-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.cs-btn svg{width:14px;height:14px;flex-shrink:0}
.cs-btn-gray{background:var(--p1);border:1px solid var(--bd2);color:var(--t2)}.cs-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.cs-btn-indigo{background:var(--accent);color:#fff;border:1px solid transparent}.cs-btn-indigo:hover{background:var(--accent2)}
.cs-btn:disabled{opacity:.5;cursor:not-allowed}

/* ── Intro banner ── */
.cs-intro{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);padding:24px 28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.cs-intro-icon{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;flex-shrink:0;background:rgba(99,102,241,.12);color:var(--accent)}
.cs-intro-icon svg{width:28px;height:28px}
.cs-intro-body{flex:1;min-width:0}
.cs-intro-title{font-size:18px;font-weight:780;color:var(--t1);letter-spacing:-.015em}
.cs-intro-desc{font-size:12px;color:var(--t2);margin-top:4px;line-height:1.6}
.cs-intro-steps{display:flex;gap:6px;margin-top:12px;flex-wrap:wrap}
.cs-step{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;border:1px solid;background:rgba(99,102,241,.08);border-color:rgba(99,102,241,.2);color:var(--accent)}
.cs-step-num{width:16px;height:16px;border-radius:50%;background:var(--accent);color:#fff;font-size:9px;font-weight:800;display:grid;place-items:center;flex-shrink:0}

/* ── Card ── */
.cs-card{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);overflow:hidden}
.cs-card-head{padding:18px 22px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:12px}
.cs-card-icon{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;background:rgba(99,102,241,.1);color:var(--accent);flex-shrink:0}
.cs-card-icon svg{width:16px;height:16px}
.cs-card-title{font-size:13px;font-weight:750;color:var(--t1)}
.cs-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}
.cs-card-body{padding:22px;display:grid;gap:18px}

/* ── Form fields ── */
.cs-label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--t2);margin-bottom:6px}
.cs-label span{color:#ef4444;margin-left:2px}
.cs-input{width:100%;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:10px 13px;font-size:13.5px;font-weight:550;color:var(--t1);font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s;-webkit-appearance:none}
.cs-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,.13)}
textarea.cs-input{resize:vertical;min-height:100px}
select.cs-input{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;cursor:pointer}
.cs-error{display:block;font-size:11.5px;color:#ef4444;margin-top:5px}
.cs-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:560px){.cs-grid-2{grid-template-columns:1fr}}

/* ── Save bar ── */
.cs-save-bar{display:flex;align-items:center;gap:10px;padding-top:20px;border-top:1px solid var(--bd)}
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
</style>

{{-- ── Header ── --}}
<div class="cs-header csa cs1">
    <h1 class="cs-page-title">Create Section</h1>
    <div class="cs-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="cs-btn cs-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to sections
        </a>
    </div>
</div>

{{-- ── Intro banner ── --}}
<div class="cs-intro csa cs2">
    <div class="cs-intro-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/>
        </svg>
    </div>
    <div class="cs-intro-body">
        <div class="cs-intro-title">New Section</div>
        <div class="cs-intro-desc">Sections group lessons inside a course. You can assign a course now, or leave it blank to create a standalone section and attach it to a course later during course creation.</div>
        <div class="cs-intro-steps">
            <span class="cs-step"><span class="cs-step-num">1</span> Name the section</span>
            <span class="cs-step"><span class="cs-step-num">2</span> Set sort order</span>
            <span class="cs-step"><span class="cs-step-num">3</span> Attach to course (optional)</span>
        </div>
    </div>
</div>

{{-- ── Details card ── --}}
<div class="cs-card csa cs3">
    <div class="cs-card-head">
        <div class="cs-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
        </div>
        <div>
            <div class="cs-card-title">Section Details</div>
            <div class="cs-card-sub">Course assignment, title, description and sort order</div>
        </div>
    </div>
    <div class="cs-card-body">

        {{-- Course (optional) --}}
        <div>
            <label class="cs-label" for="cs-course">Course <span style="font-size:10px;font-weight:500;color:var(--t2);text-transform:none;letter-spacing:0">(optional — can attach later)</span></label>
            <select id="cs-course" class="cs-input" wire:model="data.course_id">
                <option value="">— Standalone (no course yet) —</option>
                @foreach ($courses as $id => $title)
                    <option value="{{ $id }}">{{ $title }}</option>
                @endforeach
            </select>
            @error('data.course_id') <span class="cs-error">{{ $message }}</span> @enderror
        </div>

        {{-- Title + Order --}}
        <div class="cs-grid-2">
            <div>
                <label class="cs-label" for="cs-title">Title <span>*</span></label>
                <input id="cs-title" type="text" class="cs-input" wire:model="data.title" placeholder="e.g. Introduction to Laravel" required>
                @error('data.title') <span class="cs-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cs-label" for="cs-order">Sort Order</label>
                <input id="cs-order" type="number" class="cs-input" wire:model="data.order" placeholder="1" min="0">
                @error('data.order') <span class="cs-error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="cs-label" for="cs-desc">Description</label>
            <textarea id="cs-desc" class="cs-input" wire:model="data.description" placeholder="Brief overview of what this section covers…" rows="4"></textarea>
            @error('data.description') <span class="cs-error">{{ $message }}</span> @enderror
        </div>

    </div>
</div>

{{-- ── Save bar ── --}}
<div class="cs-save-bar csa cs4">
    <button type="button" wire:click="create" wire:loading.attr="disabled" class="cs-btn cs-btn-indigo">
        <span wire:loading.remove wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </span>
        <span wire:loading wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Create Section
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="cs-btn cs-btn-gray">Cancel</a>
</div>

</div>
