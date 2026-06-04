@php
    /** @var \App\Domains\Courses\Models\Course $record */
    $course = $record;
    $course->loadMissing(['instructor', 'category', 'approvedBy']);

    $sectionsCount   = $course->sections()->count();
    $lessonsCount    = $course->lessons()->count();
    $enrollmentCount = $course->enrollments()->count();
    $reviewCount     = $course->reviews()->count();

    $statusStyle = match ($course->status) {
        'published'     => ['bg' => 'rgba(52,211,153,.14)',  'color' => '#34d399', 'label' => 'Published'],
        'pending_review'=> ['bg' => 'rgba(251,191,36,.14)',  'color' => '#fbbf24', 'label' => 'Pending Review'],
        'draft'         => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#94a3b8', 'label' => 'Draft'],
        'rejected'      => ['bg' => 'rgba(248,113,113,.14)', 'color' => '#f87171', 'label' => 'Rejected'],
        'archived'      => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#94a3b8', 'label' => 'Archived'],
        default         => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#94a3b8', 'label' => ucfirst($course->status)],
    };

    $levelStyle = match ($course->level ?? '') {
        'beginner'     => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399'],
        'intermediate' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24'],
        'advanced'     => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171'],
        default        => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8'],
    };

    $thumbnailUrl = $course->thumbnail_url;

    $editUrl = url('/admin/courses/' . $course->id . '/edit');
    $backUrl = url('/admin/courses');
    $studentsUrl = url('/admin/courses/' . $course->id . '/students');
@endphp

<style>
.cv, .cv *, .cv *::before, .cv *::after { box-sizing: border-box; margin: 0; padding: 0; }
.cv {
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
    font-size: 13px; line-height: 1.5;
    display: grid; gap: 20px; padding-bottom: 48px;
    --p1: #1e293b; --p2: #263245;
    --bd: rgba(255,255,255,.07); --bd2: rgba(255,255,255,.13);
    --t1: #e2e8f0; --t2: #64748b; --t3: #334155;
    --sh: 0 4px 24px rgba(0,0,0,.3);
    --accent: #7c3aed;
    color: var(--t1);
}
html:not(.dark) .cv {
    --p1: #ffffff; --p2: #f8fafc;
    --bd: rgba(15,23,42,.08); --bd2: rgba(15,23,42,.14);
    --t1: #0f172a; --t2: #64748b; --t3: #cbd5e1;
    --sh: 0 2px 16px rgba(15,23,42,.1);
}

