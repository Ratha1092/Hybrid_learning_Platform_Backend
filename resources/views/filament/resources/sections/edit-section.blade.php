@php
    /** @var \App\Domains\Courses\Models\Section $record */
    $sec = $record;
@endphp

<div class="se">

<style>
.se,.se *,.se *::before,.se *::after{box-sizing:border-box;margin:0;padding:0}
.se{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:56px;display:grid;gap:20px;
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#6366f1;--accent2:#4f46e5;
    color:var(--t1);
}
html:not(.dark) .se{
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes seUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.sea{opacity:0;animation:seUp .38s cubic-bezier(.16,1,.3,1) forwards}
.se1{animation-delay:.04s}.se2{animation-delay:.09s}.se3{animation-delay:.14s}.se4{animation-delay:.19s}.se5{animation-delay:.24s}

.se-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.se-page-title{font-size:clamp(22px,2.6vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.se-header-actions{display:flex;align-items:center;gap:8px}
.se-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.se-btn svg{width:14px;height:14px;flex-shrink:0}
.se-btn-gray{background:var(--p1);border:1px solid var(--bd2);color:var(--t2)}.se-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.se-btn-indigo{background:var(--accent);color:#fff;border:1px solid transparent}.se-btn-indigo:hover{background:var(--accent2)}
.se-btn-outline{background:transparent;border:1px solid var(--bd2);color:var(--t2)}.se-btn-outline:hover{border-color:#ef4444;color:#ef4444}
.se-btn:disabled{opacity:.5;cursor:not-allowed}

.se-hero{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);padding:24px 28px;display:flex;align-items:center;gap:22px;flex-wrap:wrap}
.se-hero-icon{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;flex-shrink:0;background:rgba(99,102,241,.12);color:var(--accent)}
.se-hero-icon svg{width:28px;height:28px}
.se-hero-info{flex:1;min-width:0}
.se-hero-name{font-size:20px;font-weight:780;color:var(--t1);letter-spacing:-.015em;line-height:1.2}
.se-hero-course{font-size:12px;color:var(--t2);margin-top:3px}
.se-hero-stats{display:flex;gap:0;flex-shrink:0}
.se-stat{text-align:center;padding:0 20px;border-left:1px solid var(--bd)}
.se-stat-val{font-size:22px;font-weight:800;color:var(--t1)}
.se-stat-label{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);margin-top:2px}

.se-card{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);overflow:hidden}
.se-card-head{padding:18px 22px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:12px}
.se-card-icon{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;background:rgba(99,102,241,.1);color:var(--accent);flex-shrink:0}
.se-card-icon svg{width:16px;height:16px}
.se-card-title{font-size:13px;font-weight:750;color:var(--t1)}
.se-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}
.se-card-body{padding:22px;display:grid;gap:18px}

.se-label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--t2);margin-bottom:6px}
.se-label span{color:#ef4444;margin-left:2px}
.se-input{width:100%;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:10px 13px;font-size:13.5px;font-weight:550;color:var(--t1);font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s;-webkit-appearance:none}
.se-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,.13)}
textarea.se-input{resize:vertical;min-height:100px}
select.se-input{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;cursor:pointer}
.se-error{display:block;font-size:11.5px;color:#ef4444;margin-top:5px}
.se-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:560px){.se-grid-2{grid-template-columns:1fr}}

.se-save-bar{display:flex;align-items:center;gap:10px;padding-top:20px;border-top:1px solid var(--bd)}
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
</style>

{{-- ── Header ── --}}
<div class="se-header sea se1">
    <h1 class="se-page-title">Edit Section</h1>
    <div class="se-header-actions">
        <a href="{{ $backUrl }}" class="se-btn se-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to sections
        </a>
        <button type="button" wire:click="mountAction('delete')" class="se-btn se-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            Delete
        </button>
    </div>
</div>

{{-- ── Hero ── --}}
<div class="se-hero sea se2">
    <div class="se-hero-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
    </div>
    <div class="se-hero-info">
        <div class="se-hero-name">{{ $sec->title }}</div>
        <div class="se-hero-course">{{ $courseTitle }}</div>
    </div>
    <div class="se-hero-stats">
        <div class="se-stat">
            <div class="se-stat-val">{{ $sec->order ?? 1 }}</div>
            <div class="se-stat-label">Order</div>
        </div>
        <div class="se-stat">
            <div class="se-stat-val">{{ $lessonCount }}</div>
            <div class="se-stat-label">{{ Str::plural('Lesson', $lessonCount) }}</div>
        </div>
    </div>
</div>

{{-- ── Details card ── --}}
<div class="se-card sea se3">
    <div class="se-card-head">
        <div class="se-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
        </div>
        <div>
            <div class="se-card-title">Section Details</div>
            <div class="se-card-sub">Title, course, description and sort order</div>
        </div>
    </div>
    <div class="se-card-body">

        {{-- Title + Order --}}
        <div class="se-grid-2">
            <div>
                <label class="se-label" for="se-title">Title <span>*</span></label>
                <input id="se-title" type="text" class="se-input" wire:model="data.title" placeholder="Section title" required>
                @error('data.title') <span class="se-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="se-label" for="se-order">Sort Order</label>
                <input id="se-order" type="number" class="se-input" wire:model="data.order" placeholder="1" min="0">
                @error('data.order') <span class="se-error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Course --}}
        <div>
            <label class="se-label" for="se-course">Course <span>*</span></label>
            <select id="se-course" class="se-input" wire:model="data.course_id">
                <option value="">— Select a course —</option>
                @foreach ($courses as $id => $title)
                    <option value="{{ $id }}">{{ $title }}</option>
                @endforeach
            </select>
            @error('data.course_id') <span class="se-error">{{ $message }}</span> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="se-label" for="se-desc">Description</label>
            <textarea id="se-desc" class="se-input" wire:model="data.description" placeholder="Brief description of what this section covers…" rows="4"></textarea>
            @error('data.description') <span class="se-error">{{ $message }}</span> @enderror
        </div>

    </div>
</div>

{{-- ── Save bar ── --}}
<div class="se-save-bar sea se4">
    <button type="button" wire:click="save" wire:loading.attr="disabled" class="se-btn se-btn-indigo">
        <span wire:loading.remove wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </span>
        <span wire:loading wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Save changes
    </button>
    <a href="{{ $backUrl }}" class="se-btn se-btn-gray">Cancel</a>
</div>

</div>
