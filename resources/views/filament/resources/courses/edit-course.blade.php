@php
    /** @var \App\Domains\Courses\Models\Course $record */
    $crs = $record;

    $thumbnailUrl = $crs->thumbnail ? asset('storage/' . $crs->thumbnail) : null;

    $statusStyle = match($crs->status) {
        'published'      => ['bg'=>'rgba(52,211,153,.1)',  'border'=>'rgba(52,211,153,.25)',  'color'=>'#059669', 'label'=>'Published'],
        'pending_review' => ['bg'=>'rgba(251,191,36,.1)',  'border'=>'rgba(251,191,36,.25)',  'color'=>'#d97706', 'label'=>'Pending Review'],
        'draft'          => ['bg'=>'rgba(100,116,139,.08)','border'=>'rgba(100,116,139,.2)',  'color'=>'#64748b', 'label'=>'Draft'],
        'rejected'       => ['bg'=>'rgba(239,68,68,.1)',   'border'=>'rgba(239,68,68,.25)',   'color'=>'#ef4444', 'label'=>'Rejected'],
        'archived'       => ['bg'=>'rgba(100,116,139,.08)','border'=>'rgba(100,116,139,.2)',  'color'=>'#64748b', 'label'=>'Archived'],
        default          => ['bg'=>'rgba(100,116,139,.08)','border'=>'rgba(100,116,139,.2)',  'color'=>'#64748b', 'label'=>ucfirst($crs->status ?? '')],
    };

    $levelStyle = match($crs->level) {
        'beginner'     => ['color'=>'#10b981', 'label'=>'Beginner'],
        'intermediate' => ['color'=>'#f59e0b', 'label'=>'Intermediate'],
        'advanced'     => ['color'=>'#ef4444', 'label'=>'Advanced'],
        default        => ['color'=>'#64748b', 'label'=>ucfirst($crs->level ?? '—')],
    };
@endphp

<div class="cr">

