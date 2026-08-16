@php
    /** @var \App\Domains\Courses\Models\Lesson $record */
    $les = $record;
    $les->loadMissing(['section', 'section.course']);

    $typeMap = [
        'video'   => ['bg'=>'rgba(96,165,250,.12)',  'color'=>'#60a5fa',  'label'=>'Video'],
        'article' => ['bg'=>'rgba(52,211,153,.12)',  'color'=>'#34d399',  'label'=>'Article'],
        'file'    => ['bg'=>'rgba(248,113,113,.12)', 'color'=>'#f87171',  'label'=>'File / Document'],
        'live'    => ['bg'=>'rgba(167,139,250,.12)', 'color'=>'#a78bfa',  'label'=>'Live'],
        'assignment' => ['bg'=>'rgba(148,163,184,.1)','color'=>'#94a3b8','label'=>'Assignment'],
    ];
    $tc = $typeMap[$les->type] ?? ['bg'=>'rgba(148,163,184,.1)','color'=>'#94a3b8','label'=>ucfirst($les->type ?? '—')];

    $typeIcons = [
        'video'   => 'M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z',
        'article' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12',
        'file'    => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z',
    ];
    $typeIcon = $typeIcons[$les->type] ?? 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z';

    $attachmentUrl = $les->attachment_url;

    $videoUrl = $les->video_source;
@endphp

<style>
.lv, .lv *, .lv *::before, .lv *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.lv {
    font-family:Inter, ui-sans-serif, system-ui, sans-serif;
    font-size:13px;
    line-height:1.5;
    display:grid;
    gap:20px;
    padding-bottom:48px;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --sh:0 4px 24px rgba(0,0,0,.3);
    --accent:#8b5cf6;
    color:var(--t1);
}
html:not(.dark) .lv {
    --p1:#fff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --sh:0 2px 16px rgba(15,23,42,.1);
}

/* topbar */
.lv-topbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.lv-topbar-left {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}
.lv-topbar-right {
    display:flex;
    align-items:center;
    gap:8px;
}
.lv-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:9px;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    border:none;
    cursor:pointer;
    transition:all .15s;
    white-space:nowrap;
    font-family:inherit;
}
.lv-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.lv-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.lv-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.lv-btn-primary {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.lv-btn-primary:hover {
    opacity:.88;
}

/* cards */
.lv-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    overflow:hidden;
    box-shadow:var(--sh);
}
.lv-card-header {
    padding:16px 20px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:10px;
}
.lv-card-icon {
    width:32px;
    height:32px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(139,92,246,.12);
    flex-shrink:0;
}
.lv-card-icon svg {
    width:16px;
    height:16px;
    color:var(--accent);
}
.lv-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.lv-card-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:1px;
}
.lv-card-body {
    padding:20px;
}

