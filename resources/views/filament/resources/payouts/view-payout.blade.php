@php
    /** @var \App\Domains\Finance\Models\PayoutRequest $record */
    $payout = $record->loadMissing(['instructor', 'payoutAccount', 'processedBy', 'receipt']);
    $status = match ($payout->status) {
        'pending' => ['bg' => 'rgba(245,158,11,.12)', 'color' => '#f59e0b', 'label' => 'Pending'],
        'approved' => ['bg' => 'rgba(16,185,129,.12)', 'color' => '#10b981', 'label' => 'Completed'],
        'rejected' => ['bg' => 'rgba(239,68,68,.12)', 'color' => '#ef4444', 'label' => 'Rejected'],
        default => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#94a3b8', 'label' => ucfirst($payout->status ?? 'Unknown')],
    };
    $details = $payout->details ?? [];
    $account = $payout->payoutAccount;
    $currency = number_format((float) $payout->amount, 2) . ' ' . strtoupper($payout->currency ?: 'USD');
    $date = fn ($value) => $value?->setTimezone(config('app.timezone'))->format('M d, Y H:i') ?? '—';
@endphp

<div class="payout-view">
    <style>
        .payout-view,.payout-view *{box-sizing:border-box}
        .payout-view{display:grid;gap:20px;padding-bottom:48px;color:var(--payout-text);--payout-card:#1e293b;--payout-card-2:#263245;--payout-border:rgba(255,255,255,.08);--payout-text:#e2e8f0;--payout-muted:#94a3b8;--payout-shadow:0 4px 24px rgba(0,0,0,.3)}
        html:not(.dark) .payout-view{--payout-card:#fff;--payout-card-2:#f8fafc;--payout-border:rgba(15,23,42,.1);--payout-text:#0f172a;--payout-muted:#64748b;--payout-shadow:0 2px 16px rgba(15,23,42,.1)}
        .payout-view-topbar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--payout-border)}
        .payout-view-title{font-size:clamp(20px,2.2vw,28px);font-weight:800;color:var(--payout-text)}
        .payout-view-subtitle{margin-top:4px;color:var(--payout-muted);font-size:12px}
        .payout-view-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;border:1px solid var(--payout-border);background:var(--payout-card-2);color:var(--payout-muted);font-size:12px;font-weight:700;text-decoration:none}
        .payout-view-btn:hover{color:var(--payout-text)}
        .payout-view-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
        .payout-view-card{background:var(--payout-card);border:1px solid var(--payout-border);border-radius:12px;overflow:hidden;box-shadow:var(--payout-shadow)}
        .payout-view-card-header{padding:14px 18px;border-bottom:1px solid var(--payout-border);font-size:13px;font-weight:800;color:var(--payout-text)}
        .payout-view-card-body{padding:18px}
        .payout-view-field{display:grid;gap:4px;margin-bottom:14px}
        .payout-view-field:last-child{margin-bottom:0}
        .payout-view-label{font-size:10px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--payout-muted)}
        .payout-view-value{font-size:13px;color:var(--payout-text);word-break:break-word}
        .payout-view-status{display:inline-flex;width:max-content;padding:4px 9px;border-radius:7px;font-size:11px;font-weight:800}
        .payout-view-json{margin:0;padding:12px;overflow:auto;border-radius:8px;background:var(--payout-card-2);color:var(--payout-text);font:12px/1.6 ui-monospace,SFMono-Regular,monospace;white-space:pre-wrap}
        @media(max-width:800px){.payout-view-grid{grid-template-columns:1fr}}
    </style>

    <div class="payout-view-topbar">
        <div>
            <h1 class="payout-view-title">Payout #{{ $payout->id }}</h1>
            <p class="payout-view-subtitle">Complete payout request details and processing history.</p>
        </div>
        <a href="{{ $backUrl }}" wire:navigate class="payout-view-btn">← Back to payouts</a>
    </div>

    <div class="payout-view-grid">
        <section class="payout-view-card">
            <div class="payout-view-card-header">Request</div>
            <div class="payout-view-card-body">
                <div class="payout-view-field"><span class="payout-view-label">Amount</span><span class="payout-view-value">{{ $currency }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Status</span><span class="payout-view-status" style="background:{{ $status['bg'] }};color:{{ $status['color'] }}">{{ $status['label'] }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Payment method</span><span class="payout-view-value">{{ strtoupper($payout->payment_method ?: '—') }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Source</span><span class="payout-view-value">{{ ucfirst(str_replace('_', ' ', $payout->source ?: 'manual')) }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Requested</span><span class="payout-view-value">{{ $date($payout->requested_at ?: $payout->created_at) }}</span></div>
            </div>
        </section>

        <section class="payout-view-card">
            <div class="payout-view-card-header">Instructor</div>
            <div class="payout-view-card-body">
                <div class="payout-view-field"><span class="payout-view-label">Name</span><span class="payout-view-value">{{ $payout->instructor?->name ?: '—' }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Email</span><span class="payout-view-value">{{ $payout->instructor?->email ?: '—' }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Instructor ID</span><span class="payout-view-value">{{ $payout->instructor_id }}</span></div>
            </div>
        </section>

        <section class="payout-view-card">
            <div class="payout-view-card-header">Processing</div>
            <div class="payout-view-card-body">
                <div class="payout-view-field"><span class="payout-view-label">Processed at</span><span class="payout-view-value">{{ $date($payout->processed_at) }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Processed by</span><span class="payout-view-value">{{ $payout->processedBy?->name ?: '—' }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Transaction reference</span><span class="payout-view-value">{{ $payout->transaction_reference ?: '—' }}</span></div>
                <div class="payout-view-field"><span class="payout-view-label">Receipt</span><span class="payout-view-value">{{ $payout->receipt?->receipt_number ?: '—' }}</span></div>
            </div>
        </section>
    </div>

    <section class="payout-view-card">
        <div class="payout-view-card-header">Payout destination</div>
        <div class="payout-view-card-body payout-view-grid">
            <div class="payout-view-field"><span class="payout-view-label">Account name</span><span class="payout-view-value">{{ $account?->account_name ?: ($details['account_name'] ?? '—') }}</span></div>
            <div class="payout-view-field"><span class="payout-view-label">Account number</span><span class="payout-view-value">{{ $account?->account_number ?: ($details['account_number'] ?? '—') }}</span></div>
            <div class="payout-view-field"><span class="payout-view-label">Phone number</span><span class="payout-view-value">{{ $account?->phone_number ?: ($details['phone_number'] ?? '—') }}</span></div>
        </div>
    </section>

    @if($payout->rejection_reason)
        <section class="payout-view-card">
            <div class="payout-view-card-header">Rejection reason</div>
            <div class="payout-view-card-body"><div class="payout-view-value">{{ $payout->rejection_reason }}</div></div>
        </section>
    @endif

    @if($details)
        <section class="payout-view-card">
            <div class="payout-view-card-header">Additional details</div>
            <div class="payout-view-card-body"><pre class="payout-view-json">{{ json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div>
        </section>
    @endif
</div>