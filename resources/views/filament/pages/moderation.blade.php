@php
    $accent = '#2563eb';

    $activeTab = collect($tabs ?? [])->firstWhere('key', $tab) ?? ($tabs[0] ?? ['key' => 'all', 'label' => 'All', 'count' => 0, 'color' => '#2563eb']);

    $typeStyle = fn($type) => match($type) {
        'course'       => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'dot' => '#f87171'],
        'verification' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'dot' => '#fbbf24'],
        default        => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'dot' => '#94a3b8'],
    };
@endphp

<div class="md" id="md-moderation" style="--accent: {{ $accent }};">

<style>
.md, .md *, .md *::before, .md *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
.md {
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size: 13px;
    line-height: 1.5;
    padding-bottom: 48px;
    display: grid;
    gap: 20px;
    --bg: #0f172a;
    --p1: #1e293b;
    --p2: #263245;
    --bd: rgba(255,255,255,.07);
    --bd2: rgba(255,255,255,.13);
    --t1: #e2e8f0;
    --t2: #64748b;
    --t3: #334155;
    --sh: 0 4px 24px rgba(0,0,0,.3);
    color: var(--t1);
}
html:not(.dark) .md {
    --bg: #f1f5f9;
    --p1: #ffffff;
    --p2: #f8fafc;
    --bd: rgba(15,23,42,.13);
    --bd2: rgba(15,23,42,.20);
    --t1: #0f172a;
    --t2: #64748b;
    --t3: #cbd5e1;
    --sh: 0 2px 16px rgba(15,23,42,.1);
}
@keyframes mdUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: none; }
}
.mda { opacity: 0; animation: mdUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.md1 { animation-delay: .04s; }
.md2 { animation-delay: .09s; }

.md-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--bd);
}
.md-header-text h1 {
    font-size: clamp(20px, 2.2vw, 26px);
    font-weight: 780;
    letter-spacing: -.018em;
    color: var(--t1);
    line-height: 1.15;
}
.md-header-text p {
    font-size: 12px;
    color: var(--t2);
    margin-top: 5px;
}

.md-card {
    background: var(--p1);
    border: 1px solid var(--bd);
    border-radius: 12px;
    box-shadow: var(--sh);
}

.md-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--bd);
    flex-wrap: wrap;
}
.md-toolbar > form { flex-shrink: 0; }

.md-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 800;
}

.md-filter { position: relative; flex-shrink: 0; }
.md-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--t1);
    background: var(--p2);
    border: 1px solid var(--bd2);
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, border-color .15s;
}
.md-filter-btn:hover { background: var(--p2); }
.md-filter-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.md-filter-chevron {
    width: 13px;
    height: 13px;
    color: var(--t2);
    transition: transform .15s;
    margin-left: 2px;
}
.md-filter:focus-within .md-filter-chevron { transform: rotate(180deg); }
.md-filter-menu {
    display: none;
    position: absolute;
    left: 0;
    top: calc(100% + 6px);
    background: var(--p1);
    border: 1px solid var(--bd2);
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(0,0,0,.35);
    min-width: 200px;
    z-index: 50;
    padding: 6px;
}
.md-filter:focus-within .md-filter-menu { display: block; }
.md-filter-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 10px;
    border-radius: 7px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--t1);
    text-decoration: none;
    transition: background .12s;
}
.md-filter-item:hover { background: var(--p2); }
.md-filter-item.active { background: var(--p2); }
.md-filter-item-label { flex: 1 1 auto; }

.md-search-box {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--p2);
    border: 1px solid var(--bd2);
    border-radius: 8px;
    padding: 6px 12px;
}
.md-search-box svg { width: 14px; height: 14px; color: var(--t2); flex-shrink: 0; }
.md-search-box input {
    background: none;
    border: none;
    outline: none;
    color: var(--t1);
    font-size: 12px;
    font-family: inherit;
    width: 200px;
}
.md-search-box input::placeholder { color: var(--t2); }

.md-table { width: 100%; border-collapse: collapse; }
.md-table thead tr { border-bottom: 1px solid var(--bd); }
.md-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--t2);
    white-space: nowrap;
}
.md-table tbody tr {
    border-bottom: 1px solid var(--bd);
    transition: background .12s;
    cursor: pointer;
}
.md-table tbody tr:last-child { border-bottom: none; }
.md-table tbody tr:hover { background: var(--p2); }
.md-table td { padding: 12px 12px; vertical-align: middle; }

