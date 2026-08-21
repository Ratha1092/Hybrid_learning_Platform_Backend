@php
    /** @var \App\Domains\Payments\Models\Payment $record */
    $payment = $record;
    $payment->loadMissing(['order.user', 'order.items', 'transactions']);

    $backUrl = route('filament.admin.pages.payments');

    $statusVal = $payment->status?->value ?? $payment->status;
    $statusStyle = match ($statusVal) {
        'paid', 'completed' => ['bg' => 'rgba(52,211,153,.14)', 'color' => '#059669', 'dot' => '#10b981', 'label' => ucfirst($statusVal)],
        'pending'           => ['bg' => 'rgba(251,191,36,.14)',  'color' => '#d97706', 'dot' => '#f59e0b', 'label' => 'Pending'],
        'processing'        => ['bg' => 'rgba(99,102,241,.14)',  'color' => '#6366f1', 'dot' => '#818cf8', 'label' => 'Processing'],
        'failed'            => ['bg' => 'rgba(248,113,113,.14)', 'color' => '#dc2626', 'dot' => '#ef4444', 'label' => 'Failed'],
        'expired'           => ['bg' => 'rgba(248,113,113,.14)', 'color' => '#dc2626', 'dot' => '#ef4444', 'label' => 'Expired'],
        'cancelled'         => ['bg' => 'rgba(248,113,113,.14)', 'color' => '#dc2626', 'dot' => '#ef4444', 'label' => 'Cancelled'],
        default             => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#64748b', 'dot' => '#94a3b8', 'label' => ucfirst($statusVal ?? 'Unknown')],
    };

    $gwVal   = strtolower($payment->payment_gateway?->value ?? $payment->payment_gateway ?? '');
    $gwLabel = strtoupper($gwVal ?: 'UNKNOWN');
    $gwColors = [
        'bakong'  => ['bg' => 'rgba(234,88,12,.12)',  'color' => '#ea580c'],
        'khqr'    => ['bg' => 'rgba(234,88,12,.12)',  'color' => '#ea580c'],
        'aba'     => ['bg' => 'rgba(16,185,129,.12)', 'color' => '#059669'],
        'stripe'  => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#6366f1'],
        'paypal'  => ['bg' => 'rgba(37,99,235,.12)',  'color' => '#2563eb'],
    ];
    $gwStyle = $gwColors[$gwVal] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b'];

    $order    = $payment->order;
    $user     = $order?->user;
    $orderStatusVal = $order?->status?->value ?? $order?->status;
    $orderStatusStyle = match ($orderStatusVal) {
        'completed' => ['bg' => 'rgba(52,211,153,.12)', 'color' => '#059669', 'label' => 'Completed'],
        'pending'   => ['bg' => 'rgba(251,191,36,.12)', 'color' => '#d97706', 'label' => 'Pending'],
        'cancelled' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#dc2626', 'label' => 'Cancelled'],
        default     => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'label' => ucfirst($orderStatusVal ?? '—')],
    };

    $avatarName = urlencode($user?->name ?? '?');
    $avatarBg   = substr(md5($user?->name ?? ''), 0, 6);
    $avatarUrl  = $user?->avatar_url
        ?: 'https://ui-avatars.com/api/?name=' . $avatarName . '&background=' . $avatarBg . '&color=fff&bold=true&size=128';
@endphp

<div class="ov">

<style>
.ov,.ov *,.ov *::before,.ov *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.ov {
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;
    line-height:1.5;
    display:grid;
    gap:20px;
    padding-bottom:48px;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 1px 4px rgba(15,23,42,.06),0 4px 16px rgba(15,23,42,.06);
    --accent:#7c3aed;
    --radius:14px;
    color:var(--t1);
}
html.dark .ov {
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
}

/* Header */
.ov-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.ov-header-left {
    display:flex;
    flex-direction:column;
    gap:6px;
}
.ov-title-row {
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}
.ov-title {
    font-size:clamp(20px,2.4vw,28px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.ov-status-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 11px;
    border-radius:20px;
    font-size:12px;
    font-weight:700;
}
.ov-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.ov-subtitle {
    font-size:12.5px;
    color:var(--t2);
}
.ov-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}

