@php
    $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $presets = \App\Filament\Pages\Reports\RevenueReport::dateRangePresetOptions();
@endphp

<style>
.rp, .rp *, .rp *::before, .rp *::after { box-sizing:border-box; margin:0; padding:0; }
.rp {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px; line-height:1.5; padding-bottom:48px; display:grid; gap:20px;
    --bg:#0f172a; --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b; --t3:#334155; --accent:#D7A441;
    color:var(--t1);
}
html:not(.dark) .rp {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0f172a; --t2:#475569; --t3:#cbd5e1;
}
.rp-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:16px; border-bottom:1px solid var(--bd); }
.rp-header h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); }
.rp-header p { font-size:12px; color:var(--t2); margin-top:5px; }
.rp-actions { display:flex; gap:8px; flex-wrap:wrap; }
.rp-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; text-decoration:none; border:1px solid var(--bd2); background:var(--p1); color:var(--t1); }
.rp-btn:hover { background:var(--p2); }
.rp-btn-primary { background:var(--accent); color:#fff; border-color:transparent; }
.rp-btn-primary:hover { background:#c4923a !important; color:#fff !important; }
.rp-filters { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.rp-filter-select { background:var(--p1); border:1px solid var(--bd2); border-radius:8px; padding:7px 10px; font-size:12px; color:var(--t1); }
.rp-kpi-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:12px; }
.rp-kpi-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; padding:16px; }
.rp-kpi-label { font-size:10.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--t2); margin-bottom:6px; }
.rp-kpi-value { font-size:22px; font-weight:800; color:var(--t1); }
.rp-grid-2 { display:grid; grid-template-columns:1.5fr 1fr; gap:16px; }
@media (max-width: 900px) { .rp-grid-2 { grid-template-columns:1fr; } }
.rp-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; padding:16px; }
.rp-card h3 { font-size:13px; font-weight:700; margin-bottom:12px; color:var(--t1); }
.rp-trend { display:flex; align-items:flex-end; gap:8px; height:120px; }
.rp-trend-bar-wrap { flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end; }
.rp-trend-bar { width:100%; background:#D7A441; border-radius:4px 4px 0 0; min-height:2px; }
.rp-trend-label { font-size:10px; color:var(--t2); }
.rp-coupon-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--bd); font-size:12px; }
.rp-coupon-row:last-child { border-bottom:none; }
.rp-table-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; }
.rp-table { width:100%; border-collapse:collapse; }
.rp-table thead tr { border-bottom:1px solid var(--bd); }
.rp-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.rp-table td { padding:10px 12px; border-bottom:1px solid var(--bd); font-size:12.5px; color:var(--t1); }
.rp-pagination { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; font-size:12px; color:var(--t2); }
</style>

<div>
<div class="rp">
    <div class="rp-header">
        <div>
            <h1>Revenue Report</h1>
            <p>Gross revenue, platform commission, and instructor payouts by order.</p>
        </div>
        <div class="rp-actions">
            <a href="{{ route('admin.reports.csv', ['type' => 'revenue'] + request()->query()) }}" class="rp-btn">Export CSV</a>
            <a href="{{ route('admin.reports.pdf', ['type' => 'revenue'] + request()->query()) }}" class="rp-btn">Export PDF</a>
            @if(\App\Support\PanelAccess::can('reports.schedule'))
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-schedule-modal'))" class="rp-btn rp-btn-primary">Schedule</button>
            @endif
        </div>
    </div>

    <form method="GET" class="rp-filters">
        <select name="preset" class="rp-filter-select" onchange="this.form.submit()">
            @foreach($presets as $key => $label)
                <option value="{{ $key }}" @selected($activePreset === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="rp-kpi-grid">
        <div class="rp-kpi-card"><div class="rp-kpi-label">Total Revenue</div><div class="rp-kpi-value">${{ number_format($kpis['totalRevenue'], 2) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Discounts Given</div><div class="rp-kpi-value">${{ number_format($kpis['totalDiscounts'], 2) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Platform Revenue</div><div class="rp-kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Instructor Payable</div><div class="rp-kpi-value">${{ number_format($kpis['instructorPayable'], 2) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Orders</div><div class="rp-kpi-value">{{ number_format($kpis['orderCount']) }}</div></div>
        <div class="rp-kpi-card"><div class="rp-kpi-label">Avg. Order Value</div><div class="rp-kpi-value">${{ number_format($kpis['averageOrderValue'], 2) }}</div></div>
    </div>

    <div class="rp-grid-2">
        <div class="rp-card">
            <h3>Revenue Trend (6 Months)</h3>
            @php $trendMax = max(1, $revenueTrend->max('revenue')); @endphp
            <div class="rp-trend">
                @foreach($revenueTrend as $point)
                    <div class="rp-trend-bar-wrap">
                        <div class="rp-trend-bar" style="height:{{ $point['revenue'] > 0 ? max(3, ($point['revenue']/$trendMax)*100) : 0 }}%" title="${{ number_format($point['revenue'], 2) }}"></div>
                        <div class="rp-trend-label">{{ $point['month'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rp-card">
            <h3>Top Coupons</h3>
            @forelse($topCoupons as $coupon)
                <div class="rp-coupon-row">
                    <span>{{ $coupon->code }}</span>
                    <span style="color:var(--t2)">{{ $coupon->used_count }} uses</span>
                </div>
            @empty
                <p style="color:var(--t2);font-size:12px;">No coupon usage yet.</p>
            @endforelse
        </div>
    </div>

    <div class="rp-table-card">
        <table class="rp-table">
            <thead>
                <tr><th>Order #</th><th>Date</th><th>Customer</th><th>Items</th><th>Discount</th><th>Final Amount</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $order->customer_name ?? $order->user?->name ?? '—' }}</td>
                        <td>{{ $order->items->count() }}</td>
                        <td>{{ $order->discount_amount > 0 ? '-$' . number_format((float) $order->discount_amount, 2) : '—' }}</td>
                        <td>${{ number_format((float) $order->final_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--t2);padding:24px;">No paid orders found for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="rp-pagination">
            <span>Showing {{ $total > 0 ? (($curPage-1)*$perPage)+1 : 0 }}–{{ min($curPage*$perPage, $total) }} of {{ number_format($total) }}</span>
            <div style="display:flex;gap:6px;">
                @if($curPage > 1)<a href="{{ $url(['page' => $curPage-1]) }}" class="rp-btn">Prev</a>@endif
                @if($curPage < $totalPages)<a href="{{ $url(['page' => $curPage+1]) }}" class="rp-btn">Next</a>@endif
            </div>
        </div>
    </div>
</div>

@if(\App\Support\PanelAccess::can('reports.schedule'))
    @include('filament.pages.partials._schedule-modal', ['reportLabel' => 'Revenue'])
@endif

</div>
