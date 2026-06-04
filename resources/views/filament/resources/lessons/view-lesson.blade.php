@php
    /** @var \App\Domains\Courses\Models\Lesson $record */
    $lesson = $record;
    $lesson->loadMissing(['section', 'section.course']);

    $typeColors = [
        'video'      => ['bg'=>'rgba(96,165,250,.12)',  'color'=>'#60a5fa',  'label'=>'Video'],
        'article'    => ['bg'=>'rgba(52,211,153,.12)',  'color'=>'#34d399',  'label'=>'Article'],
        'quiz'       => ['bg'=>'rgba(251,191,36,.12)',  'color'=>'#fbbf24',  'label'=>'Quiz'],
        'live'       => ['bg'=>'rgba(248,113,113,.12)', 'color'=>'#f87171',  'label'=>'Live'],
        'assignment' => ['bg'=>'rgba(167,139,250,.12)', 'color'=>'#a78bfa',  'label'=>'Assignment'],
    ];
    $tc = $typeColors[$lesson->type] ?? ['bg'=>'rgba(148,163,184,.1)','color'=>'#94a3b8','label'=>ucfirst($lesson->type ?? '')];
@endphp

<style>
.lv, .lv *, .lv *::before, .lv *::after { box-sizing:border-box; margin:0; padding:0; }
.lv {
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    font-size:13px; line-height:1.5;
    display:grid; gap:20px; padding-bottom:48px;
    --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b;
    --sh:0 4px 24px rgba(0,0,0,.3);
    --accent:#7c3aed;
    color:var(--t1);
}
html:not(.dark) .lv {
    --p1:#fff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.08); --bd2:rgba(15,23,42,.14);
    --t1:#0f172a; --t2:#64748b;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
.lv-topbar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.lv-topbar-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.lv-topbar-right { display:flex; align-items:center; gap:8px; }
.lv-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:12px; font-weight:700; text-decoration:none; border:none; cursor:pointer; transition:all .15s; white-space:nowrap; }
.lv-btn svg { width:14px; height:14px; flex-shrink:0; }
.lv-btn-gray { background:var(--p2); border:1px solid var(--bd2); color:var(--t2); }
.lv-btn-gray:hover { color:var(--t1); border-color:var(--accent); }
.lv-btn-primary { background:var(--accent); color:#fff; border:1px solid transparent; }
.lv-btn-primary:hover { opacity:.88; }

.lv-card { background:var(--p1); border:1px solid var(--bd); border-radius:14px; overflow:hidden; box-shadow:var(--sh); }
.lv-card-header { padding:16px 20px; border-bottom:1px solid var(--bd); display:flex; align-items:center; gap:8px; }
.lv-card-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(124,58,237,.12); flex-shrink:0; }
.lv-card-icon svg { width:16px; height:16px; color:var(--accent); }
.lv-card-title { font-size:13px; font-weight:750; color:var(--t1); }
.lv-card-body { padding:20px; display:grid; gap:0; }

.lv-title { font-size:clamp(18px,2vw,24px); font-weight:800; color:var(--t1); letter-spacing:-.018em; }
.lv-badges { display:flex; align-items:center; gap:6px; margin-top:10px; flex-wrap:wrap; }
.lv-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:7px; font-size:11.5px; font-weight:700; }
.lv-meta { display:flex; align-items:center; gap:14px; margin-top:10px; flex-wrap:wrap; }
.lv-meta-item { display:flex; align-items:center; gap:5px; font-size:12px; color:var(--t2); }
.lv-meta-item svg { width:13px; height:13px; }

.lv-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
.lv-stat { background:var(--p2); border:1px solid var(--bd); border-radius:10px; padding:14px 16px; }
.lv-stat-val { font-size:20px; font-weight:800; color:var(--t1); }
.lv-stat-label { font-size:11px; font-weight:600; color:var(--t2); text-transform:uppercase; letter-spacing:.05em; margin-top:2px; }

.lv-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:768px) { .lv-grid-2 { grid-template-columns:1fr; } }

.lv-field { padding:12px 0; border-bottom:1px solid var(--bd); }
.lv-field:last-child { border-bottom:none; padding-bottom:0; }
.lv-field-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--t2); margin-bottom:4px; }
.lv-field-value { font-size:13px; color:var(--t1); font-weight:500; }
.lv-field-value.muted { color:var(--t2); font-style:italic; }
.lv-field-value a { color:var(--accent); text-decoration:none; }
.lv-field-value a:hover { text-decoration:underline; }
.lv-content-box { background:var(--p2); border:1px solid var(--bd); border-radius:8px; padding:14px; font-size:13px; color:var(--t1); white-space:pre-wrap; line-height:1.7; }
</style>

