@extends('admin.layout')

@section('title','Admin Dashboard — HybridLearn')
@section('page-heading','Overview')

@section('content')
    {{-- ALL-TIME STATS --}}
    <p class="section-label">All-time totals</p>
    <div class="stat-grid">
        <div class="stat-card c-orange">
            <div class="stat-icon c-orange">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">${{ number_format($summary['totalRevenue'], 2) }}</div>
            <span class="stat-badge {{ $trends['revenueTrend'] >= 0 ? 'up' : 'down' }}">
                {{ $trends['revenueTrend'] >= 0 ? '↑' : '↓' }} {{ abs($trends['revenueTrend']) }}% vs last month
            </span>
        </div>
        <div class="stat-card c-blue">
            <div class="stat-icon c-blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ number_format($summary['totalOrders']) }}</div>
            <span class="stat-badge {{ $trends['ordersTrend'] >= 0 ? 'up' : 'down' }}">
                {{ $trends['ordersTrend'] >= 0 ? '↑' : '↓' }} {{ abs($trends['ordersTrend']) }}% vs last month
            </span>
        </div>
        <div class="stat-card c-green">
            <div class="stat-icon c-green">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ number_format($summary['totalUsers']) }}</div>
            <span class="stat-badge {{ $trends['usersTrend'] >= 0 ? 'up' : 'down' }}">
                {{ $trends['usersTrend'] >= 0 ? '↑' : '↓' }} {{ abs($trends['usersTrend']) }}% vs last month
            </span>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-icon c-purple">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div class="stat-label">Total Enrollments</div>
            <div class="stat-value">{{ number_format($summary['totalEnrollments']) }}</div>
            <span class="stat-badge {{ $trends['enrollTrend'] >= 0 ? 'up' : 'down' }}">
                {{ $trends['enrollTrend'] >= 0 ? '↑' : '↓' }} {{ abs($trends['enrollTrend']) }}% vs last month
            </span>
        </div>
    </div>

    <p class="section-label">Today at a glance</p>
    <div class="today-grid">
        <div class="today-card">
            <div class="today-icon" style="background:#fde8e0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <div style="font-size:.74rem;color:var(--muted);font-weight:500">Revenue</div>
                <div style="font-size:1.15rem;font-weight:700;margin-top:2px">${{ number_format($todayData['revenue'], 2) }}</div>
            </div>
        </div>
        <div class="today-card">
            <div class="today-icon" style="background:#eff6ff">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div>
                <div style="font-size:.74rem;color:var(--muted);font-weight:500">Orders</div>
                <div style="font-size:1.15rem;font-weight:700;margin-top:2px">{{ number_format($todayData['orders']) }}</div>
            </div>
        </div>
        <div class="today-card">
            <div class="today-icon" style="background:#f5f3ff">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div style="font-size:.74rem;color:var(--muted);font-weight:500">Enrollments</div>
                <div style="font-size:1.15rem;font-weight:700;margin-top:2px">{{ number_format($todayData['enrollments']) }}</div>
            </div>
        </div>
        <div class="today-card">
            <div class="today-icon" style="background:#ecfdf5">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <div>
                <div style="font-size:.74rem;color:var(--muted);font-weight:500">New Students</div>
                <div style="font-size:1.15rem;font-weight:700;margin-top:2px">{{ number_format($todayData['new_users']) }}</div>
            </div>
        </div>
    </div>

    <div class="chart-row">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Revenue Over Time</div>
                    <div style="font-size:.75rem;color:var(--muted);margin-top:2px">From <code style="font-size:.7rem">daily_metrics</code> or <code style="font-size:.7rem">orders.paid_at</code></div>
                </div>
                <div class="period-tabs">
                    <div class="period-tab active" onclick="setPeriod(this)">7D</div>
                    <div class="period-tab" onclick="setPeriod(this)">30D</div>
                    <div class="period-tab" onclick="setPeriod(this)">90D</div>
                </div>
            </div>
            <div style="position:relative;height:230px">
                <canvas id="revenueChart" role="img" aria-label="Revenue and orders over time"></canvas>
            </div>
            <div style="display:flex;gap:16px;margin-top:12px;font-size:.74rem;color:var(--muted)">
                <span style="display:flex;align-items:center;gap:5px"><span style="width:10px;height:3px;background:var(--accent);display:inline-block;border-radius:99px"></span> Revenue ($)</span>
                <span style="display:flex;align-items:center;gap:5px"><span style="width:10px;height:0;display:inline-block;border-top:2px dashed #3b82f6"></span> Orders</span>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Top Courses</div>
                <a href="{{ route('admin.courses') }}" style="font-size:.75rem;color:var(--accent);font-weight:600">View all →</a>
            </div>
            @php $dotColors=['#e8512a','#3b82f6','#8b5cf6','#10b981','#f59e0b']; @endphp
            <table class="data-table">
                <thead><tr><th>Course</th><th>Level</th><th style="text-align:right">Students</th></tr></thead>
                <tbody>
                    @forelse($topCourses as $i => $course)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="course-dot" style="background:{{ $dotColors[$i%5] }}"></span>
                                <div>
                                    <div style="font-weight:500;line-height:1.3">{{ Str::limit($course->title,28) }}</div>
                                    @if($course->reviews_avg_rating)
                                    <div style="font-size:.7rem;color:var(--muted)">★ {{ number_format($course->reviews_avg_rating,1) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="pill {{ $course->level }}">{{ ucfirst($course->level) }}</span></td>
                        <td style="text-align:right;font-weight:600">{{ number_format($course->enrollments_count) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="empty-state">No published courses yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mid-row">
        <div class="panel">
            <div class="panel-header"><div class="panel-title">Enrollments by Level</div></div>
            @php
                $levels=['beginner','intermediate','advanced'];
                $lColors=['#10b981','#f59e0b','#ef4444'];
                $lvTotal=$enrollmentByLevel->sum();
            @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:center">
                <div style="position:relative;height:150px">
                    <canvas id="donutChart" role="img" aria-label="Enrollment by course level"></canvas>
                </div>
                <div style="display:flex;flex-direction:column;gap:9px;font-size:.8rem">
                    @foreach($levels as $k => $lv)
                    @php $cnt=$enrollmentByLevel->get($lv,0); $pct=$lvTotal>0?round($cnt/$lvTotal*100):0; @endphp
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <span style="display:flex;align-items:center;gap:7px"><span style="width:10px;height:10px;border-radius:2px;background:{{ $lColors[$k] }};display:inline-block"></span>{{ ucfirst($lv) }}</span>
                        <span style="font-weight:600">{{ $pct }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Revenue Split</div>
                <span style="font-size:.75rem;color:var(--muted)">All-time</span>
            </div>
            @php
                $gross      = $revenueBreakdown->gross_total ?? 0;
                $platform   = $revenueBreakdown->platform_total ?? 0;
                $instructor = $revenueBreakdown->instructor_total ?? 0;
                $platPct    = $gross > 0 ? round($platform/$gross*100) : 0;
                $instPct    = $gross > 0 ? round($instructor/$gross*100) : 0;
            @endphp
            <div style="margin-bottom:16px">
                <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--muted);margin-bottom:6px">
                    <span>Platform ({{ $platPct }}%)</span>
                    <span>Instructors ({{ $instPct }}%)</span>
                </div>
                <div class="split-bar">
                    <div class="split-fill" style="width:{{ $platPct }}%;background:var(--accent)"></div>
                    <div class="split-fill" style="width:{{ $instPct }}%;background:#3b82f6"></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <div style="background:#fde8e0;border-radius:10px;padding:12px">
                    <div style="font-size:.7rem;color:var(--accent);font-weight:600;margin-bottom:4px">Platform Earned</div>
                    <div style="font-size:1.05rem;font-weight:700">${{ number_format($platform,2) }}</div>
                </div>
                <div style="background:#eff6ff;border-radius:10px;padding:12px">
                    <div style="font-size:.7rem;color:#2563eb;font-weight:600;margin-bottom:4px">Instructors Earned</div>
                    <div style="font-size:1.05rem;font-weight:700">${{ number_format($instructor,2) }}</div>
                </div>
            </div>
            @if($pendingPayouts > 0)
            <div style="margin-top:10px;padding:10px 12px;background:#fef9c3;border-radius:8px;font-size:.78rem;color:#92400e">
                ⚠ Pending payouts: <strong>${{ number_format($pendingPayouts,2) }}</strong>
            </div>
            @endif
        </div>

        <div class="panel">
            <div class="panel-header"><div class="panel-title">Platform Health</div></div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:6px">
                        <span style="color:var(--muted)">Completion rate</span>
                        <span style="font-weight:600">{{ $completionRate }}%</span>
                    </div>
                    <div style="height:6px;background:#f0ede6;border-radius:99px;overflow:hidden">
                        <div style="height:100%;width:{{ $completionRate }}%;background:#10b981;border-radius:99px;transition:width .4s"></div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div style="background:#f5f2eb;border-radius:10px;padding:12px;text-align:center">
                        <div style="font-size:1.2rem;font-weight:700">{{ number_format($avgRating??0,1) }}</div>
                        <div style="font-size:.7rem;color:var(--muted);margin-top:2px">Avg Rating ★</div>
                    </div>
                    <div style="background:#f5f2eb;border-radius:10px;padding:12px;text-align:center">
                        <div style="font-size:1.2rem;font-weight:700">{{ $topInstructors->count() }}</div>
                        <div style="font-size:.7rem;color:var(--muted);margin-top:2px">Instructors</div>
                    </div>
                </div>
                @if($topInstructors->first())
                @php $top=$topInstructors->first(); @endphp
                <div style="display:flex;align-items:center;gap:10px;padding-top:4px;border-top:1px solid var(--border)">
                    <div style="width:30px;height:30px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#fff;flex-shrink:0">
                        {{ strtoupper(substr($top->name,0,2)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.8rem;font-weight:600">{{ $top->name }}</div>
                        <div style="font-size:.7rem;color:var(--muted)">Top earner · ${{ number_format($top->balance,2) }}</div>
                    </div>
                    <span style="font-size:.7rem;background:#ecfdf5;color:#059669;padding:2px 7px;border-radius:99px;font-weight:600">🏆 #1</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bottom-row">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Recent Orders</div>
                <a href="{{ route('admin.orders') }}" style="font-size:.75rem;color:var(--accent);font-weight:600">View all →</a>
            </div>
            <table class="data-table">
                <thead><tr><th>Order #</th><th>Student</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td style="font-size:.75rem;color:var(--muted);font-family:monospace">{{ $order->order_number }}</td>
                        <td>{{ Str::limit($order->customer_name ?? $order->user?->name ?? '—', 18) }}</td>
                        <td style="font-weight:600">${{ number_format($order->final_amount,2) }}</td>
                        <td><span class="pill {{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="empty-state">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">Recent Enrollments</div>
                <a href="{{ route('admin.enrollments') }}" style="font-size:.75rem;color:var(--accent);font-weight:600">View all →</a>
            </div>
            @php $avatarColors=['#e8512a','#3b82f6','#10b981','#8b5cf6','#f59e0b','#ec4899']; @endphp
            <div>
                @forelse($recentEnrollments as $i => $enroll)
                <div class="activity-item">
                    <div class="act-avatar" style="background:{{ $avatarColors[$i%6] }}">{{ strtoupper(substr($enroll->user?->name ?? '?',0,2)) }}</div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:.82rem;font-weight:500">{{ $enroll->user?->name ?? 'Unknown' }}</p>
                        <p style="font-size:.76rem;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">enrolled in <span style="color:var(--ink)">{{ Str::limit($enroll->course?->title ?? '—',32) }}</span></p>
                        <p style="font-size:.7rem;color:var(--muted);margin-top:2px">{{ $enroll->enrolled_at?->diffForHumans() }}</p>
                    </div>
                    <span class="pill {{ $enroll->status }}">{{ ucfirst($enroll->status) }}</span>
                </div>
                @empty
                <p class="empty-state">No enrollments yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const chartDates   = @json($chart->pluck('date'));
    const chartRevenue = @json($chart->pluck('total_revenue'));
    const chartOrders  = @json($chart->pluck('total_orders'));

    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: chartDates,
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: chartRevenue,
                    borderColor: '#e8512a',
                    backgroundColor: 'rgba(232,81,42,0.07)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#e8512a',
                    tension: 0.35,
                    fill: true,
                    yAxisID: 'y',
                },
                {
                    label: 'Orders',
                    data: chartOrders,
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    pointRadius: 0,
                    tension: 0.35,
                    fill: false,
                    borderDash: [5,5],
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.datasetIndex === 0
                            ? ` $${Number(ctx.parsed.y).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})}`
                            : ` ${ctx.parsed.y} orders`
                    }
                }
            },
            scales: {
                x: { grid:{display:false}, ticks:{font:{family:'DM Sans',size:11},color:'#8a8a8a',maxTicksLimit:8}, border:{display:false} },
                y: { position:'left', grid:{color:'#f0ede6'}, ticks:{font:{family:'DM Sans',size:11},color:'#8a8a8a',callback:v=>'$'+(v>=1000?(v/1000).toFixed(1)+'k':v)}, border:{display:false} },
                y1: { position:'right', grid:{display:false}, ticks:{font:{family:'DM Sans',size:11},color:'#8a8a8a'}, border:{display:false} }
            }
        }
    });

    @php
        $donutData = [
            $enrollmentByLevel->get('beginner', 0),
            $enrollmentByLevel->get('intermediate', 0),
            $enrollmentByLevel->get('advanced', 0),
        ];
    @endphp

    const donutData = @json($donutData);
    new Chart(document.getElementById('donutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Beginner','Intermediate','Advanced'],
            datasets: [{ data: donutData, backgroundColor:['#10b981','#f59e0b','#ef4444'], borderWidth:0, hoverOffset:4 }]
        },
        options: {
            responsive:true, maintainAspectRatio:false, cutout:'72%',
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: ctx=>` ${ctx.label}: ${ctx.parsed}` } } }
        }
    });

    function setPeriod(el) {
        document.querySelectorAll('.period-tab').forEach(t=>t.classList.remove('active'));
        el.classList.add('active');
    }
</script>
@endpush
