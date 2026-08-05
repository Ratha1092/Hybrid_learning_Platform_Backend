<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->order_number }}</title>
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

.receipt-title {
    font-size:13px;
    font-weight:bold;
    color:#D7A441;
    text-transform:uppercase;
    letter-spacing:1px;
}
.receipt-number {
    font-size:11px;
    color:#6b7280;
    margin-top:4px;
}

.status-pill {
    display:inline-block;
    margin-top:8px;
    padding:3px 10px;
    border-radius:999px;
    font-size:10px;
    font-weight:bold;
    background:#ecfdf5;
    color:#059669;
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
table.items td.amount, table.items th.amount {
    text-align:right;
}
.item-title {
    font-weight:bold;
    color:#111827;
}
.item-instructor {
    color:#6b7280;
    font-size:10.5px;
    margin-top:2px;
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
    color:#15110a;
    font-weight:bold;
    font-size:15px;
}
.totals-row.discount .totals-value {
    color:#059669;
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
            <div class="brand-name">{{ $siteName }}</div>
            <div class="brand-sub">Payment Receipt</div>
        </div>
        <div class="header-meta">
            <div class="receipt-title">Receipt</div>
            <div class="receipt-number">{{ $order->order_number }}</div>
            <div class="status-pill">PAID</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-grid">
        <div class="info-col">
            <div class="info-label">Billed To</div>
            <div class="info-value">{{ $order->customer_name ?? $order->user?->name ?? '—' }}</div>
            <div class="info-value">{{ $order->customer_email ?? $order->user?->email ?? '—' }}</div>
        </div>
        <div class="info-col" style="text-align: right;">
            <div class="info-label">Date Paid</div>
            <div class="info-value">{{ $order->paid_at?->format('F j, Y \a\t g:i A') ?? '—' }}</div>
            @if($payment)
                <div class="info-label" style="margin-top: 8px;">Payment Method</div>
                <div class="info-value">{{ strtoupper($payment->payment_gateway?->value ?? $payment->payment_gateway ?? '—') }}</div>
            @endif
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Course</th>
                <th class="amount">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-title">{{ $item->course_title }}</div>
                        @if($item->instructor)
                            <div class="item-instructor">by {{ $item->instructor->name }}</div>
                        @endif
                    </td>
                    <td class="amount">{{ $order->currency }} {{ number_format($item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <div class="totals-label">Subtotal</div>
            <div class="totals-value">{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</div>
        </div>
        @if($order->discount_amount > 0)
            <div class="totals-row discount">
                <div class="totals-label">Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</div>
                <div class="totals-value">-{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}</div>
            </div>
        @endif
        <div class="totals-row grand">
            <div class="totals-label">Total Paid</div>
            <div class="totals-value">{{ $order->currency }} {{ number_format($order->final_amount, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        {{ \App\Domains\System\Models\Setting::get('footer_text', "Thank you for your purchase on {$siteName}. This receipt was generated on " . now()->format('F j, Y') . ".")) }}
    </div>

</body>
</html>