/* hero inside card */
.lv-hero-row {
    display:flex;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
}
.lv-hero-icon {
    width:56px;
    height:56px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.lv-hero-icon svg {
    width:26px;
    height:26px;
}
.lv-hero-body {
    flex:1;
    min-width:0;
}
.lv-hero-title {
    font-size:clamp(17px,2vw,22px);
    font-weight:800;
    color:var(--t1);
    letter-spacing:-.018em;
}
.lv-hero-meta {
    display:flex;
    align-items:center;
    gap:12px;
    margin-top:7px;
    flex-wrap:wrap;
}
.lv-hero-meta-item {
    display:flex;
    align-items:center;
    gap:5px;
    font-size:12px;
    color:var(--t2);
}
.lv-hero-meta-item svg {
    width:13px;
    height:13px;
    flex-shrink:0;
}
.lv-hero-desc {
    font-size:12.5px;
    color:var(--t2);
    margin-top:8px;
    line-height:1.65;
}
.lv-badge {
    display:inline-flex;
    align-items:center;
    padding:3px 9px;
    border-radius:6px;
    font-size:11px;
    font-weight:700;
}

/* stats */
.lv-stats {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
}
@media(max-width:560px) {
    .lv-stats {
        grid-template-columns:1fr 1fr;
    }
}
.lv-stat {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:10px;
    padding:14px 16px;
    box-shadow:var(--sh);
}
.lv-stat-val {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.lv-stat-label {
    font-size:11px;
    font-weight:600;
    color:var(--t2);
    text-transform:uppercase;
    letter-spacing:.05em;
    margin-top:2px;
}

/* fields grid */
.lv-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}
.lv-grid-3 {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
}
@media(max-width:640px) {
    .lv-grid-2, .lv-grid-3 {
        grid-template-columns:1fr;
    }
}
.lv-field {
    display:flex;
    flex-direction:column;
    gap:4px;
}
.lv-label {
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.07em;
    text-transform:uppercase;
    color:var(--t2);
}
.lv-value {
    font-size:13px;
    color:var(--t1);
    word-break:break-word;
}
.lv-value-muted {
    font-size:13px;
    color:var(--t2);
    font-style:italic;
}
.lv-value a {
    color:var(--accent);
    text-decoration:none;
}
.lv-value a:hover {
    text-decoration:underline;
}

/* video embed */
.lv-video-wrap {
    position:relative;
    width:100%;
    padding-bottom:56.25%;
    border-radius:10px;
    overflow:hidden;
    background:#000;
}
.lv-video-wrap iframe, .lv-video-wrap video {
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    border:none;
}

/* file download */
.lv-file-box {
    display:flex;
    align-items:center;
    gap:16px;
    padding:16px 18px;
    border:1px solid var(--bd2);
    border-radius:10px;
    background:var(--p2);
}
.lv-file-icon {
    width:44px;
    height:44px;
    border-radius:10px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(248,113,113,.1);
    color:#f87171;
}
.lv-file-icon svg {
    width:20px;
    height:20px;
}
.lv-file-info {
    flex:1;
    min-width:0;
}
.lv-file-name {
    font-size:13.5px;
    font-weight:700;
    color:var(--t1);
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.lv-file-ext {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.lv-file-dl {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:7px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    background:rgba(248,113,113,.1);
    color:#f87171;
    border:1px solid rgba(248,113,113,.25);
    text-decoration:none;
    transition:opacity .15s;
    flex-shrink:0;
}
.lv-file-dl:hover {
    opacity:.8;
}
.lv-file-dl svg {
    width:13px;
    height:13px;
}
</style>

<div class="lv">

    {{-- ── Topbar ── --}}
    <div class="lv-topbar">
        <div class="lv-topbar-left">
            <a href="{{ $backUrl }}" wire:navigate class="lv-btn lv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Lessons
            </a>
            @if($les->section?->course)
            <a href="{{ url('/admin/courses/'.$les->section->course->id) }}" wire:navigate class="lv-btn lv-btn-gray" title="{{ $les->section->course->title }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px">{{ $les->section->course->title }}</span>
            </a>
            @endif
        </div>
        <div class="lv-topbar-right">
            <a href="{{ route('filament.admin.resources.lessons.edit', $les) }}" wire:navigate class="lv-btn lv-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- ── Hero card ── --}}
    <div class="lv-card">
        <div class="lv-card-body">
            <div class="lv-hero-row">
                <div class="lv-hero-icon" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}"/>
                    </svg>
                </div>
                <div class="lv-hero-body">
                    <div class="lv-hero-title">{{ $les->title }}</div>
                    <div class="lv-hero-meta">
                        <div class="lv-hero-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
                            {{ $sectionTitle }}
                        </div>
                        <div class="lv-hero-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>
                            Order {{ $les->order ?? 1 }}
                        </div>
                        <div class="lv-hero-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/></svg>
                            {{ $les->created_at?->format('M d, Y') }}
                        </div>
                        <span class="lv-badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }}">{{ $tc['label'] }}</span>
                        @if($les->is_preview)
                            <span class="lv-badge" style="background:rgba(245,158,11,.1);color:#d97706">Free Preview</span>
                        @endif
                    </div>
                    @if($les->description)
                        <div class="lv-hero-desc">{{ $les->description }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="lv-stats">
        <div class="lv-stat">
            <div class="lv-stat-val">{{ $les->duration ? $les->duration.' min' : '—' }}</div>
            <div class="lv-stat-label">Duration</div>
        </div>
        <div class="lv-stat">
            <div class="lv-stat-val">{{ $progressCount }}</div>
            <div class="lv-stat-label">Completions</div>
        </div>
        <div class="lv-stat">
            <div class="lv-stat-val">{{ $les->order ?? 1 }}</div>
            <div class="lv-stat-label">Sort Order</div>
        </div>
    </div>

    {{-- ── VIDEO ── --}}
    @if($les->type === 'video')
    <div class="lv-card">
        <div class="lv-card-header">
            <div class="lv-card-icon" style="background:rgba(96,165,250,.12)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
            </div>
            <div>
                <div class="lv-card-title">Video</div>
                <div class="lv-card-sub">{{ $les->video_provider ? ucfirst($les->video_provider).' · ' : '' }}{{ $videoUrl ? 'Source available' : 'No video source' }}</div>
            </div>
        </div>
        <div class="lv-card-body">
            @if($videoUrl)
                @php
                    $embedUrl = null;
                    if ($les->video_provider === 'youtube' || str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $m);
                        $embedUrl = isset($m[1]) ? 'https://www.youtube.com/embed/'.$m[1] : null;
                    } elseif ($les->video_provider === 'vimeo' || str_contains($videoUrl, 'vimeo.com')) {
                        preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $m);
                        $embedUrl = isset($m[1]) ? 'https://player.vimeo.com/video/'.$m[1] : null;
                    }
                @endphp
                @if($embedUrl)
                    <div class="lv-video-wrap">
                        <iframe src="{{ $embedUrl }}" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                    </div>
                @elseif($les->video_path)
                    <div class="lv-video-wrap">
                        <video controls src="{{ $videoUrl }}"></video>
                    </div>
                @else
                    <div class="lv-field">
                        <span class="lv-label">Video URL</span>
                        <span class="lv-value"><a href="{{ $videoUrl }}" target="_blank" rel="noopener">{{ $videoUrl }}</a></span>
                    </div>
                @endif
            @else
                <span class="lv-value-muted">No video source uploaded yet.</span>
            @endif
        </div>
    </div>
    @endif

    {{-- ── ARTICLE ── --}}
    @if($les->type === 'article' && $les->content)
    <div class="lv-card">
        <div class="lv-card-header">
            <div class="lv-card-icon" style="background:rgba(52,211,153,.12)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
            </div>
            <div>
                <div class="lv-card-title">Article Content</div>
                <div class="lv-card-sub">Written lesson material</div>
            </div>
        </div>
        <div class="lv-card-body">
            <div style="font-size:13.5px;line-height:1.8;color:var(--t1)">{!! $les->content !!}</div>
        </div>
    </div>
    @endif

    {{-- ── FILE / DOCUMENT ── --}}
    @if($les->type === 'file')
    <div class="lv-card">
        <div class="lv-card-header">
            <div class="lv-card-icon" style="background:rgba(248,113,113,.12)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
            </div>
            <div>
                <div class="lv-card-title">Document File</div>
                <div class="lv-card-sub">Downloadable file for students</div>
            </div>
        </div>
        <div class="lv-card-body">
            @if($attachmentUrl)
            <div class="lv-file-box">
                <div class="lv-file-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
                </div>
                <div class="lv-file-info">
                    <div class="lv-file-name">{{ $les->attachment_name ?: basename($les->attachment) }}</div>
                    <div class="lv-file-ext">{{ strtoupper(pathinfo($les->attachment, PATHINFO_EXTENSION)) }} file</div>
                </div>
                <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="lv-file-dl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Download
                </a>
            </div>
            @else
                <span class="lv-value-muted">No document uploaded yet.</span>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Optional Attachment (video / article) ── --}}
    @if(in_array($les->type, ['video','article']) && $attachmentUrl)
    <div class="lv-card">
        <div class="lv-card-header">
            <div class="lv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            </div>
            <div>
                <div class="lv-card-title">Attachment</div>
                <div class="lv-card-sub">Supplemental download for students</div>
            </div>
        </div>
        <div class="lv-card-body">
            <div class="lv-file-box">
                <div class="lv-file-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
                </div>
                <div class="lv-file-info">
                    <div class="lv-file-name">{{ $les->attachment_name ?: basename($les->attachment) }}</div>
                    <div class="lv-file-ext">{{ strtoupper(pathinfo($les->attachment, PATHINFO_EXTENSION)) }} file</div>
                </div>
                <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="lv-file-dl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Download
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Info ── --}}
    <div class="lv-card">
        <div class="lv-card-header">
            <div class="lv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            </div>
            <div>
                <div class="lv-card-title">Lesson Info</div>
                <div class="lv-card-sub">Section, course, and metadata</div>
            </div>
        </div>
        <div class="lv-card-body">
            <div class="lv-grid-3">
                <div class="lv-field">
                    <span class="lv-label">Section</span>
                    <span class="lv-value">{{ $sectionTitle }}</span>
                </div>
                <div class="lv-field">
                    <span class="lv-label">Course</span>
                    <span class="lv-value">{{ $courseTitle }}</span>
                </div>
                <div class="lv-field">
                    <span class="lv-label">Free Preview</span>
                    <span class="lv-value">{{ $les->is_preview ? 'Yes' : 'No' }}</span>
                </div>
                <div class="lv-field">
                    <span class="lv-label">Created</span>
                    <span class="lv-value">{{ $les->created_at?->format('M d, Y H:i') ?? '—' }}</span>
                </div>
                <div class="lv-field">
                    <span class="lv-label">Last Updated</span>
                    <span class="lv-value">{{ $les->updated_at?->format('M d, Y H:i') ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
