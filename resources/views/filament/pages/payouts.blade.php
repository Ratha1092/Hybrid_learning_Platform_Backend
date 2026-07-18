@php
    $accent = '#2563eb';

    $statusStyle = fn($status) => match($status) {
        'pending'  => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
        'approved' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Approved'],
        'rejected' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Rejected'],
        default    => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($status ?? '—')],
    };
@endphp

<div wire:poll.30s>
<div class="po" id="po-payouts" style="--accent:{{ $accent }};">

<style>
.po, .po *, .po *::before, .po *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.po {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:48px;
    display:grid;
    gap:20px;
    --bg:#0f172a;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .po {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes poUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.poa {
    opacity:0;
    animation:poUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.po1 {
    animation-delay:.04s;
}
.po2 {
    animation-delay:.09s;
}

.po-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.po-header-text h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.15;
}
.po-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}

.po-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
    box-shadow:var(--sh);
}
.po-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.po-tabs {
    display:flex;
    align-items:center;
    gap:4px;
    flex-wrap:wrap;
}
.po-tab {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:6px 13px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    color:var(--t2);
    background:none;
    font-family:inherit;
    border:1px solid transparent;
    transition:background .15s, color .15s, border-color .15s;
}
.po-tab:hover {
    background:var(--p2);
    color:var(--t1);
}
.po-tab-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:18px;
    height:18px;
    padding:0 5px;
    border-radius:5px;
    font-size:10px;
    font-weight:800;
}
.po-search-box {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.po-search-box svg {
    width:14px;
    height:14px;
    color:var(--t2);
    flex-shrink:0;
}
.po-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.po-search-box input::placeholder {
    color:var(--t2);
}

.po-table {
    width:100%;
    border-collapse:collapse;
}
.po-table thead tr {
    border-bottom:1px solid var(--bd);
}
.po-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.po-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.po-table tbody tr:last-child {
    border-bottom:none;
}
.po-table tbody tr:hover {
    background:var(--p2);
}
.po-table td {
    padding:12px 12px;
    vertical-align:middle;
}
.po-row-link {
    cursor:pointer;
}

.po-id {
    font-size:11.5px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.po-user-cell {
    display:flex;
    align-items:center;
    gap:8px;
}
.po-user-name {
    font-size:12.5px;
    color:var(--t1);
    font-weight:500;
}
.po-email {
    font-size:12px;
    color:var(--t2);
}
.po-amount {
    font-size:13px;
    color:var(--t1);
    font-weight:700;
}
.po-method {
    font-size:12px;
    color:var(--t2);
    text-transform:capitalize;
}
.po-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}
.po-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.po-date {
    font-size:12px;
    color:var(--t2);
    white-space:nowrap;
}

.po-actions {
    display:flex;
    align-items:center;
    gap:4px;
    justify-content:flex-end;
}
.po-act-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:36px;
    height:36px;
    border-radius:8px;
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    color:var(--t2);
    text-decoration:none;
    transition:background .15s, border-color .15s, color .15s;
}
.po-act-btn:hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.po-act-btn svg {
    width:18px;
    height:18px;
}
.po-act-btn-approve {
    color:#34d399;
}
.po-act-btn-approve:hover {
    background:rgba(52,211,153,.12) !important;
    border-color:rgba(52,211,153,.3) !important;
    color:#34d399 !important;
}
.po-act-btn-reject {
    color:#f87171;
}
.po-act-btn-reject:hover {
    background:rgba(248,113,113,.12) !important;
    border-color:rgba(248,113,113,.3) !important;
    color:#f87171 !important;
}

