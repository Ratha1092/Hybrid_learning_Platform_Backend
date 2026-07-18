<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
body {
    font-family:Arial, sans-serif;
    color:#374151;
    font-size:14px;
    margin:0;
    padding:0;
    background:#f9fafb;
}
.wrapper {
    max-width:600px;
    margin:40px auto;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
}
.top-bar {
    background:#15110a;
    padding:24px 32px;
}
.top-bar .brand {
    color:#D7A441;
    font-size:22px;
    font-weight:bold;
}
.body {
    padding:32px;
}
h2 {
    color:#111827;
    font-size:18px;
    margin:0 0 12px;
}
p {
    margin:0 0 12px;
    line-height:1.6;
    color:#4b5563;
}
.doc-box {
    background:#f3f4f6;
    border-radius:6px;
    padding:16px 20px;
    margin:20px 0;
}
.doc-box .row {
    display:flex;
    justify-content:space-between;
    margin-bottom:6px;
    font-size:13px;
}
.doc-box .label {
    color:#6b7280;
}
.doc-box .value {
    color:#111827;
    font-weight:bold;
}
.courses {
    margin:16px 0;
}
.course-item {
    padding:8px 0;
    border-bottom:1px solid #e5e7eb;
    font-size:13px;
    display:flex;
    justify-content:space-between;
}
.footer {
    padding:20px 32px;
    background:#f9fafb;
    text-align:center;
    font-size:12px;
    color:#9ca3af;
}
</style>
</head>
<body>
<div class="wrapper">
    <div class="top-bar">
        <div class="brand">{{ config('app.name') }}</div>
    </div>
    <div class="body">
        <h2>Payment Confirmed!</h2>
        <p>Hi {{ $receipt->order->user?->name ?? 'there' }},</p>
        <p>Your payment has been received. Your payment receipt is attached to this email.</p>

        <div class="doc-box">
            <div class="row"><span class="label">Receipt Number</span><span class="value">{{ $receipt->receipt_number }}</span></div>
            <div class="row"><span class="label">Order</span><span class="value">{{ $receipt->order->order_number }}</span></div>
            <div class="row"><span class="label">Amount Paid</span><span class="value">{{ $receipt->currency }} {{ number_format($receipt->amount, 2) }}</span></div>
            <div class="row"><span class="label">Payment Method</span><span class="value">{{ strtoupper($receipt->payment_gateway) }}</span></div>
            <div class="row"><span class="label">Date</span><span class="value">{{ $receipt->paid_at?->format('F j, Y \a\t g:i A') }}</span></div>
        </div>

        @if($receipt->order->items->count())
            <p><strong>Courses enrolled:</strong></p>
            <div class="courses">
                @foreach($receipt->order->items as $item)
                    <div class="course-item">
                        <span>{{ $item->course_title }}</span>
                        <span>{{ $receipt->currency }} {{ number_format($item->final_amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <p>You can now access your courses from your dashboard. Enjoy learning!</p>
        <p>Thank you for choosing {{ config('app.name') }}.</p>
    </div>
    <div class="footer">
        &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