/* Buttons */
.ov-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:9px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:none;
    font-family:inherit;
    transition:all .15s;
    white-space:nowrap;
}
.ov-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.ov-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.ov-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.ov-btn-amber {
    background:#ff0000;
    color:#fff;
    border:1px solid transparent;
}
.ov-btn-amber:hover {
    opacity:.88;
}
.ov-btn-outline {
    background:transparent;
    border:1px solid var(--bd2);
    color:var(--t2);
    font-size:12px;
    font-weight:600;
}
.ov-btn-outline:hover {
    border-color:var(--accent);
    color:var(--accent);
}

/* Cards */
.ov-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:var(--sh);
}
.ov-card-header {
    padding:14px 20px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:9px;
}
.ov-card-icon {
    width:32px;
    height:32px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.ov-card-icon svg {
    width:16px;
    height:16px;
}
.ov-card-title {
    font-size:13.5px;
    font-weight:700;
    color:var(--t1);
}
.ov-card-body {
    padding:20px;
}

/* 2-col grid */
.ov-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}
@media(max-width:900px) {
    .ov-grid-2 {
        grid-template-columns:1fr;
    }
}

/* Stats row inside cards */
.ov-stat-row {
    display:grid;
    gap:12px;
    padding-bottom:18px;
    margin-bottom:18px;
    border-bottom:1px solid var(--bd);
}
.ov-stat-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    margin-bottom:5px;
}
.ov-mini-badge {
    display:inline-flex;
    align-items:center;
    padding:3px 9px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
}

/* Big amount */
.ov-big-amount {
    font-size:36px;
    font-weight:800;
    letter-spacing:-.03em;
    color:#059669;
    line-height:1;
}

/* Dates */
.ov-dates-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}
.ov-date-item {
    display:flex;
    align-items:center;
    gap:7px;
    font-size:12.5px;
    color:var(--t2);
}
.ov-date-icon {
    width:16px;
    height:16px;
    flex-shrink:0;
}

/* Order card */
.ov-order-number {
    font-family:ui-monospace,SFMono-Regular,monospace;
    font-size:14px;
    font-weight:700;
    color:var(--accent);
}
.ov-meta-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:0;
    width:100%;
}
.ov-meta-item {
    display:flex;
    flex-direction:column;
    gap:3px;
    padding:10px 0;
    border-bottom:1px solid var(--bd);
}
.ov-meta-item:nth-last-child(-n+2) {
    border-bottom:none;
}
.ov-meta-label {
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
}
.ov-meta-value {
    font-size:13px;
    font-weight:600;
    color:var(--t1);
}

/* Customer in order card */
.ov-customer-row {
    display:flex;
    align-items:center;
    gap:10px;
    padding:14px 0;
    border-bottom:1px solid var(--bd);
    margin-bottom:14px;
}
.ov-avatar {
    width:44px;
    height:44px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid var(--bd2);
    flex-shrink:0;
}
.ov-customer-name {
    font-size:14px;
    font-weight:700;
    color:var(--t1);
}
.ov-customer-email {
    font-size:12px;
    color:var(--t2);
    margin-top:2px;
}

/* References table */
.ov-table {
    width:100%;
    border-collapse:collapse;
}
.ov-table th {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    padding:8px 16px;
    text-align:left;
    border-bottom:1px solid var(--bd);
    background:var(--p2);
}
.ov-table td {
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    vertical-align:middle;
}
.ov-table tr:last-child td {
    border-bottom:none;
}
.ov-ref-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    margin-bottom:4px;
}
.ov-ref-value {
    font-family:ui-monospace,SFMono-Regular,monospace;
    font-size:12px;
    color:var(--t1);
    word-break:break-all;
}
.ov-copy-btn {
    background:none;
    border:none;
    cursor:pointer;
    color:var(--t2);
    padding:3px;
    border-radius:4px;
    display:inline-flex;
    align-items:center;
    transition:color .15s;
    vertical-align:middle;
    margin-left:4px;
}
.ov-copy-btn:hover {
    color:var(--t1);
}
.ov-copy-btn svg {
    width:13px;
    height:13px;
}

