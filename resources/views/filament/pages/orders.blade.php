@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));

    $accent = '#059669';

    $statusStyle = fn($status) => match(true) {
        $status instanceof \App\Domains\Orders\Enums\OrderStatus => match($status) {
            \App\Domains\Orders\Enums\OrderStatus::Pending   => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
            \App\Domains\Orders\Enums\OrderStatus::Completed => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Completed'],
            \App\Domains\Orders\Enums\OrderStatus::Cancelled => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Cancelled'],
            \App\Domains\Orders\Enums\OrderStatus::Refunded  => ['bg' => 'rgba(167,139,250,.12)', 'color' => '#a78bfa', 'label' => 'Refunded'],
        },
        is_string($status) => match($status) {
            'pending'   => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
            'completed' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Completed'],
            'cancelled' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Cancelled'],
            'refunded'  => ['bg' => 'rgba(167,139,250,.12)', 'color' => '#a78bfa', 'label' => 'Refunded'],
            default     => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($status)],
        },
        default => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => '—'],
    };

    $payStatusStyle = fn($ps) => match(true) {
        $ps instanceof \App\Domains\Orders\Enums\OrderPaymentStatus => match($ps) {
            \App\Domains\Orders\Enums\OrderPaymentStatus::Paid       => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Paid'],
            \App\Domains\Orders\Enums\OrderPaymentStatus::Pending    => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
            \App\Domains\Orders\Enums\OrderPaymentStatus::Processing => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Processing'],
            \App\Domains\Orders\Enums\OrderPaymentStatus::Failed     => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Failed'],
            \App\Domains\Orders\Enums\OrderPaymentStatus::Expired    => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Expired'],
            \App\Domains\Orders\Enums\OrderPaymentStatus::Cancelled  => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Cancelled'],
            \App\Domains\Orders\Enums\OrderPaymentStatus::Refunded   => ['bg' => 'rgba(167,139,250,.12)', 'color' => '#a78bfa', 'label' => 'Refunded'],
        },
        is_string($ps) => match($ps) {
            'paid'       => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Paid'],
            'pending'    => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
            'processing' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Processing'],
            'failed'     => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Failed'],
            'expired'    => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Expired'],
            'cancelled'  => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Cancelled'],
            'refunded'   => ['bg' => 'rgba(167,139,250,.12)', 'color' => '#a78bfa', 'label' => 'Refunded'],
            default      => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($ps)],
        },
        default => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => '—'],
    };

    $createUrl = route('filament.admin.resources.orders.create');
    $viewUrl   = fn($o) => route('filament.admin.resources.orders.view', ['record' => $o->id]);
    $editUrl   = fn($o) => route('filament.admin.resources.orders.edit', ['record' => $o->id]);
@endphp

<style>
.lp, .lp *, .lp *::before, .lp *::after { box-sizing:border-box; margin:0; padding:0; }
.lp {
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
html:not(.dark) .lp {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.08); --bd2:rgba(15,23,42,.14);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes lpUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.lpa { opacity:0; animation:lpUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.lp1{animation-delay:.04s} .lp2{animation-delay:.09s}

.lp-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.lp-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.lp-header-text p { font-size:12px; color:var(--t2); margin-top:5px; }
.lp-header-btns { display:flex; align-items:center; gap:10px; flex-shrink:0; }
.lp-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:12px; font-weight:700; letter-spacing:.02em; cursor:pointer; text-decoration:none; transition:opacity .18s, transform .15s; white-space:nowrap; border:none; font-family:inherit; }
.lp-btn:hover { opacity:.85; transform:translateY(-1px); }
.lp-btn-primary { color:#fff; }
.lp-btn-gray { background:var(--p2); color:var(--t1); border:1px solid var(--bd2); }

.lp-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.lp-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bd); flex-wrap:wrap; }
.lp-tabs { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.lp-tab { display:inline-flex; align-items:center; gap:6px; padding:6px 13px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; color:var(--t2); border:1px solid transparent; transition:background .15s, color .15s, border-color .15s; }
.lp-tab:hover { background:var(--p2); color:var(--t1); }
.lp-tab-badge { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; border-radius:5px; font-size:10px; font-weight:800; }
.lp-search-box { display:flex; align-items:center; gap:6px; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 12px; }
.lp-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.lp-search-box input { background:none; border:none; outline:none; color:var(--t1); font-size:12px; font-family:inherit; width:200px; }
.lp-search-box input::placeholder { color:var(--t2); }

.lp-table { width:100%; border-collapse:collapse; }
.lp-table thead tr { border-bottom:1px solid var(--bd); }
.lp-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.lp-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.lp-table tbody tr:last-child { border-bottom:none; }
.lp-table tbody tr:hover { background:var(--p2); }
.lp-row-link { cursor:pointer; }
.lp-table td { padding:12px 12px; vertical-align:middle; }

.lp-id { font-size:11.5px; font-weight:700; color:var(--t2); white-space:nowrap; }
.lp-order-num { font-size:12.5px; font-weight:650; color:var(--t1); white-space:nowrap; }
.lp-user-cell { display:flex; align-items:center; gap:8px; }
.lp-user-name { font-size:12.5px; color:var(--t1); font-weight:500; }
.lp-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:700; white-space:nowrap; }
.lp-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.lp-amount { font-size:12.5px; font-weight:700; color:var(--t1); white-space:nowrap; }
.lp-date { font-size:12px; color:var(--t2); white-space:nowrap; }
.lp-items-count { display:inline-flex; align-items:center; justify-content:center; min-width:24px; height:24px; padding:0 7px; border-radius:5px; font-size:11.5px; font-weight:700; background:var(--p2); color:var(--t2); }

