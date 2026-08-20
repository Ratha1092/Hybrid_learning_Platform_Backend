@php
    $accent = '#2563eb';

    $statusStyle = fn($status) => match($status) {
        'unread'  => ['bg' => 'rgba(251,191,36,.12)', 'color' => '#fbbf24', 'label' => 'Unread'],
        'read'    => ['bg' => 'rgba(56,189,248,.12)', 'color' => '#38bdf8', 'label' => 'Read'],
        'replied' => ['bg' => 'rgba(52,211,153,.12)', 'color' => '#34d399', 'label' => 'Replied'],
        default   => ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'label' => ucfirst($status ?? '—')],
    };
@endphp

<div wire:poll.15s>
<div class="lp" id="lp-contact-messages" style="--accent:{{ $accent }};">

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
.lp-subject {
    font-size:12px;
    color:var(--t1);
    max-width:240px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    display:block;
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
.lp-act-btn-reply {
    color:#34d399;
}
.lp-act-btn-reply:hover {
    background:rgba(52,211,153,.12) !important;
    border-color:rgba(52,211,153,.3) !important;
    color:#34d399 !important;
}

/* Modals */
.lp-modal-overlay {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    backdrop-filter:blur(3px);
    z-index:9999;
    align-items:center;
    justify-content:center;
    padding:20px;
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
.lp-modal-btn-success {
    background:rgba(52,211,153,.15);
    color:#34d399;
    border:1px solid rgba(52,211,153,.3);
}
.lp-modal-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}
.lp-textarea {
    width:100%;
    min-height:120px;
    resize:vertical;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:10px 12px;
    color:var(--t1);
    font-size:13px;
    font-family:inherit;
    outline:none;
}
.lp-textarea:focus {
    border-color:var(--accent);
}

/* View details modal */
.lp-view-modal {
    max-width:560px;
}
.lp-vm-row {
    display:grid;
    grid-template-columns:120px 1fr;
    gap:10px;
    padding:9px 0;
    border-bottom:1px solid var(--bd);
}
.lp-vm-row:last-child {
    border-bottom:none;
}
.lp-vm-key {
    font-size:11px;
    font-weight:700;
    letter-spacing:.03em;
    text-transform:uppercase;
    color:var(--t2);
}
.lp-vm-val {
    font-size:13px;
    color:var(--t1);
    word-break:break-word;
    white-space:pre-wrap;
}
.lp-vm-val a {
    color:var(--accent);
    text-decoration:none;
}
.lp-vm-val a:hover {
    text-decoration:underline;
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
            <h1>Contact Messages</h1>
            <p>Messages submitted through the public contact form.</p>
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
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search name, email or subject...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="lp-loading" wire:target="selectTab,gotoPage,search,setPerPage">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $message)
                @php
                    $ss = $statusStyle($message->status);
                    $detailsJson = htmlspecialchars(json_encode([
                        'id' => $message->id,
                        'name' => $message->name,
                        'email' => $message->email,
                        'subject' => $message->subject,
                        'message' => $message->message,
                        'status' => $ss['label'],
                        'statusColor' => $ss['color'],
                        'replyMessage' => $message->reply_message,
                        'replierName' => $message->replier?->name,
                        'repliedAt' => $message->replied_at?->format('M d, Y · H:i'),
                        'receivedAt' => $message->created_at?->format('M d, Y · H:i'),
                        'canReply' => $message->status !== 'replied',
                    ]), ENT_QUOTES, 'UTF-8');
                @endphp
                <tr class="lp-row-link" onclick='openViewModal({{ $detailsJson }})'>
                    <td><span class="lp-id">{{ $message->id }}</span></td>

                    <td>
                        <div class="lp-user-cell">
                            <div>
                                <div class="lp-user-name">{{ $message->name }}</div>
                                <div class="lp-email">{{ $message->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td><span class="lp-subject" title="{{ $message->subject }}">{{ $message->subject }}</span></td>

                    <td>
                        <span class="lp-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="lp-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </td>

                    <td><span class="lp-date">{{ $message->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="lp-actions">
                            @if($message->status !== 'replied')
                            <button onclick='openReplyModal({{ $detailsJson }})'
                                    class="lp-act-btn lp-act-btn-reply" title="Reply">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 6 6v3"/>
                                </svg>
                            </button>
                            @endif
                            <button onclick='openViewModal({{ $detailsJson }})' class="lp-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="lp-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                            </svg>
                            <p>No contact messages found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
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
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} messages
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

    {{-- Reply Modal — wire:ignore keeps this subtree untouched by the
         wire:poll re-render above, which otherwise fights the vanilla-JS
         .open toggle and can leave the overlay half-repainted while open. --}}
    <div class="lp-modal-overlay" id="lp-reply-modal" wire:ignore onclick="if(event.target===this)closeReplyModal()">
        <div class="lp-modal">
            <h3 id="lp-reply-title">Reply</h3>
            <p id="lp-reply-subtitle">Your reply will be emailed to the sender.</p>
            <textarea id="lp-reply-textarea" class="lp-textarea" placeholder="Type your reply here..." oninput="document.getElementById('lp-reply-send').disabled = this.value.trim().length === 0"></textarea>
            <div class="lp-modal-footer">
                <button class="lp-modal-btn lp-modal-btn-gray" onclick="closeReplyModal()">Cancel</button>
                <button id="lp-reply-send" class="lp-modal-btn lp-modal-btn-success" onclick="submitReply()" disabled>Send Reply</button>
            </div>
        </div>
    </div>

    {{-- View Details Modal — see wire:ignore note above. --}}
    <div class="lp-modal-overlay" id="lp-view-modal" wire:ignore onclick="if(event.target===this)closeViewModal()">
        <div class="lp-modal lp-view-modal">
            <h3 id="lp-view-title">Message #</h3>
            <p>Full details of this contact message.</p>
            <div id="lp-view-body"></div>
            <div class="lp-modal-footer" id="lp-view-footer"></div>
        </div>
    </div>

</div>

<script>
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    }

    var replyId = null;
    function openReplyModal(r) {
        replyId = r.id;
        document.getElementById('lp-reply-title').textContent = 'Reply to ' + r.name;
        document.getElementById('lp-reply-subtitle').textContent = 'Your reply will be emailed to ' + r.email + '.';
        document.getElementById('lp-reply-textarea').value = '';
        document.getElementById('lp-reply-send').disabled = true;
        document.getElementById('lp-reply-modal').classList.add('open');
    }
    function closeReplyModal() {
        document.getElementById('lp-reply-modal').classList.remove('open');
        replyId = null;
    }
    function submitReply() {
        if (!replyId) return;
        const id = replyId;
        const text = document.getElementById('lp-reply-textarea').value.trim();
        if (!text) return;
        closeReplyModal();
        closeViewModal();
        @this.call('reply', id, text);
    }

    function openViewModal(r) {
        if (r.status === 'Unread' || r.status === undefined) {
            // mark read as soon as it's opened, best-effort, ignored if already read/replied
        }
        @this.call('markRead', r.id);

        document.getElementById('lp-view-title').textContent = 'Message #' + r.id;

        let rows = [
            ['From', escapeHtml(r.name) + ' <span style="color:var(--t2)">(' + escapeHtml(r.email) + ')</span>'],
            ['Subject', escapeHtml(r.subject)],
            ['Message', escapeHtml(r.message)],
            ['Status', '<span class="lp-badge" style="background:' + r.statusColor + '20;color:' + r.statusColor + '">' + escapeHtml(r.status) + '</span>'],
            ['Received', escapeHtml(r.receivedAt)],
        ];
        if (r.replyMessage) {
            rows.push(['Reply Sent', escapeHtml(r.replyMessage)]);
            rows.push(['Replied By', escapeHtml(r.replierName ?? '—') + ' · ' + escapeHtml(r.repliedAt ?? '—')]);
        }

        document.getElementById('lp-view-body').innerHTML = rows.map(([k, v]) =>
            '<div class="lp-vm-row"><div class="lp-vm-key">' + k + '</div><div class="lp-vm-val">' + v + '</div></div>'
        ).join('');

        const footer = document.getElementById('lp-view-footer');
        footer.innerHTML = '';
        const closeBtn = document.createElement('button');
        closeBtn.className = 'lp-modal-btn lp-modal-btn-gray';
        closeBtn.textContent = 'Close';
        closeBtn.onclick = closeViewModal;
        footer.appendChild(closeBtn);

        if (r.canReply) {
            const replyBtn = document.createElement('button');
            replyBtn.className = 'lp-modal-btn lp-modal-btn-success';
            replyBtn.textContent = 'Reply';
            replyBtn.onclick = function () { openReplyModal(r); };
            footer.appendChild(replyBtn);
        }

        document.getElementById('lp-view-modal').classList.add('open');
    }
    function closeViewModal() {
        document.getElementById('lp-view-modal').classList.remove('open');
    }
</script>
</div>{{-- end single root --}}
