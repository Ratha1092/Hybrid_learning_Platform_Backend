@php
    $accent = '#6366f1';
@endphp

<div class="iv" style="--accent:{{ $accent }};">

<style>
.iv, .iv *, .iv *::before, .iv *::after { box-sizing:border-box; margin:0; padding:0; }
.iv {
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
html:not(.dark) .iv {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes ivUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.iva { opacity:0; animation:ivUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.iv1{animation-delay:.04s} .iv2{animation-delay:.09s}

.iv-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.iv-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.iv-header-text p  { font-size:12px; color:var(--t2); margin-top:5px; }

.iv-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.iv-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bd); flex-wrap:wrap; }
.iv-filters { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.iv-search-box { display:flex; align-items:center; gap:6px; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 12px; }
.iv-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.iv-search-box input { background:none; border:none; outline:none; color:var(--t1); font-size:12px; font-family:inherit; width:180px; }
.iv-search-box input::placeholder { color:var(--t2); }
.iv-select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 28px 6px 10px; font-size:12px; font-weight:600; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; outline:none; }

.iv-table { width:100%; border-collapse:collapse; }
.iv-table thead tr { border-bottom:1px solid var(--bd); }
.iv-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.iv-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.iv-table tbody tr:last-child { border-bottom:none; }
.iv-table tbody tr:hover { background:var(--p2); }
.iv-table td { padding:12px 12px; vertical-align:middle; }

.iv-num { font-size:12.5px; font-weight:700; color:var(--t1); font-variant-numeric:tabular-nums; }
.iv-sub { font-size:11.5px; color:var(--t2); margin-top:2px; }
.iv-amount { font-size:13px; font-weight:700; color:var(--t1); font-variant-numeric:tabular-nums; }

.iv-badge { display:inline-flex; align-items:center; padding:2px 9px; border-radius:99px; font-size:10.5px; font-weight:700; letter-spacing:.03em; }
.iv-badge-invoice  { background:rgba(99,102,241,.15); color:#818cf8; }
.iv-badge-cn       { background:rgba(239,68,68,.12);  color:#f87171; }
.iv-badge-issued   { background:rgba(52,211,153,.12); color:#34d399; }
.iv-badge-void     { background:rgba(100,116,139,.15);color:#94a3b8; }

.iv-actions { display:flex; align-items:center; gap:6px; }
.iv-btn { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none; border:1px solid transparent; cursor:pointer; font-family:inherit; transition:opacity .12s; white-space:nowrap; }
.iv-btn:hover { opacity:.8; }
.iv-btn-dl   { background:rgba(100,116,139,.18); color:var(--t1); border-color:var(--bd2); }
.iv-btn-send { background:rgba(99,102,241,.2);   color:#818cf8; }
.iv-btn-regen{ background:rgba(234,179,8,.15);   color:#fbbf24; }
.iv-btn svg  { width:12px; height:12px; }

.iv-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 24px; gap:10px; color:var(--t2); }
.iv-empty svg { width:40px; height:40px; opacity:.35; }
.iv-empty p { font-size:13px; }

.iv-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--bd); flex-wrap:wrap; gap:10px; }
.iv-footer-info { font-size:12px; color:var(--t2); }
.iv-pages { display:flex; align-items:center; gap:6px; }
.iv-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; font-size:12px; font-weight:700; text-decoration:none; color:var(--t2); background:none; font-family:inherit; cursor:pointer; border:1px solid transparent; transition:background .15s, border-color .15s, color .15s; }
.iv-loading{opacity:.45;pointer-events:none;transition:opacity .1s}
.iv-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.iv-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.iv-page-btn.disabled { opacity:.35; pointer-events:none; }
.iv-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.iv-per-page select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:7px; padding:4px 22px 4px 9px; font-size:12px; font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 6px center; outline:none; }
</style>

    {{-- Header --}}
    <div class="iv-header iva iv1">
        <div class="iv-header-text">
            <h1>Invoices</h1>
            <p>Tax invoices and credit notes issued across all orders.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="iv-card iva iv2">

        <div class="iv-toolbar">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <div class="iv-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Invoice #, order #, customer…">
                </div>

                <select wire:model.live="type" class="iv-select">
                    <option value="">All Types</option>
                    <option value="invoice">Invoice</option>
                    <option value="credit_note">Credit Note</option>
                </select>

                <select wire:model.live="status" class="iv-select">
                    <option value="">All Statuses</option>
                    <option value="issued">Issued</option>
                    <option value="void">Void</option>
                </select>
            </div>
        </div>

        <div style="overflow-x:auto" wire:loading.class="iv-loading" wire:target="gotoPage,search,type,status,setPerPage">
        <table class="iv-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Type</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Issued</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>
                        <div class="iv-num">{{ $invoice->invoice_number }}</div>
                    </td>
                    <td>
                        @if($invoice->type === 'credit_note')
                            <span class="iv-badge iv-badge-cn">Credit Note</span>
                        @else
                            <span class="iv-badge iv-badge-invoice">Invoice</span>
                        @endif
                    </td>
                    <td>
                        <span class="iv-num">{{ $invoice->order?->order_number ?? '—' }}</span>
                    </td>
                    <td>
                        <div class="iv-num">{{ $invoice->order?->user?->name ?? '—' }}</div>
                        <div class="iv-sub">{{ $invoice->order?->user?->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="iv-amount">{{ $invoice->currency }} {{ number_format(abs((float) $invoice->total), 2) }}</span>
                    </td>
                    <td>
                        @if($invoice->status === 'issued')
                            <span class="iv-badge iv-badge-issued">Issued</span>
                        @else
                            <span class="iv-badge iv-badge-void">Void</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:12px;color:var(--t2);">
                            {{ $invoice->issued_at?->format('M d, Y') ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div class="iv-actions">
                            @if($canDownload)
                            <a href="{{ route('admin.billing.invoices.download', $invoice->id) }}"
                               class="iv-btn iv-btn-dl" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                PDF
                            </a>
                            @endif
                            @if($canResend)
                            <form method="POST" action="{{ route('admin.billing.invoices.resend', $invoice->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="iv-btn iv-btn-send">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                    Resend
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.billing.invoices.regenerate', $invoice->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="iv-btn iv-btn-regen">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                    Regen
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="iv-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                            </svg>
                            <p>No invoices found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="iv-footer">
            <div class="iv-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} invoices
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="iv-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="iv-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="iv-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="iv-page-btn {{ $curPage === $p ? 'active' : '' }}">{{ $p }}</button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="iv-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