.lp-actions { display:flex; align-items:center; gap:4px; justify-content:flex-end; }
.lp-act-btn { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; background:none; border:1px solid transparent; cursor:pointer; color:var(--t2); text-decoration:none; transition:background .15s, border-color .15s, color .15s; }
.lp-act-btn:hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.lp-act-btn svg { width:14px; height:14px; }

.lp-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 24px; gap:10px; color:var(--t2); }
.lp-empty svg { width:40px; height:40px; opacity:.35; }
.lp-empty p { font-size:13px; }

.lp-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--bd); flex-wrap:wrap; gap:10px; }
.lp-footer-info { font-size:12px; color:var(--t2); }
.lp-pages { display:flex; align-items:center; gap:6px; }
.lp-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; font-size:12px; font-weight:700; text-decoration:none; color:var(--t2); background:none; border:1px solid transparent; transition:background .15s, border-color .15s, color .15s; }
.lp-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.lp-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.lp-page-btn.disabled { opacity:.35; pointer-events:none; }
.lp-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.lp-per-page select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:7px; padding:4px 22px 4px 9px; font-size:12px; font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 6px center; outline:none; }
</style>

<div wire:poll.1s>
<div class="lp" id="lp-orders" style="--accent:{{ $accent }}">

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Orders</h1>
            <p>Track and manage all customer orders across the platform.</p>
        </div>
        <div class="lp-header-btns">
            <a href="{{ $createUrl }}" class="lp-btn lp-btn-primary" style="background:{{ $accent }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Order
            </a>
        </div>
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar --}}
        <div class="lp-toolbar">
            <div class="lp-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <a href="{{ $url(['tab' => $t['key'], 'page' => 1]) }}" class="lp-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="lp-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </a>
                @endforeach
            </div>

            <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:0">
                @foreach(request()->except(['search', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <div class="lp-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Order number or customer...">
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                @php
                    $ss  = $statusStyle($order->status);
                    $ps  = $payStatusStyle($order->payment_status);
                    $bgHex = substr(md5($order->user?->name ?? ''), 0, 6);
                    $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($order->user?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                    $amount = $order->final_amount ?? $order->total_amount ?? 0;
                @endphp
                <tr class="lp-row-link" onclick="window.location='{{ $viewUrl($order) }}'">
                    <td><span class="lp-id">{{ $order->id }}</span></td>

                    <td><span class="lp-order-num">{{ $order->order_number ?? '—' }}</span></td>

                    <td>
                        <div class="lp-user-cell">
                            <img src="{{ $avUrl }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">
                            <span class="lp-user-name">{{ $order->user?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="lp-items-count">{{ $order->items_count }}</span>
                    </td>

                    <td><span class="lp-amount">${{ number_format((float)$amount, 2) }}</span></td>

                    <td>
                        <span class="lp-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="lp-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td>
                        <span class="lp-badge" style="background:{{ $ps['bg'] }};color:{{ $ps['color'] }}">
                            <span class="lp-dot" style="background:{{ $ps['color'] }}"></span>
                            {{ $ps['label'] }}
                        </span>
                    </td>

                    <td><span class="lp-date">{{ $order->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="lp-actions">
                            <a href="{{ $viewUrl($order) }}" class="lp-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            <a href="{{ $editUrl($order) }}" class="lp-act-btn" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                            </svg>
                            <p>No orders found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="lp-footer">
            <div class="lp-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} orders
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="lp-per-page">
                    Per page
                    <select onchange="window.location.href='{{ $url([]) }}&per_page=' + this.value + '&page=1'">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="lp-pages">
                    <a href="{{ $url(['page' => max(1, $curPage - 1)]) }}"
                       class="lp-page-btn {{ $curPage === 1 ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <a href="{{ $url(['page' => $p]) }}"
                           class="lp-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </a>
                    @endfor
                    <a href="{{ $url(['page' => min($totalPages, $curPage + 1)]) }}"
                       class="lp-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
</div>