/* Header */
.cv-topbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding-bottom: 20px; border-bottom: 1px solid var(--bd); }
.cv-topbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.cv-topbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.cv-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 9px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; border: none; transition: all .15s; white-space: nowrap; }
.cv-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.cv-btn-gray { background: var(--p2); border: 1px solid var(--bd2); color: var(--t2); }
.cv-btn-gray:hover { color: var(--t1); border-color: var(--accent); }
.cv-btn-primary { background: var(--accent); color: #fff; border: 1px solid transparent; }
.cv-btn-primary:hover { opacity: .88; }
.cv-btn-success { background: rgba(52,211,153,.15); color: #34d399; border: 1px solid rgba(52,211,153,.3); }
.cv-btn-success:hover { background: rgba(52,211,153,.25); }
.cv-btn-danger { background: rgba(248,113,113,.12); color: #f87171; border: 1px solid rgba(248,113,113,.25); }
.cv-btn-danger:hover { background: rgba(248,113,113,.22); }
.cv-btn-warning { background: rgba(251,191,36,.12); color: #fbbf24; border: 1px solid rgba(251,191,36,.25); }
.cv-btn-warning:hover { background: rgba(251,191,36,.22); }

/* Card */
.cv-card { background: var(--p1); border: 1px solid var(--bd); border-radius: 14px; overflow: hidden; box-shadow: var(--sh); }
.cv-card-header { padding: 16px 20px; border-bottom: 1px solid var(--bd); display: flex; align-items: center; gap: 8px; }
.cv-card-header-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(124,58,237,.12); flex-shrink: 0; }
.cv-card-header-icon svg { width: 16px; height: 16px; color: var(--accent); }
.cv-card-title { font-size: 13px; font-weight: 750; color: var(--t1); }
.cv-card-body { padding: 20px; }

/* Hero */
.cv-hero { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }
@media (max-width: 768px) { .cv-hero { grid-template-columns: 1fr; } }
.cv-thumbnail { width: 100%; aspect-ratio: 16/9; border-radius: 10px; object-fit: cover; background: var(--p2); border: 1px solid var(--bd2); display: flex; align-items: center; justify-content: center; overflow: hidden; }
.cv-thumbnail img { width: 100%; height: 100%; object-fit: cover; }
.cv-thumbnail-placeholder { display: flex; flex-direction: column; align-items: center; gap: 6px; color: var(--t3); }
.cv-thumbnail-placeholder svg { width: 36px; height: 36px; opacity: .4; }
.cv-hero-meta { display: flex; flex-direction: column; gap: 12px; }
.cv-course-title { font-size: clamp(18px, 2.2vw, 26px); font-weight: 800; color: var(--t1); line-height: 1.2; letter-spacing: -.02em; }
.cv-short-desc { font-size: 13px; color: var(--t2); line-height: 1.6; }
.cv-badges { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.cv-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 7px; font-size: 11.5px; font-weight: 700; }
.cv-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.cv-price { font-size: 22px; font-weight: 800; color: var(--t1); }
.cv-meta-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.cv-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--t2); }
.cv-meta-item svg { width: 13px; height: 13px; color: var(--t2); }

/* Stats grid */
.cv-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
@media (max-width: 640px) { .cv-stats { grid-template-columns: repeat(2, 1fr); } }
.cv-stat { background: var(--p2); border: 1px solid var(--bd); border-radius: 10px; padding: 14px 16px; display: flex; flex-direction: column; gap: 4px; }
.cv-stat-val { font-size: 22px; font-weight: 800; color: var(--t1); }
.cv-stat-label { font-size: 11px; font-weight: 600; color: var(--t2); text-transform: uppercase; letter-spacing: .05em; }
.cv-stat-link { text-decoration: none; transition: border-color .15s; cursor: pointer; }
.cv-stat-link:hover { border-color: var(--accent); }
.cv-stat-link .cv-stat-val { color: var(--accent); }

/* Grid sections */
.cv-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 768px) { .cv-grid-2 { grid-template-columns: 1fr; } }
.cv-field { display: flex; flex-direction: column; gap: 4px; padding: 12px 0; border-bottom: 1px solid var(--bd); }
.cv-field:last-child { border-bottom: none; padding-bottom: 0; }
.cv-field-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--t2); }
.cv-field-value { font-size: 13px; color: var(--t1); font-weight: 500; }
.cv-field-value.muted { color: var(--t2); font-style: italic; }
.cv-field-value.mono { font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: 12px; word-break: break-all; }
.cv-field-full { grid-column: 1 / -1; }

/* Alert */
.cv-alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; }
.cv-alert-success { background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.3); color: #34d399; }
.cv-alert-error { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); color: #f87171; }

