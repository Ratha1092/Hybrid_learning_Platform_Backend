@php
    /** @var \App\Domains\Promotions\Models\Coupon $record */
    $coupon = $record;

    $backUrl = route('filament.admin.pages.coupons');
    $editUrl = route('filament.admin.resources.coupons.edit', ['record' => $coupon->id]);

    $canUpdate = auth()->user()?->can('coupons.update') ?? false;

    $status = match (true) {
        !$coupon->is_active => ['label' => 'Disabled', 'bg' => 'rgba(148,163,184,.12)', 'color' => '#64748b'],
        $coupon->isExpired() => ['label' => 'Expired', 'bg' => 'rgba(248,113,113,.12)', 'color' => '#dc2626'],
        !$coupon->hasStarted() => ['label' => 'Scheduled', 'bg' => 'rgba(96,165,250,.12)', 'color' => '#3b82f6'],
        !$coupon->hasUsesLeft() => ['label' => 'Fully Redeemed', 'bg' => 'rgba(251,191,36,.12)', 'color' => '#d97706'],
        default => ['label' => 'Active', 'bg' => 'rgba(34,197,94,.12)', 'color' => '#16a34a'],
    };

    $valueLabel = $coupon->type === 'percentage'
        ? number_format((float) $coupon->value, 0) . '% off'
        : '$' . number_format((float) $coupon->value, 2) . ' off';

    $usagePct = $coupon->max_uses ? min(100, round(($coupon->used_count / $coupon->max_uses) * 100)) : null;

    $orders = $coupon->orders()->with('user')->latest()->limit(10)->get();
    $ordersCount = $coupon->orders()->count();
    $totalDiscountGiven = (float) $coupon->orders()->sum('discount_amount');
@endphp

<div class="cv">

<style>
.cv,.cv *,.cv *::before,.cv *::after{box-sizing:border-box;margin:0;padding:0}
.cv{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:48px;display:grid;gap:20px;
    --bg:#0f172a;--p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .cv{
    --bg:#f1f5f9;--p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes cvUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.cva{opacity:0;animation:cvUp .45s cubic-bezier(.16,1,.3,1) forwards}
.cv1{animation-delay:.04s}.cv2{animation-delay:.09s}.cv3{animation-delay:.14s}

.cv-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.cv-header-text h1{font-size:clamp(20px,2.2vw,26px);font-weight:780;letter-spacing:-.018em;color:var(--t1);line-height:1.15}
.cv-header-text p{font-size:12px;color:var(--t2);margin-top:5px}
.cv-header-btns{display:flex;align-items:center;gap:10px;flex-shrink:0}
.cv-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:.02em;cursor:pointer;text-decoration:none;transition:opacity .18s,transform .15s;white-space:nowrap;border:none;font-family:inherit}
.cv-btn:hover{opacity:.85;transform:translateY(-1px)}
.cv-btn-primary{background:#16a34a;color:#fff}
.cv-btn-gray{background:var(--p2);color:var(--t1);border:1px solid var(--bd2)}

.cv-card{background:var(--p1);border:1px solid var(--bd);border-radius:12px;box-shadow:var(--sh);padding:20px}

.cv-hero{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.cv-hero-icon{width:56px;height:56px;border-radius:14px;display:grid;place-items:center;flex-shrink:0;background:rgba(99,102,241,.12);color:#6366f1}
.cv-hero-icon svg{width:26px;height:26px}
.cv-hero-code{font-size:20px;font-weight:800;letter-spacing:.03em;color:var(--t1);font-family:ui-monospace,monospace;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.cv-badge{font-size:10.5px;font-weight:780;padding:3px 9px;border-radius:6px}
.cv-hero-sub{font-size:13px;color:var(--t2);margin-top:4px}
.cv-stats{display:flex;align-items:center;gap:24px;margin-left:auto;flex-wrap:wrap}
.cv-stat{text-align:right}
.cv-stat-val{font-size:18px;font-weight:780;color:var(--t1)}
.cv-stat-label{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);margin-top:2px}
.cv-stat-divider{width:1px;height:32px;background:var(--bd)}

.cv-card-title{font-size:14px;font-weight:750;color:var(--t1)}
.cv-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}

.cv-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media (max-width:860px){.cv-grid-2{grid-template-columns:1fr}}

.cv-detail-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--bd)}
.cv-detail-row:last-child{border-bottom:none}
.cv-detail-label{font-size:12px;color:var(--t2)}
.cv-detail-value{font-size:12.5px;font-weight:650;color:var(--t1)}

