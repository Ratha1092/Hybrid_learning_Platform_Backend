@php
    /** @var \App\Domains\Orders\Models\Order $record */
    $order = $record;
    $order->loadMissing(['user', 'items.course', 'items.instructor', 'payments']);

    $backUrl = route('filament.admin.pages.orders');
    $editUrl = url('/admin/orders/' . $order->id . '/edit');

    $statusVal = $order->status?->value ?? $order->status;
    $statusStyle = match ($statusVal) {
        'completed' => ['bg' => 'rgba(52,211,153,.14)', 'color' => '#059669', 'dot' => '#10b981', 'label' => 'Completed'],
        'pending'   => ['bg' => 'rgba(251,191,36,.14)', 'color' => '#d97706', 'dot' => '#f59e0b', 'label' => 'Pending'],
        'cancelled' => ['bg' => 'rgba(248,113,113,.14)', 'color' => '#dc2626', 'dot' => '#ef4444', 'label' => 'Cancelled'],
        'refunded'  => ['bg' => 'rgba(99,102,241,.14)', 'color' => '#6366f1', 'dot' => '#818cf8', 'label' => 'Refunded'],
        default     => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#64748b', 'dot' => '#94a3b8', 'label' => ucfirst($statusVal)],
    };

    $payStatusVal = $order->payment_status?->value ?? $order->payment_status;
    $payStatusStyle = match ($payStatusVal) {
        'paid'       => ['bg' => 'rgba(52,211,153,.12)', 'color' => '#059669', 'label' => 'Paid'],
        'processing' => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#6366f1', 'label' => 'Processing'],
        'pending'    => ['bg' => 'rgba(251,191,36,.12)', 'color' => '#d97706', 'label' => 'Pending'],
        'failed'     => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#dc2626', 'label' => 'Failed'],
        'expired'    => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#dc2626', 'label' => 'Expired'],
        'cancelled'  => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#dc2626', 'label' => 'Cancelled'],
        'refunded'   => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#6366f1', 'label' => 'Refunded'],
        default      => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'label' => ucfirst($payStatusVal)],
    };

    $user         = $order->user;
    $avatarName   = urlencode($user?->name ?? '?');
    $avatarBg     = substr(md5($user?->name ?? ''), 0, 6);
    $avatarUrl    = $user?->avatar
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar)
        : 'https://ui-avatars.com/api/?name=' . $avatarName . '&background=' . $avatarBg . '&color=fff&bold=true&size=128';

    $userViewUrl  = $user ? url('/admin/users/' . $user->id) : null;

    $paymentGatewayColors = [
        'bakong'  => ['bg' => 'rgba(234,88,12,.12)',  'color' => '#ea580c'],
        'stripe'  => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#6366f1'],
        'paypal'  => ['bg' => 'rgba(37,99,235,.12)',  'color' => '#2563eb'],
        'khqr'    => ['bg' => 'rgba(234,88,12,.12)',  'color' => '#ea580c'],
        'free'    => ['bg' => 'rgba(52,211,153,.1)',  'color' => '#059669'],
    ];
@endphp

<div class="ov">

<style>
.ov,.ov *,.ov *::before,.ov *::after{box-sizing:border-box;margin:0;padding:0}
.ov{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;
    display:grid;gap:20px;padding-bottom:48px;
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 1px 4px rgba(15,23,42,.06),0 4px 16px rgba(15,23,42,.06);
    --accent:#7c3aed;--radius:14px;
    color:var(--t1);
}
html.dark .ov{
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
}