.md-title { font-size: 12.5px; color: var(--t1); font-weight: 600; }
.md-subject { font-size: 12px; color: var(--t2); }
.md-reason { font-size: 12px; color: var(--t2); max-width: 300px; }
.md-date { font-size: 12px; color: var(--t2); white-space: nowrap; }
.md-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
    white-space: nowrap;
}
.md-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

.md-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 56px 24px;
    gap: 10px;
    color: var(--t2);
}
.md-empty svg { width: 40px; height: 40px; opacity: .35; }
.md-empty p { font-size: 13px; }

.md-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-top: 1px solid var(--bd);
    flex-wrap: wrap;
    gap: 10px;
}
.md-footer-info { font-size: 12px; color: var(--t2); }
.md-pages { display: flex; align-items: center; gap: 6px; }
.md-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    height: 30px;
    padding: 0 8px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    color: var(--t2);
    background: none;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    transition: background .15s, border-color .15s, color .15s;
}
.md-loading{opacity:.45;pointer-events:none;transition:opacity .1s}
.md-page-btn:not(.disabled):hover { background: var(--p2); border-color: var(--bd2); color: var(--t1); }
.md-page-btn.active { background: var(--accent); color: #fff; border-color: transparent; }
.md-page-btn.disabled { opacity: .35; pointer-events: none; }
.md-per-page { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--t2); }
.md-per-page select {
    appearance: none;
    background: var(--p2);
    border: 1px solid var(--bd2);
    border-radius: 7px;
    padding: 4px 22px 4px 9px;
    font-size: 12px;
    font-weight: 700;
    color: var(--t1);
    font-family: inherit;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    outline: none;
}
</style>

    {{-- Header --}}
    <div class="md-header mda md1">
        <div class="md-header-text">
            <h1>Moderation Log</h1>
            <p>Rejected courses and instructor verifications. Take action from the Courses or Verifications pages.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="md-card mda md2">

        {{-- Toolbar --}}
        <div class="md-toolbar">

            {{-- Dropdown filter --}}
            <div class="md-filter" tabindex="0">
                <button type="button" class="md-filter-btn">
                    <span class="md-filter-dot" style="background: {{ $activeTab['color'] }}"></span>
                    {{ $activeTab['label'] }}
                    <span class="md-tab-badge" style="background: {{ $activeTab['color'] }}20; color: {{ $activeTab['color'] }}">
                        {{ $activeTab['count'] }}
                    </span>
                    <svg class="md-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div class="md-filter-menu">
                    @foreach ($tabs as $t)
                    <button type="button" wire:click="selectTab('{{ $t['key'] }}')" @click="$el.blur()"
                       class="md-filter-item {{ $tab === $t['key'] ? 'active' : '' }}" style="width:100%;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left">
                        <span class="md-filter-dot" style="background: {{ $t['color'] }}"></span>
                        <span class="md-filter-item-label">{{ $t['label'] }}</span>
                        <span class="md-tab-badge" style="background: {{ $t['color'] }}20; color: {{ $t['color'] }}">
                            {{ $t['count'] }}
                        </span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Search --}}
            <div class="md-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search title or person...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x: auto; border-radius: 0 0 12px 12px;" wire:loading.class="md-loading" wire:target="selectTab,gotoPage,search,setPerPage">
        <table class="md-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Person</th>
                    <th>Reason</th>
                    <th>Reviewed</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $entry)
                @php $ts = $typeStyle($entry['type']); @endphp
                <tr onclick="Livewire.navigate('{{ $entry['url'] }}')">
                    <td>
                        <span class="md-badge" style="background: {{ $ts['bg'] }}; color: {{ $ts['color'] }}">
                            <span class="md-dot" style="background: {{ $ts['dot'] }}"></span>
                            {{ $entry['type_label'] }}
                        </span>
                    </td>
                    <td><span class="md-title">{{ $entry['title'] }}</span></td>
                    <td><span class="md-subject">{{ $entry['subject'] }}</span></td>
                    <td><span class="md-reason">{{ Str::limit($entry['reason'] ?? '—', 60) }}</span></td>
                    <td><span class="md-date">{{ $entry['date']?->format('M d, Y') ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="md-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <p>No moderation activity{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Footer / Pagination --}}
        <div class="md-footer">
            <div class="md-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} entries
                @else
                    No results
                @endif
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="md-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="md-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="md-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 12px; height: 12px">
                            <path d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="md-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="md-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 12px; height: 12px">
                            <path d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