/* JSON block */
.ov-json-block {
    background:var(--p2);
    border:1px solid var(--bd);
    border-radius:8px;
    padding:14px 16px;
    font-family:ui-monospace,SFMono-Regular,monospace;
    font-size:11.5px;
    color:var(--t2);
    white-space:pre-wrap;
    word-break:break-all;
    max-height:280px;
    overflow-y:auto;
    line-height:1.6;
}

/* Verification stats */
.ov-verify-grid {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
}

/* Transactions table */
.ov-txn-badge {
    display:inline-flex;
    align-items:center;
    padding:3px 9px;
    border-radius:6px;
    font-size:11px;
    font-weight:700;
}

@keyframes ovUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.ova {
    opacity:0;
    animation:ovUp .4s cubic-bezier(.16,1,.3,1) forwards;
}
.ov1 {
    animation-delay:.05s;
}
.ov2 {
    animation-delay:.1s;
}
.ov3 {
    animation-delay:.15s;
}
.ov4 {
    animation-delay:.2s;
}
.ov5 {
    animation-delay:.25s;
}
</style>

{{-- ── Header ─────────────────────────────────────────────────────────── --}}
<div class="ov-header ova ov1">
    <div class="ov-header-left">
        <div class="ov-title-row">
            <h1 class="ov-title">Payment #{{ $payment->id }}</h1>
            <span class="ov-status-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                <span class="ov-dot" style="background:{{ $statusStyle['dot'] }}"></span>
                {{ $statusStyle['label'] }}
            </span>
            <span class="ov-mini-badge" style="background:{{ $gwStyle['bg'] }};color:{{ $gwStyle['color'] }}">
                {{ $gwLabel }}
            </span>
        </div>
        <p class="ov-subtitle">Created on {{ $payment->created_at?->setTimezone(config('app.timezone'))->format('M d, Y') }} at {{ $payment->created_at?->setTimezone(config('app.timezone'))->format('H:i') }}
            @if($order) · Order <strong>{{ $order->order_number }}</strong> @endif
        </p>
    </div>
    <div class="ov-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="ov-btn ov-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Payments
        </a>
    </div>
</div>

