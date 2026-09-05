@php
    $statusStyle = match ($report->status) {
        'pending' => ['bg' => 'rgba(251,191,36,.12)', 'color' => '#fbbf24', 'label' => 'Pending'],
        'reviewed' => ['bg' => 'rgba(52,211,153,.12)', 'color' => '#34d399', 'label' => 'Reviewed'],
        'dismissed' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Dismissed'],
        default => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => ucfirst($report->status ?? 'Unknown')],
    };
    $typeLabel = ucfirst($report->reportable_type ?? 'Content');
    $reportedAt = $report->created_at?->setTimezone(config('app.timezone'))->format('M d, Y · H:i');
    $reviewedAt = $report->reviewed_at?->setTimezone(config('app.timezone'))->format('M d, Y · H:i');
    $reportableTitle = $report->reportable?->title
        ?? $report->reportable?->name
        ?? ($typeLabel . ' #' . $report->reportable_id);
@endphp

<div class="crd" id="crd-content-report">
    <style>
        .crd, .crd *, .crd *::before, .crd *::after { box-sizing:border-box; }
        .crd { font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; color:#e2e8f0; max-width:960px; margin:0 auto; padding-bottom:48px; }
        html:not(.dark) .crd { color:#0f172a; }
        .crd-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
        .crd-back { color:#94a3b8; text-decoration:none; font-size:12px; font-weight:700; }
        .crd-back:hover { color:#38bdf8; }
        .crd-title { margin:18px 0 4px; font-size:clamp(20px,2.2vw,28px); line-height:1.15; font-weight:800; letter-spacing:-.02em; }
        .crd-subtitle { margin:0; color:#64748b; font-size:12px; }
        .crd-status { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:8px; background:{{ $statusStyle['bg'] }}; color:{{ $statusStyle['color'] }}; font-size:11px; font-weight:800; }
        .crd-status-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }
        .crd-grid { display:grid; grid-template-columns:minmax(0,1fr) 280px; gap:20px; }
        .crd-card { background:#1e293b; border:1px solid rgba(255,255,255,.08); border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.25); overflow:hidden; }
        html:not(.dark) .crd-card { background:#fff; border-color:rgba(15,23,42,.13); box-shadow:0 2px 16px rgba(15,23,42,.1); }
        .crd-card-head { padding:16px 18px; border-bottom:1px solid rgba(148,163,184,.16); font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; }
        .crd-card-body { padding:20px; }
        .crd-reason { margin:0 0 18px; font-size:20px; font-weight:800; line-height:1.25; }
        .crd-details { margin:0; color:#94a3b8; font-size:14px; line-height:1.7; white-space:pre-wrap; overflow-wrap:anywhere; }
        .crd-meta { display:grid; gap:14px; padding:18px; }
        .crd-meta-row { display:flex; justify-content:space-between; gap:12px; padding-bottom:12px; border-bottom:1px solid rgba(148,163,184,.14); font-size:12px; }
        .crd-meta-row:last-child { padding-bottom:0; border-bottom:0; }
        .crd-meta-label { color:#64748b; }
        .crd-meta-value { color:#e2e8f0; font-weight:700; text-align:right; overflow-wrap:anywhere; }
        html:not(.dark) .crd-meta-value { color:#0f172a; }
        .crd-link { color:#38bdf8; font-weight:700; text-decoration:none; }
        .crd-link:hover { text-decoration:underline; }
        .crd-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:20px; }
        .crd-btn { border:0; border-radius:8px; padding:10px 16px; color:#fff; font:700 12px Inter,ui-sans-serif,system-ui,sans-serif; cursor:pointer; }
        .crd-btn:disabled { opacity:.5; cursor:not-allowed; }
        .crd-btn--review { background:#10b981; }
        .crd-btn--dismiss { background:#ef4444; }
        .crd-empty { color:#64748b; font-size:13px; }
        @media (max-width:700px) { .crd-grid { grid-template-columns:1fr; } .crd-card-body { padding:16px; } }
    </style>

    <div class="crd-head">
        <div>
            <a class="crd-back" href="{{ route('filament.admin.pages.content-reports') }}">← Back to content reports</a>
            <h1 class="crd-title">Content Report #{{ $report->id }}</h1>
            <p class="crd-subtitle">Reported {{ $reportedAt ?? '—' }}</p>
        </div>
        <span class="crd-status"><span class="crd-status-dot"></span>{{ $statusStyle['label'] }}</span>
    </div>

    <div class="crd-grid">
        <section class="crd-card">
            <div class="crd-card-head">Report Details</div>
            <div class="crd-card-body">
                <h2 class="crd-reason">{{ $report->reason }}</h2>
                @if($report->details)
                    <p class="crd-details">{{ $report->details }}</p>
                @else
                    <p class="crd-empty">No additional details were provided.</p>
                @endif

                <div class="crd-actions">
                    @if($reportableUrl)
                        <a class="crd-btn" style="background:#2563eb;text-decoration:none;" href="{{ $reportableUrl }}">Open {{ $typeLabel }}</a>
                    @endif
                    @if($canUpdate && $report->status === 'pending')
                        <button class="crd-btn crd-btn--review" type="button" wire:click="markReviewed">Mark Reviewed</button>
                        <button class="crd-btn crd-btn--dismiss" type="button" wire:click="dismiss">Dismiss</button>
                    @endif
                </div>
            </div>
        </section>

        <aside class="crd-card">
            <div class="crd-card-head">Report Information</div>
            <div class="crd-meta">
                <div class="crd-meta-row"><span class="crd-meta-label">Type</span><strong class="crd-meta-value">{{ $typeLabel }}</strong></div>
                <div class="crd-meta-row"><span class="crd-meta-label">Item</span><strong class="crd-meta-value">#{{ $report->reportable_id }}<br>{{ $reportableTitle }}</strong></div>
                <div class="crd-meta-row"><span class="crd-meta-label">Reported by</span><strong class="crd-meta-value">{{ $report->reporter?->name ?? '—' }}<br><span style="font-weight:400;color:#64748b;">{{ $report->reporter?->email ?? '—' }}</span></strong></div>
                @if($report->reviewer)
                    <div class="crd-meta-row"><span class="crd-meta-label">Reviewed by</span><strong class="crd-meta-value">{{ $report->reviewer->name }}<br><span style="font-weight:400;color:#64748b;">{{ $reviewedAt }}</span></strong></div>
                @endif
            </div>
        </aside>
    </div>
</div>
