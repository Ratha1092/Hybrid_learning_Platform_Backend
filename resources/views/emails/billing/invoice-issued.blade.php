<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #374151; font-size: 14px; margin: 0; padding: 0; background: #f9fafb; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .top-bar { background: #15110a; padding: 24px 32px; }
        .top-bar .brand { color: #D7A441; font-size: 22px; font-weight: bold; }
        .body { padding: 32px; }
        h2 { color: #111827; font-size: 18px; margin: 0 0 12px; }
        p { margin: 0 0 12px; line-height: 1.6; color: #4b5563; }
        .doc-box { background: #f3f4f6; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .doc-box .row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
        .doc-box .label { color: #6b7280; }
        .doc-box .value { color: #111827; font-weight: bold; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="top-bar">
        <div class="brand">{{ config('app.name') }}</div>
    </div>
    <div class="body">
        <h2>{{ $invoice->isCreditNote() ? 'Credit Note Issued' : 'Your Invoice is Ready' }}</h2>
        <p>Hi {{ $invoice->order->user?->name ?? 'there' }},</p>
        @if($invoice->isCreditNote())
            <p>A credit note has been issued for your refunded order. Please find the credit note attached to this email.</p>
        @else
            <p>Your invoice for order <strong>{{ $invoice->order->order_number }}</strong> has been issued. The PDF is attached to this email.</p>
        @endif

        <div class="doc-box">
            <div class="row"><span class="label">Document Number</span><span class="value">{{ $invoice->invoice_number }}</span></div>
            <div class="row"><span class="label">Order</span><span class="value">{{ $invoice->order->order_number }}</span></div>
            <div class="row"><span class="label">Total</span><span class="value">{{ $invoice->currency }} {{ number_format(abs($invoice->total), 2) }}</span></div>
            <div class="row"><span class="label">Date</span><span class="value">{{ $invoice->issued_at?->format('F j, Y') }}</span></div>
        </div>

        <p>If you have any questions, please contact our support team.</p>
        <p>Thank you for learning with {{ config('app.name') }}.</p>
    </div>
    <div class="footer">
        &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
