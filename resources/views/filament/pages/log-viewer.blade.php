<style>
.lv,
.lv *,
.lv *::before,
.lv *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.lv {
    font-family:Inter, ui-sans-serif, system-ui, sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:48px;
    display:grid;
    gap:20px;
    --bg:#0f172a;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --accent:#2563eb;
    --err:#f87171;
    --warn:#f59e0b;
    --info:#10b981;
    --dbg:#6366f1;
    color:var(--t1);
}
html:not(.dark) .lv {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f1f5f9;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#94a3b8;
}
.lv-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.lv-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.lv-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.lv-actions {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
.lv-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:1px solid var(--bd2);
    background:var(--p1);
    color:var(--t1);
}
.lv-btn:hover {
    background:var(--p2);
}
.lv-btn-active {
    background:var(--accent);
    color:#fff !important;
    border-color:transparent;
}
.lv-tabs {
    display:flex;
    gap:8px;
    border-bottom:1px solid var(--bd);
    padding-bottom:12px;
}
.lv-tab {
    padding:6px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    color:var(--t2);
    border:1px solid transparent;
}
.lv-tab:hover {
    background:var(--p2);
    color:var(--t1);
}
.lv-tab-active {
    background:var(--p1);
    border-color:var(--bd2);
    color:var(--t1);
}
.lv-level-tabs {
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}
.lv-level-pill {
    padding:4px 12px;
    border-radius:99px;
    font-size:11px;
    font-weight:700;
    text-decoration:none;
    border:1px solid transparent;
}
.lv-kpi-grid {
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:12px;
}
@media(max-width:700px) {
    .lv-kpi-grid {
        grid-template-columns:repeat(2, 1fr);
    }
}
.lv-kpi {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:14px 16px;
}
.lv-kpi-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:4px;
}
.lv-kpi-value {
    font-size:22px;
    font-weight:800;
}
.lv-filters {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}
.lv-input {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:7px 10px;
    font-size:12px;
    color:var(--t1);
    min-width:0;
}
.lv-input::placeholder {
    color:var(--t2);
}
.lv-entries {
    display:grid;
    gap:8px;
}
.lv-entry {
    background:var(--p1);
    border:1px solid var(--bd);
    border-left:3px solid;
    border-radius:10px;
    padding:12px 14px;
}
.lv-entry-head {
    display:flex;
    align-items:flex-start;
    gap:10px;
}
.lv-badge {
    display:inline-flex;
    padding:2px 8px;
    border-radius:99px;
    font-size:10px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    flex-shrink:0;
    margin-top:1px;
}
.lv-entry-time {
    font-size:11px;
    color:var(--t2);
    flex-shrink:0;
    white-space:nowrap;
}
.lv-entry-msg {
    flex:1;
    font-size:12.5px;
    word-break:break-word;
    color:var(--t1);
}
.lv-entry-msg-short {
    max-height:3.6em;
    overflow:hidden;
}
.lv-trace-toggle {
    font-size:11px;
    color:var(--accent);
    cursor:pointer;
    margin-top:6px;
    background:none;
    border:none;
    padding:0;
    text-decoration:underline;
}
.lv-trace {
    margin-top:8px;
    background:var(--p2);
    border-radius:6px;
    padding:10px 12px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;
    color:var(--t2);
    overflow-x:auto;
    display:none;
    white-space:pre-wrap;
    word-break:break-all;
}
.lv-trace.open {
    display:block;
}
.lv-pagination {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 0;
    font-size:12px;
    color:var(--t2);
}
.lv-empty {
    text-align:center;
    padding:48px;
    color:var(--t2);
    font-size:13px;
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
}
</style>

@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $levelDef = [
        'all'     => ['label' => 'All',     'color' => '#6366f1'],
        'error'   => ['label' => 'Error',   'color' => '#f87171'],
        'warning' => ['label' => 'Warning', 'color' => '#f59e0b'],
        'info'    => ['label' => 'Info',    'color' => '#10b981'],
        'debug'   => ['label' => 'Debug',   'color' => '#6366f1'],
    ];
@endphp