<style>
.cr,.cr *,.cr *::before,.cr *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cr {
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
html:not(.dark) .cr {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes crUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.cra {
    opacity:0;
    animation:crUp .38s cubic-bezier(.16,1,.3,1) forwards;
}
.cr1 {
    animation-delay:.04s;
}
.cr2 {
    animation-delay:.09s;
}
.cr3 {
    animation-delay:.14s;
}
.cr4 {
    animation-delay:.19s;
}
.cr5 {
    animation-delay:.24s;
}
.cr6 {
    animation-delay:.28s;
}
.cr7 {
    animation-delay:.32s;
}
.cr8 {
    animation-delay:.36s;
}
.cr9 {
    animation-delay:.40s;
}

/* ── Header ── */
.cr-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.cr-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.cr-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
}
.cr-btn {
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
.cr-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.cr-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.cr-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.cr-btn-blue {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.cr-btn-blue:hover {
    background:var(--accent2);
}
.cr-btn-outline {
    background:transparent;
    border:1px solid var(--bd2);
    color:var(--t2);
}
.cr-btn-outline:hover {
    border-color:#ef4444;
    color:#ef4444;
}
.cr-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}

/* ── Hero ── */
.cr-hero {
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
.cr-thumb {
    width:80px;
    height:80px;
    border-radius:14px;
    object-fit:cover;
    border:2px solid var(--bd2);
    flex-shrink:0;
}
.cr-thumb-placeholder {
    width:80px;
    height:80px;
    border-radius:14px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(59,130,246,.12);
    color:var(--accent);
}
.cr-thumb-placeholder svg {
    width:34px;
    height:34px;
}
.cr-hero-info {
    flex:1;
    min-width:0;
}
.cr-hero-title {
    font-size:20px;
    font-weight:780;
    color:var(--t1);
    letter-spacing:-.015em;
    line-height:1.2;
}
.cr-hero-meta {
    font-size:12px;
    color:var(--t2);
    margin-top:3px;
}
.cr-hero-pills {
    display:flex;
    align-items:center;
    gap:6px;
    margin-top:10px;
    flex-wrap:wrap;
}
.cr-pill {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    border:1px solid;
}
.cr-hero-stats {
    display:flex;
    gap:0;
    flex-shrink:0;
}
.cr-stat {
    text-align:center;
    padding:0 20px;
    border-left:1px solid var(--bd);
}
.cr-stat-val {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.cr-stat-label {
    font-size:10px;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    margin-top:2px;
}

/* ── Cards ── */
.cr-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    overflow:hidden;
}
.cr-card-head {
    padding:18px 22px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:12px;
}
.cr-card-icon {
    width:34px;
    height:34px;
    border-radius:9px;
    display:grid;
    place-items:center;
    background:rgba(59,130,246,.1);
    color:var(--accent);
    flex-shrink:0;
}
.cr-card-icon svg {
    width:16px;
    height:16px;
}
.cr-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.cr-card-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cr-card-body {
    padding:22px;
    display:grid;
    gap:18px;
}

/* ── Custom fields ── */
.cr-label {
    display:block;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.cr-label span {
    color:#ef4444;
    margin-left:2px;
}
.cr-input {
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
.cr-input:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,130,246,.13);
}
textarea.cr-input {
    resize:vertical;
    min-height:100px;
}
select.cr-input {
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:18px;
    padding-right:36px;
    cursor:pointer;
}
.cr-error {
    display:block;
    font-size:11.5px;
    color:#ef4444;
    margin-top:5px;
}
.cr-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}
.cr-grid-3 {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:18px;
}
@media(max-width:700px) {
    .cr-grid-3 {
        grid-template-columns:1fr 1fr;
    }
}
@media(max-width:560px) {
    .cr-grid-2,.cr-grid-3 {
        grid-template-columns:1fr;
    }
}

/* ── Toggle ── */
.cr-toggle-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:14px 0;
    border-bottom:1px solid var(--bd);
}
.cr-toggle-row:last-of-type {
    border-bottom:none;
    padding-bottom:0;
}
.cr-toggle-info .cr-toggle-title {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.cr-toggle-info .cr-toggle-desc {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cr-toggle-wrap {
    position:relative;
    width:44px;
    height:24px;
    flex-shrink:0;
}
.cr-toggle-wrap input {
    opacity:0;
    width:0;
    height:0;
    position:absolute;
}
.cr-toggle-track {
    position:absolute;
    inset:0;
    border-radius:12px;
    background:var(--t3);
    cursor:pointer;
    transition:background .2s;
}
.cr-toggle-wrap input:checked ~ .cr-toggle-track {
    background:var(--accent);
}
.cr-toggle-thumb {
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
.cr-toggle-wrap input:checked ~ .cr-toggle-track .cr-toggle-thumb {
    transform:translateX(20px);
}

/* ── Sub-form overrides (description + thumbnail) ── */
.cr-desc-wrap .fi-fo-field-wrp-label-content, .cr-thumb-wrap .fi-fo-field-wrp-label-content {
    font-size:11px!important;
    font-weight:700!important;
    text-transform:uppercase!important;
    letter-spacing:.05em!important;
    color:var(--t2)!important;
}
.cr-desc-wrap .fi-rte {
    border-color:var(--bd2)!important;
    border-radius:9px!important;
    overflow:hidden!important;
}
.cr-desc-wrap .fi-rte-toolbar {
    border-color:var(--bd2)!important;
    background:var(--p2)!important;
}
.cr-desc-wrap .fi-rte-content {
    background:var(--p2)!important;
    color:var(--t1)!important;
    min-height:180px;
}
.cr-thumb-wrap .fi-fo-field-wrp {
    width:100%!important;
}
.cr-thumb-wrap .fi-fo-file-upload {
    border-color:var(--bd2)!important;
    border-radius:9px!important;
    background:var(--p2)!important;
}

/* ── Save bar ── */
.cr-save-bar {
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
<div class="cr-header cra cr1">
    <h1 class="cr-page-title">Edit Course</h1>
    <div class="cr-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="cr-btn cr-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to courses
        </a>
        <button type="button" wire:click="mountAction('delete')" class="cr-btn cr-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            Delete
        </button>
    </div>
</div>

{{-- ── Hero ── --}}
<div class="cr-hero cra cr2">
    @if ($thumbnailUrl)
        <img src="{{ $thumbnailUrl }}" alt="{{ $crs->title }}" class="cr-thumb">
    @else
        <div class="cr-thumb-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
        </div>
    @endif
    <div class="cr-hero-info">
        <div class="cr-hero-title">{{ $crs->title }}</div>
        <div class="cr-hero-meta">{{ $crs->instructor?->name ?? '—' }} &middot; {{ $crs->category?->name ?? '—' }}</div>
        <div class="cr-hero-pills">
            <span class="cr-pill" style="background:{{ $statusStyle['bg'] }};border-color:{{ $statusStyle['border'] }};color:{{ $statusStyle['color'] }}">
                {{ $statusStyle['label'] }}
            </span>
            <span class="cr-pill" style="background:rgba(100,116,139,.08);border-color:rgba(100,116,139,.2);color:{{ $levelStyle['color'] }}">
                {{ $levelStyle['label'] }}
            </span>
            @if($crs->is_published)
                <span class="cr-pill" style="background:rgba(59,130,246,.1);border-color:rgba(59,130,246,.25);color:#3b82f6">Live</span>
            @endif
            @if($crs->certificate_enabled)
                <span class="cr-pill" style="background:rgba(251,191,36,.08);border-color:rgba(251,191,36,.2);color:#d97706">Certificate</span>
            @endif
        </div>
    </div>
    <div class="cr-hero-stats">
        <div class="cr-stat">
            <div class="cr-stat-val">${{ number_format($crs->price ?? 0, 0) }}</div>
            <div class="cr-stat-label">Price</div>
        </div>
        <div class="cr-stat">
            <div class="cr-stat-val">{{ $enrollmentCount }}</div>
            <div class="cr-stat-label">Students</div>
        </div>
        @if($reviewCount > 0)
        <div class="cr-stat">
            <div class="cr-stat-val">{{ $avgRating }}</div>
            <div class="cr-stat-label">Rating</div>
        </div>
        @endif
    </div>
</div>

{{-- ── Basic Information card ── --}}
<div class="cr-card cra cr3">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Basic Information</div>
            <div class="cr-card-sub">Title, slug, and short description</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div class="cr-grid-2">
            <div>
                <label class="cr-label" for="cr-title">Title <span>*</span></label>
                <input id="cr-title" type="text" class="cr-input" wire:model="data.title" placeholder="Course title" required>
                @error('data.title') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cr-label" for="cr-slug">Slug <span>*</span></label>
                <input id="cr-slug" type="text" class="cr-input" wire:model="data.slug" placeholder="course-slug">
                @error('data.slug') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="cr-label" for="cr-short-desc">Short Description</label>
            <textarea id="cr-short-desc" class="cr-input" wire:model="data.short_description" placeholder="Brief one-liner about the course…" rows="3"></textarea>
            @error('data.short_description') <span class="cr-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Description card (RichEditor sub-form) ── --}}
<div class="cr-card cra cr4">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Full Description</div>
            <div class="cr-card-sub">Rich text content shown to students</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div class="cr-desc-wrap">
            {{ $this->descriptionForm }}
        </div>
    </div>
</div>

{{-- ── Media card ── --}}
<div class="cr-card cra cr5">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Media</div>
            <div class="cr-card-sub">Course thumbnail and preview video</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div class="cr-thumb-wrap">
            {{ $this->thumbnailForm }}
        </div>
        <div>
            <label class="cr-label" for="cr-video-url">Preview Video URL</label>
            <input id="cr-video-url" type="url" class="cr-input" wire:model="data.preview_video_url" placeholder="https://youtube.com/…">
            @error('data.preview_video_url') <span class="cr-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Course Details card ── --}}
<div class="cr-card cra cr6">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Course Details</div>
            <div class="cr-card-sub">Instructor, category, price, level, and language</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div class="cr-grid-2">
            <div>
                <label class="cr-label" for="cr-instructor">Instructor <span>*</span></label>
                <select id="cr-instructor" class="cr-input" wire:model="data.instructor_id">
                    <option value="">— Select instructor —</option>
                    @foreach ($instructors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('data.instructor_id') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cr-label" for="cr-category">Category <span>*</span></label>
                <select id="cr-category" class="cr-input" wire:model="data.category_id">
                    <option value="">— Select category —</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('data.category_id') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="cr-grid-3">
            <div>
                <label class="cr-label" for="cr-price">
                    Price <span>*</span>
                    @if(!$canEditPrice)
                    <span style="margin-left:6px;font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#f59e0b;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:4px;padding:1px 6px">Admin only</span>
                    @endif
                </label>
                @if($canEditPrice)
                    <input id="cr-price" type="number" class="cr-input" wire:model="data.price" placeholder="0.00" min="0" step="0.01">
                    @error('data.price') <span class="cr-error">{{ $message }}</span> @enderror
                @else
                    <div style="display:flex;align-items:center;gap:10px;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:10px 13px">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="width:15px;height:15px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
                        <span style="font-size:15px;font-weight:700;color:var(--t1)">${{ number_format($crs->price ?? 0, 2) }}</span>
                        <span style="font-size:11px;color:var(--t2);margin-left:auto">Contact admin to change</span>
                    </div>
                @endif
            </div>
            <div>
                <label class="cr-label" for="cr-duration">Duration (minutes)</label>
                <input id="cr-duration" type="number" class="cr-input" wire:model="data.duration" placeholder="60" min="0">
                @error('data.duration') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cr-label" for="cr-language">Language <span>*</span></label>
                <input id="cr-language" type="text" class="cr-input" wire:model="data.language" placeholder="English">
                @error('data.language') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div>
            <label class="cr-label" for="cr-level">Level <span>*</span></label>
            <select id="cr-level" class="cr-input" wire:model="data.level">
                <option value="">— Select level —</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            @error('data.level') <span class="cr-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Learning Content card ── --}}
<div class="cr-card cra cr7">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Learning Content</div>
            <div class="cr-card-sub">Requirements and learning outcomes</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div class="cr-grid-2">
            <div>
                <label class="cr-label" for="cr-requirements">Requirements</label>
                <textarea id="cr-requirements" class="cr-input" wire:model="data.requirements" placeholder="What should students know before starting?" rows="5"></textarea>
                @error('data.requirements') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cr-label" for="cr-outcomes">What You Will Learn</label>
                <textarea id="cr-outcomes" class="cr-input" wire:model="data.what_you_will_learn" placeholder="What will students learn from this course?" rows="5"></textarea>
                @error('data.what_you_will_learn') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Publishing card ── --}}
<div class="cr-card cra cr8">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Publishing</div>
            <div class="cr-card-sub">Status, visibility, and feature flags</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div class="cr-grid-2">
            <div>
                <label class="cr-label" for="cr-status">Status <span>*</span></label>
                <select id="cr-status" class="cr-input" wire:model="data.status">
                    <option value="">— Select status —</option>
                    <option value="draft">Draft</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="published">Published</option>
                    <option value="rejected">Rejected</option>
                    <option value="archived">Archived</option>
                </select>
                @error('data.status') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cr-label" for="cr-visibility">Visibility <span>*</span></label>
                <select id="cr-visibility" class="cr-input" wire:model="data.visibility">
                    <option value="">— Select visibility —</option>
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                    <option value="unlisted">Unlisted</option>
                </select>
                @error('data.visibility') <span class="cr-error">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="cr-toggle-row">
            <div class="cr-toggle-info">
                <div class="cr-toggle-title">Published</div>
                <div class="cr-toggle-desc">Make this course visible to students on the platform</div>
            </div>
            <label class="cr-toggle-wrap">
                <input type="checkbox" wire:model="data.is_published" value="1">
                <div class="cr-toggle-track"><div class="cr-toggle-thumb"></div></div>
            </label>
        </div>

        <div class="cr-toggle-row">
            <div class="cr-toggle-info">
                <div class="cr-toggle-title">Enable Certificate</div>
                <div class="cr-toggle-desc">Issue completion certificates to students who finish this course</div>
            </div>
            <label class="cr-toggle-wrap">
                <input type="checkbox" wire:model="data.certificate_enabled" value="1">
                <div class="cr-toggle-track"><div class="cr-toggle-thumb"></div></div>
            </label>
        </div>
    </div>
</div>

{{-- ── Platform Settings card ── --}}
<div class="cr-card cra cr9">
    <div class="cr-card-head">
        <div class="cr-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
        </div>
        <div>
            <div class="cr-card-title">Platform Settings</div>
            <div class="cr-card-sub">Commission and administrative notes</div>
        </div>
    </div>
    <div class="cr-card-body">
        <div>
            <label class="cr-label" for="cr-commission">
                Commission Percentage
                @if(!$canEditPrice)
                <span style="margin-left:6px;font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#f59e0b;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:4px;padding:1px 6px">Admin only</span>
                @endif
            </label>
            @if($canEditPrice)
                <input id="cr-commission" type="number" class="cr-input" wire:model="data.commission_percentage" placeholder="20" min="0" max="100" step="0.1" style="max-width:200px">
                @error('data.commission_percentage') <span class="cr-error">{{ $message }}</span> @enderror
            @else
                <div style="display:flex;align-items:center;gap:10px;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:10px 13px;max-width:200px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="width:15px;height:15px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
                    <span style="font-size:15px;font-weight:700;color:var(--t1)">{{ $crs->commission_percentage ?? 20 }}%</span>
                </div>
            @endif
        </div>
        <div>
            <label class="cr-label" for="cr-rejection">Rejection Reason</label>
            <textarea id="cr-rejection" class="cr-input" wire:model="data.rejection_reason" placeholder="Reason for rejection (if applicable)…" rows="3"></textarea>
            @error('data.rejection_reason') <span class="cr-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- ── Save bar ── --}}
<div class="cr-save-bar cra cr9">
    <button type="button" wire:click="save" wire:loading.attr="disabled" class="cr-btn cr-btn-blue">
        <span wire:loading.remove wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </span>
        <span wire:loading wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Save changes
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="cr-btn cr-btn-gray">Cancel</a>
</div>

</div>
