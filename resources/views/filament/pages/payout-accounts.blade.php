@php
    $accent = '#7c3aed';

    $statusStyle = fn($status) => match($status) {
        'pending'  => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
        'verified' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Verified'],
        'rejected' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Rejected'],
        default    => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($status ?? '—')],
    };

    $viewUrl = fn($a) => route('filament.admin.resources.payout-accounts.view', ['record' => $a->id]);
@endphp

<div wire:poll.30s>
<div class="pq" id="pq-payout-accounts" style="--accent:{{ $accent }};">

<style>
.pq, .pq *, .pq *::before, .pq *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.pq {
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
html:not(.dark) .pq {
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
@keyframes pqUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.pqa {
    opacity:0;
    animation:pqUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.pq1 {
    animation-delay:.04s;
}
.pq2 {
    animation-delay:.09s;
}

.pq-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.pq-header-text h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.15;
}
.pq-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}

.pq-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
    box-shadow:var(--sh);
}
.pq-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.pq-tabs {
    display:flex;
    align-items:center;
    gap:4px;
    flex-wrap:wrap;
}
.pq-tab {
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
.pq-tab:hover {
    background:var(--p2);
    color:var(--t1);
}
.pq-tab-badge {
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
.pq-search-box {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.pq-search-box svg {
    width:14px;
    height:14px;
    color:var(--t2);
    flex-shrink:0;
}
.pq-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.pq-search-box input::placeholder {
    color:var(--t2);
}

.pq-table {
    width:100%;
    border-collapse:collapse;
}
.pq-table thead tr {
    border-bottom:1px solid var(--bd);
}
.pq-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.pq-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.pq-table tbody tr:last-child {
    border-bottom:none;
}
.pq-table tbody tr:hover {
    background:var(--p2);
}
.pq-row-link {
    cursor:pointer;
}
.pq-table td {
    padding:12px 12px;
    vertical-align:middle;
}

.pq-id {
    font-size:11.5px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.pq-user-cell {
    display:flex;
    align-items:center;
    gap:8px;
}
.pq-qr-thumb {
    width:32px;
    height:32px;
    border-radius:6px;
    object-fit:cover;
    border:1px solid var(--bd2);
    background:#fff;
    flex-shrink:0;
}
.pq-qr-thumb-empty {
    width:32px;
    height:32px;
    border-radius:6px;
    border:1px dashed var(--bd2);
    flex-shrink:0;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--t2);
}
.pq-qr-thumb-empty svg {
    width:16px;
    height:16px;
}
.pq-user-name {
    font-size:12.5px;
    color:var(--t1);
    font-weight:500;
}
.pq-email {
    font-size:12px;
    color:var(--t2);
}
.pq-method {
    font-size:12px;
    color:var(--t2);
    text-transform:capitalize;
}
.pq-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}
.pq-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.pq-date {
    font-size:12px;
    color:var(--t2);
    white-space:nowrap;
}

.pq-actions {
    display:flex;
    align-items:center;
    gap:4px;
    justify-content:flex-end;
}
.pq-act-btn {
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
.pq-act-btn:hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.pq-act-btn svg {
    width:18px;
    height:18px;
}
.pq-act-btn-approve {
    color:#34d399;
}
.pq-act-btn-approve:hover {
    background:rgba(52,211,153,.12) !important;
    border-color:rgba(52,211,153,.3) !important;
    color:#34d399 !important;
}
.pq-act-btn-reject {
    color:#f87171;
}
.pq-act-btn-reject:hover {
    background:rgba(248,113,113,.12) !important;
    border-color:rgba(248,113,113,.3) !important;
    color:#f87171 !important;
}

.pq-modal-overlay {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    z-index:9999;
    align-items:center;
    justify-content:center;
}
.pq-modal-overlay.open {
    display:flex;
}
.pq-modal {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:14px;
    padding:26px;
    width:100%;
    max-width:460px;
    box-shadow:0 20px 60px rgba(0,0,0,.4);
}
.pq-modal h3 {
    font-size:15px;
    font-weight:750;
    color:var(--t1);
    margin-bottom:6px;
}
.pq-modal p {
    font-size:12.5px;
    color:var(--t2);
    margin-bottom:16px;
}
.pq-modal textarea {
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
.pq-modal textarea:focus {
    border-color:#7c3aed;
}
.pq-modal-footer {
    display:flex;
    justify-content:flex-end;
    gap:8px;
    margin-top:14px;
}
.pq-modal-btn {
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
.pq-modal-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.pq-modal-btn-danger {
    background:rgba(248,113,113,.15);
    color:#f87171;
    border:1px solid rgba(248,113,113,.3);
}
.pq-modal-btn-success {
    background:rgba(52,211,153,.15);
    color:#34d399;
    border:1px solid rgba(52,211,153,.3);
}

.pq-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.pq-empty svg {
    width:40px;
    height:40px;
    opacity:.35;
}
.pq-empty p {
    font-size:13px;
}

.pq-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.pq-footer-info {
    font-size:12px;
    color:var(--t2);
}
.pq-pages {
    display:flex;
    align-items:center;
    gap:6px;
}
.pq-page-btn {
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
.pq-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
.pq-page-btn:not(.disabled):hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.pq-page-btn.active {
    background:var(--accent);
    color:#fff;
    border-color:transparent;
}
.pq-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
.pq-per-page {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    color:var(--t2);
}
.pq-per-page select {
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
    <div class="pq-header pqa pq1">
        <div class="pq-header-text">
            <h1>Payout Accounts</h1>
            <p>Verify instructor bank/QR details before they can request payouts.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="pq-card pqa pq2">

        {{-- Toolbar --}}
        <div class="pq-toolbar">
            <div class="pq-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <button type="button" wire:click="selectTab('{{ $t['key'] }}')" class="pq-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="pq-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </button>
                @endforeach
            </div>

            <div class="pq-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search instructor...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="pq-loading" wire:target="selectTab,gotoPage,search,setPerPage">
        <table class="pq-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>QR</th>
                    <th>Instructor</th>
                    <th>Email</th>
                    <th>Method</th>
                    <th>Account Name</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    @if($canUpdate)<th style="text-align:right">Actions</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                @php
                    $ss    = $statusStyle($account->status);
                    $qrUrl = $account->qr_code_path ? \Illuminate\Support\Facades\Storage::disk('r2')->url($account->qr_code_path) : null;
                @endphp
                <tr class="pq-row-link" onclick="Livewire.navigate('{{ $viewUrl($account) }}')">
                    <td><span class="pq-id">{{ $account->id }}</span></td>

                    <td onclick="event.stopPropagation()">
                        @if($qrUrl)
                            <img src="{{ $qrUrl }}" class="pq-qr-thumb" alt="QR">
                        @else
                            <div class="pq-qr-thumb-empty" title="No QR uploaded">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                        @endif
                    </td>

                    <td>
                        <div class="pq-user-cell">
                            <span class="pq-user-name">{{ $account->instructor?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td><span class="pq-email">{{ $account->instructor?->email ?? '—' }}</span></td>

                    <td><span class="pq-method">{{ str_replace('_', ' ', $account->method) }}</span></td>

                    <td><span class="pq-user-name">{{ $account->account_name }}</span></td>

                    <td>
                        <span class="pq-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="pq-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td><span class="pq-date">{{ $account->created_at?->format('M d, Y') }}</span></td>

                    @if($canUpdate)
                    <td onclick="event.stopPropagation()">
                        <div class="pq-actions">
                            @if($account->status === 'pending')
                            <button onclick="openApproveModal({{ $account->id }}, '{{ addslashes($account->instructor?->name) }}')"
                                    class="pq-act-btn pq-act-btn-approve" title="Verify">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </button>
                            <button onclick="openRejectModal({{ $account->id }}, '{{ addslashes($account->instructor?->name) }}')"
                                    class="pq-act-btn pq-act-btn-reject" title="Reject">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </button>
                            @endif
                            <a href="{{ $viewUrl($account) }}" wire:navigate class="pq-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $canUpdate ? 9 : 8 }}">
                        <div class="pq-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                            </svg>
                            <p>No payout accounts found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="pq-footer">
            <div class="pq-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} accounts
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="pq-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="pq-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="pq-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="pq-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="pq-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($canUpdate)
    {{-- Approve Modal --}}
    <div class="pq-modal-overlay" id="pq-approve-modal" onclick="if(event.target===this)closeApproveModal()">
        <div class="pq-modal">
            <h3>Verify Payout Account</h3>
            <p id="pq-approve-name-text">Are you sure you want to verify this payout account?</p>
            <div class="pq-modal-footer">
                <button class="pq-modal-btn pq-modal-btn-gray" onclick="closeApproveModal()">Cancel</button>
                <button class="pq-modal-btn pq-modal-btn-success" onclick="submitApprove()">Verify Account</button>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="pq-modal-overlay" id="pq-reject-modal" onclick="if(event.target===this)closeRejectModal()">
        <div class="pq-modal">
            <h3>Reject Payout Account</h3>
            <p id="pq-reject-name-text">Explain why this payout account is being rejected. The instructor will be notified.</p>
            <textarea id="pq-reject-reason" placeholder="e.g. QR code unreadable, name mismatch..."></textarea>
            <div class="pq-modal-footer">
                <button class="pq-modal-btn pq-modal-btn-gray" onclick="closeRejectModal()">Cancel</button>
                <button class="pq-modal-btn pq-modal-btn-danger" onclick="submitReject()">Reject Account</button>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    var approveId = null;
    function openApproveModal(id, name) {
        approveId = id;
        document.getElementById('pq-approve-name-text').textContent = 'Are you sure you want to verify ' + name + '\'s payout account?';
        document.getElementById('pq-approve-modal').classList.add('open');
    }
    function closeApproveModal() {
        document.getElementById('pq-approve-modal').classList.remove('open');
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
        document.getElementById('pq-reject-name-text').textContent = 'Explain why ' + name + '\'s payout account is being rejected. They will be notified.';
        document.getElementById('pq-reject-reason').value = '';
        document.getElementById('pq-reject-modal').classList.add('open');
        setTimeout(() => document.getElementById('pq-reject-reason').focus(), 100);
    }
    function closeRejectModal() {
        document.getElementById('pq-reject-modal').classList.remove('open');
        rejectId = null;
    }
    function submitReject() {
        const reason = document.getElementById('pq-reject-reason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason.');
            return;
        }
        const id = rejectId;
        closeRejectModal();
        @this.call('reject', id, reason);
    }
</script>
</div>
