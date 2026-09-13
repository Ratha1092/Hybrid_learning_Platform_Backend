@php
    $statusStyle = fn ($status) => match ($status) {
        'pending' => [
            'bg' => 'rgba(245,158,11,.12)',
            'color' => '#f59e0b',
            'label' => 'Pending',
        ],

        'approved' => [
            'bg' => 'rgba(16,185,129,.12)',
            'color' => '#10b981',
            'label' => 'Completed',
        ],

        'rejected' => [
            'bg' => 'rgba(239,68,68,.12)',
            'color' => '#ef4444',
            'label' => 'Rejected',
        ],

        default => [
            'bg' => 'rgba(148,163,184,.12)',
            'color' => '#94a3b8',
            'label' => ucfirst($status ?? 'Unknown'),
        ],
    };

    $selectedAccount = $selectedPayout?->payoutAccount;
    $selectedQr = $selectedAccount?->qr_code_url;
    $selectedDetails = $selectedPayout?->details ?? [];
    $currency = fn ($amount, $code) =>
        number_format((float) $amount, 2) . ' ' . strtoupper($code ?: 'USD');
@endphp

<div
    class="hl-payout-page"
    @if(!$modal)
        wire:poll.30s
    @endif
>
<style>
.fi-main:has(.hl-payout-page){
    padding-inline:0 !important;
}
.fi-page-header-main-ctn:has(.hl-payout-page){
    padding-block:0 !important;
    row-gap:0 !important;
}
.fi-page-content:has(.hl-payout-page){
    row-gap:0 !important;
}
.hl-payout-page,.hl-payout-page *,.hl-payout-page *::before,.hl-payout-page *::after{box-sizing:border-box}
.hl-payout-page{
    width:100%;
    min-height:100vh;
    padding:16px 20px 48px;
    display:flex;
    flex-direction:column;
    gap:16px;
    background:var(--hl-payout-bg);
    color:var(--hl-payout-text);
    --hl-payout-bg:#0f172a;
    --hl-payout-card:#1e293b;
    --hl-payout-card-2:#263245;
    --hl-payout-card-3:#334155;
    --hl-payout-border:rgba(255,255,255,.08);
    --hl-payout-border-strong:rgba(255,255,255,.14);
    --hl-payout-text:#e2e8f0;
    --hl-payout-text-strong:#f8fafc;
    --hl-payout-muted:#64748b;
    --hl-payout-muted-2:#94a3b8;
    --hl-payout-input:#0f172a;
    --hl-payout-hover:rgba(51,65,85,.35);
    --hl-payout-shadow:0 25px 80px rgba(0,0,0,.45);
    --hl-payout-accent:#2563eb;
}
html:not(.dark) .hl-payout-page{
    --hl-payout-bg:#f1f5f9;
    --hl-payout-card:#ffffff;
    --hl-payout-card-2:#f8fafc;
    --hl-payout-card-3:#e2e8f0;
    --hl-payout-border:rgba(15,23,42,.10);
    --hl-payout-border-strong:rgba(15,23,42,.18);
    --hl-payout-text:#334155;
    --hl-payout-text-strong:#0f172a;
    --hl-payout-muted:#64748b;
    --hl-payout-muted-2:#64748b;
    --hl-payout-input:#ffffff;
    --hl-payout-hover:#f8fafc;
    --hl-payout-shadow:0 25px 80px rgba(15,23,42,.20);
}
.hl-payout-header{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:20px;
    margin-bottom:0;
}
.hl-payout-title{
    margin:0;
    font-size:26px;
    font-weight:750;
    line-height:1.2;
    letter-spacing:-.02em;
    color:var(--hl-payout-text-strong);
}
.hl-payout-subtitle{
    margin:6px 0 0;
    color:var(--hl-payout-muted);
    font-size:13px;
}
.hl-payout-refresh{
    display:inline-flex;
    align-items:center;
    gap:7px;
    border:1px solid var(--hl-payout-border-strong);
    background:var(--hl-payout-card);
    color:var(--hl-payout-text);
    border-radius:9px;
    padding:9px 13px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    transition:.15s ease;
}
.hl-payout-refresh:hover{
    background:var(--hl-payout-card-2);
    border-color:var(--hl-payout-border-strong);
}
.hl-payout-tabs-card{
    background:var(--hl-payout-card);
    border:1px solid var(--hl-payout-border);
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
}
.hl-payout-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    padding:14px 16px;
    border-bottom:1px solid var(--hl-payout-border);
    flex-wrap:wrap;
}
.hl-payout-tabs{
    display:flex;
    gap:5px;
    flex-wrap:wrap;
}
.hl-payout-tab{
    display:inline-flex;
    align-items:center;
    gap:7px;
    border:1px solid transparent;
    background:transparent;
    color:var(--hl-payout-muted);
    padding:7px 12px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    transition:.15s ease;
}
.hl-payout-tab:hover{
    color:var(--hl-payout-text-strong);
    background:var(--hl-payout-hover);
}
.hl-payout-tab.active{
    background:rgba(37,99,235,.12);
    color:#2563eb;
    border-color:rgba(37,99,235,.18);
}
.dark .hl-payout-tab.active{
    color:#60a5fa;
}
.hl-payout-count{
    min-width:19px;
    height:19px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0 5px;
    border-radius:6px;
    font-size:10px;
    font-weight:800;
    background:var(--hl-payout-card-2);
}
.hl-payout-search{
    width:250px;
    max-width:100%;
}
.hl-payout-search input{
    width:100%;
    height:36px;
    padding:0 12px;
    border-radius:8px;
    border:1px solid var(--hl-payout-border-strong);
    background:var(--hl-payout-input);
    color:var(--hl-payout-text-strong);
    outline:none;
    font-size:12px;
}
.hl-payout-search input::placeholder{
    color:var(--hl-payout-muted);
}
.hl-payout-search input:focus{
    border-color:rgba(37,99,235,.6);
    box-shadow:0 0 0 3px rgba(37,99,235,.08);
}
.hl-payout-table-wrap{
    overflow-x:auto;
}
.hl-payout-table{
    width:100%;
    min-width:900px;
    border-collapse:collapse;
}
.hl-payout-table th{
    padding:12px 15px;
    text-align:left;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:.07em;
    font-weight:800;
    color:var(--hl-payout-muted);
    border-bottom:1px solid var(--hl-payout-border);
    white-space:nowrap;
}
.hl-payout-table td{
    padding:14px 15px;
    border-bottom:1px solid var(--hl-payout-border);
    vertical-align:middle;
}
.hl-payout-table tbody tr{
    transition:background .12s ease;
}
.hl-payout-table tbody tr:hover{
    background:var(--hl-payout-hover);
}
.hl-payout-id{
    color:var(--hl-payout-muted-2);
    font-size:11px;
    font-weight:700;
}
.hl-payout-user{
    display:flex;
    flex-direction:column;
    gap:2px;
}
.hl-payout-user-name{
    color:var(--hl-payout-text-strong);
    font-size:12.5px;
    font-weight:700;
}
.hl-payout-user-email{
    color:var(--hl-payout-muted);
    font-size:11px;
}
.hl-payout-amount{
    color:var(--hl-payout-text-strong);
    font-size:13px;
    font-weight:800;
}
.hl-payout-method{
    color:var(--hl-payout-muted-2);
    text-transform:uppercase;
    font-size:11px;
    font-weight:700;
}
.hl-payout-date{
    color:var(--hl-payout-muted-2);
    font-size:11px;
    white-space:nowrap;
}
.hl-payout-status{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:5px 9px;
    border-radius:7px;
    font-size:10.5px;
    font-weight:800;
    white-space:nowrap;
}
.hl-payout-status-dot{
    width:6px;
    height:6px;
    border-radius:999px;
}
.hl-payout-actions{
    display:flex;
    justify-content:flex-end;
    gap:5px;
}
.hl-payout-action{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    height:32px;
    border-radius:8px;
    padding:0 9px;
    font-size:11px;
    font-weight:800;
    border:1px solid transparent;
    cursor:pointer;
    transition:.15s ease;
}
.hl-payout-view{
    color:#2563eb;
    background:rgba(37,99,235,.08);
    border-color:rgba(37,99,235,.16);
}
.dark .hl-payout-view{
    color:#93c5fd;
    background:rgba(37,99,235,.10);
    border-color:rgba(37,99,235,.18);
}
.hl-payout-view:hover{
    background:rgba(37,99,235,.16);
}
.hl-payout-approve{
    color:#059669;
    background:rgba(16,185,129,.08);
    border-color:rgba(16,185,129,.18);
}
.dark .hl-payout-approve{
    color:#34d399;
    background:rgba(16,185,129,.10);
}
.hl-payout-approve:hover{
    background:rgba(16,185,129,.16);
}
.hl-payout-reject{
    color:#dc2626;
    background:rgba(239,68,68,.08);
    border-color:rgba(239,68,68,.16);
}
.dark .hl-payout-reject{
    color:#f87171;
    background:rgba(239,68,68,.10);
}
.hl-payout-reject:hover{
    background:rgba(239,68,68,.16);
}
.hl-payout-empty{
    padding:60px 20px;
    text-align:center;
    color:var(--hl-payout-muted);
}
.hl-payout-empty strong{
    display:block;
    color:var(--hl-payout-text-strong);
    margin-bottom:5px;
    font-size:13px;
}
.hl-payout-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:13px 15px;
    color:var(--hl-payout-muted);
    font-size:11px;
    flex-wrap:wrap;
}
.hl-payout-pages{
    display:flex;
    align-items:center;
    gap:5px;
}
.hl-payout-page-btn{
    min-width:30px;
    height:30px;
    padding:0 8px;
    border-radius:7px;
    border:1px solid var(--hl-payout-border-strong);
    background:var(--hl-payout-card-2);
    color:var(--hl-payout-muted-2);
    font-size:11px;
    font-weight:700;
    cursor:pointer;
}
.hl-payout-page-btn:hover:not(:disabled){
    color:var(--hl-payout-text-strong);
    background:var(--hl-payout-card-3);
}
.hl-payout-page-btn:disabled{
    opacity:.35;
    cursor:not-allowed;
}
.hl-payout-page-btn.active{
    background:#2563eb;
    border-color:#2563eb;
    color:white;
}
.hl-payout-modal{
    position:fixed;
    inset:0;
    z-index:99999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
}
.hl-payout-modal-backdrop{
    position:absolute;
    inset:0;
    background:rgba(2,6,23,.68);
}
html:not(.dark) .hl-payout-modal-backdrop{
    background:rgba(15,23,42,.45);
}
.hl-payout-modal-panel{
    position:relative;
    z-index:1;
    width:100%;
    max-width:760px;
    max-height:calc(100vh - 48px);
    overflow-y:auto;
    border-radius:16px;
    border:1px solid var(--hl-payout-border-strong);
    background:var(--hl-payout-card);
    box-shadow:var(--hl-payout-shadow);
}
.hl-payout-modal-header{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:15px;
    padding:20px 22px;
    border-bottom:1px solid var(--hl-payout-border);
}
.hl-payout-modal-title{
    color:var(--hl-payout-text-strong);
    font-size:16px;
    font-weight:800;
}
.hl-payout-modal-subtitle{
    margin-top:4px;
    color:var(--hl-payout-muted);
    font-size:11px;
}
.hl-payout-close{
    width:32px;
    height:32px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    border:1px solid var(--hl-payout-border-strong);
    background:var(--hl-payout-card-2);
    color:var(--hl-payout-muted-2);
    cursor:pointer;
    font-size:17px;
}
.hl-payout-close:hover{
    color:var(--hl-payout-text-strong);
    background:var(--hl-payout-card-3);
}
.hl-payout-modal-body{
    padding:22px;
}
.hl-payout-summary{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:10px;
    margin-bottom:18px;
}
.hl-payout-summary-card{
    padding:14px;
    border-radius:11px;
    border:1px solid var(--hl-payout-border);
    background:var(--hl-payout-card-2);
}
.hl-payout-summary-label{
    color:var(--hl-payout-muted);
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.07em;
}
.hl-payout-summary-value{
    margin-top:5px;
    color:var(--hl-payout-text-strong);
    font-size:14px;
    font-weight:800;
}
.hl-payout-section{
    margin-top:15px;
    padding:17px;
    border-radius:12px;
    border:1px solid var(--hl-payout-border);
    background:var(--hl-payout-card-2);
}
.hl-payout-section-title{
    margin-bottom:14px;
    color:var(--hl-payout-text-strong);
    font-size:12px;
    font-weight:800;
}
.hl-payout-info-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
}
.hl-payout-info-label{
    color:var(--hl-payout-muted);
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.07em;
    font-weight:800;
}
.hl-payout-info-value{
    margin-top:4px;
    color:var(--hl-payout-text);
    font-size:12px;
    word-break:break-word;
}
.hl-payout-destination{
    display:grid;
    grid-template-columns:1fr 240px;
    gap:22px;
    align-items:start;
}
.hl-payout-qr{
    width:100%;
    max-width:230px;
    margin:0 auto;
    display:block;
    border-radius:10px;
    padding:10px;
    background:white;
    border:1px solid var(--hl-payout-border-strong);
}
.hl-payout-no-qr{
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:180px;
    border-radius:10px;
    border:1px dashed var(--hl-payout-border-strong);
    color:var(--hl-payout-muted);
    font-size:11px;
    text-align:center;
}
.hl-payout-payment-warning{
    margin-top:15px;
    padding:12px 14px;
    border-radius:9px;
    border:1px solid rgba(245,158,11,.20);
    background:rgba(245,158,11,.08);
    color:#b45309;
    font-size:11px;
    line-height:1.5;
}
.dark .hl-payout-payment-warning{
    color:#fbbf24;
}
.hl-payout-modal-footer{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    padding:16px 22px;
    border-top:1px solid var(--hl-payout-border);
}
.hl-payout-btn{
    min-height:36px;
    padding:0 14px;
    border-radius:8px;
    border:1px solid transparent;
    font-size:11px;
    font-weight:800;
    cursor:pointer;
}
.hl-payout-btn-secondary{
    background:var(--hl-payout-card-3);
    border-color:var(--hl-payout-border-strong);
    color:var(--hl-payout-text);
}
html:not(.dark) .hl-payout-btn-secondary{
    background:var(--hl-payout-card);
}
.hl-payout-btn-secondary:hover{
    background:var(--hl-payout-card-3);
}
.hl-payout-btn-success{
    background:#10b981;
    color:white;
}
.hl-payout-btn-success:hover{
    background:#059669;
}
.hl-payout-btn-danger{
    background:#dc2626;
    color:white;
}
.hl-payout-btn-danger:hover{
    background:#b91c1c;
}
.hl-payout-input-label{
    display:block;
    margin-bottom:7px;
    color:var(--hl-payout-muted-2);
    font-size:10px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.07em;
}
.hl-payout-input,.hl-payout-textarea{
    width:100%;
    border:1px solid var(--hl-payout-border-strong);
    border-radius:9px;
    background:var(--hl-payout-input);
    color:var(--hl-payout-text-strong);
    outline:none;
    font-size:12px;
}
.hl-payout-input{
    height:42px;
    padding:0 12px;
}
.hl-payout-textarea{
    min-height:110px;
    padding:11px 12px;
    resize:vertical;
}
.hl-payout-input::placeholder,.hl-payout-textarea::placeholder{
    color:var(--hl-payout-muted);
}
.hl-payout-input:focus,.hl-payout-textarea:focus{
    border-color:rgba(37,99,235,.65);
    box-shadow:0 0 0 3px rgba(37,99,235,.08);
}
.hl-payout-help{
    margin-top:7px;
    color:var(--hl-payout-muted);
    font-size:10px;
    line-height:1.5;
}
@media(max-width:800px){
    .hl-payout-summary{grid-template-columns:1fr}
    .hl-payout-destination{grid-template-columns:1fr}
    .hl-payout-info-grid{grid-template-columns:1fr}
    .hl-payout-header{flex-direction:column}
}
</style>


    {{--PAGE HEADER --}}

    <div class="hl-payout-header">

        <div>
            <h1 class="hl-payout-title">
                Instructor Payouts
            </h1>
            <p class="hl-payout-subtitle">
                Review payout requests, verify payment destinations,
                and record completed instructor payments.
            </p>
        </div>
        <button
            type="button"
            class="hl-payout-refresh"
            wire:click="$refresh"
        >
            ↻
            Refresh
        </button>

    </div>


    {{--MAIN CARD --}}

    <div class="hl-payout-tabs-card">
        {{-- Toolbar --}}
        <div class="hl-payout-toolbar">
            <div class="hl-payout-tabs">
                @foreach($tabs as $item)
                    <button
                        type="button"
                        class="hl-payout-tab {{ $tab === $item['key'] ? 'active' : '' }}"
                        wire:click="selectTab('{{ $item['key'] }}')"
                    >
                        {{ $item['label'] }}
                        <span
                            class="hl-payout-count"
                            style="
                                color: {{ $item['color'] }};
                            "
                        >
                            {{ $item['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>
            <div class="hl-payout-search">
                <input
                    type="search"
                    placeholder="Search instructor..."
                    wire:model.live.debounce.400ms="search"
                >
            </div>
        </div>
        {{--TABLE--}}
        <div class="hl-payout-table-wrap">
            <table class="hl-payout-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Instructor</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th style="text-align:right;">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                        @php
                            $style = $statusStyle($payout->status);
                        @endphp
                        <tr wire:key="payout-{{ $payout->id }}">
                            <td>
                                <span class="hl-payout-id">
                                    #{{ $payout->id }}
                                </span>
                            </td>
                            <td>
                                <div class="hl-payout-user">
                                    <span class="hl-payout-user-name">
                                        {{ $payout->instructor?->name ?? 'Unknown instructor' }}
                                    </span>
                                    <span class="hl-payout-user-email">
                                        {{ $payout->instructor?->email ?? '—' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="hl-payout-amount">
                                    {{ $currency($payout->amount, $payout->currency) }}
                                </span>
                            </td>
                            <td>
                                <span class="hl-payout-method">
                                    {{ strtoupper($payout->payment_method ?? '—') }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="hl-payout-status"
                                    style="
                                        background: {{ $style['bg'] }};
                                        color: {{ $style['color'] }};
                                    "
                                >
                                    <span
                                        class="hl-payout-status-dot"
                                        style="
                                            background: {{ $style['color'] }};
                                        "
                                    ></span>
                                    {{ $style['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="hl-payout-date">
                                    {{ optional($payout->requested_at ?? $payout->created_at)->format('M d, Y H:i') }}
                                </span>
                            </td>
                            <td>
                                <div class="hl-payout-actions">
                                    <a
                                        href="{{ route('filament.admin.resources.payout-requests.view', ['record' => $payout->id]) }}"
                                        class="hl-payout-action hl-payout-view"
                                        wire:navigate
                                    >
                                        View
                                    </a>
                                    @if($canUpdate && $payout->status === 'pending')
                                        <button
                                            type="button"
                                            class="hl-payout-action hl-payout-approve"
                                            wire:click="openApprove({{ $payout->id }})"
                                        >
                                            Pay
                                        </button>
                                        <button
                                            type="button"
                                            class="hl-payout-action hl-payout-reject"
                                            wire:click="openReject({{ $payout->id }})"
                                        >
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                class="hl-payout-empty"
                            >
                                <strong>
                                    No payout requests found
                                </strong>
                                There are no payout requests matching
                                the current filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{--  FOOTER --}}
        <div class="hl-payout-footer">
            <div>
                Showing page {{ $curPage }}
                of {{ $totalPages }}
                · {{ $total }} total payouts
            </div>
            <div class="hl-payout-pages">
                <button
                    type="button"
                    class="hl-payout-page-btn"
                    wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                    @disabled($curPage <= 1)
                >
                    ←
                </button>
                @for($i = max(1, $curPage - 2); $i <= min($totalPages, $curPage + 2); $i++)
                    <button
                        type="button"
                        class="hl-payout-page-btn {{ $i === $curPage ? 'active' : '' }}"
                        wire:click="gotoPage({{ $i }})"
                    >
                        {{ $i }}
                    </button>
                @endfor
                <button
                    type="button"
                    class="hl-payout-page-btn"
                    wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                    @disabled($curPage >= $totalPages)
                >
                    →
                </button>
            </div>
        </div>
    </div>
    {{--DETAILS MODAL --}}
    @if($modal === 'details' && $selectedPayout)
        @php
            $detailStyle = $statusStyle($selectedPayout->status);
            $detailAccount = $selectedPayout->payoutAccount;
            $detailQr = $detailAccount?->qr_code_url;
        @endphp
        <div class="hl-payout-modal">
            <div
                class="hl-payout-modal-backdrop"
                wire:click="closeModal"
            ></div>
            <div class="hl-payout-modal-panel">
                <div class="hl-payout-modal-header">
                    <div>
                        <div class="hl-payout-modal-title">
                            Payout #{{ $selectedPayout->id }}
                        </div>
                        <div class="hl-payout-modal-subtitle">
                            Review payout details before processing.
                        </div>
                    </div>
                    <button
                        type="button"
                        class="hl-payout-close"
                        wire:click="closeModal"
                    >
                        ×
                    </button>
                </div>
                <div class="hl-payout-modal-body">
                    {{-- Summary --}}
                    <div class="hl-payout-summary">
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Amount
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ $currency($selectedPayout->amount, $selectedPayout->currency) }}
                            </div>
                        </div>
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Status
                            </div>
                            <div
                                class="hl-payout-summary-value"
                                style="color: {{ $detailStyle['color'] }}"
                            >
                                {{ $detailStyle['label'] }}
                            </div>
                        </div>
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Method
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ strtoupper($selectedPayout->payment_method ?? '—') }}
                            </div>
                        </div>
                    </div>
                    {{-- Instructor --}}
                    <div class="hl-payout-section">
                        <div class="hl-payout-section-title">
                            Instructor
                        </div>
                        <div class="hl-payout-info-grid">
                            <div>
                                <div class="hl-payout-info-label">
                                    Name
                                </div>
                                <div class="hl-payout-info-value">
                                    {{ $selectedPayout->instructor?->name ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="hl-payout-info-label">
                                    Email
                                </div>
                                <div class="hl-payout-info-value">
                                    {{ $selectedPayout->instructor?->email ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="hl-payout-info-label">
                                    Requested
                                </div>
                                <div class="hl-payout-info-value">
                                    {{ optional($selectedPayout->requested_at ?? $selectedPayout->created_at)->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div>
                                <div class="hl-payout-info-label">
                                    Source
                                </div>
                                <div class="hl-payout-info-value">
                                    {{ ucfirst(str_replace('_', ' ', $selectedPayout->source ?? 'manual')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Payout destination --}}
                    <div class="hl-payout-section">
                        <div class="hl-payout-section-title">
                            Payout Destination
                        </div>
                        <div class="hl-payout-destination">
                            <div>
                                <div class="hl-payout-info-grid">
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Payment method
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ strtoupper($detailAccount?->method ?? $selectedPayout->payment_method ?? '—') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Account name
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $detailAccount?->account_name ?? ($selectedDetails['account_name'] ?? '—') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Account number
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $detailAccount?->account_number ?? ($selectedDetails['account_number'] ?? '—') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Phone number
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $detailAccount?->phone_number ?? ($selectedDetails['phone_number'] ?? '—') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="hl-payout-payment-warning">
                                    Verify the recipient name and destination carefully
                                    before sending the payout. The external payment
                                    should be completed before marking this request
                                    as completed.
                                </div>
                            </div>
                            <div>
                                @if($detailQr)
                                    <img
                                        src="{{ $detailQr }}"
                                        alt="Payout QR code"
                                        class="hl-payout-qr"
                                    >
                                @else
                                    <div class="hl-payout-no-qr">
                                        No QR code available
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Existing transaction reference --}}
                    @if($selectedPayout->transaction_reference)
                        <div class="hl-payout-section">
                            <div class="hl-payout-section-title">
                                Transaction
                            </div>
                            <div class="hl-payout-info-grid">
                                <div>
                                    <div class="hl-payout-info-label">
                                        Reference
                                    </div>
                                    <div class="hl-payout-info-value">
                                        {{ $selectedPayout->transaction_reference }}
                                    </div>
                                </div>
                                <div>
                                    <div class="hl-payout-info-label">
                                        Processed at
                                    </div>
                                    <div class="hl-payout-info-value">
                                        {{ optional($selectedPayout->processed_at)->format('M d, Y H:i') ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- Rejection --}}
                    @if($selectedPayout->rejection_reason)
                        <div class="hl-payout-section">
                            <div class="hl-payout-section-title">
                                Rejection reason
                            </div>
                            <div class="hl-payout-info-value">
                                {{ $selectedPayout->rejection_reason }}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="hl-payout-modal-footer">
                    <button
                        type="button"
                        class="hl-payout-btn hl-payout-btn-secondary"
                        wire:click="closeModal"
                    >
                        Close
                    </button>
                    @if($canUpdate && $selectedPayout->status === 'pending')
                        <button
                            type="button"
                            class="hl-payout-btn hl-payout-btn-danger"
                            wire:click="openReject({{ $selectedPayout->id }})"
                        >
                            Reject
                        </button>
                        <button
                            type="button"
                            class="hl-payout-btn hl-payout-btn-success"
                            wire:click="openApprove({{ $selectedPayout->id }})"
                        >
                            Pay & Complete
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
    {{--APPROVE MODAL --}}
    @if($modal === 'approve' && $selectedPayout)
        @php
            $approveAccount = $selectedPayout->payoutAccount;
            $approveQr = $approveAccount?->qr_code_url;
        @endphp

        <div class="hl-payout-modal">
            <div
                class="hl-payout-modal-backdrop"
                wire:click="closeModal"
            ></div>
            <div class="hl-payout-modal-panel">
                <div class="hl-payout-modal-header">
                    <div>
                        <div class="hl-payout-modal-title">
                            Complete Payout
                        </div>
                        <div class="hl-payout-modal-subtitle">
                            Verify the payment destination before confirming.
                        </div>
                    </div>
                    <button
                        type="button"
                        class="hl-payout-close"
                        wire:click="closeModal"
                    >
                        ×
                    </button>
                </div>
                <div class="hl-payout-modal-body">
                    <div class="hl-payout-summary">
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Amount to pay
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ $currency($selectedPayout->amount, $selectedPayout->currency) }}
                            </div>
                        </div>
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Instructor
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ $selectedPayout->instructor?->name ?? '—' }}
                            </div>
                        </div>
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Method
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ strtoupper($selectedPayout->payment_method ?? '—') }}
                            </div>
                        </div>
                    </div>
                    <div class="hl-payout-section">
                        <div class="hl-payout-section-title">
                            Payment Destination
                        </div>
                        <div class="hl-payout-destination">
                            <div>
                                <div class="hl-payout-info-grid">
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Account name
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $approveAccount?->account_name ?? ($selectedPayout->details['account_name'] ?? '—') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Account number
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $approveAccount?->account_number ?? ($selectedPayout->details['account_number'] ?? '—') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Phone
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $approveAccount?->phone_number ?? ($selectedPayout->details['phone_number'] ?? '—') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="hl-payout-info-label">
                                            Instructor
                                        </div>
                                        <div class="hl-payout-info-value">
                                            {{ $selectedPayout->instructor?->name ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($approveQr)
                                    <img
                                        src="{{ $approveQr }}"
                                        alt="Payout QR code"
                                        class="hl-payout-qr"
                                    >
                                @else
                                    <div class="hl-payout-no-qr">
                                        No QR code available
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="hl-payout-section">
                        <div class="hl-payout-section-title">
                            Payment confirmation
                        </div>
                        <label class="hl-payout-input-label">
                            Transaction reference *
                        </label>
                        <input
                            type="text"
                            class="hl-payout-input"
                            wire:model="approveReference"
                            placeholder="e.g. KHQR transaction ID / bank transfer reference"
                            maxlength="150"
                        >
                        <div class="hl-payout-help">
                            First complete the external payment. Then enter the
                            transaction reference here. This reference becomes part
                            of the payout audit record.
                        </div>
                    </div>
                </div>
                <div class="hl-payout-modal-footer">
                    <button
                        type="button"
                        class="hl-payout-btn hl-payout-btn-secondary"
                        wire:click="closeModal"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="hl-payout-btn hl-payout-btn-success"
                        wire:click="approve"
                        wire:loading.attr="disabled"
                        wire:target="approve"
                    >
                        <span wire:loading.remove wire:target="approve">
                            Confirm Payout
                        </span>
                        <span wire:loading wire:target="approve">
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    @endif


    {{--REJECT MODAL --}}

    @if($modal === 'reject' && $selectedPayout)

        <div class="hl-payout-modal">
            <div
                class="hl-payout-modal-backdrop"
                wire:click="closeModal"
            ></div>
            <div class="hl-payout-modal-panel">
                <div class="hl-payout-modal-header">
                    <div>
                        <div class="hl-payout-modal-title">
                            Reject Payout
                        </div>
                        <div class="hl-payout-modal-subtitle">
                            The payout amount will be returned to the instructor wallet.
                        </div>
                    </div>
                    <button
                        type="button"
                        class="hl-payout-close"
                        wire:click="closeModal"
                    >
                        ×
                    </button>
                </div>
                <div class="hl-payout-modal-body">
                    <div class="hl-payout-summary">
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Payout
                            </div>
                            <div class="hl-payout-summary-value">
                                #{{ $selectedPayout->id }}
                            </div>
                        </div>
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Instructor
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ $selectedPayout->instructor?->name ?? '—' }}
                            </div>
                        </div>
                        <div class="hl-payout-summary-card">
                            <div class="hl-payout-summary-label">
                                Amount
                            </div>
                            <div class="hl-payout-summary-value">
                                {{ $currency($selectedPayout->amount, $selectedPayout->currency) }}
                            </div>
                        </div>
                    </div>
                    <div class="hl-payout-section">
                        <label class="hl-payout-input-label">
                            Rejection reason *
                        </label>
                        <textarea
                            class="hl-payout-textarea"
                            wire:model="rejectReason"
                            maxlength="1000"
                            placeholder="Explain why this payout cannot be processed..."
                        ></textarea>
                        <div class="hl-payout-help">
                            The instructor will receive this reason with the
                            payout rejection notification.
                        </div>
                    </div>
                </div>
                <div class="hl-payout-modal-footer">
                    <button
                        type="button"
                        class="hl-payout-btn hl-payout-btn-secondary"
                        wire:click="closeModal"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="hl-payout-btn hl-payout-btn-danger"
                        wire:click="reject"
                        wire:loading.attr="disabled"
                        wire:target="reject"
                    >
                        <span wire:loading.remove wire:target="reject">
                            Reject Payout
                        </span>
                        <span wire:loading wire:target="reject">
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>