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
    color:#7c3aed;
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
        <div class="brand">{{ \App\Domains\System\Models\Setting::get('site_name', config('app.name')) }}</div>
    </div>
    <div class="body">
        <h2>Your Payout Has Been Sent!</h2>
        <p>Hi {{ $receipt->payoutRequest->instructor?->name ?? 'there' }},</p>
        <p>Your instructor payout has been processed. Your payout receipt is attached to this email.</p>

        <div class="doc-box">
            <div class="row"><span class="label">Receipt Number</span><span class="value">{{ $receipt->receipt_number }}</span></div>
            <div class="row"><span class="label">Payout Reference</span><span class="value">#{{ $receipt->payoutRequest->id }}</span></div>
            <div class="row"><span class="label">Amount Paid</span><span class="value">{{ $receipt->currency }} {{ number_format($receipt->amount, 2) }}</span></div>
            <div class="row"><span class="label">Payment Method</span><span class="value">{{ strtoupper(str_replace('_', ' ', $receipt->payment_method)) }}</span></div>
            <div class="row"><span class="label">Date</span><span class="value">{{ $receipt->paid_at?->format('F j, Y \a\t g:i A') }}</span></div>
        </div>

        <p>If you have any questions, contact our support team at <a href="mailto:{{ \App\Domains\System\Models\Setting::get('support_email', config('mail.from.address')) }}">{{ \App\Domains\System\Models\Setting::get('support_email', config('mail.from.address')) }}</a>.</p>
        <p>Thank you for teaching on {{ \App\Domains\System\Models\Setting::get('site_name', config('app.name')) }}.</p>
    </div>
    <div class="footer">
        {!! \App\Domains\System\Models\Setting::get('footer_text', "&copy; {" . now()->year . "} " . \App\Domains\System\Models\Setting::get('site_name', config('app.name')) . ". All rights reserved.") !!}
    </div>
</div>
</body>
</html>