{{-- ── Row 1: Payment Summary + Linked Order ──────────────────────────── --}}
<div class="ov-grid-2 ova ov2">

    {{-- Payment Summary --}}
    <div class="ov-card">
        <div class="ov-card-header">
            <div class="ov-card-icon" style="background:rgba(5,150,105,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/></svg>
            </div>
            <span class="ov-card-title">Payment Summary</span>
        </div>
        <div class="ov-card-body">

            {{-- Big amount --}}
            <div style="padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd)">
                <div class="ov-stat-label">Amount Charged</div>
                <div style="display:flex;align-items:baseline;gap:8px;margin-top:4px">
                    <div class="ov-big-amount">${{ number_format((float)$payment->amount, 2) }}</div>
                    <span class="ov-mini-badge" style="background:rgba(148,163,184,.1);color:var(--t2)">{{ strtoupper($payment->currency ?? 'USD') }}</span>
                </div>
            </div>

            {{-- Status + Gateway row --}}
            <div class="ov-stat-row" style="grid-template-columns:1fr 1fr">
                <div>
                    <div class="ov-stat-label">Status</div>
                    <span class="ov-mini-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                        <span class="ov-dot" style="background:{{ $statusStyle['dot'] }};margin-right:4px"></span>
                        {{ $statusStyle['label'] }}
                    </span>
                </div>
                <div>
                    <div class="ov-stat-label">Gateway</div>
                    <span class="ov-mini-badge" style="background:{{ $gwStyle['bg'] }};color:{{ $gwStyle['color'] }}">{{ $gwLabel }}</span>
                </div>
            </div>

            {{-- Payer account --}}
            @if($payment->payer_account)
            <div style="padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd)">
                <div class="ov-stat-label">Payer Account</div>
                <div style="font-family:ui-monospace,monospace;font-size:13px;font-weight:600;color:var(--t1);margin-top:4px">{{ $payment->payer_account }}</div>
            </div>
            @endif

            {{-- Dates --}}
            <div class="ov-dates-row">
                <div class="ov-date-item">
                    @if($payment->paid_at)
                        <svg class="ov-date-icon" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    @else
                        <svg class="ov-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    @endif
                    <div>
                        <div class="ov-stat-label" style="margin-bottom:2px">Paid At</div>
                        @if($payment->paid_at)
                            <div style="font-size:12.5px;color:var(--t1);font-weight:500">{{ $payment->paid_at->setTimezone(config('app.timezone'))->format('M d, Y · H:i') }}</div>
                        @else
                            <div style="font-size:12.5px;color:var(--t2);font-style:italic">Not paid yet</div>
                        @endif
                    </div>
                </div>
                <div class="ov-date-item">
                    <svg class="ov-date-icon" viewBox="0 0 24 24" fill="none" stroke="{{ $payment->expires_at && $payment->expires_at->isPast() ? '#dc2626' : 'currentColor' }}" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    <div>
                        <div class="ov-stat-label" style="margin-bottom:2px">Expires At</div>
                        @if($payment->expires_at)
                            <div style="font-size:12.5px;font-weight:500;color:{{ $payment->expires_at->isPast() ? '#dc2626' : 'var(--t1)' }}">
                                {{ $payment->expires_at->setTimezone(config('app.timezone'))->format('M d, Y · H:i') }}
                                @if($payment->expires_at->isPast())
                                    <span style="color:#dc2626;font-size:11px;font-weight:700"> · EXPIRED</span>
                                @endif
                            </div>
                        @else
                            <div style="font-size:12.5px;color:var(--t2);font-style:italic">No expiry</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Failure reason --}}
            @if($payment->failure_reason)
            <div style="margin-top:16px;padding:12px 14px;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);border-radius:8px">
                <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#dc2626;margin-bottom:4px">Failure Reason</div>
                <div style="font-size:12.5px;color:#dc2626">{{ $payment->failure_reason }}</div>
            </div>
            @endif

        </div>
    </div>

    {{-- Linked Order & Customer --}}
    <div class="ov-card">
        <div class="ov-card-header">
            <div class="ov-card-icon" style="background:rgba(37,99,235,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
            </div>
            <span class="ov-card-title">Linked Order</span>
        </div>
        <div class="ov-card-body">
            @if($order)
                {{-- Customer --}}
                <div class="ov-customer-row">
                    <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="ov-avatar">
                    <div style="min-width:0">
                        <div class="ov-customer-name">{{ $user?->name ?? '—' }}</div>
                        <div class="ov-customer-email">{{ $user?->email ?? '—' }}</div>
                    </div>
                    @if($user)
                        <a href="{{ url('/admin/users/' . $user->id) }}" target="_blank" style="margin-left:auto;flex-shrink:0" class="ov-btn-outline ov-btn" style="font-size:11px;padding:5px 10px">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        </a>
                    @endif
                </div>

                {{-- Order details grid --}}
                <div class="ov-meta-grid" style="margin-bottom:16px">
                    <div class="ov-meta-item">
                        <div class="ov-meta-label">Order #</div>
                        <div class="ov-order-number">{{ $order->order_number }}</div>
                    </div>
                    <div class="ov-meta-item">
                        <div class="ov-meta-label">Order Status</div>
                        <span class="ov-mini-badge" style="background:{{ $orderStatusStyle['bg'] }};color:{{ $orderStatusStyle['color'] }}">{{ $orderStatusStyle['label'] }}</span>
                    </div>
                    <div class="ov-meta-item">
                        <div class="ov-meta-label">Order Total</div>
                        <div class="ov-meta-value">${{ number_format((float)($order->total_amount ?? 0), 2) }}</div>
                    </div>
                    <div class="ov-meta-item">
                        <div class="ov-meta-label">Items</div>
                        <div class="ov-meta-value">{{ $order->items?->count() ?? 0 }} {{ Str::plural('course', $order->items?->count() ?? 0) }}</div>
                    </div>
                </div>

                <a href="{{ url('/admin/orders/' . $order->id) }}" wire:navigate class="ov-btn ov-btn-outline" style="width:100%;justify-content:center">
                    View Full Order
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            @else
                <div style="text-align:center;padding:32px;color:var(--t2);font-style:italic">No linked order</div>
            @endif
        </div>
    </div>