<div class="lv">
    <div class="lv-header">
        <div>
            <h1>Log Viewer</h1>
            <p>Application logs from <code>storage/logs/</code> — showing last 2000 lines.</p>
        </div>
        <div class="lv-actions">
            <a href="{{ $url(['page' => 1]) }}" class="lv-btn">Refresh</a>
        </div>
    </div>

    {{-- File tabs --}}
    <div class="lv-tabs">
        @foreach(['laravel' => 'laravel.log', 'schedule' => 'schedule.log'] as $key => $name)
            <a href="{{ $url(['file' => $key, 'page' => 1]) }}"
               class="lv-tab {{ $file === $key ? 'lv-tab-active' : '' }}">
                {{ $name }}
            </a>
        @endforeach
    </div>

    {{-- KPI counts --}}
    <div class="lv-kpi-grid">
        <div class="lv-kpi"><div class="lv-kpi-label">Errors</div><div class="lv-kpi-value" style="color:#f87171">{{ number_format($levelCounts['error']) }}</div></div>
        <div class="lv-kpi"><div class="lv-kpi-label">Warnings</div><div class="lv-kpi-value" style="color:#f59e0b">{{ number_format($levelCounts['warning']) }}</div></div>
        <div class="lv-kpi"><div class="lv-kpi-label">Info</div><div class="lv-kpi-value" style="color:#10b981">{{ number_format($levelCounts['info']) }}</div></div>
        <div class="lv-kpi"><div class="lv-kpi-label">Debug</div><div class="lv-kpi-value" style="color:#6366f1">{{ number_format($levelCounts['debug']) }}</div></div>
    </div>

    {{-- Level filter pills --}}
    <div class="lv-level-tabs">
        @foreach($levelDef as $key => $def)
            @php $active = $level === $key; @endphp
            <a href="{{ $url(['level' => $key, 'page' => 1]) }}"
               class="lv-level-pill"
               style="background:{{ $active ? $def['color'] : 'transparent' }};
                      color:{{ $active ? '#fff' : $def['color'] }};
                      border-color:{{ $def['color'] }}30;">
                {{ $def['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Search / date filters --}}
    <form method="GET" class="lv-filters">
        <input type="hidden" name="file" value="{{ $file }}">
        <input type="hidden" name="level" value="{{ $level }}">
        <input name="search" value="{{ $search }}" placeholder="Search messages…" class="lv-input" style="flex:1;min-width:200px;">
        <input name="date" type="date" value="{{ $date }}" class="lv-input">
        <button type="submit" class="lv-btn lv-btn-active">Filter</button>
        @if($search || $date)
            <a href="{{ $url(['search' => '', 'date' => '', 'page' => 1]) }}" class="lv-btn">Clear</a>
        @endif
    </form>

    {{-- Log entries --}}
    @if(count($entries) === 0)
        <div class="lv-empty">No log entries match your filters.</div>
    @else
        <div class="lv-entries">
            @foreach($entries as $i => $entry)
                @php $hasTrace = count($entry['trace']) > 0; @endphp
                <div class="lv-entry" style="border-left-color:{{ $entry['color'] }}">
                    <div class="lv-entry-head">
                        <span class="lv-badge" style="background:{{ $entry['color'] }}22;color:{{ $entry['color'] }}">{{ strtoupper($entry['level']) }}</span>
                        <div class="lv-entry-msg">
                            <div class="lv-entry-msg-short" id="msg-{{ $i }}">{{ $entry['message'] }}</div>
                            @if($entry['context'])
                                <div style="font-size:11px;color:var(--t2);margin-top:3px;font-family:monospace;word-break:break-all">{{ \Illuminate\Support\Str::limit($entry['context'], 200) }}</div>
                            @endif
                        </div>
                        <span class="lv-entry-time">{{ $entry['datetime'] }}</span>
                    </div>
                    @if($hasTrace)
                        <button class="lv-trace-toggle" onclick="
                            var t=this.nextElementSibling;
                            t.classList.toggle('open');
                            this.textContent=t.classList.contains('open')?'Hide trace':'Show {{ count($entry['trace']) }} trace lines';
                        ">Show {{ count($entry['trace']) }} trace lines</button>
                        <pre class="lv-trace">{{ implode("\n", array_slice($entry['trace'], 0, 40)) }}</pre>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="lv-pagination">
            <span>Showing {{ $total > 0 ? (($curPage-1)*$perPage)+1 : 0 }}–{{ min($curPage*$perPage, $total) }} of {{ number_format($total) }}</span>
            <div style="display:flex;gap:6px;">
                @if($curPage > 1)<a href="{{ $url(['page' => $curPage-1]) }}" class="lv-btn">Prev</a>@endif
                @if($curPage < $totalPages)<a href="{{ $url(['page' => $curPage+1]) }}" class="lv-btn">Next</a>@endif
            </div>
        </div>
    @endif
</div>
