@php
    $backUrl = $backUrl ?? route('filament.admin.pages.lessons');
    $type = $this->data['type'] ?? 'video';
@endphp

<div class="cl">

<style>
.cl,.cl *,.cl *::before,.cl *::after{box-sizing:border-box;margin:0;padding:0}
.cl{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:56px;display:grid;gap:20px;
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#8b5cf6;--accent2:#7c3aed;
    color:var(--t1);
}
html:not(.dark) .cl{
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes clUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.cla{opacity:0;animation:clUp .38s cubic-bezier(.16,1,.3,1) forwards}
.cl1{animation-delay:.04s}.cl2{animation-delay:.09s}.cl3{animation-delay:.14s}
.cl4{animation-delay:.19s}.cl5{animation-delay:.24s}.cl6{animation-delay:.28s}
.cl7{animation-delay:.32s}

/* ── Header ── */
.cl-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.cl-page-title{font-size:clamp(22px,2.6vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.cl-header-actions{display:flex;align-items:center;gap:8px}
.cl-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.cl-btn svg{width:14px;height:14px;flex-shrink:0}
.cl-btn-gray{background:var(--p1);border:1px solid var(--bd2);color:var(--t2)}.cl-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.cl-btn-violet{background:var(--accent);color:#fff;border:1px solid transparent}.cl-btn-violet:hover{background:var(--accent2)}
.cl-btn:disabled{opacity:.5;cursor:not-allowed}

/* ── Intro banner ── */
.cl-intro{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);padding:24px 28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.cl-intro-icon{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;flex-shrink:0;background:rgba(139,92,246,.12);color:var(--accent)}
.cl-intro-icon svg{width:28px;height:28px}
.cl-intro-body{flex:1;min-width:0}
.cl-intro-title{font-size:18px;font-weight:780;color:var(--t1);letter-spacing:-.015em}
.cl-intro-desc{font-size:12px;color:var(--t2);margin-top:4px;line-height:1.6}
.cl-intro-steps{display:flex;gap:6px;margin-top:12px;flex-wrap:wrap}
.cl-step{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;border:1px solid;background:rgba(139,92,246,.08);border-color:rgba(139,92,246,.2);color:var(--accent)}
.cl-step-num{width:16px;height:16px;border-radius:50%;background:var(--accent);color:#fff;font-size:9px;font-weight:800;display:grid;place-items:center;flex-shrink:0}

/* ── Cards ── */
.cl-card{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);overflow:hidden}
.cl-card-head{padding:18px 22px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:12px}
.cl-card-icon{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;background:rgba(139,92,246,.1);color:var(--accent);flex-shrink:0}
.cl-card-icon svg{width:16px;height:16px}
.cl-card-title{font-size:13px;font-weight:750;color:var(--t1)}
.cl-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}
.cl-card-body{padding:22px;display:grid;gap:18px}

/* ── Form fields ── */
.cl-label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--t2);margin-bottom:6px}
.cl-label span{color:#ef4444;margin-left:2px}
.cl-input{width:100%;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:10px 13px;font-size:13.5px;font-weight:550;color:var(--t1);font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s;-webkit-appearance:none}
.cl-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(139,92,246,.13)}
textarea.cl-input{resize:vertical;min-height:90px}
select.cl-input{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;cursor:pointer}
.cl-error{display:block;font-size:11.5px;color:#ef4444;margin-top:5px}
.cl-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.cl-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
@media(max-width:700px){.cl-grid-3{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.cl-grid-2,.cl-grid-3{grid-template-columns:1fr}}

/* ── Toggle ── */
.cl-toggle-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid var(--bd)}
.cl-toggle-row:last-of-type{border-bottom:none;padding-bottom:0}
.cl-toggle-info .cl-toggle-title{font-size:13px;font-weight:650;color:var(--t1)}
.cl-toggle-info .cl-toggle-desc{font-size:11.5px;color:var(--t2);margin-top:2px}
.cl-toggle-wrap{position:relative;width:44px;height:24px;flex-shrink:0}
.cl-toggle-wrap input{opacity:0;width:0;height:0;position:absolute}
.cl-toggle-track{position:absolute;inset:0;border-radius:12px;background:var(--t3);cursor:pointer;transition:background .2s}
.cl-toggle-wrap input:checked ~ .cl-toggle-track{background:var(--accent)}
.cl-toggle-thumb{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s;pointer-events:none}
.cl-toggle-wrap input:checked ~ .cl-toggle-track .cl-toggle-thumb{transform:translateX(20px)}

/* ── Content sub-form overrides ── */
.cl-sub-wrap .fi-section{background:transparent!important;border:none!important;box-shadow:none!important;border-radius:0!important}
.cl-sub-wrap .fi-section-header{display:none!important}
.cl-sub-wrap .fi-section-content-ctn{padding:0!important}
.cl-sub-wrap .fi-section-content{gap:18px!important}
.cl-sub-wrap .fi-fo-field-wrp-label .fi-fo-field-wrp-label-content{
    font-size:11px!important;font-weight:700!important;text-transform:uppercase!important;
    letter-spacing:.05em!important;color:var(--t2)!important;
}
.cl-sub-wrap .fi-input,.cl-sub-wrap .fi-select-input,.cl-sub-wrap .fi-textarea{
    border-radius:9px!important;background:var(--p2)!important;
    border-color:var(--bd2)!important;color:var(--t1)!important;
    font-size:13.5px!important;padding:10px 13px!important;
}
.cl-sub-wrap .fi-input:focus,.cl-sub-wrap .fi-select-input:focus,.cl-sub-wrap .fi-textarea:focus{
    border-color:var(--accent)!important;box-shadow:0 0 0 3px rgba(139,92,246,.12)!important;
}
.cl-sub-wrap .fi-input-wrp{border-radius:9px!important;background:var(--p2)!important;border-color:var(--bd2)!important}
.cl-sub-wrap .fi-fo-file-upload{border-color:var(--bd2)!important;border-radius:9px!important;background:var(--p2)!important}
.cl-sub-wrap .fi-repeater-item{border-radius:10px!important;border-color:var(--bd2)!important;background:var(--p2)!important}
.cl-sub-wrap .fi-rte{border-color:var(--bd2)!important;border-radius:9px!important;overflow:hidden!important}
.cl-sub-wrap .fi-rte-toolbar{border-color:var(--bd2)!important;background:var(--p2)!important}
.cl-sub-wrap .fi-rte-content{background:var(--p2)!important;color:var(--t1)!important;min-height:180px}
.cl-sub-wrap .fi-form-actions{display:none!important}

/* ── Save bar ── */
.cl-save-bar{display:flex;align-items:center;gap:10px;padding-top:20px;border-top:1px solid var(--bd)}
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
</style>

{{-- ── Header ── --}}
<div class="cl-header cla cl1">
    <h1 class="cl-page-title">Create Lesson</h1>
    <div class="cl-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="cl-btn cl-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to lessons
        </a>
    </div>
</div>

{{-- ── Intro banner ── --}}
<div class="cl-intro cla cl2">
    <div class="cl-intro-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
    </div>
    <div class="cl-intro-body">
        <div class="cl-intro-title">New Lesson</div>
        <div class="cl-intro-desc">Add a lesson to a course section. Pick a type — Video, Article, Quiz, or File — and the relevant content fields will appear.</div>
        <div class="cl-intro-steps">
            <span class="cl-step"><span class="cl-step-num">1</span> Pick section &amp; type</span>
            <span class="cl-step"><span class="cl-step-num">2</span> Add content</span>
            <span class="cl-step"><span class="cl-step-num">3</span> Configure settings</span>
        </div>
    </div>
</div>

{{-- ── Basic Information ── --}}
<div class="cl-card cla cl3">
    <div class="cl-card-head">
        <div class="cl-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
        </div>
        <div>
            <div class="cl-card-title">Basic Information</div>
            <div class="cl-card-sub">Lesson title, section assignment, and type</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-grid-2">
            <div>
                <label class="cl-label" for="cl-section">Section <span>*</span></label>
                <select id="cl-section" class="cl-input" wire:model="data.section_id">
                    <option value="">— Select section —</option>
                    @foreach ($sections as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('data.section_id') <span class="cl-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cl-label" for="cl-title">Title <span>*</span></label>
                <input id="cl-title" type="text" class="cl-input" wire:model="data.title" placeholder="Lesson title" required>
                @error('data.title') <span class="cl-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="cl-label" for="cl-type">Type <span>*</span></label>
            <select id="cl-type" class="cl-input" wire:model.live="data.type">
                <option value="video">Video</option>
                <option value="article">Article</option>
                <option value="quiz">Quiz</option>
                <option value="file">File / Document</option>
                <option value="live">Live</option>
                <option value="assignment">Assignment</option>
            </select>
            @error('data.type') <span class="cl-error">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="cl-label" for="cl-desc">Description</label>
            <textarea id="cl-desc" class="cl-input" wire:model="data.description" placeholder="Short description of this lesson…" rows="3"></textarea>
        </div>
    </div>
</div>

{{-- ── Video Content ── --}}
@if($type === 'video')
<div class="cl-card cla cl4">
    <div class="cl-card-head">
        <div class="cl-card-icon" style="background:rgba(96,165,250,.1);color:#60a5fa">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
        </div>
        <div>
            <div class="cl-card-title">Video</div>
            <div class="cl-card-sub">Upload a video file or link to YouTube, Vimeo, etc.</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-sub-wrap">{{ $this->videoForm }}</div>
    </div>
</div>
@endif

{{-- ── Article Content ── --}}
@if($type === 'article')
<div class="cl-card cla cl4">
    <div class="cl-card-head">
        <div class="cl-card-icon" style="background:rgba(52,211,153,.1);color:#34d399">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
        </div>
        <div>
            <div class="cl-card-title">Article Content</div>
            <div class="cl-card-sub">Write the lesson content using the rich text editor</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-sub-wrap">{{ $this->articleForm }}</div>
    </div>
</div>
@endif

{{-- ── Quiz Content ── --}}
@if($type === 'quiz')
<div class="cl-card cla cl4">
    <div class="cl-card-head">
        <div class="cl-card-icon" style="background:rgba(251,191,36,.1);color:#fbbf24">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
        </div>
        <div>
            <div class="cl-card-title">Quiz Questions</div>
            <div class="cl-card-sub">Add multiple-choice questions for this quiz</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-sub-wrap">{{ $this->quizForm }}</div>
    </div>
</div>
@endif

{{-- ── File / Document ── --}}
@if($type === 'file')
<div class="cl-card cla cl4">
    <div class="cl-card-head">
        <div class="cl-card-icon" style="background:rgba(248,113,113,.1);color:#f87171">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
        </div>
        <div>
            <div class="cl-card-title">File / Document</div>
            <div class="cl-card-sub">Upload a PDF, PowerPoint, Word, or spreadsheet</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-sub-wrap">{{ $this->fileForm }}</div>
    </div>
</div>
@endif

{{-- ── Attachment (video / article only) ── --}}
@if(in_array($type, ['video','article']))
<div class="cl-card cla cl5">
    <div class="cl-card-head">
        <div class="cl-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        </div>
        <div>
            <div class="cl-card-title">Attachment</div>
            <div class="cl-card-sub">Optional downloadable file for students</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-sub-wrap">{{ $this->attachmentForm }}</div>
    </div>
</div>
@endif

{{-- ── Settings ── --}}
<div class="cl-card cla cl6">
    <div class="cl-card-head">
        <div class="cl-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
        </div>
        <div>
            <div class="cl-card-title">Settings</div>
            <div class="cl-card-sub">Duration, sort order, and preview access</div>
        </div>
    </div>
    <div class="cl-card-body">
        <div class="cl-grid-2">
            <div>
                <label class="cl-label" for="cl-duration">Duration (minutes)</label>
                <input id="cl-duration" type="number" class="cl-input" wire:model="data.duration" placeholder="0" min="0">
                @error('data.duration') <span class="cl-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cl-label" for="cl-order">Sort Order</label>
                <input id="cl-order" type="number" class="cl-input" wire:model="data.order" placeholder="1" min="1" value="1">
                @error('data.order') <span class="cl-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="cl-toggle-row">
            <div class="cl-toggle-info">
                <div class="cl-toggle-title">Free Preview</div>
                <div class="cl-toggle-desc">Allow non-enrolled students to preview this lesson for free</div>
            </div>
            <label class="cl-toggle-wrap">
                <input type="checkbox" wire:model="data.is_preview" value="1">
                <div class="cl-toggle-track"><div class="cl-toggle-thumb"></div></div>
            </label>
        </div>
    </div>
</div>

{{-- ── Save bar ── --}}
<div class="cl-save-bar cla cl7">
    <button type="button" wire:click="create" wire:loading.attr="disabled" class="cl-btn cl-btn-violet">
        <span wire:loading.remove wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </span>
        <span wire:loading wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Create Lesson
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="cl-btn cl-btn-gray">Cancel</a>
</div>

</div>
