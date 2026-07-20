@php
    $backUrl = $backUrl ?? url('/admin/courses');
@endphp

<div class="cc">

<style>
.cc,.cc *,.cc *::before,.cc *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cc {
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
    --accent:#3b82f6;
    --accent2:#2563eb;
    color:var(--t1);
}
html:not(.dark) .cc {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes ccUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.cca {
    opacity:0;
    animation:ccUp .38s cubic-bezier(.16,1,.3,1) forwards;
}
.cc1 {
    animation-delay:.04s;
}
.cc2 {
    animation-delay:.09s;
}
.cc3 {
    animation-delay:.14s;
}
.cc4 {
    animation-delay:.19s;
}
.cc5 {
    animation-delay:.24s;
}
.cc6 {
    animation-delay:.28s;
}
.cc7 {
    animation-delay:.32s;
}
.cc8 {
    animation-delay:.36s;
}
.cc9 {
    animation-delay:.40s;
}
.cc10 {
    animation-delay:.44s;
}

/* ── Header ── */
.cc-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.cc-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.cc-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
}
.cc-btn {
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
.cc-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.cc-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.cc-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.cc-btn-blue {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.cc-btn-blue:hover {
    background:var(--accent2);
}
.cc-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}

/* ── Intro banner ── */
.cc-intro {
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
.cc-intro-icon {
    width:64px;
    height:64px;
    border-radius:16px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(59,130,246,.12);
    color:var(--accent);
}
.cc-intro-icon svg {
    width:28px;
    height:28px;
}
.cc-intro-body {
    flex:1;
    min-width:0;
}
.cc-intro-title {
    font-size:18px;
    font-weight:780;
    color:var(--t1);
    letter-spacing:-.015em;
}
.cc-intro-desc {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
    line-height:1.6;
}
.cc-intro-steps {
    display:flex;
    gap:6px;
    margin-top:12px;
    flex-wrap:wrap;
}
.cc-step {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    border:1px solid;
    background:rgba(59,130,246,.08);
    border-color:rgba(59,130,246,.2);
    color:var(--accent);
}
.cc-step-num {
    width:16px;
    height:16px;
    border-radius:50%;
    background:var(--accent);
    color:#fff;
    font-size:9px;
    font-weight:800;
    display:grid;
    place-items:center;
    flex-shrink:0;
}

/* ── Cards ── */
.cc-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    overflow:hidden;
}
.cc-card-head {
    padding:18px 22px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:12px;
}
.cc-card-icon {
    width:34px;
    height:34px;
    border-radius:9px;
    display:grid;
    place-items:center;
    background:rgba(59,130,246,.1);
    color:var(--accent);
    flex-shrink:0;
}
.cc-card-icon svg {
    width:16px;
    height:16px;
}
.cc-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.cc-card-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cc-card-body {
    padding:22px;
    display:grid;
    gap:18px;
}

/* ── Form fields ── */
.cc-label {
    display:block;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.cc-label span {
    color:#ef4444;
    margin-left:2px;
}
.cc-input {
    width:100%;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:9px;
    padding:10px 13px;
    font-size:13.5px;
    font-weight:550;
    color:var(--t1);
    font-family:inherit;
    outline:none;
    transition:border-color .15s,box-shadow .15s;
    -webkit-appearance:none;
}
.cc-input:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,130,246,.13);
}
textarea.cc-input {
    resize:vertical;
    min-height:100px;
}
select.cc-input {
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:18px;
    padding-right:36px;
    cursor:pointer;
}
.cc-error {
    display:block;
    font-size:11.5px;
    color:#ef4444;
    margin-top:5px;
}
.cc-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}
.cc-grid-3 {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:18px;
}
@media(max-width:700px) {
    .cc-grid-3 {
        grid-template-columns:1fr 1fr;
    }
}
@media(max-width:560px) {
    .cc-grid-2,.cc-grid-3 {
        grid-template-columns:1fr;
    }
}

