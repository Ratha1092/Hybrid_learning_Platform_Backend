@php
    $order = $refund->order;
    $user  = $order?->user;

    $backUrl  = route('filament.admin.pages.refunds');
    $orderUrl = $order ? url('/admin/orders/' . $order->id) : null;
    $userUrl  = $user ? url('/admin/users/' . $user->id) : null;

    $avatarBg  = substr(md5($user?->name ?? ''), 0, 6);
    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? '?') . '&background=' . $avatarBg . '&color=fff&bold=true&size=128';
@endphp

<div class="rfv">

<style>
.rfv,.rfv *,.rfv *::before,.rfv *::after{box-sizing:border-box;margin:0;padding:0}
.rfv{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;
    display:grid;gap:20px;padding-bottom:48px;
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 1px 4px rgba(15,23,42,.06),0 4px 16px rgba(15,23,42,.06);
    --accent:#2563eb;--radius:14px;
    color:var(--t1);
}
html.dark .rfv{
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
}

.rfv-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.rfv-header-left{display:flex;flex-direction:column;gap:6px}
.rfv-title-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.rfv-title{font-size:clamp(20px,2.4vw,28px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.rfv-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:12px;font-weight:700;background:rgba(248,113,113,.12);color:#dc2626}
.rfv-dot{width:6px;height:6px;border-radius:50%;background:#ef4444;flex-shrink:0}
.rfv-subtitle{font-size:12.5px;color:var(--t2)}

.rfv-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.rfv-btn svg{width:14px;height:14px;flex-shrink:0}
.rfv-btn-gray{background:var(--p2);border:1px solid var(--bd2);color:var(--t2)}
.rfv-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.rfv-btn-outline{background:transparent;border:1px solid var(--bd2);color:var(--t2)}
.rfv-btn-outline:hover{border-color:var(--accent);color:var(--accent)}

.rfv-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:900px){.rfv-grid-2{grid-template-columns:1fr}}

.rfv-card{background:var(--p1);border:1px solid var(--bd);border-radius:var(--radius);overflow:hidden;box-shadow:var(--sh)}
.rfv-card-header{padding:14px 20px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:9px}
.rfv-card-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.rfv-card-icon svg{width:16px;height:16px}
.rfv-card-title{font-size:13.5px;font-weight:700;color:var(--t1)}
.rfv-card-body{padding:20px}

.rfv-amount-block{text-align:center;padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd)}
.rfv-amount-label{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--t2);margin-bottom:6px}
.rfv-amount-value{font-size:32px;font-weight:800;letter-spacing:-.02em;color:#dc2626}

.rfv-field-row{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:11px 0;border-bottom:1px solid var(--bd)}
.rfv-field-row:last-child{border-bottom:none}
.rfv-field-label{font-size:11.5px;font-weight:600;color:var(--t2);flex-shrink:0;padding-top:1px}
.rfv-field-value{font-size:13px;font-weight:600;color:var(--t1);text-align:right}
.rfv-field-value.muted{color:var(--t2);font-weight:500;font-style:italic}

.rfv-customer-profile{display:flex;flex-direction:column;align-items:center;text-align:center;padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid var(--bd);gap:10px}
.rfv-avatar{width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid var(--bd2);flex-shrink:0}
.rfv-customer-name{font-size:16px;font-weight:800;color:var(--t1);letter-spacing:-.01em}
.rfv-customer-email{font-size:12.5px;color:var(--t2)}

.rfv-order-link{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 14px;background:var(--p2);border:1px solid var(--bd2);border-radius:10px;text-decoration:none;color:inherit;transition:border-color .15s}
.rfv-order-link:hover{border-color:var(--accent)}
.rfv-order-num{font-size:13px;font-weight:700;color:var(--t1)}
.rfv-order-amt{font-size:11.5px;color:var(--t2);margin-top:2px}
.rfv-order-link svg{width:16px;height:16px;color:var(--t2);flex-shrink:0}

@keyframes rfvUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.rfva{opacity:0;animation:rfvUp .4s cubic-bezier(.16,1,.3,1) forwards}
.rfv1{animation-delay:.05s}.rfv2{animation-delay:.1s}
</style>

{{-- Header --}}
<div class="rfv-header rfva rfv1">
    <div class="rfv-header-left">
        <div class="rfv-title-row">
            <h1 class="rfv-title">Refund #{{ $refund->id }}</h1>
            <span class="rfv-badge">
                <span class="rfv-dot"></span>
                Refunded
            </span>
        </div>
        <p class="rfv-subtitle">Processed on {{ $refund->created_at?->format('M d, Y') }} at {{ $refund->created_at?->format('H:i') }}</p>
    </div>
    <a href="{{ $backUrl }}" wire:navigate class="rfv-btn rfv-btn-gray">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        Back to Refunds
    </a>
</div>

<div class="rfv-grid-2 rfva rfv2">

    {{-- Refund details --}}
    <div class="rfv-card">
        <div class="rfv-card-header">
            <div class="rfv-card-icon" style="background:rgba(220,38,38,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
            </div>
            <span class="rfv-card-title">Refund Details</span>
        </div>
        <div class="rfv-card-body">
            <div class="rfv-amount-block">
                <div class="rfv-amount-label">Amount Refunded</div>
                <div class="rfv-amount-value">{{ number_format((float) $refund->amount, 2) }} {{ $order->currency ?? 'USD' }}</div>
            </div>

            <div class="rfv-field-row">
                <span class="rfv-field-label">Reason</span>
                <span class="rfv-field-value" style="text-align:right;max-width:60%">{{ $refund->reason ?: '—' }}</span>
            </div>
            <div class="rfv-field-row">
                <span class="rfv-field-label">Refunded by</span>
                <span class="rfv-field-value">{{ $refund->refundedBy?->name ?? '—' }}</span>
            </div>
            <div class="rfv-field-row">
                <span class="rfv-field-label">Refunded at</span>
                <span class="rfv-field-value">{{ $refund->created_at?->format('M d, Y H:i') ?? '—' }}</span>
            </div>
            <div class="rfv-field-row">
                <span class="rfv-field-label">Order placed</span>
                <span class="rfv-field-value">{{ $order?->created_at?->format('M d, Y') ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Customer + order --}}
    <div class="rfv-card">
        <div class="rfv-card-header">
            <div class="rfv-card-icon" style="background:rgba(124,58,237,.1)">
                <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </div>
            <span class="rfv-card-title">Customer</span>
        </div>
        <div class="rfv-card-body">
            <div class="rfv-customer-profile">
                <img src="{{ $avatarUrl }}" class="rfv-avatar" alt="">
                <div>
                    <div class="rfv-customer-name">{{ $user?->name ?? $order?->customer_name ?? 'Guest' }}</div>
                    <div class="rfv-customer-email">{{ $user?->email ?? $order?->customer_email ?? '' }}</div>
                </div>
            </div>

            @if($order)
            <a href="{{ $orderUrl }}" wire:navigate class="rfv-order-link">
                <div>
                    <div class="rfv-order-num">Order {{ $order->order_number }}</div>
                    <div class="rfv-order-amt">{{ number_format((float) $order->final_amount, 2) }} {{ $order->currency }} total</div>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </a>
            @endif
        </div>
    </div>

</div>

</div>
