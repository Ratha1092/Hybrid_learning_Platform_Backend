@php
    $accent = '#2563eb';

    $statusStyle = fn($status) => match($status) {
        'pending'  => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
        'approved' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Approved'],
        'rejected' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Rejected'],
        default    => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($status ?? '—')],
    };

    $viewUrl = fn($v) => route('filament.admin.resources.instructor-verifications.view', ['record' => $v->id]);
@endphp

<div wire:poll.15s>
<div class="lp" id="lp-verifications" style="--accent:{{ $accent }};">

<style>
.lp, .lp *, .lp *::before, .lp *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.lp {
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
html:not(.dark) .lp {
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
@keyframes lpUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.lpa {
    opacity:0;
    animation:lpUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.lp1 {
    animation-delay:.04s;
}
.lp2 {
    animation-delay:.09s;
}

.lp-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.lp-header-text h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
    line-height:1.15;
}
.lp-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}

.lp-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
    box-shadow:var(--sh);
    min-width:0;
}
.lp-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px 16px;
    border-bottom:1px solid var(--bd);
    flex-wrap:wrap;
}
.lp-tabs {
    display:flex;
    align-items:center;
    gap:4px;
    flex-wrap:wrap;
}
.lp-tab {
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
.lp-tab:hover {
    background:var(--p2);
    color:var(--t1);
}
.lp-tab-badge {
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
.lp-search-box {
    display:flex;
    align-items:center;
    gap:6px;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:6px 12px;
}
.lp-search-box svg {
    width:14px;
    height:14px;
    color:var(--t2);
    flex-shrink:0;
}
.lp-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.lp-search-box input::placeholder {
    color:var(--t2);
}

.lp-table {
    width:100%;
    border-collapse:collapse;
}
.lp-table thead tr {
    border-bottom:1px solid var(--bd);
}
.lp-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}
.lp-table tbody tr {
    border-bottom:1px solid var(--bd);
    transition:background .12s;
}
.lp-table tbody tr:last-child {
    border-bottom:none;
}
.lp-table tbody tr:hover {
    background:var(--p2);
}
.lp-row-link {
    cursor:pointer;
}
.lp-table td {
    padding:12px 12px;
    vertical-align:middle;
}

.lp-id {
    font-size:11.5px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.lp-user-cell {
    display:flex;
    align-items:center;
    gap:8px;
}
.lp-user-name {
    font-size:12.5px;
    color:var(--t1);
    font-weight:500;
}
.lp-email {
    font-size:12px;
    color:var(--t2);
}
.lp-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}
.lp-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.lp-date {
    font-size:12px;
    color:var(--t2);
    white-space:nowrap;
}
.lp-qual {
    font-size:12px;
    color:var(--t1);
}

.lp-actions {
    display:flex;
    align-items:center;
    gap:4px;
    justify-content:flex-end;
}
.lp-act-btn {
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
.lp-act-btn:hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.lp-act-btn svg {
    width:18px;
    height:18px;
}
.lp-act-btn-approve {
    color:#34d399;
}
.lp-act-btn-approve:hover {
    background:rgba(52,211,153,.12) !important;
    border-color:rgba(52,211,153,.3) !important;
    color:#34d399 !important;
}
.lp-act-btn-reject {
    color:#f87171;
}
.lp-act-btn-reject:hover {
    background:rgba(248,113,113,.12) !important;
    border-color:rgba(248,113,113,.3) !important;
    color:#f87171 !important;
}

/* Reject modal */
.lp-modal-overlay {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    backdrop-filter:blur(3px);
    z-index:9999;
    align-items:center;
    justify-content:center;
}
.lp-modal-overlay.open {
    display:flex;
}
.lp-modal {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:14px;
    padding:26px;
    width:100%;
    max-width:460px;
    box-shadow:0 20px 60px rgba(0,0,0,.4);
}
.lp-modal h3 {
    font-size:15px;
    font-weight:750;
    color:var(--t1);
    margin-bottom:6px;
}
.lp-modal p {
    font-size:12.5px;
    color:var(--t2);
    margin-bottom:16px;
}
.lp-modal textarea {
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
.lp-modal textarea:focus {
    border-color:#2563eb;
}
.lp-modal-footer {
    display:flex;
    justify-content:flex-end;
    gap:8px;
    margin-top:14px;
}
.lp-modal-btn {
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
.lp-modal-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.lp-modal-btn-danger {
    background:rgba(248,113,113,.15);
    color:#f87171;
    border:1px solid rgba(248,113,113,.3);
}
.lp-modal-btn-success {
    background:rgba(52,211,153,.15);
    color:#34d399;
    border:1px solid rgba(52,211,153,.3);
}

.lp-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:10px;
    color:var(--t2);
}
.lp-empty svg {
    width:40px;
    height:40px;
    opacity:.35;
}
.lp-empty p {
    font-size:13px;
}

.lp-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.lp-footer-info {
    font-size:12px;
    color:var(--t2);
}
.lp-pages {
    display:flex;
    align-items:center;
    gap:6px;
}
.lp-page-btn {
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
.lp-loading {
    opacity:.45;
    pointer-events:none;
    transition:opacity .1s;
}
.lp-page-btn:not(.disabled):hover {
    background:var(--p2);
    border-color:var(--bd2);
    color:var(--t1);
}
.lp-page-btn.active {
    background:var(--accent);
    color:#fff;
    border-color:transparent;
}
.lp-page-btn.disabled {
    opacity:.35;
    pointer-events:none;
}
.lp-per-page {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    color:var(--t2);
}
.lp-per-page select {
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
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Instructor Verifications</h1>
            <p>Review and manage instructor verification applications.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar --}}
        <div class="lp-toolbar">
            <div class="lp-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <button type="button" wire:click="selectTab('{{ $t['key'] }}')" class="lp-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="lp-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </button>
                @endforeach
            </div>

            <div class="lp-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search name or email...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="lp-loading" wire:target="selectTab,gotoPage,search,setPerPage">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Instructor</th>
                    <th>Email</th>
                    <th>Qualification</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($verifications as $verification)
                @php
                    $ss    = $statusStyle($verification->status);
                    $bgHex = substr(md5($verification->user?->name ?? ''), 0, 6);
                    $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($verification->user?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                    $qual  = ucfirst(str_replace('_', ' ', $verification->qualification_type ?? '—'));
                @endphp
                <tr class="lp-row-link" wire:key="verification-row-{{ $verification->id }}" onclick="Livewire.navigate('{{ $viewUrl($verification) }}')">
                    <td><span class="lp-id">{{ $verification->id }}</span></td>

                    <td>
                        <div class="lp-user-cell">
                            <img src="{{ $avUrl }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">
                            <span class="lp-user-name">{{ $verification->user?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td><span class="lp-email">{{ $verification->user?->email ?? '—' }}</span></td>

                    <td><span class="lp-qual">{{ $qual }}</span></td>

                    <td>
                        <span class="lp-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="lp-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td><span class="lp-date">{{ $verification->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="lp-actions">
                            @if($verification->status === 'pending')
                            <button onclick="openApproveModal({{ $verification->id }}, '{{ addslashes($verification->user?->name) }}')"
                                    class="lp-act-btn lp-act-btn-approve" title="Approve">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </button>
                            <button onclick="openRejectModal({{ $verification->id }}, '{{ addslashes($verification->user?->name) }}')"
                                    class="lp-act-btn lp-act-btn-reject" title="Reject">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </button>
                            @endif
                            <a href="{{ $viewUrl($verification) }}" wire:navigate class="lp-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <p>No verifications found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="lp-footer">
            <div class="lp-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} verifications
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="lp-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="lp-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="lp-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="lp-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="lp-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Approve Modal — wire:ignore keeps this subtree untouched by the
         wire:poll re-render above, which otherwise fights the vanilla-JS
         .open toggle and can leave the overlay half-repainted while open. --}}
    <div class="lp-modal-overlay" id="lp-approve-modal" wire:ignore onclick="if(event.target===this)closeApproveModal()">
        <div class="lp-modal">
            <h3>Approve Application</h3>
            <p id="lp-approve-name-text">Are you sure you want to approve this application? The instructor will be notified.</p>
            <div class="lp-modal-footer">
                <button class="lp-modal-btn lp-modal-btn-gray" onclick="closeApproveModal()">Cancel</button>
                <button class="lp-modal-btn lp-modal-btn-success" onclick="submitApprove()">Approve Application</button>
            </div>
        </div>
    </div>

    {{-- Reject Modal — inside .lp so CSS vars (--p1, --t1 etc.) are accessible.
         See wire:ignore note above. --}}
    <div class="lp-modal-overlay" id="lp-reject-modal" wire:ignore onclick="if(event.target===this)closeRejectModal()">
        <div class="lp-modal">
            <h3>Reject Application</h3>
            <p id="lp-reject-name-text">Explain why this application is being rejected. The instructor will be notified.</p>
            <textarea id="lp-reject-reason" placeholder="e.g. Insufficient qualifications, invalid documents..."></textarea>
            <div class="lp-modal-footer">
                <button class="lp-modal-btn lp-modal-btn-gray" onclick="closeRejectModal()">Cancel</button>
                <button class="lp-modal-btn lp-modal-btn-danger" onclick="submitReject()">Reject Application</button>
            </div>
        </div>
    </div>

</div>

<script>
    var approveId = null;
    function openApproveModal(id, name) {
        approveId = id;
        document.getElementById('lp-approve-name-text').textContent = 'Are you sure you want to approve ' + name + '\'s application? They will be notified.';
        document.getElementById('lp-approve-modal').classList.add('open');
    }
    function closeApproveModal() {
        document.getElementById('lp-approve-modal').classList.remove('open');
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
        document.getElementById('lp-reject-name-text').textContent ='Explain why ' + name + '\'s application is being rejected. They will be notified.';
        document.getElementById('lp-reject-reason').value = '';
        document.getElementById('lp-reject-modal').classList.add('open');
        setTimeout(() => document.getElementById('lp-reject-reason').focus(), 100);
    }

    function closeRejectModal() {
        document.getElementById('lp-reject-modal').classList.remove('open');
        rejectId = null;
    }   
    function submitReject() {
        const reason = document.getElementById('lp-reject-reason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason.');
            return;
        }
        const id = rejectId;
        closeRejectModal();
        @this.call('reject', id, reason);
    }
</script>
</div>{{-- end single root --}}
