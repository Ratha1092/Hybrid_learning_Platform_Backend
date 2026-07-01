@php
    $accent = '#ea580c';
@endphp

<div class="wl" id="wl-wallets" style="--accent:{{ $accent }};">

<style>
.wl, .wl *, .wl *::before, .wl *::after { box-sizing:border-box; margin:0; padding:0; }
.wl {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px; line-height:1.5;
    padding-bottom:48px;
    display:grid; gap:20px;
    --bg:#0f172a; --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b; --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .wl {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes wlUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.wla { opacity:0; animation:wlUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.wl1{animation-delay:.04s} .wl2{animation-delay:.09s}

.wl-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.wl-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.wl-header-text p { font-size:12px; color:var(--t2); margin-top:5px; }

.wl-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
.wl-stat { background:var(--p1); border:1px solid var(--bd); border-radius:12px; padding:16px 18px; box-shadow:var(--sh); }
.wl-stat-val { font-size:22px; font-weight:800; color:var(--t1); letter-spacing:-.02em; }
.wl-stat-lbl { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--t2); margin-top:3px; }

.wl-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.wl-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bd); flex-wrap:wrap; }
.wl-search-box { display:flex; align-items:center; gap:6px; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 12px; }
.wl-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.wl-search-box input { background:none; border:none; outline:none; color:var(--t1); font-size:12px; font-family:inherit; width:200px; }
.wl-search-box input::placeholder { color:var(--t2); }

.wl-table { width:100%; border-collapse:collapse; }
.wl-table thead tr { border-bottom:1px solid var(--bd); }
.wl-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.wl-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.wl-table tbody tr:last-child { border-bottom:none; }
.wl-table tbody tr:hover { background:var(--p2); }
.wl-table td { padding:12px 12px; vertical-align:middle; }

.wl-user-name { font-size:12.5px; color:var(--t1); font-weight:500; }
.wl-email { font-size:12px; color:var(--t2); }
.wl-amount { font-size:13px; color:var(--t1); font-weight:700; }
.wl-amount-pending { font-size:13px; color:#fbbf24; font-weight:700; }
.wl-currency { font-size:12px; color:var(--t2); }

.wl-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 24px; gap:10px; color:var(--t2); }
.wl-empty svg { width:40px; height:40px; opacity:.35; }
.wl-empty p { font-size:13px; }

.wl-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--bd); flex-wrap:wrap; gap:10px; }
.wl-footer-info { font-size:12px; color:var(--t2); }
.wl-pages { display:flex; align-items:center; gap:6px; }
.wl-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; font-size:12px; font-weight:700; text-decoration:none; color:var(--t2); background:none; font-family:inherit; cursor:pointer; border:1px solid transparent; transition:background .15s, border-color .15s, color .15s; }
.wl-loading{opacity:.45;pointer-events:none;transition:opacity .1s}
.wl-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.wl-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.wl-page-btn.disabled { opacity:.35; pointer-events:none; }
.wl-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.wl-per-page select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:7px; padding:4px 22px 4px 9px; font-size:12px; font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 6px center; outline:none; }
</style>

    {{-- Header --}}
    <div class="wl-header wla wl1">
        <div class="wl-header-text">
            <h1>Instructor Wallets</h1>
            <p>Balances held across every instructor wallet on the platform.</p>
        </div>
    </div>

    {{-- Summary stats --}}
    <div class="wl-stats wla wl1">
        <div class="wl-stat">
            <div class="wl-stat-val">{{ number_format($totals['balance'], 2) }}</div>
            <div class="wl-stat-lbl">Total Balance</div>
        </div>
        <div class="wl-stat">
            <div class="wl-stat-val">{{ number_format($totals['pending_balance'], 2) }}</div>
            <div class="wl-stat-lbl">Total Pending</div>
        </div>
        <div class="wl-stat">
            <div class="wl-stat-val">{{ number_format($totals['count']) }}</div>
            <div class="wl-stat-lbl">Wallets</div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="wl-card wla wl2">

        <div class="wl-toolbar">
            <div class="wl-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search instructor...">
            </div>
        </div>

        <div style="overflow-x:auto" wire:loading.class="wl-loading" wire:target="gotoPage,search,setPerPage">
        <table class="wl-table">
            <thead>
                <tr>
                    <th>Instructor</th>
                    <th>Email</th>
                    <th>Balance</th>
                    <th>Pending</th>
                    <th>Currency</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($wallets as $wallet)
                <tr>
                    <td><span class="wl-user-name">{{ $wallet->instructor?->name ?? '—' }}</span></td>
                    <td><span class="wl-email">{{ $wallet->instructor?->email ?? '—' }}</span></td>
                    <td><span class="wl-amount">{{ number_format($wallet->balance, 2) }}</span></td>
                    <td><span class="wl-amount-pending">{{ number_format($wallet->pending_balance, 2) }}</span></td>
                    <td><span class="wl-currency">{{ $wallet->currency }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="wl-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3"/>
                            </svg>
                            <p>No wallets found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="wl-footer">
            <div class="wl-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} wallets
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="wl-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="wl-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="wl-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="wl-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="wl-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
