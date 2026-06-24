@php
    $presets = \App\Filament\Pages\Reports\LearningIntelligenceReport::dateRangePresetOptions();
@endphp

<style>
.rp, .rp *, .rp *::before, .rp *::after { box-sizing:border-box; margin:0; padding:0; }
.rp {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px; line-height:1.5; padding-bottom:48px; display:grid; gap:20px;
    --bg:#0f172a; --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b; --t3:#334155; --accent:#D7A441;
    color:var(--t1);
}
html:not(.dark) .rp {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0f172a; --t2:#475569; --t3:#cbd5e1;
}
.rp-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:16px; border-bottom:1px solid var(--bd); }
.rp-header h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); }
.rp-header p { font-size:12px; color:var(--t2); margin-top:5px; }
.rp-actions { display:flex; gap:8px; flex-wrap:wrap; }
.rp-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; text-decoration:none; border:1px solid var(--bd2); background:var(--p1); color:var(--t1); }
.rp-btn:hover { background:var(--p2); }
.rp-btn-primary { background:var(--accent); color:#fff; border-color:transparent; }
.rp-btn-primary:hover { background:#c4923a !important; color:#fff !important; }
.rp-filters { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.rp-filter-select { background:var(--p1); border:1px solid var(--bd2); border-radius:8px; padding:7px 10px; font-size:12px; color:var(--t1); }
.rp-kpi-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:12px; }
.rp-kpi-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; padding:16px; }
.rp-kpi-card.soon { border-style:dashed; }
.rp-kpi-card.soon .rp-kpi-value { opacity:.45; }
.rp-kpi-label { font-size:10.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--t2); margin-bottom:6px; }
.rp-kpi-value { font-size:22px; font-weight:800; color:var(--t1); }
.rp-kpi-soon-badge { font-size:9.5px; font-weight:700; color:#8a6315; text-transform:uppercase; letter-spacing:.04em; margin-top:4px; }
html.dark .rp-kpi-soon-badge { color:#D7A441; }
.rp-table-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; max-height:600px; overflow-y:auto; }
.rp-table { width:100%; border-collapse:collapse; }
.rp-table thead tr { border-bottom:1px solid var(--bd); }
.rp-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; position:sticky; top:0; background:var(--p1); }
.rp-table td { padding:10px 12px; border-bottom:1px solid var(--bd); font-size:12.5px; color:var(--t1); }
</style>

<div>
<div class="rp">
    <div class="rp-header">
        <div>
            <h1>Learning Intelligence Report</h1>
            <p>Engagement, completion, and dropout across the platform.</p>
        </div>
        <div class="rp-actions">
            <a href="{{ route('admin.reports.csv', ['type' => 'learning_intelligence'] + request()->query()) }}" class="rp-btn">Export CSV</a>
            <a href="{{ route('admin.reports.pdf', ['type' => 'learning_intelligence'] + request()->query()) }}" class="rp-btn">Export PDF</a>
            @if(\App\Support\PanelAccess::can('reports.schedule'))
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-schedule-modal'))" class="rp-btn rp-btn-primary">Schedule</button>
            @endif
        </div>
    </div>

    <form method="GET" class="rp-filters">
        <select name="preset" class="rp-filter-select" onchange="this.form.submit()">
            @foreach($presets as $key => $label)
                <option value="{{ $key }}" @selected($activePreset === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="rp-kpi-grid">
        <div class="rp-kpi-card"><div class="rp-kpi-label">Learning Hours</div><div class="rp-kpi-value">{{ number_format($kpis['totalLearningHours'], 1) }}h</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Active Learners</div><div class="rp-kpi-value">{{ number_format($kpis['activeLearners']) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Completion Rate</div><div class="rp-kpi-value">{{ $kpis['completionRate'] }}%</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Dropout Rate</div><div class="rp-kpi-value">{{ $kpis['dropoutRate'] }}%</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Certificate Rate</div><div class="rp-kpi-value">{{ $kpis['certificateRate'] }}%</div></div>
        <div class="rp-kpi-card soon">
            <div class="rp-kpi-label">Avg. Quiz Score</div>
            <div class="rp-kpi-value">—</div>
            <div class="rp-kpi-soon-badge">Coming soon — requires a Quiz/Assessment model</div>
        </div>
    </div>

    <div class="rp-table-card">
        <table class="rp-table">
            <thead>
                <tr><th>Course</th><th>Enrollments</th><th>Avg Progress</th><th>Completion %</th><th>Certificate %</th><th>Watch Hours</th><th>Hrs/Learner</th></tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td>{{ \Illuminate\Support\Str::limit($course['title'], 40) }}</td>
                        <td>{{ number_format($course['enrollments']) }}</td>
                        <td>{{ $course['avgProgress'] }}%</td>
                        <td>{{ $course['completionRate'] }}%</td>
                        <td>{{ $course['certificateRate'] }}%</td>
                        <td>{{ $course['totalWatchHours'] }}h</td>
                        <td>{{ $course['avgWatchHoursPerLearner'] }}h</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--t2);padding:24px;">No learning data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(\App\Support\PanelAccess::can('reports.schedule'))
    @include('filament.pages.partials._schedule-modal', ['reportLabel' => 'Learning Intelligence'])
@endif

</div>