.cv-progress-wrap{margin-top:16px}
.cv-progress-label{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--t2);margin-bottom:6px}
.cv-progress-bar{height:7px;background:var(--bd);border-radius:4px;overflow:hidden}
.cv-progress-fill{height:100%;background:linear-gradient(90deg,#6366f1,#8b5cf6);border-radius:4px}

.cv-orders{display:grid;gap:8px;margin-top:14px}
.cv-order-row{display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;text-decoration:none;color:inherit;transition:background .12s}
.cv-order-row:hover{background:var(--p2)}
.cv-order-avatar{width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0}
.cv-order-name{font-size:12.5px;font-weight:650;color:var(--t1)}
.cv-order-meta{font-size:11.5px;color:var(--t2)}
.cv-order-amount{margin-left:auto;font-size:12.5px;font-weight:700;color:#16a34a}
.cv-orders-empty{font-size:12.5px;color:var(--t2);padding:8px 0}
.cv-orders-more{font-size:11.5px;color:var(--t2);padding-top:4px}
</style>

{{-- Header --}}
<div class="cv-header cva cv1">
    <div class="cv-header-text">
        <h1>View coupon</h1>
        <p>Discount rules, usage limits, and redemption history.</p>
    </div>
    <div class="cv-header-btns">
        <a href="{{ $backUrl }}" class="cv-btn cv-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to Coupons
        </a>
        @if($canUpdate)
        <a href="{{ $editUrl }}" class="cv-btn cv-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
            Edit Coupon
        </a>
        @endif
    </div>
</div>

{{-- Hero --}}
<div class="cv-card cva cv2">
    <div class="cv-hero">
        <div class="cv-hero-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
        </div>
        <div>
            <div class="cv-hero-code">
                {{ $coupon->code }}
                <span class="cv-badge" style="background:{{ $status['bg'] }};color:{{ $status['color'] }}">{{ $status['label'] }}</span>
            </div>
            <div class="cv-hero-sub">{{ $valueLabel }}@if($coupon->min_order_amount) &middot; min order ${{ number_format((float) $coupon->min_order_amount, 2) }}@endif</div>
        </div>
        <div class="cv-stats">
            <div class="cv-stat">
                <div class="cv-stat-val">{{ number_format($coupon->used_count) }}{{ $coupon->max_uses ? ' / ' . number_format($coupon->max_uses) : '' }}</div>
                <div class="cv-stat-label">Redemptions</div>
            </div>
            <div class="cv-stat-divider"></div>
            <div class="cv-stat">
                <div class="cv-stat-val">${{ number_format($totalDiscountGiven, 2) }}</div>
                <div class="cv-stat-label">Total Discount</div>
            </div>
            <div class="cv-stat-divider"></div>
            <div class="cv-stat">
                <div class="cv-stat-val">{{ $coupon->created_at?->format('M d, Y') ?? '—' }}</div>
                <div class="cv-stat-label">Created</div>
            </div>
        </div>
    </div>

    @if($coupon->max_uses)
    <div class="cv-progress-wrap">
        <div class="cv-progress-label">
            <span>Redemptions used</span>
            <span style="font-weight:700;color:var(--t1)">{{ $usagePct }}%</span>
        </div>
        <div class="cv-progress-bar">
            <div class="cv-progress-fill" style="width:{{ $usagePct }}%"></div>
        </div>
    </div>
    @endif
</div>

<div class="cv-grid-2">
    {{-- Details --}}
    <div class="cv-card cva cv3">
        <div class="cv-card-title">Details</div>
        <div class="cv-card-sub">Configuration for this coupon.</div>
        <div style="margin-top:10px">
            <div class="cv-detail-row">
                <span class="cv-detail-label">Type</span>
                <span class="cv-detail-value">{{ \Illuminate\Support\Str::headline($coupon->type) }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Value</span>
                <span class="cv-detail-value">{{ $valueLabel }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Minimum order</span>
                <span class="cv-detail-value">{{ $coupon->min_order_amount ? '$' . number_format((float) $coupon->min_order_amount, 2) : 'None' }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Uses per customer</span>
                <span class="cv-detail-value">{{ number_format($coupon->max_uses_per_user) }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Total redemption limit</span>
                <span class="cv-detail-value">{{ $coupon->max_uses ? number_format($coupon->max_uses) : 'Unlimited' }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Starts</span>
                <span class="cv-detail-value">{{ $coupon->starts_at?->format('M d, Y H:i') ?? 'Immediately' }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Expires</span>
                <span class="cv-detail-value">{{ $coupon->expires_at?->format('M d, Y H:i') ?? 'Never' }}</span>
            </div>
            <div class="cv-detail-row">
                <span class="cv-detail-label">Created by</span>
                <span class="cv-detail-value">{{ $coupon->creator?->name ?? '—' }}</span>
            </div>
            @if($coupon->description)
            <div class="cv-detail-row" style="display:block">
                <span class="cv-detail-label">Description</span>
                <p class="cv-detail-value" style="margin-top:4px;font-weight:500">{{ $coupon->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="cv-card cva cv3">
        <div class="cv-card-title">Recent redemptions</div>
        <div class="cv-card-sub">{{ number_format($ordersCount) }} {{ Str::plural('order', $ordersCount) }} used this coupon.</div>
        <div class="cv-orders">
            @forelse ($orders as $order)
                @php
                    $orderUser = $order->user;
                    $bgHex = substr(md5($orderUser->name ?? ''), 0, 6);
                    $avUrl = $orderUser?->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($orderUser->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64');
                @endphp
                <a href="{{ route('filament.admin.resources.orders.view', ['record' => $order->id]) }}" class="cv-order-row">
                    <img src="{{ $avUrl }}" class="cv-order-avatar" alt="">
                    <div>
                        <div class="cv-order-name">{{ $orderUser->name ?? 'Unknown' }}</div>
                        <div class="cv-order-meta">{{ $order->order_number }} &middot; {{ $order->created_at?->format('M d, Y') }}</div>
                    </div>
                    <div class="cv-order-amount">-${{ number_format((float) $order->discount_amount, 2) }}</div>
                </a>
            @empty
                <div class="cv-orders-empty">No orders have used this coupon yet.</div>
            @endforelse
            @if($ordersCount > $orders->count())
                <div class="cv-orders-more">+{{ number_format($ordersCount - $orders->count()) }} more</div>
            @endif
        </div>
    </div>
</div>

</div>