/* Header */
.ov-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.ov-header-left{display:flex;flex-direction:column;gap:6px}
.ov-title-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.ov-title{font-size:clamp(20px,2.4vw,28px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.ov-status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:12px;font-weight:700}
.ov-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.ov-subtitle{font-size:12.5px;color:var(--t2)}
.ov-header-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

/* Buttons */
.ov-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.ov-btn svg{width:14px;height:14px;flex-shrink:0}
.ov-btn-gray{background:var(--p2);border:1px solid var(--bd2);color:var(--t2)}
.ov-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.ov-btn-amber{background:#ff0000;color:#fff;border:1px solid transparent}
.ov-btn-amber:hover{opacity:.88}
.ov-btn-outline{background:transparent;border:1px solid var(--bd2);color:var(--t2);font-size:12px;font-weight:600}
.ov-btn-outline:hover{border-color:var(--accent);color:var(--accent)}
.ov-btn-link{background:none;border:none;color:var(--accent);font-size:12.5px;font-weight:600;padding:0;cursor:pointer;display:inline-flex;align-items:center;gap:4px}
.ov-btn-link:hover{opacity:.75}
.ov-btn-link svg{width:13px;height:13px}

/* Cards */
.ov-card{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);overflow:hidden;box-shadow:var(--sh)}
.ov-card-header{padding:14px 20px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:9px}
.ov-card-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ov-card-icon svg{width:16px;height:16px}
.ov-card-title{font-size:13.5px;font-weight:700;color:var(--t1)}
.ov-card-body{padding:20px}

/* 2-col grid */
.ov-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:900px){.ov-grid-2{grid-template-columns:1fr}}

/* Order summary internals */
.ov-status-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd)}
.ov-status-item-label{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-bottom:6px}
.ov-status-value{display:inline-flex;align-items:center;gap:5px}
.ov-mini-badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:6px;font-size:11.5px;font-weight:700}

.ov-amounts-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd)}
.ov-amount-label{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-bottom:4px}
.ov-amount-value{font-size:21px;font-weight:800;letter-spacing:-.02em}

.ov-dates-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.ov-date-item{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)}
.ov-date-icon{width:16px;height:16px;flex-shrink:0}
.ov-date-text{}

/* Customer card */
.ov-customer-profile{display:flex;flex-direction:column;align-items:center;text-align:center;padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd);gap:10px}
.ov-avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--bd2);flex-shrink:0}
.ov-customer-name{font-size:18px;font-weight:800;color:var(--t1);letter-spacing:-.01em}
.ov-customer-email{display:flex;align-items:center;justify-content:center;gap:5px;font-size:12.5px;color:var(--t2)}
.ov-customer-email svg{width:13px;height:13px;flex-shrink:0}
.ov-role-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700}
.ov-customer-meta{display:grid;grid-template-columns:1fr 1fr;gap:0;width:100%}
.ov-meta-item{display:flex;flex-direction:column;gap:3px;padding:10px 0;border-bottom:1px solid var(--bd)}
.ov-meta-item:nth-last-child(-n+2){border-bottom:none}
.ov-meta-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2)}
.ov-meta-value{font-size:13px;font-weight:600;color:var(--t1)}

