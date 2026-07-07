@include('filament.pages.bi._bi-shared')

<div>
<div class="bi">

    <div class="bi-header">
        <div>
            <h1>Financial Intelligence 💳</h1>
            <p>Cash flow, payouts, profit, and wallet balances.</p>
        </div>
        <div class="bi-actions">
            <div class="rp-drp-wrap" wire:key="bi-drp-fin"
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

    <div class="bi-kpi-grid cols-3">
        <div class="bi-kpi"><div class="bi-kpi-label">Gross Revenue</div><div class="bi-kpi-value">${{ number_format($kpis['grossRevenue'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Platform Profit</div><div class="bi-kpi-value">${{ number_format($kpis['platformProfit'],2) }}</div><div class="bi-kpi-sub">{{ $kpis['operatingMargin'] }}% margin</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Instructor Earnings</div><div class="bi-kpi-value">${{ number_format($kpis['instructorEarnings'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Pending Payout</div><div class="bi-kpi-value">${{ number_format($kpis['pendingPayout'],2) }}</div><div class="bi-kpi-sub">Wallet pending balance</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Completed Payout</div><div class="bi-kpi-value">${{ number_format($kpis['completedPayout'],2) }}</div><div class="bi-kpi-sub">Period approved payouts</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Total Wallet Balance</div><div class="bi-kpi-value">${{ number_format($kpis['totalWalletBalance'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Tax Collected</div><div class="bi-kpi-value">${{ number_format($kpis['taxCollected'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Refund Amount</div><div class="bi-kpi-value">${{ number_format($kpis['refundAmount'],2) }}</div></div>
        <div class="bi-kpi"><div class="bi-kpi-label">Cash Flow</div><div class="bi-kpi-value @if($kpis['cashFlow'] < 0) style="color:var(--err)" @endif">${{ number_format($kpis['cashFlow'],2) }}</div><div class="bi-kpi-sub">Revenue - payouts - refunds</div></div>
    </div>

    <div class="bi-charts-2">
        <div class="bi-card">
            <h3>Cash Flow — 12 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-fin-cashflow"></canvas></div>
        </div>
        <div class="bi-card">
            <h3>Payout Trend — 6 Months</h3>
            <div class="bi-chart-wrap"><canvas id="bi-fin-payouts"></canvas></div>
        </div>
    </div>

    <div class="bi-grid-2">
        <div class="bi-table-card">
            <h3>Pending Payouts</h3>
            <table class="bi-table">
                <thead><tr><th>Instructor</th><th>Amount</th><th>Method</th><th>Requested</th></tr></thead>
                <tbody>
                    @forelse($pendingPayouts as $payout)
                    <tr>
                        <td>{{ $payout->instructor?->name ?? '—' }}</td>
                        <td>${{ number_format((float)$payout->amount,2) }}</td>
                        <td style="color:var(--t2)">{{ $payout->payment_method ?? '—' }}</td>
                        <td style="color:var(--t2)">{{ $payout->created_at?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--t2);padding:20px">No pending payouts</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bi-table-card">
            <h3>Outstanding Wallet Balances</h3>
            <table class="bi-table">
                <thead><tr><th>Instructor</th><th>Balance</th><th>Pending</th></tr></thead>
                <tbody>
                    @forelse($outstandingBalances as $wallet)
                    <tr>
                        <td>{{ $wallet->instructor?->name ?? '—' }}</td>
                        <td>${{ number_format((float)$wallet->balance,2) }}</td>
                        <td style="color:var(--t2)">${{ number_format((float)$wallet->pending_balance,2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--t2);padding:20px">No wallet balances</td></tr>
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
        x: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor, font: { family: ff, size: 10 } }, grid: { color: gridColor }, beginAtZero: true }
    };
    var tooltipOpts = {
        backgroundColor: isDark ? '#1e293b' : '#fff',
        titleColor: isDark ? '#e2e8f0' : '#0f172a',
        bodyColor: '#64748b',
        borderColor: 'rgba(148,163,184,0.2)',
        borderWidth: 1
    };

    // Cash flow stacked bar
    (function() {
        var el = document.getElementById('bi-fin-cashflow');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($cfLabels),
                datasets: [
                    { label: 'Revenue', data: @json($cfRevenue), backgroundColor: 'rgba(34,197,94,0.7)', borderColor: '#22c55e', borderWidth: 1, borderRadius: 3, stack: 'a' },
                    { label: 'Payouts', data: @json($cfPayouts), backgroundColor: 'rgba(215,164,65,0.7)', borderColor: '#D7A441', borderWidth: 1, borderRadius: 3, stack: 'b' },
                    { label: 'Refunds', data: @json($cfRefunds), backgroundColor: 'rgba(239,68,68,0.7)', borderColor: '#ef4444', borderWidth: 1, borderRadius: 3, stack: 'b' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                plugins: {
                    legend: { labels: { color: tickColor, font: { family: ff, size: 10 }, boxWidth: 10 } },
                    tooltip: tooltipOpts
                },
                scales: baseScales
            }
        });
    })();

    // Payout trend bar
    (function() {
        var el = document.getElementById('bi-fin-payouts');
        if (!el || !window.Chart) return;
        if (el._ch) { el._ch.destroy(); el._ch = null; }
        el._ch = new window.Chart(el, {
            type: 'bar',
            data: {
                labels: @json($ptLabels),
                datasets: [{ label: 'Payouts ($)', data: @json($ptValues), backgroundColor: 'rgba(215,164,65,0.7)', borderColor: '#D7A441', borderWidth: 1, borderRadius: 4 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                plugins: { legend: { display: false }, tooltip: tooltipOpts },
                scales: baseScales
            }
        });
    })();
});
</script>
