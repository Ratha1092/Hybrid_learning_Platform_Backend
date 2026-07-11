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
        @include('filament.pages.bi._bi-actions', ['biKey' => 'rev'])
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
                datasets: [{label:'Revenue ($)',data:@json($trendValues),borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,0.08)',fill:true,tension:0.4,pointRadius:2,borderWidth:2}]
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
