<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credit Note {{ $invoice->invoice_number }}</title>
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
    margin-bottom:28px;
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
    color:#6b7280;
    margin-top:2px;
}

.doc-title {
    font-size:13px;
    font-weight:bold;
    color:#ef4444;
    text-transform:uppercase;
    letter-spacing:1px;
}
.doc-number {
    font-size:11px;
    color:#6b7280;
    margin-top:4px;
}
.doc-date {
    font-size:11px;
    color:#6b7280;
    margin-top:2px;
}

.status-pill {
    display:inline-block;
    margin-top:8px;
    padding:3px 10px;
    border-radius:999px;
    font-size:10px;
    font-weight:bold;
    background:#fef2f2;
    color:#ef4444;
}

.divider {
    border-top:1px solid #e5e7eb;
    margin:18px 0;
}

.info-grid {
    display:table;
    width:100%;
    margin-bottom:20px;
}
.info-col {
    display:table-cell;
    width:50%;
    vertical-align:top;
}
.info-label {
    font-size:9.5px;
    color:#9ca3af;
    text-transform:uppercase;
    letter-spacing:.5px;
    margin-bottom:3px;
}
.info-value {
    font-size:12px;
    color:#111827;
}

.ref-box {
    background:#fef2f2;
    border:1px solid #fecaca;
    border-radius:4px;
    padding:10px 14px;
    margin-bottom:16px;
    font-size:11px;
    color:#374151;
}
.ref-box strong {
    color:#ef4444;
}

table.items {
    width:100%;
    border-collapse:collapse;
    margin-top:6px;
}
table.items th {
    text-align:left;
    font-size:9.5px;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:#9ca3af;
    padding:8px 0;
    border-bottom:1px solid #e5e7eb;
}
table.items td {
    padding:10px 0;
    border-bottom:1px solid #f3f4f6;
    font-size:12px;
    vertical-align:top;
}
table.items td.right, table.items th.right {
    text-align:right;
}
.item-title {
    font-weight:bold;
    color:#111827;
}

.totals {
    width:100%;
    margin-top:16px;
}
.totals-row {
    display:table;
    width:100%;
    margin-bottom:6px;
}
.totals-label {
    display:table-cell;
    text-align:right;
    width:80%;
    color:#6b7280;
    font-size:11.5px;
    padding-right:14px;
}
.totals-value {
    display:table-cell;
    text-align:right;
    width:20%;
    font-size:11.5px;
    color:#111827;
}
.totals-row.grand .totals-label {
    color:#111827;
    font-weight:bold;
    font-size:13px;
}
.totals-row.grand .totals-value {
    color:#ef4444;
    font-weight:bold;
    font-size:15px;
}

.footer {
    margin-top:36px;
    padding-top:16px;
    border-top:1px solid #e5e7eb;
    text-align:center;
    color:#9ca3af;
    font-size:10px;
}
</style>
</head>
<body>

    <div class="header">
        <div class="header-brand">
            <div class="brand-name">{{ \App\Domains\System\Models\Setting::get('site_name', config('app.name')) }}</div>
            <div class="brand-sub">Credit Note</div>
        </div>
        <div class="header-meta">
            <div class="doc-title">Credit Note</div>
            <div class="doc-number">{{ $invoice->invoice_number }}</div>
            <div class="doc-date">Issued: {{ $invoice->issued_at?->format('F j, Y') ?? now()->format('F j, Y') }}</div>
            <div class="status-pill">REFUNDED</div>
        </div>
    </div>

    <div class="divider"></div>

    @if($invoice->originalInvoice)
        <div class="ref-box">
            <strong>Credit against Invoice:</strong> {{ $invoice->originalInvoice->invoice_number }}
            &nbsp;|&nbsp; Order: {{ $invoice->order->order_number }}
            @if($reason)
                &nbsp;|&nbsp; Reason: {{ $reason }}
            @endif
        </div>
    @endif

    <div class="info-grid">
        <div class="info-col">
            <div class="info-label">Issued To</div>
            <div class="info-value">{{ $invoice->order->customer_name ?? $invoice->order->user?->name ?? '—' }}</div>
            <div class="info-value">{{ $invoice->order->customer_email ?? $invoice->order->user?->email ?? '—' }}</div>
        </div>
        <div class="info-col" style="text-align: right;">
            <div class="info-label">Credit Note Date</div>
            <div class="info-value">{{ $invoice->issued_at?->format('F j, Y') ?? now()->format('F j, Y') }}</div>
            <div class="info-label" style="margin-top: 8px;">Currency</div>
            <div class="info-value">{{ $invoice->currency }}</div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Course</th>
                <th class="right">Unit Price</th>
                <th class="right">Amount Credited</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td><div class="item-title">{{ $item->course_title }}</div></td>
                    <td class="right">{{ $invoice->currency }} {{ number_format(abs($item->unit_price), 2) }}</td>
                    <td class="right">{{ $invoice->currency }} {{ number_format(abs($item->amount), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <div class="totals-label">Subtotal Credited</div>
            <div class="totals-value">{{ $invoice->currency }} {{ number_format(abs($invoice->subtotal), 2) }}</div>
        </div>
        @if($invoice->tax_amount != 0)
            <div class="totals-row">
                <div class="totals-label">Tax ({{ number_format($invoice->tax_rate, 2) }}%)</div>
                <div class="totals-value">{{ $invoice->currency }} {{ number_format(abs($invoice->tax_amount), 2) }}</div>
            </div>
        @endif
        <div class="totals-row grand">
            <div class="totals-label">Total Credited</div>
            <div class="totals-value">{{ $invoice->currency }} {{ number_format(abs($invoice->total), 2) }}</div>
        </div>
    </div>

    <div class="footer">
        {{ \App\Domains\System\Models\Setting::get('footer_text', "© " . now()->year . " " . \App\Domains\System\Models\Setting::get('site_name', config('app.name')) . ". All rights reserved.") }}<br>
        The original invoice has not been modified.
    </div>

</body>
</html>