</div>

{{-- ── Transaction References ──────────────────────────────────────────── --}}
<div class="ov-card ova ov3">
    <div class="ov-card-header">
        <div class="ov-card-icon" style="background:rgba(124,58,237,.1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
        </div>
        <span class="ov-card-title">Transaction References</span>
    </div>
    <div style="overflow-x:auto">
        <table class="ov-table">
            <thead>
                <tr>
                    <th>Reference Type</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['label' => 'Transaction ID',    'key' => 'transaction_id'],
                    ['label' => 'External Reference','key' => 'external_reference'],
                    ['label' => 'Idempotency Key',   'key' => 'idempotency_key'],
                ] as $ref)
                    @if($payment->{$ref['key']})
                    <tr>
                        <td style="min-width:180px">
                            <div class="ov-ref-label">{{ $ref['label'] }}</div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px">
                                <span class="ov-ref-value">{{ $payment->{$ref['key']} }}</span>
                                <button class="ov-copy-btn" title="Copy"
                                    onclick="
                                        navigator.clipboard.writeText('{{ addslashes($payment->{$ref['key']}) }}');
                                        this.innerHTML='<svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#059669\' stroke-width=\'2\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z\'/></svg>';
                                        setTimeout(()=>this.innerHTML='<svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184\'/></svg>',1500)
                                    ">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
                @if(!$payment->transaction_id && !$payment->external_reference && !$payment->idempotency_key)
                <tr>
                    <td colspan="2" style="text-align:center;padding:28px;color:var(--t2);font-style:italic">No reference data available</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- ── Verification + KHQR ─────────────────────────────────────────────── --}}
<div class="ov-grid-2 ova ov4">

    {{-- Verification --}}
    <div class="ov-card">
        <div class="ov-card-header">
            <div class="ov-card-icon" style="background:rgba(245,158,11,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <span class="ov-card-title">Verification</span>
        </div>
        <div class="ov-card-body">
            <div class="ov-verify-grid" style="margin-bottom:16px">
                <div>
                    <div class="ov-stat-label">Attempts</div>
                    @php $attempts = $payment->verification_attempts ?? 0; @endphp
                    <span class="ov-mini-badge" style="background:{{ $attempts > 3 ? 'rgba(248,113,113,.12)' : 'rgba(148,163,184,.1)' }};color:{{ $attempts > 3 ? '#dc2626' : 'var(--t2)' }};font-size:15px;padding:4px 12px;margin-top:4px">
                        {{ $attempts }}
                    </span>
                </div>
                <div style="grid-column:span 2">
                    <div class="ov-stat-label">Last Verified</div>
                    <div style="font-size:12.5px;color:var(--t1);margin-top:4px;font-weight:500">
                        {{ $payment->last_verified_at?->setTimezone(config('app.timezone'))->format('M d, Y · H:i') ?? 'Never verified' }}
                    </div>
                </div>
            </div>
            @if($payment->failure_reason)
                <div style="padding:12px 14px;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);border-radius:8px">
                    <div class="ov-stat-label" style="color:#dc2626;margin-bottom:4px">Failure Reason</div>
                    <div style="font-size:12.5px;color:#dc2626">{{ $payment->failure_reason }}</div>
                </div>
            @else
                <div style="padding:12px 14px;background:rgba(52,211,153,.06);border:1px solid rgba(52,211,153,.18);border-radius:8px;text-align:center">
                    <div style="font-size:12.5px;color:#059669;font-weight:600">No failure reasons recorded</div>
                </div>
            @endif
        </div>
    </div>

    {{-- KHQR Payload --}}
    <div class="ov-card">
        <div class="ov-card-header">
            <div class="ov-card-icon" style="background:rgba(234,88,12,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/></svg>
            </div>
            <span class="ov-card-title">KHQR Payload</span>
        </div>
        <div class="ov-card-body">
            @if($payment->khqr_payload)
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <span style="font-size:12px;color:var(--t2)">QR payload string</span>
                    <button class="ov-btn ov-btn-outline" style="padding:5px 10px;font-size:11px"
                        onclick="
                            navigator.clipboard.writeText(this.dataset.val);
                            this.textContent='Copied!';
                            setTimeout(()=>this.textContent='Copy',1500)
                        " data-val="{{ $payment->khqr_payload }}">
                        Copy
                    </button>
                </div>
                <div class="ov-json-block" style="font-size:11px;word-break:break-all;max-height:180px">{{ $payment->khqr_payload }}</div>
            @else
                <div style="text-align:center;padding:32px;color:var(--t2)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;margin:0 auto 10px;opacity:.3"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5z"/></svg>
                    <div style="font-size:12.5px;font-style:italic">No KHQR payload for this payment</div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Gateway Transactions Log ─────────────────────────────────────────── --}}