<div class="lv">

    {{-- Topbar --}}
    <div class="lv-topbar">
        <div class="lv-topbar-left">
            <a href="{{ route('filament.admin.pages.lessons') }}" class="lv-btn lv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Lessons
            </a>
            @if($lesson->section)
            <a href="{{ route('filament.admin.resources.sections.view', $lesson->section) }}" class="lv-btn lv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75z"/></svg>
                {{ $lesson->section->title }}
            </a>
            @endif
            @if($lesson->section?->course)
            <a href="{{ url('/admin/courses/' . $lesson->section->course->id) }}" class="lv-btn lv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                {{ $lesson->section->course->title }}
            </a>
            @endif
        </div>
        <div class="lv-topbar-right">
            <a href="{{ route('filament.admin.resources.lessons.edit', $lesson) }}" class="lv-btn lv-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Hero --}}
    <div class="lv-card">
        <div class="lv-card-body" style="padding:20px">
            <div class="lv-title">{{ $lesson->title }}</div>
            <div class="lv-badges">
                <span class="lv-badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }}">{{ $tc['label'] }}</span>
                <span class="lv-badge" style="background:{{ $lesson->is_preview ? 'rgba(52,211,153,.12)' : 'rgba(148,163,184,.1)' }};color:{{ $lesson->is_preview ? '#34d399' : '#94a3b8' }}">
                    {{ $lesson->is_preview ? 'Free Preview' : 'Premium' }}
                </span>
            </div>
            <div class="lv-meta">
                <div class="lv-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5"/></svg>
                    Order {{ $lesson->order }}
                </div>
                @if($lesson->duration)
                <div class="lv-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    {{ $lesson->duration }} min
                </div>
                @endif
                <div class="lv-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5"/></svg>
                    {{ $lesson->created_at?->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Content + Video row --}}
    <div class="lv-grid-2">

        {{-- Content --}}
        <div class="lv-card">
            <div class="lv-card-header">
                <div class="lv-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
                </div>
                <span class="lv-card-title">Content</span>
            </div>
            <div class="lv-card-body">
                <div class="lv-field" style="border:none;padding-top:0">
                    @if($lesson->content)
                    <div class="lv-content-box">{{ $lesson->content }}</div>
                    @else
                    <div class="lv-field-value muted">No content added.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Video + Attachment --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            <div class="lv-card">
                <div class="lv-card-header">
                    <div class="lv-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653z"/></svg>
                    </div>
                    <span class="lv-card-title">Video</span>
                </div>
                <div class="lv-card-body">
                    <div class="lv-field">
                        <div class="lv-field-label">Provider</div>
                        <div class="lv-field-value {{ !$lesson->video_provider ? 'muted' : '' }}">{{ $lesson->video_provider ?: 'None' }}</div>
                    </div>
                    <div class="lv-field">
                        <div class="lv-field-label">Video URL</div>
                        <div class="lv-field-value">
                            @if($lesson->video_url)
                                <a href="{{ $lesson->video_url }}" target="_blank">{{ $lesson->video_url }}</a>
                            @else
                                <span class="muted">No URL</span>
                            @endif
                        </div>
                    </div>
                    <div class="lv-field">
                        <div class="lv-field-label">Uploaded File</div>
                        @if($lesson->video_path)
                            <video controls style="width:100%;border-radius:8px;margin-top:6px;max-height:320px;background:#000">
                                <source src="{{ $lesson->video_source }}">
                            </video>
                        @else
                            <div class="lv-field-value muted">No uploaded video</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lv-card">
                <div class="lv-card-header">
                    <div class="lv-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                    </div>
                    <span class="lv-card-title">Attachment</span>
                </div>
                <div class="lv-card-body">
                    <div class="lv-field">
                        <div class="lv-field-label">File Name</div>
                        <div class="lv-field-value {{ !$lesson->attachment_name ? 'muted' : '' }}">{{ $lesson->attachment_name ?: 'No attachment' }}</div>
                    </div>
                    <div class="lv-field">
                        <div class="lv-field-label">File</div>
                        @if($lesson->attachment)
                            <a href="{{ $lesson->attachment_url }}" target="_blank" download style="color:var(--accent);font-size:13px">
                                Download {{ $lesson->attachment_name ?: basename($lesson->attachment) }}
                            </a>
                        @else
                            <div class="lv-field-value muted">No file uploaded</div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