/* ── Toggle ── */
.cc-toggle-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:14px 0;
    border-bottom:1px solid var(--bd);
}
.cc-toggle-row:last-of-type {
    border-bottom:none;
    padding-bottom:0;
}
.cc-toggle-info .cc-toggle-title {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.cc-toggle-info .cc-toggle-desc {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cc-toggle-wrap {
    position:relative;
    width:44px;
    height:24px;
    flex-shrink:0;
}
.cc-toggle-wrap input {
    opacity:0;
    width:0;
    height:0;
    position:absolute;
}
.cc-toggle-track {
    position:absolute;
    inset:0;
    border-radius:12px;
    background:var(--t3);
    cursor:pointer;
    transition:background .2s;
}
.cc-toggle-wrap input:checked ~ .cc-toggle-track {
    background:var(--accent);
}
.cc-toggle-thumb {
    position:absolute;
    top:3px;
    left:3px;
    width:18px;
    height:18px;
    border-radius:50%;
    background:#fff;
    box-shadow:0 1px 4px rgba(0,0,0,.2);
    transition:transform .2s;
    pointer-events:none;
}
.cc-toggle-wrap input:checked ~ .cc-toggle-track .cc-toggle-thumb {
    transform:translateX(20px);
}

/* ── Description / Thumbnail sub-form overrides ── */
.cc-desc-wrap .fi-fo-field-wrp-label-content, .cc-thumb-wrap .fi-fo-field-wrp-label-content {
    font-size:11px!important;
    font-weight:700!important;
    text-transform:uppercase!important;
    letter-spacing:.05em!important;
    color:var(--t2)!important;
}
.cc-desc-wrap .fi-rte {
    border-color:var(--bd2)!important;
    border-radius:9px!important;
    overflow:hidden!important;
}
.cc-desc-wrap .fi-rte-toolbar {
    border-color:var(--bd2)!important;
    background:var(--p2)!important;
}
.cc-desc-wrap .fi-rte-content {
    background:var(--p2)!important;
    color:var(--t1)!important;
    min-height:180px;
}
.cc-thumb-wrap .fi-fo-field-wrp {
    width:100%!important;
}
.cc-thumb-wrap .fi-fo-file-upload {
    border-color:var(--bd2)!important;
    border-radius:9px!important;
    background:var(--p2)!important;
}

/* ── Section picker ── */
.cc-section-grid {
    display:grid;
    gap:10px;
}
.cc-section-item {
    display:flex;
    align-items:center;
    gap:14px;
    padding:13px 16px;
    border-radius:10px;
    border:1.5px solid var(--bd2);
    background:var(--p2);
    cursor:pointer;
    transition:border-color .15s,background .15s;
    user-select:none;
}
.cc-section-item:hover {
    border-color:var(--accent);
    background:rgba(59,130,246,.04);
}
.cc-section-item input[type=checkbox] {
    width:16px;
    height:16px;
    accent-color:var(--accent);
    flex-shrink:0;
    cursor:pointer;
}
.cc-section-item.selected {
    border-color:var(--accent);
    background:rgba(59,130,246,.07);
}
.cc-section-info {
    flex:1;
    min-width:0;
}
.cc-section-name {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.cc-section-meta {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cc-section-empty {
    text-align:center;
    padding:32px 16px;
    color:var(--t2);
    font-size:13px;
}
.cc-section-empty svg {
    width:36px;
    height:36px;
    margin:0 auto 10px;
    opacity:.35;
    display:block;
}

/* ── Save bar ── */
.cc-save-bar {
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
<div class="cc-header cca cc1">
    <h1 class="cc-page-title">Create Course</h1>
    <div class="cc-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="cc-btn cc-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to courses
        </a>
    </div>
</div>

{{-- ── Intro banner ── --}}
<div class="cc-intro cca cc2">
    <div class="cc-intro-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
        </svg>
    </div>
    <div class="cc-intro-body">
        <div class="cc-intro-title">New Course</div>
        <div class="cc-intro-desc">Fill out the sections below to publish a new course on the platform. Assign an instructor, upload a thumbnail, write a description, and configure pricing before saving.</div>
        <div class="cc-intro-steps">
            <span class="cc-step"><span class="cc-step-num">1</span> Basic info &amp; description</span>
            <span class="cc-step"><span class="cc-step-num">2</span> Instructor &amp; pricing</span>
            <span class="cc-step"><span class="cc-step-num">3</span> Publish settings</span>
            <span class="cc-step"><span class="cc-step-num">4</span> Attach sections (optional)</span>
        </div>
    </div>
</div>

{{-- ── Basic Information ── --}}
<div class="cc-card cca cc3">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Basic Information</div>
            <div class="cc-card-sub">Title, slug, and short description</div>
        </div>
    </div>
    <div class="cc-card-body">
        <div class="cc-grid-2">
            <div>
                <label class="cc-label" for="cc-title">Title <span>*</span></label>
                <input id="cc-title" type="text" class="cc-input" wire:model="data.title" placeholder="Course title" required>
                @error('data.title') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cc-label" for="cc-slug">Slug <span>*</span></label>
                <input id="cc-slug" type="text" class="cc-input" wire:model="data.slug" placeholder="course-slug">
                @error('data.slug') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="cc-label" for="cc-short-desc">Short Description</label>
            <textarea id="cc-short-desc" class="cc-input" wire:model="data.short_description" placeholder="Brief one-liner about the course…" rows="3"></textarea>
            @error('data.short_description') <span class="cc-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Full Description ── --}}
<div class="cc-card cca cc4">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Full Description</div>
            <div class="cc-card-sub">Rich text content shown to students</div>
        </div>
    </div>
    <div class="cc-card-body">
        <div class="cc-desc-wrap">
            {{ $this->descriptionForm }}
        </div>
    </div>
</div>

{{-- ── Media ── --}}
<div class="cc-card cca cc5">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Media</div>
            <div class="cc-card-sub">Course thumbnail and preview video URL</div>
        </div>
    </div>
    <div class="cc-card-body">
        <div class="cc-thumb-wrap">
            {{ $this->thumbnailForm }}
        </div>
        <div>
            <label class="cc-label" for="cc-video-url">Preview Video URL</label>
            <input id="cc-video-url" type="url" class="cc-input" wire:model="data.preview_video_url" placeholder="https://youtube.com/watch?v=…">
            @error('data.preview_video_url') <span class="cc-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Course Details ── --}}
<div class="cc-card cca cc6">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Course Details</div>
            <div class="cc-card-sub">Instructor, category, price, level, and language</div>
        </div>
    </div>
    <div class="cc-card-body">
        <div class="cc-grid-2">
            <div>
                <label class="cc-label" for="cc-instructor">Instructor <span>*</span></label>
                <select id="cc-instructor" class="cc-input" wire:model="data.instructor_id">
                    <option value="">— Select instructor —</option>
                    @foreach ($instructors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('data.instructor_id') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cc-label" for="cc-category">Category <span>*</span></label>
                <select id="cc-category" class="cc-input" wire:model="data.category_id">
                    <option value="">— Select category —</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('data.category_id') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="cc-grid-3">
            <div>
                <label class="cc-label" for="cc-price">Price <span>*</span></label>
                <input id="cc-price" type="number" class="cc-input" wire:model="data.price" placeholder="0.00" min="0" step="0.01">
                @error('data.price') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cc-label" for="cc-duration">Duration (minutes)</label>
                <input id="cc-duration" type="number" class="cc-input" wire:model="data.duration" placeholder="60" min="0">
                @error('data.duration') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cc-label" for="cc-language">Language <span>*</span></label>
                <input id="cc-language" type="text" class="cc-input" wire:model="data.language" placeholder="English">
                @error('data.language') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="cc-label" for="cc-level">Level <span>*</span></label>
            <select id="cc-level" class="cc-input" wire:model="data.level">
                <option value="">— Select level —</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            @error('data.level') <span class="cc-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Learning Content ── --}}
<div class="cc-card cca cc7">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Learning Content</div>
            <div class="cc-card-sub">Requirements and learning outcomes</div>
        </div>
    </div>
    <div class="cc-card-body">
        <div class="cc-grid-2">
            <div>
                <label class="cc-label" for="cc-requirements">Requirements</label>
                <textarea id="cc-requirements" class="cc-input" wire:model="data.requirements" placeholder="What should students know before starting?" rows="5"></textarea>
                @error('data.requirements') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cc-label" for="cc-outcomes">What You Will Learn</label>
                <textarea id="cc-outcomes" class="cc-input" wire:model="data.what_you_will_learn" placeholder="What will students gain from this course?" rows="5"></textarea>
                @error('data.what_you_will_learn') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Publishing ── --}}
<div class="cc-card cca cc8">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Publishing</div>
            <div class="cc-card-sub">Status, visibility, and feature flags</div>
        </div>
    </div>
    <div class="cc-card-body">
        <div class="cc-grid-2">
            <div>
                <label class="cc-label" for="cc-status">Status <span>*</span></label>
                <select id="cc-status" class="cc-input" wire:model="data.status">
                    <option value="">— Select status —</option>
                    <option value="draft">Draft</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="published">Published</option>
                    <option value="rejected">Rejected</option>
                    <option value="archived">Archived</option>
                </select>
                @error('data.status') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cc-label" for="cc-visibility">Visibility <span>*</span></label>
                <select id="cc-visibility" class="cc-input" wire:model="data.visibility">
                    <option value="">— Select visibility —</option>
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                    <option value="unlisted">Unlisted</option>
                </select>
                @error('data.visibility') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="cc-toggle-row">
            <div class="cc-toggle-info">
                <div class="cc-toggle-title">Published</div>
                <div class="cc-toggle-desc">Make this course visible to students on the platform</div>
            </div>
            <label class="cc-toggle-wrap">
                <input type="checkbox" wire:model="data.is_published" value="1">
                <div class="cc-toggle-track"><div class="cc-toggle-thumb"></div></div>
            </label>
        </div>
        <div class="cc-toggle-row">
            <div class="cc-toggle-info">
                <div class="cc-toggle-title">Enable Certificate</div>
                <div class="cc-toggle-desc">Issue completion certificates to students who finish this course</div>
            </div>
            <label class="cc-toggle-wrap">
                <input type="checkbox" wire:model="data.certificate_enabled" value="1">
                <div class="cc-toggle-track"><div class="cc-toggle-thumb"></div></div>
            </label>
        </div>
    </div>
</div>

{{-- ── Attach Sections ── --}}
<div class="cc-card cca cc9">
    <div class="cc-card-head">
        <div class="cc-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
        </div>
        <div>
            <div class="cc-card-title">Attach Sections</div>
            <div class="cc-card-sub">Pick standalone sections to include in this course — optional</div>
        </div>
    </div>
    <div class="cc-card-body">
        @if($unattachedSections->isEmpty())
            <div class="cc-section-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
                No standalone sections yet. Create a section without a course first, then come back to attach it here.
            </div>
        @else
            <div class="cc-section-grid">
                @foreach($unattachedSections as $section)
                    <label class="cc-section-item" x-bind:class="$wire.selectedSectionIds.includes({{ $section->id }}) ? 'selected' : ''">
                        <input type="checkbox"
                               wire:model="selectedSectionIds"
                               value="{{ $section->id }}">
                        <div class="cc-section-info">
                            <div class="cc-section-name">{{ $section->title }}</div>
                            <div class="cc-section-meta">
                                {{ $section->lessons_count }} {{ Str::plural('lesson', $section->lessons_count) }}
                                &nbsp;·&nbsp; Sort order: {{ $section->order }}
                            </div>
                        </div>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" style="width:16px;height:16px;color:var(--t2);flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Save bar ── --}}
<div class="cc-save-bar cca cc10">
    <button type="button" wire:click="create" wire:loading.attr="disabled" class="cc-btn cc-btn-blue">
        <span wire:loading.remove wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </span>
        <span wire:loading wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Create Course
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="cc-btn cc-btn-gray">Cancel</a>
</div>

</div>
