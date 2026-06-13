@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));

    $accent = '#7c3aed';

    $gatewayStyle = fn($gw) => match(true) {
        $gw instanceof \App\Domains\Payments\Enums\PaymentGateway => match($gw) {
            \App\Domains\Payments\Enums\PaymentGateway::Bakong => ['bg' => 'rgba(59,130,246,.12)',  'color' => '#60a5fa', 'label' => 'Bakong'],
            \App\Domains\Payments\Enums\PaymentGateway::Khqr   => ['bg' => 'rgba(16,185,129,.12)', 'color' => '#34d399', 'label' => 'KHQR'],
            \App\Domains\Payments\Enums\PaymentGateway::Aba    => ['bg' => 'rgba(245,158,11,.12)', 'color' => '#fbbf24', 'label' => 'ABA'],
            \App\Domains\Payments\Enums\PaymentGateway::Stripe => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#818cf8', 'label' => 'Stripe'],
            \App\Domains\Payments\Enums\PaymentGateway::Paypal => ['bg' => 'rgba(59,130,246,.12)', 'color' => '#60a5fa', 'label' => 'PayPal'],
        },
        is_string($gw) => match(strtolower($gw)) {
            'bakong' => ['bg' => 'rgba(59,130,246,.12)',  'color' => '#60a5fa', 'label' => 'Bakong'],
            'khqr'   => ['bg' => 'rgba(16,185,129,.12)',  'color' => '#34d399', 'label' => 'KHQR'],
            'aba'    => ['bg' => 'rgba(245,158,11,.12)',  'color' => '#fbbf24', 'label' => 'ABA'],
            'stripe' => ['bg' => 'rgba(99,102,241,.12)',  'color' => '#818cf8', 'label' => 'Stripe'],
            'paypal' => ['bg' => 'rgba(59,130,246,.12)',  'color' => '#60a5fa', 'label' => 'PayPal'],
            default  => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($gw)],
        },
        default => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => '—'],
    };

    $statusStyle = fn($s) => match(true) {
        $s instanceof \App\Domains\Payments\Enums\PaymentStatus => match($s) {
            \App\Domains\Payments\Enums\PaymentStatus::Paid,
            \App\Domains\Payments\Enums\PaymentStatus::Completed  => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => $s === \App\Domains\Payments\Enums\PaymentStatus::Paid ? 'Paid' : 'Completed'],
            \App\Domains\Payments\Enums\PaymentStatus::Pending,
            \App\Domains\Payments\Enums\PaymentStatus::Processing => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => $s === \App\Domains\Payments\Enums\PaymentStatus::Pending ? 'Pending' : 'Processing'],
            \App\Domains\Payments\Enums\PaymentStatus::Failed      => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Failed'],
            \App\Domains\Payments\Enums\PaymentStatus::Expired     => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Expired'],
            \App\Domains\Payments\Enums\PaymentStatus::Cancelled   => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Cancelled'],
            \App\Domains\Payments\Enums\PaymentStatus::Refunded   => ['bg' => 'rgba(167,139,250,.12)', 'color' => '#a78bfa', 'label' => 'Refunded'],
        },
        is_string($s) => match($s) {
            'paid', 'completed' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => ucfirst($s)],
            'pending', 'processing' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => ucfirst($s)],
            'failed', 'expired', 'cancelled' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => ucfirst($s)],
            'refunded' => ['bg' => 'rgba(167,139,250,.12)', 'color' => '#a78bfa', 'label' => 'Refunded'],
            default    => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($s)],
        },
        default => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => '—'],
    };

    $viewUrl = fn($p) => route('filament.admin.resources.payments.view', ['record' => $p->id]);
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

.lp-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.lp-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.lp-header-text p { font-size:12px; color:var(--t2); margin-top:5px; }

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
.lp-table td { padding:12px 12px; vertical-align:middle; }

.lp-id { font-size:11.5px; font-weight:700; color:var(--t2); white-space:nowrap; }
.lp-order-num { font-size:12.5px; font-weight:650; color:var(--t1); white-space:nowrap; }
.lp-user-cell { display:flex; align-items:center; gap:8px; }
.lp-user-name { font-size:12.5px; color:var(--t1); font-weight:500; }
.lp-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:700; white-space:nowrap; }
.lp-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.lp-amount { font-size:12.5px; font-weight:700; color:var(--t1); white-space:nowrap; }
.lp-date { font-size:12px; color:var(--t2); white-space:nowrap; }

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
<div class="lp" id="lp-payments" style="--accent:{{ $accent }}">

    @if(session('force_verify_success'))
    <div style="background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.35);color:#34d399;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;">
        ✓ {{ session('force_verify_success') }}
    </div>
    @endif
    @if(session('force_verify_info'))
    <div style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.35);color:#fbbf24;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;">
        ⚠ {{ session('force_verify_info') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Payments</h1>
            <p>Monitor all payment transactions and their statuses across the platform.</p>
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
                <a href="{{ $url(['tab' => $t['key'], 'page' => 1]) }}"
                    class="lp-tab"
                    style="{{ $tabStyle }}"
                >
                    {{ $t['label'] }}
                    <span class="lp-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </a>
                @endforeach
            </div>

            <form id="lp-search-form" method="GET" action="{{ url()->current() }}" style="display:none">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
            </form>
            <div class="lp-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    form="lp-search-form"
                    value="{{ $search }}"
                    placeholder="Search order number..."
                    onchange="document.getElementById('lp-search-form').submit()"
                >
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Gateway</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid At</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                @php
                    $gs  = $gatewayStyle($payment->payment_gateway);
                    $ss  = $statusStyle($payment->status);
                    $customer = $payment->order?->user?->name ?? '—';
                    $bgHex = substr(md5($customer), 0, 6);
                    $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($customer) . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                @endphp
                <tr>
                    <td><span class="lp-id">{{ $payment->id }}</span></td>

                    <td><span class="lp-order-num">{{ $payment->order?->order_number ?? '—' }}</span></td>

                    <td>
                        <div class="lp-user-cell">
                            <img src="{{ $avUrl }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">
                            <span class="lp-user-name">{{ $customer }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="lp-badge" style="background:{{ $gs['bg'] }};color:{{ $gs['color'] }}">
                            {{ $gs['label'] }}
                        </span>
                    </td>

                    <td><span class="lp-amount">${{ number_format((float)$payment->amount, 2) }}</span></td>

                    <td>
                        <span class="lp-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="lp-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td><span class="lp-date">{{ $payment->paid_at?->format('M d, Y H:i') ?? '—' }}</span></td>

                    <td><span class="lp-date">{{ $payment->created_at?->format('M d, Y') }}</span></td>

                    <td>
                        <div class="lp-actions">
                            @php $statusVal = $payment->status?->value ?? $payment->status; @endphp
                            @if(in_array($statusVal, ['pending', 'processing', 'expired']))
                            <form method="POST" action="{{ route('admin.payments.force-verify', $payment) }}" style="display:inline" onsubmit="this.querySelector('button').disabled=true">
                                @csrf
                                <button type="submit" class="lp-act-btn" title="Force Verify with Bakong" style="color:#fbbf24">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            <a href="{{ $viewUrl($payment) }}" class="lp-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/>
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/>
                            </svg>
                            <p>No payments found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
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
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} payments
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="lp-per-page">
                    Per page
                    <select onchange="window.location.href='{{ $url([]) }}&per_page='+this.value+'&page=1'">
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
