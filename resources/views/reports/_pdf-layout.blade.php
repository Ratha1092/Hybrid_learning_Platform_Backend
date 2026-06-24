{{-- Shared PDF chrome for all report exports. @include this right after <body> opens;
     each report view then appends its own KPI cards / table markup using the same
     class names established here (and in resources/views/receipts/order.blade.php,
     which this intentionally matches so receipts and reports look like one family). --}}
<style>
    @page { 
        margin: 36px 40px; 
    }
    * { 
        box-sizing: border-box; 
    }
    body { 
        font-family: 'Helvetica', 'Arial', sans-serif; 
        color: #1f2937; 
        font-size: 12px; }
    .header { 
        display: table; 
        width: 100%; margin-bottom: 20px; 
    }
    .header-brand { 
        display: table-cell; 
        vertical-align: top; 
    }
    .header-meta { 
        display: table-cell; 
        vertical-align: top; 
        text-align: right; 
    }
    .brand-name { 
        font-size: 20px; 
        font-weight: bold; 
        color: #15110a; 
    }
    .brand-sub {
        font-size: 11px;
        color: #000;
        margin-top: 2px; }
    .report-title { 
        font-size: 13px; 
        font-weight: bold; 
        color: #D7A441; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
    }
    .report-meta {
        font-size: 11px;
        color: #000;
        margin-top: 4px;
    }
    .divider { 
        border-top: 1px solid #e5e7eb; 
        margin: 16px 0; 
    }
    table.kpi-grid {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: separate;
        border-spacing: 8px 0;
    }
    table.kpi-grid td.kpi-cell {
        width: 25%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #f9fafb;
        vertical-align: top;
    }
    .kpi-label {
        font-size: 9.5px;
        color: #000;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 16px;
        font-weight: bold;
        color: #000;
    }
    table.items {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        border: 1px solid #d1d5db;
    }
    table.items th {
        text-align: left;
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #000;
        padding: 8px 10px;
        background: #f3f4f6;
        border-bottom: 1px solid #d1d5db;
    }
    table.items td {
        padding: 8px 10px;
        border-bottom: 1px solid #d1d5db;
        font-size: 11px;
        vertical-align: top;
    }
    table.items td.amount, table.items th.amount {
        text-align: right;
    }
    .footer {
        margin-top: 32px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        color: #000;
        font-size: 10px;
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