/* Reject modal */
.cv-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 9998; align-items: center; justify-content: center; }
.cv-modal-overlay.open { display: flex; }
.cv-modal { background: var(--p1); border: 1px solid var(--bd2); border-radius: 16px; padding: 28px; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
.cv-modal h3 { font-size: 16px; font-weight: 750; color: var(--t1); margin-bottom: 6px; }
.cv-modal p { font-size: 12.5px; color: var(--t2); margin-bottom: 18px; }
.cv-modal textarea { width: 100%; background: var(--p2); border: 1px solid var(--bd2); border-radius: 9px; padding: 10px 14px; color: var(--t1); font-size: 13px; font-family: inherit; resize: vertical; min-height: 100px; outline: none; }
.cv-modal textarea:focus { border-color: var(--accent); }
.cv-modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
</style>

<div class="cv">

    {{-- Top bar --}}
    <div class="cv-topbar">
        <div class="cv-topbar-left">
            <a href="{{ $backUrl }}" class="cv-btn cv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Courses
            </a>
            <span style="font-size:12px;color:var(--t2)">Course #{{ $course->id }}</span>
        </div>
        <div class="cv-topbar-right">
            @if($course->isPendingReview())
            <button type="button" wire:click="approveCourse" wire:loading.attr="disabled" class="cv-btn cv-btn-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                <span wire:loading.remove wire:target="approveCourse">Approve & Publish</span>
                <span wire:loading wire:target="approveCourse">Approving…</span>
            </button>
            <button type="button" class="cv-btn cv-btn-danger" onclick="document.getElementById('cv-reject-modal').classList.add('open')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Reject
            </button>
            @endif
            @if($course->isPublished())
            <button type="button" wire:click="archiveCourse" wire:loading.attr="disabled" class="cv-btn cv-btn-warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v.375c0 .621.504 1.125 1.125 1.125z"/></svg>
                <span wire:loading.remove wire:target="archiveCourse">Archive</span>
                <span wire:loading wire:target="archiveCourse">Archiving…</span>
            </button>
            @endif
            <a href="{{ $editUrl }}" class="cv-btn cv-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Hero card --}}
    <div class="cv-card">
        <div class="cv-card-body">
            <div class="cv-hero">
                <div class="cv-thumbnail">
                    @if($thumbnailUrl)
                        <img src="{{ $thumbnailUrl }}" alt="{{ $course->title }}">
                    @else
                        <div class="cv-thumbnail-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
                            <span style="font-size:11px">No thumbnail</span>
                        </div>
                    @endif
                </div>
                <div class="cv-hero-meta">
                    <div class="cv-course-title">{{ $course->title }}</div>
                    @if($course->short_description)
                    <div class="cv-short-desc">{{ $course->short_description }}</div>
                    @endif
                    <div class="cv-badges">
                        <span class="cv-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                            <span class="cv-dot" style="background:{{ $statusStyle['color'] }}"></span>
                            {{ $statusStyle['label'] }}
                        </span>
                        @if($course->level)
                        <span class="cv-badge" style="background:{{ $levelStyle['bg'] }};color:{{ $levelStyle['color'] }}">{{ ucfirst($course->level) }}</span>
                        @endif
                        @if($course->language)
                        <span class="cv-badge" style="background:rgba(99,102,241,.12);color:#818cf8">{{ $course->language }}</span>
                        @endif
                        @if($course->visibility)
                        <span class="cv-badge" style="background:rgba(148,163,184,.1);color:#94a3b8">{{ ucfirst($course->visibility) }}</span>
                        @endif
                    </div>
                    <div class="cv-price">${{ number_format((float)$course->price, 2) }}</div>
                    <div class="cv-meta-row">
                        @if($course->instructor)
                        <div class="cv-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            {{ $course->instructor->name }}
                        </div>
                        @endif
                        @if($course->category)
                        <div class="cv-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                            {{ $course->category->name }}
                        </div>
                        @endif
                        @if($course->duration)
                        <div class="cv-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                            {{ $course->duration }} min
                        </div>
                        @endif
                        <div class="cv-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            {{ $course->created_at?->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="cv-stats">
        <a href="{{ $studentsUrl }}" class="cv-stat cv-stat-link" title="View enrolled students">
            <div class="cv-stat-val">{{ number_format($enrollmentCount) }}</div>
            <div class="cv-stat-label">Students</div>
        </a>
        <a href="{{ route('filament.admin.pages.sections', ['course_id' => $course->id]) }}" class="cv-stat cv-stat-link" title="View sections">
            <div class="cv-stat-val">{{ number_format($sectionsCount) }}</div>
            <div class="cv-stat-label">Sections</div>
        </a>
        <a href="{{ route('filament.admin.pages.lessons', ['course_id' => $course->id]) }}" class="cv-stat cv-stat-link" title="View lessons">
            <div class="cv-stat-val">{{ number_format($lessonsCount) }}</div>
            <div class="cv-stat-label">Lessons</div>
        </a>
        <a href="{{ route('filament.admin.pages.reviews', ['course_id' => $course->id]) }}" class="cv-stat cv-stat-link" title="View reviews">
            <div class="cv-stat-val">{{ number_format($reviewCount) }}</div>
            <div class="cv-stat-label">Reviews</div>
        </a>
    </div>

    {{-- Content + Instructor row --}}
    <div class="cv-grid-2">

        {{-- Course content --}}
        <div class="cv-card">
            <div class="cv-card-header">
                <div class="cv-card-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                </div>
                <span class="cv-card-title">Course Content</span>
            </div>
            <div class="cv-card-body" style="display:grid;gap:0">
                <div class="cv-field">
                    <div class="cv-field-label">Description</div>
                    <div class="cv-field-value" style="white-space:pre-line">{{ strip_tags($course->description ?? '') ?: '—' }}</div>
                </div>
                <div class="cv-field">
                    <div class="cv-field-label">Requirements</div>
                    <div class="cv-field-value {{ !$course->requirements ? 'muted' : '' }}">{{ $course->requirements ?: 'No requirements' }}</div>
                </div>
                <div class="cv-field" style="border-bottom:none">
                    <div class="cv-field-label">What You Will Learn</div>
                    <div class="cv-field-value {{ !$course->what_you_will_learn ? 'muted' : '' }}">{{ $course->what_you_will_learn ?: 'No learning outcomes' }}</div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Instructor --}}
            <div class="cv-card">
                <div class="cv-card-header">
                    <div class="cv-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <span class="cv-card-title">Instructor</span>
                </div>
                <div class="cv-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                    <div class="cv-field">
                        <div class="cv-field-label">Name</div>
                        <div class="cv-field-value">{{ $course->instructor?->name ?? '—' }}</div>
                    </div>
                    <div class="cv-field">
                        <div class="cv-field-label">Email</div>
                        <div class="cv-field-value">{{ $course->instructor?->email ?? '—' }}</div>
                    </div>
                    <div class="cv-field">
                        <div class="cv-field-label">Commission</div>
                        <div class="cv-field-value">{{ $course->commission_percentage }}%</div>
                    </div>
                    <div class="cv-field">
                        <div class="cv-field-label">Certificate</div>
                        <div class="cv-field-value">{{ $course->certificate_enabled ? 'Enabled' : 'Disabled' }}</div>
                    </div>
                    @if($course->preview_video_url)
                    <div class="cv-field cv-field-full">
                        <div class="cv-field-label">Preview Video</div>
                        <div class="cv-field-value"><a href="{{ $course->preview_video_url }}" target="_blank" style="color:var(--accent)">{{ $course->preview_video_url }}</a></div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Approval --}}
            <div class="cv-card">
                <div class="cv-card-header">
                    <div class="cv-card-header-icon" style="background:rgba(52,211,153,.1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" style="color:#34d399"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <span class="cv-card-title">Approval</span>
                </div>
                <div class="cv-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                    <div class="cv-field">
                        <div class="cv-field-label">Approved By</div>
                        <div class="cv-field-value {{ !$course->approvedBy ? 'muted' : '' }}">{{ $course->approvedBy?->name ?? 'Not approved' }}</div>
                    </div>
                    <div class="cv-field">
                        <div class="cv-field-label">Approved At</div>
                        <div class="cv-field-value {{ !$course->approved_at ? 'muted' : '' }}">{{ $course->approved_at?->format('M d, Y H:i') ?? 'Not approved' }}</div>
                    </div>
                    <div class="cv-field">
                        <div class="cv-field-label">Created</div>
                        <div class="cv-field-value">{{ $course->created_at?->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="cv-field">
                        <div class="cv-field-label">Updated</div>
                        <div class="cv-field-value">{{ $course->updated_at?->format('M d, Y H:i') }}</div>
                    </div>
                    @if($course->isRejected() && $course->rejection_reason)
                    <div class="cv-field cv-field-full" style="grid-column:1/-1;border-bottom:none">
                        <div class="cv-field-label" style="color:#f87171">Rejection Reason</div>
                        <div class="cv-field-value" style="color:#f87171">{{ $course->rejection_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Reject modal --}}
@if($course->isPendingReview())
<div class="cv-modal-overlay" id="cv-reject-modal" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="cv-modal">
        <h3>Reject Course</h3>
        <p>Explain what the instructor needs to fix. They'll receive an email with your feedback.</p>
        <textarea id="cv-reject-reason" placeholder="e.g. Video quality is too low, please re-record lessons 3–5 with better audio..."></textarea>
        <div class="cv-modal-footer">
            <button type="button" class="cv-btn cv-btn-gray" onclick="document.getElementById('cv-reject-modal').classList.remove('open')">Cancel</button>
            <button type="button" class="cv-btn cv-btn-danger" onclick="
                var reason = document.getElementById('cv-reject-reason').value.trim();
                if(!reason){ alert('Rejection reason is required.'); return; }
                document.getElementById('cv-reject-modal').classList.remove('open');
                @this.call('rejectCourse', reason);
            ">Reject Course</button>
        </div>
    </div>
</div>
@endif
