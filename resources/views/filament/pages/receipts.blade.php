@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $accent = '#10b981';
@endphp

<style>
.rc, .rc *, .rc *::before, .rc *::after { box-sizing:border-box; margin:0; padding:0; }
.rc {
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
html:not(.dark) .rc {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes rcUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.rca { opacity:0; animation:rcUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.rc1{animation-delay:.04s} .rc2{animation-delay:.09s}

.rc-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.rc-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.rc-header-text p  { font-size:12px; color:var(--t2); margin-top:5px; }

.rc-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.rc-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bd); flex-wrap:wrap; }
.rc-search-box { display:flex; align-items:center; gap:6px; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 12px; }
.rc-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.rc-search-box input { background:none; border:none; outline:none; color:var(--t1); font-size:12px; font-family:inherit; width:180px; }
.rc-search-box input::placeholder { color:var(--t2); }
.rc-select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 28px 6px 10px; font-size:12px; font-weight:600; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; outline:none; }

.rc-table { width:100%; border-collapse:collapse; }
.rc-table thead tr { border-bottom:1px solid var(--bd); }
.rc-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.rc-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.rc-table tbody tr:last-child { border-bottom:none; }
.rc-table tbody tr:hover { background:var(--p2); }
.rc-table td { padding:12px 12px; vertical-align:middle; }

.rc-num    { font-size:12.5px; font-weight:700; color:var(--t1); font-variant-numeric:tabular-nums; }
.rc-sub    { font-size:11.5px; color:var(--t2); margin-top:2px; }
.rc-amount { font-size:13px; font-weight:700; color:#34d399; font-variant-numeric:tabular-nums; }
.rc-gw     { display:inline-flex; align-items:center; padding:2px 9px; border-radius:99px; font-size:10.5px; font-weight:700; letter-spacing:.03em; background:rgba(16,185,129,.12); color:#34d399; }

.rc-actions { display:flex; align-items:center; gap:6px; }
.rc-btn { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none; border:1px solid transparent; cursor:pointer; font-family:inherit; transition:opacity .12s; white-space:nowrap; }
.rc-btn:hover { opacity:.8; }
.rc-btn-dl   { background:rgba(100,116,139,.18); color:var(--t1); border-color:var(--bd2); }
.rc-btn-send { background:rgba(16,185,129,.15);  color:#34d399; }
.rc-btn svg  { width:12px; height:12px; }

.rc-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 24px; gap:10px; color:var(--t2); }
.rc-empty svg { width:40px; height:40px; opacity:.35; }
.rc-empty p { font-size:13px; }

.rc-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--bd); flex-wrap:wrap; gap:10px; }
.rc-footer-info { font-size:12px; color:var(--t2); }
.rc-pages { display:flex; align-items:center; gap:6px; }
.rc-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; font-size:12px; font-weight:700; text-decoration:none; color:var(--t2); background:none; border:1px solid transparent; transition:background .15s, border-color .15s, color .15s; }
.rc-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.rc-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.rc-page-btn.disabled { opacity:.35; pointer-events:none; }
.rc-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.rc-per-page select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:7px; padding:4px 22px 4px 9px; font-size:12px; font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 6px center; outline:none; }
</style>

<div class="rc" style="--accent:{{ $accent }};">

    {{-- Header --}}
    <div class="rc-header rca rc1">
        <div class="rc-header-text">
            <h1>Receipts</h1>
            <p>Payment receipts issued for all completed orders.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="rc-card rca rc2">

        <div class="rc-toolbar">
            <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                @foreach(request()->except(['search','gateway','page']) as $k => $v)
                    @if(is_scalar($v))
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach

                <div class="rc-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Receipt #, order #, customer…">
                </div>

                @if($gateways->isNotEmpty())
                <select name="gateway" class="rc-select" onchange="this.form.submit()">
                    <option value="">All Gateways</option>
                    @foreach($gateways as $gw)
                        <option value="{{ $gw }}" {{ $gateway === $gw ? 'selected' : '' }}>{{ strtoupper($gw) }}</option>
                    @endforeach
                </select>
                @endif

                <button type="submit" style="display:none"></button>
            </form>
        </div>

        <div style="overflow-x:auto">
        <table class="rc-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Paid At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                <tr>
                    <td>
                        <div class="rc-num">{{ $receipt->receipt_number }}</div>
                    </td>
                    <td>
                        <span class="rc-num">{{ $receipt->order?->order_number ?? '—' }}</span>
                    </td>
                    <td>
                        <div class="rc-num">{{ $receipt->order?->user?->name ?? '—' }}</div>
                        <div class="rc-sub">{{ $receipt->order?->user?->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="rc-amount">{{ $receipt->currency }} {{ number_format((float) $receipt->amount, 2) }}</span>
                    </td>
                    <td>
                        <span class="rc-gw">{{ strtoupper($receipt->payment_gateway) }}</span>
                    </td>
                    <td>
                        <span style="font-size:12px;color:var(--t2);">
                            {{ $receipt->paid_at?->format('M d, Y H:i') ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div class="rc-actions">
                            @if($canDownload)
                            <a href="{{ route('admin.billing.receipts.download', $receipt->id) }}"
                               class="rc-btn rc-btn-dl" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                PDF
                            </a>
                            @endif
                            @if($canResend)
                            <form method="POST" action="{{ route('admin.billing.receipts.resend', $receipt->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="rc-btn rc-btn-send">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                    Resend
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="rc-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185z"/>
                            </svg>
                            <p>No receipts found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="rc-footer">
            <div class="rc-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} receipts
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="rc-per-page">
                    Per page
                    <select onchange="window.location.href='{{ $url([]) }}&per_page=' + this.value + '&page=1'">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="rc-pages">
                    <a href="{{ $url(['page' => max(1, $curPage - 1)]) }}"
                       class="rc-page-btn {{ $curPage === 1 ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <a href="{{ $url(['page' => $p]) }}"
                           class="rc-page-btn {{ $curPage === $p ? 'active' : '' }}">{{ $p }}</a>
                    @endfor
                    <a href="{{ $url(['page' => min($totalPages, $curPage + 1)]) }}"
                       class="rc-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
