<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payout Receipt {{ $receipt->receipt_number }}</title>
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
    color:#7c3aed;
    text-transform:uppercase;
    letter-spacing:1px;
}
.doc-number {
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

.totals {
    width:100%;
    margin-top:16px;
}
.totals-row {
    display:table;
    width:100%;
    margin-bottom:6px;
}
.totals-row.grand .totals-label {
    display:table-cell;
    text-align:right;
    width:80%;
    color:#111827;
    font-weight:bold;
    font-size:13px;
    padding-right:14px;
}
.totals-row.grand .totals-value {
    display:table-cell;
    text-align:right;
    width:20%;
    color:#15110a;
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
            <div class="brand-name">{{ config('app.name') }}</div>
            <div class="brand-sub">Instructor Payout Receipt</div>
        </div>
        <div class="header-meta">
            <div class="doc-title">Payout Receipt</div>
            <div class="doc-number">{{ $receipt->receipt_number }}</div>
            <div class="status-pill">PAID</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-grid">
        <div class="info-col">
            <div class="info-label">Paid To</div>
            <div class="info-value">{{ $payout->instructor?->name ?? '—' }}</div>
            <div class="info-value">{{ $payout->instructor?->email ?? '—' }}</div>
        </div>
        <div class="info-col" style="text-align: right;">
            <div class="info-label">Date Paid</div>
            <div class="info-value">{{ $receipt->paid_at?->format('F j, Y \a\t g:i A') ?? '—' }}</div>
            <div class="info-label" style="margin-top: 8px;">Payment Method</div>
            <div class="info-value">{{ strtoupper(str_replace('_', ' ', $receipt->payment_method)) }}</div>
            <div class="info-label" style="margin-top: 8px;">Payout Reference</div>
            <div class="info-value">#{{ $payout->id }}</div>
            @if($payout->transaction_reference)
            <div class="info-label" style="margin-top: 8px;">Transaction Reference</div>
            <div class="info-value">{{ $payout->transaction_reference }}</div>
            @endif
        </div>
    </div>

    @if($payout->payoutAccount)
    <div class="info-grid">
        <div class="info-col">
            <div class="info-label">Account Name</div>
            <div class="info-value">{{ $payout->payoutAccount->account_name ?? '—' }}</div>
        </div>
        <div class="info-col" style="text-align: right;">
            @if($payout->payoutAccount->account_number)
            <div class="info-label">Account Number</div>
            <div class="info-value">{{ $payout->payoutAccount->account_number }}</div>
            @elseif($payout->payoutAccount->phone_number)
            <div class="info-label">Phone Number</div>
            <div class="info-value">{{ $payout->payoutAccount->phone_number }}</div>
            @endif
        </div>
    </div>
    @endif

    <div class="divider"></div>

    <div class="totals">
        <div class="totals-row grand">
            <div class="totals-label">Total Paid</div>
            <div class="totals-value">{{ $receipt->currency }} {{ number_format($receipt->amount, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        This receipt confirms payout of your course earnings on {{ config('app.name') }}.<br>
        Generated on {{ now()->format('F j, Y') }}.
    </div>

</body>
</html>
