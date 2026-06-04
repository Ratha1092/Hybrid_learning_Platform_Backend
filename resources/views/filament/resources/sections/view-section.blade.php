@php
    /** @var \App\Domains\Courses\Models\Section $record */
    $section = $record;
    $section->loadMissing(['course', 'lessons']);
    $lessons = $section->lessons()->orderBy('order')->get();
@endphp

<style>
.sv, .sv *, .sv *::before, .sv *::after { box-sizing: border-box; margin: 0; padding: 0; }
.sv {
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 13px; line-height: 1.5;
    display: grid; gap: 20px; padding-bottom: 48px;
    --p1: #1e293b; --p2: #263245;
    --bd: rgba(255,255,255,.07); --bd2: rgba(255,255,255,.13);
    --t1: #e2e8f0; --t2: #64748b;
    --sh: 0 4px 24px rgba(0,0,0,.3);
    --accent: #7c3aed;
    color: var(--t1);
}
html:not(.dark) .sv {
    --p1: #fff; --p2: #f8fafc;
    --bd: rgba(15,23,42,.08); --bd2: rgba(15,23,42,.14);
    --t1: #0f172a; --t2: #64748b;
    --sh: 0 2px 16px rgba(15,23,42,.1);
}
.sv-topbar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.sv-topbar-left { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.sv-topbar-right { display:flex; align-items:center; gap:8px; }
.sv-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:12px; font-weight:700; text-decoration:none; border:none; cursor:pointer; transition:all .15s; white-space:nowrap; }
.sv-btn svg { width:14px; height:14px; flex-shrink:0; }
.sv-btn-gray { background:var(--p2); border:1px solid var(--bd2); color:var(--t2); }
.sv-btn-gray:hover { color:var(--t1); border-color:var(--accent); }
.sv-btn-primary { background:var(--accent); color:#fff; border:1px solid transparent; }
.sv-btn-primary:hover { opacity:.88; }

.sv-card { background:var(--p1); border:1px solid var(--bd); border-radius:14px; overflow:hidden; box-shadow:var(--sh); }
.sv-card-header { padding:16px 20px; border-bottom:1px solid var(--bd); display:flex; align-items:center; gap:8px; }
.sv-card-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(124,58,237,.12); flex-shrink:0; }
.sv-card-icon svg { width:16px; height:16px; color:var(--accent); }
.sv-card-title { font-size:13px; font-weight:750; color:var(--t1); }
.sv-card-body { padding:20px; }

.sv-hero { margin-bottom:4px; }
.sv-title { font-size:clamp(18px,2vw,24px); font-weight:800; color:var(--t1); letter-spacing:-.018em; }
.sv-meta { display:flex; align-items:center; gap:12px; margin-top:8px; flex-wrap:wrap; }
.sv-meta-item { display:flex; align-items:center; gap:5px; font-size:12px; color:var(--t2); }
.sv-meta-item svg { width:13px; height:13px; }
.sv-badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:700; background:rgba(124,58,237,.12); color:var(--accent); }

.sv-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
.sv-stat { background:var(--p2); border:1px solid var(--bd); border-radius:10px; padding:14px 16px; }
.sv-stat-val { font-size:22px; font-weight:800; color:var(--t1); }
.sv-stat-label { font-size:11px; font-weight:600; color:var(--t2); text-transform:uppercase; letter-spacing:.05em; margin-top:2px; }

.sv-lesson-row { display:flex; align-items:center; gap:12px; padding:12px 20px; border-bottom:1px solid var(--bd); transition:background .12s; }
.sv-lesson-row:last-child { border-bottom:none; }
.sv-lesson-row:hover { background:var(--p2); }
.sv-lesson-num { width:24px; height:24px; border-radius:6px; background:rgba(124,58,237,.1); color:var(--accent); font-size:11px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.sv-lesson-info { flex:1; min-width:0; }
.sv-lesson-title { font-size:13px; font-weight:650; color:var(--t1); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sv-lesson-meta { font-size:11.5px; color:var(--t2); margin-top:2px; }
.sv-type-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:5px; font-size:10.5px; font-weight:700; }
.sv-empty { padding:32px 20px; text-align:center; color:var(--t2); font-size:12px; }
</style>

<div class="sv">

    {{-- Top bar --}}
    <div class="sv-topbar">
        <div class="sv-topbar-left">
            <a href="{{ route('filament.admin.pages.sections') }}" class="sv-btn sv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Sections
            </a>
            @if($section->course)
            <a href="{{ url('/admin/courses/' . $section->course->id) }}" class="sv-btn sv-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                {{ $section->course->title }}
            </a>
            @endif
        </div>
        <div class="sv-topbar-right">
            <a href="{{ route('filament.admin.resources.sections.edit', $section) }}" class="sv-btn sv-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Hero --}}
    <div class="sv-card">
        <div class="sv-card-body">
            <div class="sv-hero">
                <div class="sv-title">{{ $section->title }}</div>
                <div class="sv-meta">
                    <div class="sv-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>
                        Order {{ $section->order }}
                    </div>
                    @if($section->course)
                    <div class="sv-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                        {{ $section->course->title }}
                    </div>
                    @endif
                    <div class="sv-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/></svg>
                        {{ $section->created_at?->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="sv-stats">
        <div class="sv-stat">
            <div class="sv-stat-val">{{ $lessons->count() }}</div>
            <div class="sv-stat-label">Lessons</div>
        </div>
        <div class="sv-stat">
            <div class="sv-stat-val">{{ $lessons->sum('duration') ?? 0 }} min</div>
            <div class="sv-stat-label">Total Duration</div>
        </div>
    </div>

    {{-- Lessons list --}}
    <div class="sv-card">
        <div class="sv-card-header">
            <div class="sv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653z"/></svg>
            </div>
            <span class="sv-card-title">Lessons ({{ $lessons->count() }})</span>
        </div>
        @forelse($lessons as $lesson)
        @php
            $typeColors = [
                'video'      => ['bg'=>'rgba(96,165,250,.12)',  'color'=>'#60a5fa'],
                'article'    => ['bg'=>'rgba(52,211,153,.12)',  'color'=>'#34d399'],
                'quiz'       => ['bg'=>'rgba(251,191,36,.12)',  'color'=>'#fbbf24'],
                'live'       => ['bg'=>'rgba(248,113,113,.12)', 'color'=>'#f87171'],
                'assignment' => ['bg'=>'rgba(167,139,250,.12)', 'color'=>'#a78bfa'],
            ];
            $tc = $typeColors[$lesson->type] ?? ['bg'=>'rgba(148,163,184,.1)','color'=>'#94a3b8'];
        @endphp
        <div class="sv-lesson-row">
            <div class="sv-lesson-num">{{ $lesson->order }}</div>
            <div class="sv-lesson-info">
                <div class="sv-lesson-title">{{ $lesson->title }}</div>
                <div class="sv-lesson-meta">
                    @if($lesson->duration) {{ $lesson->duration }} min · @endif
                    {{ $lesson->is_preview ? 'Free Preview' : 'Premium' }}
                </div>
            </div>
            <span class="sv-type-badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }}">
                {{ ucfirst($lesson->type) }}
            </span>
            <a href="{{ route('filament.admin.resources.lessons.view', $lesson) }}" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid var(--bd2);color:var(--t2);text-decoration:none;transition:all .15s" onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--bd2)';this.style.color='var(--t2)'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
        </div>
        @empty
        <div class="sv-empty">No lessons in this section yet.</div>
        @endforelse
    </div>

</div>