.po-modal-overlay {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(15,23,42,.35);
    backdrop-filter:blur(3px);
    -webkit-backdrop-filter:blur(3px);
    z-index:9999;
    align-items:center;
    justify-content:center;
    padding:20px;
}
.po-modal-overlay.open {
    display:flex;
}
@keyframes poModalIn {
    from {
        opacity:0;
        transform:translateY(8px) scale(.98);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.po-modal {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:14px;
    padding:26px;
    width:100%;
    max-width:460px;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
    animation:poModalIn .18s cubic-bezier(.16,1,.3,1) forwards;
}
.po-modal h3 {
    font-size:15px;
    font-weight:750;
    color:var(--t1);
    margin-bottom:6px;
}
.po-modal p {
    font-size:12.5px;
    color:var(--t2);
    margin-bottom:16px;
}
.po-modal textarea {
    width:100%;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:9px;
    padding:10px 13px;
    color:var(--t1);
    font-size:13px;
    font-family:inherit;
    resize:vertical;
    min-height:90px;
    outline:none;
}
.po-modal textarea:focus {
    border-color:#2563eb;
}
.po-modal-footer {
    display:flex;
    justify-content:flex-end;
    gap:8px;
    margin-top:14px;
}
.po-modal-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:9px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    border:none;
    font-family:inherit;
    transition:opacity .15s;
}
.po-modal-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.po-modal-btn-danger {
    background:rgba(248,113,113,.15);
    color:#f87171;
    border:1px solid rgba(248,113,113,.3);
}
.po-modal-btn-success {
    background:rgba(52,211,153,.15);
    color:#34d399;
    border:1px solid rgba(52,211,153,.3);
}

.po-field {
    display:flex;
    flex-direction:column;
    gap:3px;
}
.po-field-label {
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.07em;
    text-transform:uppercase;
    color:var(--t2);
}
.po-field-value {
    font-size:13px;
    color:var(--t1);
    word-break:break-word;
}
.po-qr-img {
    width:100%;
    max-width:220px;
    border-radius:8px;
    margin-top:6px;
    border:1px solid var(--bd2);
    background:#fff;
    padding:8px;
}

.po-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.po-empty svg {
    width:40px;
    height:40px;
    opacity:.35;
}
.po-empty p {
    font-size:13px;
}

.po-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.po-footer-info {
    font-size:12px;
    color:var(--t2);
}
.po-pages {
    display:flex;
    align-items:center;
    gap:6px;
}
.po-page-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:30px;
    height:30px;
    padding:0 8px;
    border-radius:7px;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    color:var(--t2);
    background:none;
    font-family:inherit;
    cursor:pointer;
    border:1px solid transparent;
    transition:background .15s, border-color .15s, color .15s;
}
.po-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
.po-page-btn:not(.disabled):hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.po-page-btn.active {
    background:var(--accent);
    color:#fff;
    border-color:transparent;
}
.po-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
.po-per-page {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    color:var(--t2);
}
.po-per-page select {
    appearance:none;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:7px;
    padding:4px 22px 4px 9px;
    font-size:12px;
    font-weight:700;
    color:var(--t1);
    font-family:inherit;
    cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 6px center;
    outline:none;
}
</style>

    {{-- Header --}}
    <div class="po-header poa po1">
        <div class="po-header-text">
            <h1>Instructor Payouts</h1>
            <p>Review and process instructor payout requests.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="po-card poa po2">

        {{-- Toolbar --}}
        <div class="po-toolbar">
            <div class="po-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <button type="button" wire:click="selectTab('{{ $t['key'] }}')" class="po-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="po-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </button>
                @endforeach
            </div>

            <div class="po-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search instructor...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="po-loading" wire:target="selectTab,gotoPage,search,perPage">
        <table class="po-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Instructor</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Requested</th>
                    @if($canUpdate || $canDownload)<th style="text-align:right">Actions</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($payouts as $payout)
                @php
                    $ss = $statusStyle($payout->status);
                    $d = $payout->details ?? [];
                    $qrUrl = !empty($d['qr_code_path']) ? \Illuminate\Support\Facades\Storage::disk('r2')->url($d['qr_code_path']) : null;
                    $accountData = [
                        'method' => str_replace('_', ' ', $payout->payment_method),
                        'account_name' => $d['account_name'] ?? null,
                        'account_number' => $d['account_number'] ?? null,
                        'phone_number' => $d['phone_number'] ?? null,
                        'qr_url' => $qrUrl,
                    ];
                @endphp
                <tr class="po-row-link" onclick='openAccountModal(@json($accountData))'>
                    <td><span class="po-id">{{ $payout->id }}</span></td>

                    <td>
                        <div class="po-user-cell">
                            <span class="po-user-name">{{ $payout->instructor?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td><span class="po-email">{{ $payout->instructor?->email ?? '—' }}</span></td>

                    <td><span class="po-amount">{{ number_format($payout->amount, 2) }} {{ $payout->currency }}</span></td>

                    <td><span class="po-method">{{ str_replace('_', ' ', $payout->payment_method) }}</span></td>

                    <td>
                        <span class="po-badge" style="background:{{ $payout->source === 'monthly_auto' ? 'rgba(124,58,237,.12)' : 'rgba(148,163,184,.1)' }};color:{{ $payout->source === 'monthly_auto' ? '#7c3aed' : '#94a3b8' }}">
                            {{ $payout->source === 'monthly_auto' ? 'Monthly Auto' : 'Manual' }}
                        </span>
                    </td>

                    <td>
                        <span class="po-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="po-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td><span class="po-date">{{ $payout->created_at?->format('M d, Y') }}</span></td>

                    @if($canUpdate || $canDownload)
                    <td onclick="event.stopPropagation()">
                        <div class="po-actions">
                            @if($canUpdate && $payout->status === 'pending')
                            <button onclick="openApproveModal({{ $payout->id }}, '{{ addslashes($payout->instructor?->name) }}')"
                                    class="po-act-btn po-act-btn-approve" title="Approve">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </button>
                            <button onclick="openRejectModal({{ $payout->id }}, '{{ addslashes($payout->instructor?->name) }}')"
                                    class="po-act-btn po-act-btn-reject" title="Reject">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </button>
                            @endif
                            @if($canDownload && $payout->receipt)
                            <a href="{{ route('admin.finance.payout-receipts.download', $payout->receipt->id) }}"
                               class="po-act-btn" title="Download Receipt">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ ($canUpdate || $canDownload) ? 9 : 8 }}">
                        <div class="po-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.625c.621 0 1.125.504 1.125 1.125v.375M3.75 4.5h16.5"/>
                            </svg>
                            <p>No payout requests found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="po-footer">
            <div class="po-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} payouts
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="po-per-page">
                    Per page
                    <select wire:model.live="perPage">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="po-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="po-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="po-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="po-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payout Account Modal --}}
    <div class="po-modal-overlay" id="po-account-modal" onclick="if(event.target===this)closeAccountModal()">
        <div class="po-modal">
            <h3>Payout Destination</h3>
            <p>Use these details to send the instructor their funds.</p>
            <div id="po-account-body" style="display:grid;gap:8px;font-size:13px;color:var(--t1)"></div>
            <div class="po-modal-footer">
                <button class="po-modal-btn po-modal-btn-gray" onclick="closeAccountModal()">Close</button>
            </div>
        </div>
    </div>

    @if($canUpdate)
    {{-- Approve Modal --}}
    <div class="po-modal-overlay" id="po-approve-modal" onclick="if(event.target===this)closeApproveModal()">
        <div class="po-modal">
            <h3>Approve Payout</h3>
            <p id="po-approve-name-text">Are you sure you want to approve this payout request?</p>
            <div class="po-modal-footer">
                <button class="po-modal-btn po-modal-btn-gray" onclick="closeApproveModal()">Cancel</button>
                <button class="po-modal-btn po-modal-btn-success" onclick="submitApprove()">Approve Payout</button>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="po-modal-overlay" id="po-reject-modal" onclick="if(event.target===this)closeRejectModal()">
        <div class="po-modal">
            <h3>Reject Payout</h3>
            <p id="po-reject-name-text">Explain why this payout is being rejected. The funds will be returned to the instructor's wallet.</p>
            <textarea id="po-reject-reason" placeholder="e.g. Invalid bank details, suspicious activity..."></textarea>
            <div class="po-modal-footer">
                <button class="po-modal-btn po-modal-btn-gray" onclick="closeRejectModal()">Cancel</button>
                <button class="po-modal-btn po-modal-btn-danger" onclick="submitReject()">Reject Payout</button>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    function poField(label, value) {
        return '<div class="po-field"><span class="po-field-label">' + label + '</span><span class="po-field-value">' + value + '</span></div>';
    }
    function openAccountModal(account) {
        var rows = [];
        rows.push(poField('Method', account.method || '—'));
        rows.push(poField('Account Name', account.account_name || '—'));
        if (account.account_number) rows.push(poField('Account Number', account.account_number));
        if (account.phone_number) rows.push(poField('Phone Number', account.phone_number));
        if (account.qr_url) {
            rows.push('<img src="' + account.qr_url + '" alt="Payout QR code" class="po-qr-img">');
        }
        document.getElementById('po-account-body').innerHTML = rows.join('');
        document.getElementById('po-account-modal').classList.add('open');
    }
    function closeAccountModal() {
        document.getElementById('po-account-modal').classList.remove('open');
    }

    var approveId = null;
    function openApproveModal(id, name) {
        approveId = id;
        document.getElementById('po-approve-name-text').textContent = 'Are you sure you want to approve ' + name + '\'s payout request?';
        document.getElementById('po-approve-modal').classList.add('open');
    }
    function closeApproveModal() {
        document.getElementById('po-approve-modal').classList.remove('open');
        approveId = null;
    }
    function submitApprove() {
        if (!approveId) return;
        const id = approveId;
        closeApproveModal();
        @this.call('approve', id);
    }

    var rejectId = null;
    function openRejectModal(id, name) {
        rejectId = id;
        document.getElementById('po-reject-name-text').textContent = 'Explain why ' + name + '\'s payout is being rejected. Funds will be returned to their wallet.';
        document.getElementById('po-reject-reason').value = '';
        document.getElementById('po-reject-modal').classList.add('open');
        setTimeout(() => document.getElementById('po-reject-reason').focus(), 100);
    }
    function closeRejectModal() {
        document.getElementById('po-reject-modal').classList.remove('open');
        rejectId = null;
    }
    function submitReject() {
        const reason = document.getElementById('po-reject-reason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason.');
            return;
        }
        const id = rejectId;
        closeRejectModal();
        @this.call('reject', id, reason);
    }

    // Ensure Filament sidebar is collapsed on small screens so this page matches other Filament pages on mobile.
    (function(){
        function ensureMobileSidebar() {
            try {
                if (window.innerWidth <= 768) {
                    document.body.classList.remove('fi-sidebar-open');
                }
            } catch (e) { /* ignore */ }
        }
        // Run on load and after a short delay to allow Filament JS to initialize
        ensureMobileSidebar();
        setTimeout(ensureMobileSidebar, 250);
        window.addEventListener('resize', ensureMobileSidebar);
    })();
</script>
</div>
