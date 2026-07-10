@php
$biColors = ['#D7A441','#2563eb','#22c55e','#ef4444','#a855f7','#f97316','#06b6d4','#ec4899'];
@endphp

@include('filament.pages.bi._bi-shared')

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Revenue Intelligence 💰</h1>
            <p>Revenue breakdown, growth trends, and top performers.</p>
        </div>
        <div class="bi-actions">
            <div class="rp-drp-wrap" wire:key="bi-drp-rev"
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
        <div class="bi-kpi">
            <div class="bi-kpi-label">Gross Revenue</div>
            <div class="bi-kpi-value">${{ number_format($kpis['grossRevenue'],2) }}</div>
            <div class="bi-kpi-grow {{ $kpis['revenueGrowth'] >= 0 ? 'up' : 'dn' }}">{{ $kpis['revenueGrowth'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['revenueGrowth']) }}% vs prev period</div>
        </div>
        <div class="bi-kpi"><div class="bi-kpi-label">Net Revenue</div><div class="bi-kpi-value">${{ number_format($kpis['netRevenue'],2) }}</div><div class="bi-kpi-sub">After ${{ number_format($kpis['refundAmount'],2) }} refunds</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Platform Revenue</div><div class="bi-kpi-value">${{ number_format($kpis['platformRevenue'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Instructor Revenue</div><div class="bi-kpi-value">${{ number_format($kpis['instructorRevenue'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Discounts Given</div><div class="bi-kpi-value">${{ number_format($kpis['discounts'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Pending Payout</div><div class="bi-kpi-value">${{ number_format($kpis['pendingRevenue'],2) }}</div><div class="bi-kpi-sub">Wallet pending balance</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Orders</div><div class="bi-kpi-value">{{ number_format($kpis['orderCount']) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Avg Order Value</div><div class="bi-kpi-value">${{ number_format($kpis['aov'],2) }}</div></div>
    </div>

    <div class="bi-charts-3">
        <div class="bi-card">
            <h3>Revenue Trend</h3>
            <div class="bi-chart-wrap"><canvas id="bi-rev-trend"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Revenue by Category</h3>
            <div class="bi-chart-wrap"><canvas id="bi-rev-cat"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Revenue by Gateway</h3>
            <div class="bi-chart-wrap"><canvas id="bi-rev-gw"></canvas></div>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Top Courses by Revenue</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Course</th><th>Revenue</th><th>Sales</th></tr></thead>
                <tbody>
                    @forelse($topCourses as $i => $row)
                    <tr>
                        <td style="color:var(--t2);font-size:11px">{{ $i+1 }}</td>
                        <td>{{ $row->course?->title ?? 'Unknown' }}</td>
                        <td>${{ number_format((float)$row->revenue,2) }}</td>
                        <td>{{ $row->sales }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No sales in this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Top Instructors by Revenue</h3>
            <table class="bi-table">
                <thead><tr><th>#</th><th>Instructor</th><th>Revenue</th><th>Sales</th></tr></thead>
                <tbody>
                    @forelse($topInstructors as $i => $row)
                    <tr>
                        <td style="color:var(--t2);font-size:11px">{{ $i+1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td>${{ number_format($row['revenue'],2) }}</td>
                        <td>{{ $row['sales'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No data for this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
window.__biRun(function() {
    var tickColor = '#64748b';
    var gridColor = 'rgba(148,163,184,0.12)';
    var ff = "'DM Sans', ui-sans-serif, system-ui, sans-serif";
    var isDark = document.documentElement.classList.contains('cl-dark') || document.documentElement.classList.contains('dark');
    var baseScales = {
        x:{ticks:{color:tickColor,font:{family:ff,size:10}},grid:{color:gridColor}},
        y:{ticks:{color:tickColor,font:{family:ff,size:10}},grid:{color:gridColor},beginAtZero:true}
    };

    // Revenue trend
    (function(){
        var el = document.getElementById('bi-rev-trend');
        if (!el || !window.Chart) return;
        if (el._ch) {el._ch.destroy(); el._ch=null;}
        el._ch = new window.Chart(el, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{label:'Revenue ($)',data:@json($trendValues),borderColor:'#D7A441',backgroundColor:'rgba(215,164,65,0.08)',fill:true,tension:0.4,pointRadius:2,borderWidth:2}]
            },
            options:{responsive:true,maintainAspectRatio:false,animation:{duration:400},plugins:{legend:{display:false},tooltip:{backgroundColor:isDark?'#1e293b':'#fff',titleColor:isDark?'#e2e8f0':'#0f172a',bodyColor:'#64748b',borderColor:'rgba(148,163,184,0.2)',borderWidth:1}},scales:baseScales}
        });
    })();

    // Category donut
    (function(){
        var el = document.getElementById('bi-rev-cat');
        if (!el || !window.Chart) return;
        if (el._ch) {el._ch.destroy(); el._ch=null;}
        var colors = @json($biColors);
        el._ch = new window.Chart(el, {
            type: 'doughnut',
            data: {
                labels: @json($categoryLabels),
                datasets: [{data:@json($categoryValues),backgroundColor:colors,borderWidth:1,borderColor:'rgba(0,0,0,0.1)'}]
            },
            options:{responsive:true,maintainAspectRatio:false,animation:{duration:400},plugins:{legend:{position:'right',labels:{color:tickColor,font:{family:ff,size:10},boxWidth:10}}}}
        });
    })();

    // Gateway bar
    (function(){
        var el = document.getElementById('bi-rev-gw');
        if (!el || !window.Chart) return;
        if (el._ch) {el._ch.destroy(); el._ch=null;}
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($gatewayLabels),
                datasets: [{label:'Revenue ($)',data:@json($gatewayValues),backgroundColor:'rgba(37,99,235,0.7)',borderColor:'#2563eb',borderWidth:1,borderRadius:4}]
            },
            options:{responsive:true,maintainAspectRatio:false,animation:{duration:400},plugins:{legend:{display:false}},scales:baseScales}
        });
    })();
});
</script>
