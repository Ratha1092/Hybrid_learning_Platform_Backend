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
        .hl-payout-page {
            width: 100%;
            color: rgb(226 232 240);
        }
        .hl-payout-page * {
            box-sizing: border-box;
        }
        .hl-payout-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }
        .hl-payout-title {
            font-size: 26px;
            font-weight: 750;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        .hl-payout-subtitle {
            margin-top: 6px;
            color: rgb(100 116 139);
            font-size: 13px;
        }
        .hl-payout-refresh {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: 1px solid rgba(148,163,184,.15);
            background: rgba(30,41,59,.8);
            color: rgb(203 213 225);
            border-radius: 9px;
            padding: 9px 13px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: .15s ease;
        }
        .hl-payout-refresh:hover {
            background: rgba(51,65,85,.9);
            border-color: rgba(148,163,184,.25);
        }
        .hl-payout-tabs-card {
            background: rgb(15 23 42 / .72);
            border: 1px solid rgba(148,163,184,.12);
            border-radius: 14px;
            overflow: hidden;
        }

        .hl-payout-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border-bottom: 1px solid rgba(148,163,184,.10);
            flex-wrap: wrap;
        }

        .hl-payout-tabs {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .hl-payout-tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: 1px solid transparent;
            background: transparent;
            color: rgb(100 116 139);
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .hl-payout-tab:hover {
            color: rgb(226 232 240);
            background: rgba(51,65,85,.45);
        }

        .hl-payout-tab.active {
            background: rgba(37,99,235,.15);
            color: rgb(96 165 250);
            border-color: rgba(37,99,235,.20);
        }

        .hl-payout-count {
            min-width: 19px;
            height: 19px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            background: rgba(148,163,184,.12);
        }

        .hl-payout-search {
            width: 250px;
            max-width: 100%;
        }

        .hl-payout-search input {
            width: 100%;
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid rgba(148,163,184,.15);
            background: rgba(15,23,42,.85);
            color: rgb(226 232 240);
            outline: none;
            font-size: 12px;
        }

        .hl-payout-search input:focus {
            border-color: rgba(37,99,235,.6);
        }

        .hl-payout-table-wrap {
            overflow-x: auto;
        }

        .hl-payout-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .hl-payout-table th {
            padding: 12px 15px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 800;
            color: rgb(100 116 139);
            border-bottom: 1px solid rgba(148,163,184,.10);
            white-space: nowrap;
        }

        .hl-payout-table td {
            padding: 14px 15px;
            border-bottom: 1px solid rgba(148,163,184,.08);
            vertical-align: middle;
        }

        .hl-payout-table tbody tr {
            transition: background .12s ease;
        }

        .hl-payout-table tbody tr:hover {
            background: rgba(51,65,85,.25);
        }

        .hl-payout-id {
            color: rgb(148 163 184);
            font-size: 11px;
            font-weight: 700;
        }

        .hl-payout-user {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .hl-payout-user-name {
            color: rgb(226 232 240);
            font-size: 12.5px;
            font-weight: 700;
        }

        .hl-payout-user-email {
            color: rgb(100 116 139);
            font-size: 11px;
        }

        .hl-payout-amount {
            color: rgb(248 250 252);
            font-size: 13px;
            font-weight: 800;
        }

        .hl-payout-method {
            color: rgb(148 163 184);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
        }

        .hl-payout-date {
            color: rgb(148 163 184);
            font-size: 11px;
            white-space: nowrap;
        }

        .hl-payout-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 7px;
            font-size: 10.5px;
            font-weight: 800;
            white-space: nowrap;
        }

        .hl-payout-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
        }

        .hl-payout-actions {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
        }

        .hl-payout-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            height: 32px;
            border-radius: 8px;
            padding: 0 9px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid transparent;
            cursor: pointer;
            transition: .15s ease;
        }

        .hl-payout-view {
            color: rgb(147 197 253);
            background: rgba(37,99,235,.10);
            border-color: rgba(37,99,235,.18);
        }

        .hl-payout-view:hover {
            background: rgba(37,99,235,.18);
        }

        .hl-payout-approve {
            color: rgb(52 211 153);
            background: rgba(16,185,129,.10);
            border-color: rgba(16,185,129,.18);
        }

        .hl-payout-approve:hover {
            background: rgba(16,185,129,.18);
        }

        .hl-payout-reject {
            color: rgb(248 113 113);
            background: rgba(239,68,68,.10);
            border-color: rgba(239,68,68,.18);
        }

        .hl-payout-reject:hover {
            background: rgba(239,68,68,.18);
        }

        .hl-payout-empty {
            padding: 60px 20px;
            text-align: center;
            color: rgb(100 116 139);
        }

        .hl-payout-empty strong {
            display: block;
            color: rgb(203 213 225);
            margin-bottom: 5px;
            font-size: 13px;
        }

        .hl-payout-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 15px;
            color: rgb(100 116 139);
            font-size: 11px;
            flex-wrap: wrap;
        }

        .hl-payout-pages {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .hl-payout-page-btn {
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border-radius: 7px;
            border: 1px solid rgba(148,163,184,.12);
            background: rgba(30,41,59,.55);
            color: rgb(148 163 184);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .hl-payout-page-btn:hover:not(:disabled) {
            color: white;
            background: rgba(51,65,85,.8);
        }

        .hl-payout-page-btn:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .hl-payout-page-btn.active {
            background: rgb(37 99 235);
            border-color: rgb(37 99 235);
            color: white;
        }

        /*
        
        | Modal
        
        */

        .hl-payout-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .hl-payout-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2,6,23,.68);
        }

        .hl-payout-modal-panel {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 760px;
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            border-radius: 16px;
            border: 1px solid rgba(148,163,184,.18);
            background: rgb(15 23 42);
            box-shadow:
                0 25px 80px rgba(0,0,0,.55);
        }

        .hl-payout-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            padding: 20px 22px;
            border-bottom: 1px solid rgba(148,163,184,.10);
        }

        .hl-payout-modal-title {
            color: rgb(248 250 252);
            font-size: 16px;
            font-weight: 800;
        }

        .hl-payout-modal-subtitle {
            margin-top: 4px;
            color: rgb(100 116 139);
            font-size: 11px;
        }

        .hl-payout-close {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid rgba(148,163,184,.12);
            background: rgba(30,41,59,.65);
            color: rgb(148 163 184);
            cursor: pointer;
            font-size: 17px;
        }

        .hl-payout-close:hover {
            color: white;
            background: rgba(51,65,85,.8);
        }

        .hl-payout-modal-body {
            padding: 22px;
        }

        .hl-payout-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .hl-payout-summary-card {
            padding: 14px;
            border-radius: 11px;
            border: 1px solid rgba(148,163,184,.10);
            background: rgba(30,41,59,.55);
        }

        .hl-payout-summary-label {
            color: rgb(100 116 139);
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .hl-payout-summary-value {
            margin-top: 5px;
            color: rgb(241 245 249);
            font-size: 14px;
            font-weight: 800;
        }

        .hl-payout-section {
            margin-top: 15px;
            padding: 17px;
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,.10);
            background: rgba(30,41,59,.42);
        }

        .hl-payout-section-title {
            margin-bottom: 14px;
            color: rgb(226 232 240);
            font-size: 12px;
            font-weight: 800;
        }

        .hl-payout-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .hl-payout-info-label {
            color: rgb(100 116 139);
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 800;
        }

        .hl-payout-info-value {
            margin-top: 4px;
            color: rgb(226 232 240);
            font-size: 12px;
            word-break: break-word;
        }

        .hl-payout-destination {
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 22px;
            align-items: start;
        }

        .hl-payout-qr {
            width: 100%;
            max-width: 230px;
            margin: 0 auto;
            border-radius: 10px;
            padding: 10px;
            background: white;
            border: 1px solid rgba(148,163,184,.2);
        }

        .hl-payout-no-qr {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 180px;
            border-radius: 10px;
            border: 1px dashed rgba(148,163,184,.18);
            color: rgb(100 116 139);
            font-size: 11px;
            text-align: center;
        }

        .hl-payout-payment-warning {
            margin-top: 15px;
            padding: 12px 14px;
            border-radius: 9px;
            border: 1px solid rgba(245,158,11,.20);
            background: rgba(245,158,11,.08);
            color: rgb(251 191 36);
            font-size: 11px;
            line-height: 1.5;
        }

        .hl-payout-modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            padding: 16px 22px;
            border-top: 1px solid rgba(148,163,184,.10);
        }

        .hl-payout-btn {
            min-height: 36px;
            padding: 0 14px;
            border-radius: 8px;
            border: 1px solid transparent;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .hl-payout-btn-secondary {
            background: rgba(51,65,85,.55);
            border-color: rgba(148,163,184,.12);
            color: rgb(203,213,225);
        }

        .hl-payout-btn-secondary:hover {
            background: rgba(71,85,105,.75);
        }

        .hl-payout-btn-success {
            background: rgb(16 185 129);
            color: white;
        }

        .hl-payout-btn-success:hover {
            background: rgb(5 150 105);
        }

        .hl-payout-btn-danger {
            background: rgb(220 38 38);
            color: white;
        }

        .hl-payout-btn-danger:hover {
            background: rgb(185 28 28);
        }

        .hl-payout-input-label {
            display: block;
            margin-bottom: 7px;
            color: rgb(148 163 184);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .hl-payout-input,
        .hl-payout-textarea {
            width: 100%;
            border: 1px solid rgba(148,163,184,.16);
            border-radius: 9px;
            background: rgb(15 23 42);
            color: rgb(226 232 240);
            outline: none;
            font-size: 12px;
        }

        .hl-payout-input {
            height: 42px;
            padding: 0 12px;
        }

        .hl-payout-textarea {
            min-height: 110px;
            padding: 11px 12px;
            resize: vertical;
        }

        .hl-payout-input:focus,
        .hl-payout-textarea:focus {
            border-color: rgba(37,99,235,.65);
        }

        .hl-payout-help {
            margin-top: 7px;
            color: rgb(100 116 139);
            font-size: 10px;
            line-height: 1.5;
        }

        @media (max-width: 800px) {
            .hl-payout-summary {
                grid-template-columns: 1fr;
            }

            .hl-payout-destination {
                grid-template-columns: 1fr;
            }

            .hl-payout-info-grid {
                grid-template-columns: 1fr;
            }

            .hl-payout-header {
                flex-direction: column;
            }
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
                                    <button
                                        type="button"
                                        class="hl-payout-action hl-payout-view"
                                        wire:click="openDetails({{ $payout->id }})"
                                    >
                                        View
                                    </button>
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