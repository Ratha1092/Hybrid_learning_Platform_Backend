{{-- Shared PDF chrome for all report exports. @include this right after <body> opens;
     each report view then appends its own KPI cards / table markup using the same
     class names established here (and in resources/views/receipts/order.blade.php,
     which this intentionally matches so receipts and reports look like one family). --}}
<style>
@page {
    margin:36px 40px;
}
* {
    box-sizing:border-box;
}
body {
    font-family:'Helvetica', 'Arial', sans-serif;
    color:#1f2937;
    font-size:12px;
}
.header {
    display:table;
    width:100%;
    margin-bottom:20px;
}
.header-brand {
    display:table-cell;
    vertical-align:top;
}
.header-meta {
    display:table-cell;
    vertical-align:top;
    text-align:right;
}
.brand-name {
    font-size:20px;
    font-weight:bold;
    color:#15110a;
}
.brand-sub {
    font-size:11px;
    color:#000;
    margin-top:2px;
}
.report-title {
    font-size:13px;
    font-weight:bold;
    color:#D7A441;
    text-transform:uppercase;
    letter-spacing:1px;
}
.report-meta {
    font-size:11px;
    color:#000;
    margin-top:4px;
}
.divider {
    border-top:1px solid #e5e7eb;
    margin:16px 0;
}
table.kpi-grid {
    width:100%;
    margin-bottom:20px;
    border-collapse:separate;
    border-spacing:8px 0;
}
table.kpi-grid td.kpi-cell {
    width:25%;
    padding:10px 12px;
    border:1px solid #e5e7eb;
    border-radius:6px;
    background:#f9fafb;
    vertical-align:top;
}
.kpi-label {
    font-size:9.5px;
    color:#000;
    text-transform:uppercase;
    letter-spacing:.5px;
    margin-bottom:4px;
}
.kpi-value {
    font-size:16px;
    font-weight:bold;
    color:#000;
}
table.items {
    width:100%;
    border-collapse:collapse;
    margin-top:6px;
    border:1px solid #d1d5db;
}
table.items th {
    text-align:left;
    font-size:9.5px;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:#000;
    padding:8px 10px;
    background:#f3f4f6;
    border-bottom:1px solid #d1d5db;
}
table.items td {
    padding:8px 10px;
    border-bottom:1px solid #d1d5db;
    font-size:11px;
    vertical-align:top;
}
table.items td.amount, table.items th.amount {
    text-align:right;
}
.footer {
    margin-top:32px;
    padding-top:14px;
    border-top:1px solid #e5e7eb;
    text-align:center;
    color:#000;
    font-size:10px;
}
/* ── Chart section ── */
.charts-row {
    display:table;
    width:100%;
    margin-bottom:20px;
    border-collapse:separate;
    border-spacing:10px 0;
}
.chart-card {
    display:table-cell;
    width:50%;
    padding:12px 14px;
    border:1px solid #e5e7eb;
    border-radius:6px;
    background:#f9fafb;
    vertical-align:top;
}
.chart-card.full {
    width:100%;
}
.chart-title {
    font-size:9.5px;
    font-weight:bold;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:#374151;
    margin-bottom:10px;
}
table.bar-chart {
    width:100%;
    border-collapse:collapse;
}
table.bar-chart td {
    padding:3px 0;
    vertical-align:middle;
    border:none;
}
.bar-label {
    font-size:10px;
    color:#6b7280;
    width:100px;
    padding-right:8px;
    white-space:nowrap;
}
.bar-bg {
    background:#e5e7eb;
    border-radius:3px;
    height:10px;
}
.bar-fill {
    height:10px;
    border-radius:3px;
}
.bar-value {
    font-size:10px;
    font-weight:bold;
    color:#111827;
    width:75px;
    text-align:right;
    padding-left:8px;
    white-space:nowrap;
}
/* Column trend chart */
table.trend-chart {
    width:100%;
    border-collapse:collapse;
}
table.trend-chart td {
    text-align:center;
    vertical-align:bottom;
    padding:0 4px;
    border:none;
}
.trend-col-wrap {
    display:block;
    height:70px;
    vertical-align:bottom;
    position:relative;
}
.trend-col-fill {
    display:block;
    background:#D7A441;
    border-radius:3px 3px 0 0;
    width:100%;
}
.trend-month {
    font-size:8.5px;
    color:#9ca3af;
    margin-top:4px;
}
.trend-amount {
    font-size:8px;
    color:#374151;
    font-weight:bold;
}
/* Coupon list */
.coupon-row {
    display:table;
    width:100%;
    padding:4px 0;
    border-bottom:1px solid #e5e7eb;
}
.coupon-code {
    display:table-cell;
    font-size:10px;
    font-weight:bold;
    color:#111827;
}
.coupon-uses {
    display:table-cell;
    text-align:right;
    font-size:10px;
    color:#6b7280;
}
</style>

<div class="header">
    <div class="header-brand">
        <div class="brand-name">{{ $siteName }}</div>
        <div class="brand-sub">Report Center</div>
    </div>
    <div class="header-meta">
        <div class="report-title">{{ $title }}</div>
        <div class="report-meta">Generated {{ now()->format('F j, Y \a\t g:i A') }}</div>
        @if(!empty($filtersSummary))
            <div class="report-meta">{{ $filtersSummary }}</div>
        @endif
    </div>
</div>

<div class="divider"></div>
