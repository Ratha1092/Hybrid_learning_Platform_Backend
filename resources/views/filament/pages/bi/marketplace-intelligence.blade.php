@include('filament.pages.bi._bi-shared')

<style>
.bi-funnel-wrap {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 8px 0;
}

.bi-funnel-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bi-funnel-label {
    width: 110px;
    font-size: 11.5px;
    color: var(--t2);
    font-weight: 600;
    flex-shrink: 0;
}

.bi-funnel-bar-wrap {
    flex: 1;
    background: var(--p2);
    border-radius: 4px;
    height: 22px;
    overflow: hidden;
    position: relative;
}

.bi-funnel-bar {
    height: 100%;
    border-radius: 4px;
    background: rgba(215,164,65,0.7);
    transition: width 0.4s ease;
    display: flex;
    align-items: center;
    padding-left: 8px;
    min-width: 24px;
}

.bi-funnel-bar span {
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
}

.bi-funnel-count {
    width: 70px;
    text-align: right;
    font-size: 12px;
    font-weight: 700;
    color: var(--t1);
    flex-shrink: 0;
}

.bi-funnel-pct {
    width: 42px;
    text-align: right;
    font-size: 11px;
    color: var(--t2);
    flex-shrink: 0;
}
</style>

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Marketplace Intelligence 🛒</h1>
            <p>Funnel analysis, conversion rates, wishlists, and category performance.</p>
        </div>
        <div class="bi-actions">
            <div class="rp-drp-wrap" wire:key="bi-drp-mkt"
                x-data="rpDate({preset:'{{ $activePreset }}',from:'{{ $activeDateFrom }}',to:'{{ $activeDateTo }}',extras:{}})"
                data-rp-preset="{{ $activePreset }}" data-rp-from="{{ $activeDateFrom }}" data-rp-to="{{ $activeDateTo }}"
                @click.outside="open=false">
                <div class="rp-drp-pill" @click="open=!open">
                    <span class="rp-drp-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    <span class="rp-drp-from" x-text="pFrom"></span><span class="rp-drp-sep">|</span><span class="rp-drp-to" x-text="pTo"></span>
                    <span class="rp-drp-chev" :class="{open}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></span>
                </div>
                <div class="rp-drp-panel" :class="{open}">
                    <div class="rp-drp-presets">
                        @foreach(\App\Support\Concerns\HasDateRangePresets::dateRangePresetOptions() as $key => $label)
                            @if($key !== 'custom')<button type="button" class="rp-drp-pre" :class="{active:preset==='{{ $key }}'}" @click="pick('{{ $key }}')">{{ $label }}</button>@endif
                        @endforeach
                    </div>
                    <hr class="rp-drp-hr">
                    <div class="rp-drp-cus-lbl">Custom Range</div>
                    <div class="rp-drp-cus-row">
                        <input type="date" class="rp-drp-dt" x-model="from" @change="preset='custom'">
                        <span class="rp-drp-dt-sep">—</span>
                        <input type="date" class="rp-drp-dt" x-model="to" @change="preset='custom'">
                    </div>
                    <div class="rp-drp-btns">
                        <button type="button" class="rp-drp-apply" @click="apply()">Apply</button>
                        <button type="button" class="rp-drp-reset" @click="reset()">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bi-kpi-grid">
        <div class="bi-kpi"><div class="bi-kpi-label">GMV</div><div class="bi-kpi-value">${{ number_format($kpis['gmv'],2) }}</div><div class="bi-kpi-sub">Gross Merchandise Value</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Platform Commission</div><div class="bi-kpi-value">${{ number_format($kpis['platformRevenue'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Course Price</div><div class="bi-kpi-value">${{ number_format($kpis['avgPrice'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Conversion Rate</div><div class="bi-kpi-value">{{ $kpis['conversionRate'] }}%</div><div class="bi-kpi-sub">Enrollments / unique viewers</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Wishlist Count</div><div class="bi-kpi-value">{{ number_format($kpis['wishlistCount']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Coupon Usage Rate</div><div class="bi-kpi-value">{{ $kpis['couponUsageRate'] }}%</div><div class="bi-kpi-sub">Orders with coupon</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Refund Rate</div><div class="bi-kpi-value">{{ $kpis['refundRate'] }}%</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Revenue/Course</div><div class="bi-kpi-value">${{ number_format($kpis['avgRevPerCourse'],2) }}</div></div>
    </div>

    <div class="bi-charts-2">
        <div class="bi-card">
            <h3>Conversion Funnel</h3>
            <div class="bi-funnel-wrap">
                @php $funnelMax = max(1, $funnel[0]['value'] ?? 1); @endphp
                @foreach($funnel as $step)
                <div class="bi-funnel-row">
                    <div class="bi-funnel-label">{{ $step['label'] }}</div>
                    <div class="bi-funnel-bar-wrap">
                        <div class="bi-funnel-bar" style="width:{{ max(2, round($step['value'] / $funnelMax * 100)) }}%">
                            <span>{{ number_format($step['value']) }}</span>
                        </div>
                    </div>
                    <div class="bi-funnel-count">{{ number_format($step['value']) }}</div>
                    <div class="bi-funnel-pct">{{ $loop->first ? '100%' : round($step['value'] / $funnelMax * 100).'%' }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bi-card">
            <h3>Category Enrollments</h3>
            <div class="bi-chart-wrap"><canvas id="bi-mkt-cat"></canvas></div>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Most Wishlisted Courses</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Course</th><th>Wishlists</th><th>Price</th></tr></thead>
                <tbody>
                    @forelse($mostWishlisted as $i => $row)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $row->course?->title ?? '—' }}</td>
                        <td>{{ number_format($row->wish_count) }}</td>
                        <td style="color:var(--t2)">${{ number_format((float)($row->course?->price ?? 0),2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No wishlist data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Highest Conversion Rate</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Course</th><th>Views</th><th>Orders</th><th>Conv.</th></tr></thead>
                <tbody>
                    @forelse($highestConversion as $i => $row)
                    <tr>
                        <td><span class="bi-rank">{{ $i+1 }}</span></td>
                        <td>{{ $row['title'] }}</td>
                        <td style="color:var(--t2)">{{ number_format($row['views']) }}</td>
                        <td style="color:var(--t2)">{{ number_format($row['orders']) }}</td>
                        <td><span class="bi-tag bi-tag-ok">{{ $row['rate'] }}%</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--t2);padding:20px">No conversion data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(count($activeCoupons) > 0)
    <div class="bi-table-card">
        <h3>Active Coupons</h3>
        <table class="bi-table">
            <thead><tr><th>Code</th><th>Discount</th><th>Uses</th><th>Revenue</th><th>Expires</th></tr></thead>
            <tbody>
                @foreach($activeCoupons as $coupon)
                <tr>
                    <td style="font-family:monospace;font-size:11px;font-weight:700;color:var(--acc)">{{ $coupon->code }}</td>
                    <td>{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '$'.number_format($coupon->discount_value,2) }}</td>
                    <td style="color:var(--t2)">{{ $coupon->used_count ?? 0 }}</td>
                    <td>${{ number_format((float)($coupon->total_revenue ?? 0),2) }}</td>
                    <td style="color:var(--t2)">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
</div>

<script>
window.__biRun(function() {
    var tickColor = '#64748b';
    var gridColor = 'rgba(148,163,184,0.12)';
    var ff = "'DM Sans', ui-sans-serif, system-ui, sans-serif";
    var colors = ['#D7A441','#2563eb','#22c55e','#ef4444','#a855f7','#f97316','#06b6d4','#ec4899'];
    var baseScales = {
        x: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor }, beginAtZero: true }
    };

    // Category enrollments bar
    (function() {
        var el = document.getElementById('bi-mkt-cat');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        var d = @json($categoryEnrollments);
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: d.map(function(r){ return r.name; }),
                datasets: [{ label: 'Enrollments', data: d.map(function(r){ return r.count; }), backgroundColor: colors, borderWidth: 0, borderRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: baseScales }
        });
    })();
});
</script>