/* Courses table */
.ov-table{width:100%;border-collapse:collapse}
.ov-table th{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);padding:8px 12px;text-align:left;border-bottom:1px solid var(--bd);background:var(--p2)}
.ov-table td{padding:14px 12px;border-bottom:1px solid var(--bd);vertical-align:middle}
.ov-table tr:last-child td{border-bottom:none}
.ov-course-cell{display:flex;align-items:center;gap:10px}
.ov-course-thumb{width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid var(--bd2);flex-shrink:0;background:var(--p2)}
.ov-course-thumb-placeholder{width:44px;height:44px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;letter-spacing:.04em;color:#fff}
.ov-course-name{font-size:13px;font-weight:700;color:var(--t1);line-height:1.3}
.ov-course-id{font-size:11px;color:var(--t2);margin-top:2px}
.ov-instructor-cell{display:flex;align-items:center;gap:5px;font-size:12.5px;color:var(--t1)}
.ov-instructor-cell svg{width:14px;height:14px;color:var(--t2);flex-shrink:0}
.ov-price{font-size:13px;font-weight:600;color:var(--t1)}
.ov-payout-green{font-size:13px;font-weight:700;color:#059669}
.ov-payout-amber{font-size:13px;font-weight:700;color:#d97706}
.ov-table-footer{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-top:1px solid var(--bd);background:var(--p2)}
.ov-item-count{font-size:12px;color:var(--t2);font-weight:600}

/* Payment history table */
.ov-txn-id{font-family:ui-monospace,SFMono-Regular,monospace;font-size:11.5px;color:var(--t1);max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ov-copy-btn{background:none;border:none;cursor:pointer;color:var(--t2);padding:3px;border-radius:4px;display:inline-flex;align-items:center;transition:color .15s}
.ov-copy-btn:hover{color:var(--t1)}
.ov-copy-btn svg{width:13px;height:13px}
.ov-txn-cell{display:flex;align-items:center;gap:6px}
.ov-payments-footer{display:flex;align-items:center;justify-content:flex-start;padding:12px 20px;border-top:1px solid var(--bd);background:var(--p2)}

@keyframes ovUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.ova{opacity:0;animation:ovUp .4s cubic-bezier(.16,1,.3,1) forwards}
.ov1{animation-delay:.05s}.ov2{animation-delay:.1s}.ov3{animation-delay:.15s}.ov4{animation-delay:.2s}
</style>

{{-- ── Header ─────────────────────────────────────────────────────────── --}}
<div class="ov-header ova ov1">
    <div class="ov-header-left">
        <div class="ov-title-row">
            <h1 class="ov-title">Order {{ $order->order_number }}</h1>
            <span class="ov-status-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                <span class="ov-dot" style="background:{{ $statusStyle['dot'] }}"></span>
                {{ $statusStyle['label'] }}
            </span>
        </div>
        <p class="ov-subtitle">Placed on {{ $order->created_at?->format('M d, Y') }} at {{ $order->created_at?->format('H:i') }}</p>
    </div>
    <div class="ov-header-actions">
        <a href="{{ $backUrl }}" class="ov-btn ov-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Orders
        </a>
        <a href="{{ $editUrl }}" class="ov-btn ov-btn-amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
            Edit Order
        </a>
    </div>
</div>

{{-- ── Row 1: Order Summary + Customer ───────────────────────────────── --}}
<div class="ov-grid-2 ova ov2">

    {{-- Order Summary --}}
    <div class="ov-card">
        <div class="ov-card-header">
            <div class="ov-card-icon" style="background:rgba(37,99,235,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
            </div>
            <span class="ov-card-title">Order Summary</span>
        </div>
        <div class="ov-card-body">
            {{-- Status row --}}
            <div class="ov-status-row">
                <div>
                    <div class="ov-status-item-label">Order Status</div>
                    <div class="ov-status-value">
                        <span class="ov-dot" style="background:{{ $statusStyle['dot'] }}"></span>
                        <span style="font-size:13px;font-weight:600;color:{{ $statusStyle['color'] }}">{{ $statusStyle['label'] }}</span>
                    </div>
                </div>
                <div>
                    <div class="ov-status-item-label">Payment Status</div>
                    <span class="ov-mini-badge" style="background:{{ $payStatusStyle['bg'] }};color:{{ $payStatusStyle['color'] }}">
                        {{ $payStatusStyle['label'] }}
                    </span>
                </div>
                <div>
                    <div class="ov-status-item-label">Payment Method</div>
                    @php
                        $pm = strtolower($order->payment_method ?? '');
                        $pmColor = $paymentGatewayColors[$pm] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b'];
                    @endphp
                    <span class="ov-mini-badge" style="background:{{ $pmColor['bg'] }};color:{{ $pmColor['color'] }}">
                        {{ strtoupper($order->payment_method ?? '—') }}
                    </span>
                </div>
            </div>

            {{-- Amounts --}}
            <div class="ov-amounts-row">
                <div>
                    <div class="ov-amount-label">Total Charged</div>
                    <div class="ov-amount-value" style="color:#059669">${{ number_format((float)$order->final_amount, 2) }}</div>
                </div>
                <div>
                    <div class="ov-amount-label">Discount</div>
                    <div class="ov-amount-value" style="color:#d97706">${{ number_format((float)$order->discount_amount, 2) }}</div>
                </div>
                <div>
                    <div class="ov-amount-label">Amount Paid</div>
                    <div class="ov-amount-value" style="color:#059669">${{ number_format((float)$order->final_amount, 2) }}</div>
                </div>
            </div>

            {{-- Dates --}}
            <div class="ov-dates-row">
                <div class="ov-date-item">
                    <svg class="ov-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    <div>
                        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-bottom:2px">Created</div>
                        <div style="font-size:12.5px;color:var(--t1);font-weight:500">{{ $order->created_at?->format('M d, Y') }} at {{ $order->created_at?->format('H:i') }}</div>
                    </div>
                </div>
                <div class="ov-date-item">
                    @if($order->paid_at)
                        <svg class="ov-date-icon" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    @else
                        <svg class="ov-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    @endif
                    <div>
                        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-bottom:2px">Paid At</div>
                        @if($order->paid_at)
                            <div style="font-size:12.5px;color:var(--t1);font-weight:500">{{ $order->paid_at->format('M d, Y') }} at {{ $order->paid_at->format('H:i') }}</div>
                        @else
                            <div style="font-size:12.5px;color:var(--t2);font-style:italic">Not yet paid</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer --}}
    <div class="ov-card">
        <div class="ov-card-header">
            <div class="ov-card-icon" style="background:rgba(124,58,237,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </div>
            <span class="ov-card-title">Customer</span>
        </div>
        <div class="ov-card-body">
            {{-- Profile block --}}
            <div class="ov-customer-profile">
                <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="ov-avatar">
                <div style="display:flex;flex-direction:column;align-items:center;gap:5px">
                    <div class="ov-customer-name">{{ $user?->name ?? $order->customer_name ?? '—' }}</div>
                    <div class="ov-customer-email">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        {{ $order->customer_email ?? $user?->email ?? '—' }}
                    </div>
                    @php
                        $role = $user?->getRoleNames()->first() ?? '';
                        $roleStyle = match ($role) {
                            'super-admin', 'admin' => ['bg' => 'rgba(239,68,68,.12)', 'color' => '#dc2626'],
                            'finance-manager', 'accountant' => ['bg' => 'rgba(13,148,136,.12)', 'color' => '#0d9488'],
                            'content-manager', 'moderator' => ['bg' => 'rgba(245,158,11,.12)', 'color' => '#d97706'],
                            'instructor' => ['bg' => 'rgba(245,158,11,.12)', 'color' => '#d97706'],
                            'student'    => ['bg' => 'rgba(37,99,235,.12)', 'color' => '#2563eb'],
                            default      => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b'],
                        };
                    @endphp
                    @if($role)
                        <span class="ov-role-badge" style="background:{{ $roleStyle['bg'] }};color:{{ $roleStyle['color'] }}">
                            {{ ucfirst($role) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Meta --}}
            <div class="ov-customer-meta" style="margin-bottom:16px">
                <div class="ov-meta-item">
                    <div class="ov-meta-label">Role</div>
                    <div class="ov-meta-value">{{ $role ? ucfirst($role) : '—' }}</div>
                </div>
                <div class="ov-meta-item">
                    <div class="ov-meta-label">User ID</div>
                    <div class="ov-meta-value" style="font-family:ui-monospace,monospace;font-size:12px">#{{ $user?->id ?? '—' }}</div>
                </div>
            </div>

            {{-- View profile button --}}
            @if($userViewUrl)
                <a href="{{ $userViewUrl }}" target="_blank" class="ov-btn ov-btn-outline" style="width:100%;justify-content:center">
                    View Customer Profile
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            @endif
        </div>
    </div>

</div>

{{-- ── Purchased Courses ──────────────────────────────────────────────── --}}
<div class="ov-card ova ov3">
    <div class="ov-card-header">
        <div class="ov-card-icon" style="background:rgba(16,185,129,.1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
        </div>
        <span class="ov-card-title">Purchased Courses</span>
    </div>
    <div style="overflow-x:auto">
        <table class="ov-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Price</th>
                    <th>Commission %</th>
                    <th>Instructor Payout</th>
                    <th>Platform Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                    @php
                        $thumb = $item->course?->thumbnail_url;
                        $courseUrl = $item->course_id ? url('/admin/courses/' . $item->course_id) : null;
                        $initials  = strtoupper(substr($item->course_title ?? 'C', 0, 2));
                        $thumbBg   = '#' . substr(md5($item->course_title ?? ''), 0, 6);
                    @endphp
                    <tr>
                        <td style="min-width:220px">
                            <div class="ov-course-cell">
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $item->course_title }}" class="ov-course-thumb">
                                @else
                                    <div class="ov-course-thumb-placeholder" style="background:{{ $thumbBg }}">{{ $initials }}</div>
                                @endif
                                <div>
                                    <div class="ov-course-name">{{ $item->course_title ?? '—' }}</div>
                                    @if($item->course_id)
                                        <div class="ov-course-id">Course ID: C-{{ $item->course_id }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="min-width:140px">
                            <div class="ov-instructor-cell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                {{ $item->instructor?->name ?? '—' }}
                            </div>
                        </td>
                        <td class="ov-price">${{ number_format((float)$item->price, 2) }}</td>
                        <td>
                            <span class="ov-mini-badge" style="background:rgba(148,163,184,.1);color:var(--t2)">
                                {{ number_format((float)$item->commission_percentage, 2) }}%
                            </span>
                        </td>
                        <td class="ov-payout-green">${{ number_format((float)$item->instructor_amount, 2) }}</td>
                        <td class="ov-payout-amber">${{ number_format((float)$item->platform_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:32px;color:var(--t2);font-style:italic">No courses in this order</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="ov-table-footer">
        <div style="display:flex;gap:8px">
            @foreach($order->items as $item)
                @if($item->course_id)
                    <a href="{{ url('/admin/courses/' . $item->course_id) }}" target="_blank" class="ov-btn ov-btn-outline" style="font-size:11.5px;padding:6px 12px">
                        View Course
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    </a>
                @endif
            @endforeach
        </div>
        <span class="ov-item-count">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
    </div>
</div>

{{-- ── Payment History ─────────────────────────────────────────────────── --}}
<div class="ov-card ova ov4">
    <div class="ov-card-header">
        <div class="ov-card-icon" style="background:rgba(37,99,235,.1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/></svg>
        </div>
        <span class="ov-card-title">Payment History</span>
    </div>
    <div style="overflow-x:auto">
        <table class="ov-table">
            <thead>
                <tr>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Transaction ID</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->payments as $payment)
                    @php
                        $gw      = strtolower($payment->payment_gateway?->value ?? $payment->payment_gateway ?? '');
                        $gwStyle = $paymentGatewayColors[$gw] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b'];
                        $gwLabel = strtolower($payment->payment_gateway?->value ?? $payment->payment_gateway ?? '—');

                        $pStatusVal = $payment->status?->value ?? $payment->status;
                        $pStatusStyle = match ($pStatusVal) {
                            'paid'       => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#059669', 'label' => 'Paid'],
                            'pending'    => ['bg' => 'rgba(251,191,36,.12)', 'color' => '#d97706', 'label' => 'Pending'],
                            'processing' => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#6366f1', 'label' => 'Processing'],
                            'failed'     => ['bg' => 'rgba(248,113,113,.12)','color' => '#dc2626', 'label' => 'Failed'],
                            'expired'    => ['bg' => 'rgba(248,113,113,.12)','color' => '#dc2626', 'label' => 'Expired'],
                            default      => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#64748b', 'label' => ucfirst($pStatusVal)],
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="ov-mini-badge" style="background:{{ $gwStyle['bg'] }};color:{{ $gwStyle['color'] }}">
                                {{ $gwLabel }}
                            </span>
                        </td>
                        <td>
                            <span class="ov-mini-badge" style="background:{{ $pStatusStyle['bg'] }};color:{{ $pStatusStyle['color'] }}">
                                {{ $pStatusStyle['label'] }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:var(--t1)">${{ number_format((float)$payment->amount, 2) }}</td>
                        <td>
                            <div class="ov-txn-cell">
                                <span class="ov-txn-id" title="{{ $payment->transaction_id ?? '—' }}">{{ $payment->transaction_id ?? '—' }}</span>
                                @if($payment->transaction_id)
                                    <button class="ov-copy-btn" title="Copy transaction ID"
                                        onclick="navigator.clipboard.writeText('{{ $payment->transaction_id }}');this.innerHTML='<svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#059669\' stroke-width=\'2\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z\'/></svg>';setTimeout(()=>this.innerHTML='<svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184\'/></svg>',1500)">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td style="font-size:12.5px;color:var(--t2);white-space:nowrap">
                            {{ $payment->paid_at?->format('M d, Y') }} at {{ $payment->paid_at?->format('H:i') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:var(--t2);font-style:italic">No payments recorded</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($order->payments->count() > 1)
        <div class="ov-payments-footer">
            <a href="{{ $backUrl }}?order={{ $order->id }}" class="ov-btn-link">
                View Full History
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    @endif
</div>

</div>