@if($payment->transactions->count() > 0)
<div class="ov-card ova ov5">
    <div class="ov-card-header">
        <div class="ov-card-icon" style="background:rgba(99,102,241,.1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75z"/></svg>
        </div>
        <span class="ov-card-title">Gateway Transactions</span>
        <span class="ov-mini-badge" style="background:rgba(99,102,241,.1);color:#6366f1;margin-left:auto">{{ $payment->transactions->count() }}</span>
    </div>
    <div style="overflow-x:auto">
        <table class="ov-table">
            <thead>
                <tr>
                    <th>Event Type</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->transactions->sortByDesc('created_at') as $txn)
                    @php
                        $txnGw = strtolower($txn->gateway ?? '');
                        $txnGwStyle = $gwColors[$txnGw] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b'];
                        $txnStatus = strtolower($txn->status ?? '');
                        $txnStatusStyle = match ($txnStatus) {
                            'success', 'paid', 'completed' => ['bg' => 'rgba(52,211,153,.12)', 'color' => '#059669'],
                            'pending', 'processing' => ['bg' => 'rgba(251,191,36,.12)', 'color' => '#d97706'],
                            'failed', 'error', 'expired' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#dc2626'],
                            default => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <span style="font-size:12.5px;font-weight:600;color:var(--t1)">{{ $txn->event_type ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="ov-txn-badge" style="background:{{ $txnGwStyle['bg'] }};color:{{ $txnGwStyle['color'] }}">
                                {{ strtoupper($txn->gateway ?? '—') }}
                            </span>
                        </td>
                        <td>
                            <span class="ov-txn-badge" style="background:{{ $txnStatusStyle['bg'] }};color:{{ $txnStatusStyle['color'] }}">
                                {{ ucfirst($txn->status ?? '—') }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--t2);white-space:nowrap">
                            {{ $txn->created_at?->setTimezone(config('app.timezone'))->format('M d, Y · H:i') ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── Gateway Response (raw JSON) ─────────────────────────────────────── --}}
@if($payment->gateway_response)
<div class="ov-card ova ov5">
    <div class="ov-card-header">
        <div class="ov-card-icon" style="background:rgba(100,116,139,.1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/></svg>
        </div>
        <span class="ov-card-title">Gateway Response</span>
        <button class="ov-btn ov-btn-outline" style="margin-left:auto;padding:5px 10px;font-size:11px"
            onclick="
                navigator.clipboard.writeText(this.dataset.val);
                this.textContent='Copied!';
                setTimeout(()=>this.textContent='Copy JSON',1500)
            " data-val="{{ addslashes(json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}">
            Copy JSON
        </button>
    </div>
    <div class="ov-card-body">
        <pre class="ov-json-block">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>
@endif

</div>
