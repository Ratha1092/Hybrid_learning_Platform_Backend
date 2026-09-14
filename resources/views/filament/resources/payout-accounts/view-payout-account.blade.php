@php
    $a      = $record->load(['instructor', 'reviewer']);
    $accent = '#7c3aed';

    $ss = match($a->status) {
        'pending'  => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
        'verified' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Verified'],
        'rejected' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Rejected'],
        default    => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($a->status ?? '—')],
    };

    $method  = ucfirst(str_replace('_', ' ', $a->method ?? '—'));
    $qrUrl   = $a->qr_code_path ? \Illuminate\Support\Facades\Storage::disk('r2')->url($a->qr_code_path) : null;

    $bgHex   = substr(md5($a->instructor?->name ?? ''), 0, 6);
    $avUrl   = 'https://ui-avatars.com/api/?name=' . urlencode($a->instructor?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=128';
    $listUrl = route('filament.admin.pages.payout-accounts');
@endphp

<div class="pa" id="pa-root" style="--accent:{{ $accent }};">
<style>
.pa, .pa *, .pa *::before, .pa *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.pa {
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
html:not(.dark) .pa {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes paUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.paa {
    opacity:0;
    animation:paUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.pa1 {
    animation-delay:.04s;
}
.pa2 {
    animation-delay:.09s;
}
.pa3 {
    animation-delay:.14s;
}

/* Header */
.pa-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.pa-header-left {
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
}
.pa-back {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:7px 13px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    color:var(--t2);
    background:var(--p1);
    border:1px solid var(--bd2);
    transition:background .15s, color .15s;
}
.pa-back:hover {
    background:var(--p2);
    color:var(--t1);
}
.pa-back svg {
    width:14px;
    height:14px;
}
.pa-header-title h1 {
    font-size:clamp(18px,2vw,24px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.pa-header-title p {
    font-size:12px;
    color:var(--t2);
    margin-top:3px;
}
.pa-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}

/* Action buttons */
.pa-btn {
    display:inline-flex;
    align-items:center;
    gap:7px;
    padding:9px 18px;
    border-radius:9px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
    border:none;
    font-family:inherit;
    transition:opacity .15s, transform .1s;
}
.pa-btn:hover {
    opacity:.85;
}
.pa-btn:active {
    transform:scale(.97);
}
.pa-btn-approve {
    background:rgba(52,211,153,.15);
    color:#34d399;
    border:1px solid rgba(52,211,153,.3);
}
.pa-btn-reject {
    background:rgba(248,113,113,.15);
    color:#f87171;
    border:1px solid rgba(248,113,113,.3);
}
.pa-btn svg {
    width:16px;
    height:16px;
}

/* Cards */
.pa-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
    box-shadow:var(--sh);
}
.pa-card-header {
    display:flex;
    align-items:center;
    gap:10px;
    padding:16px 20px;
    border-bottom:1px solid var(--bd);
}
.pa-card-header svg {
    width:18px;
    height:18px;
    color:var(--t2);
    flex-shrink:0;
}
.pa-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
    letter-spacing:-.01em;
}
.pa-card-body {
    padding:20px;
}

/* Grid */
.pa-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));
    gap:16px;
}

/* Fields */
.pa-field {
    display:flex;
    flex-direction:column;
    gap:4px;
}
.pa-label {
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.07em;
    text-transform:uppercase;
    color:var(--t2);
}
.pa-value {
    font-size:13px;
    color:var(--t1);
    word-break:break-word;
}
.pa-value-muted {
    color:var(--t2);
    font-style:italic;
}
.pa-value-long {
    font-size:12.5px;
    line-height:1.7;
    color:var(--t1);
    white-space:pre-wrap;
}

/* Profile section */
.pa-profile {
    display:flex;
    align-items:center;
    gap:16px;
}
.pa-avatar {
    width:64px;
    height:64px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid var(--bd2);
    flex-shrink:0;
}
.pa-profile-info {
    display:flex;
    flex-direction:column;
    gap:4px;
}
.pa-profile-name {
    font-size:16px;
    font-weight:750;
    color:var(--t1);
}
.pa-profile-email {
    font-size:12.5px;
    color:var(--t2);
}

/* Badge */
.pa-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}
.pa-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}

/* QR code */
.pa-qr-box {
    display:flex;
    justify-content:center;
    padding:8px;
}
.pa-qr-box img {
    width:100%;
    max-width:260px;
    border-radius:10px;
    border:1px solid var(--bd2);
    background:#fff;
    padding:12px;
    cursor:zoom-in;
    transition:transform .15s, box-shadow .15s;
}
.pa-qr-box img:hover {
    transform:scale(1.03);
    box-shadow:0 6px 20px rgba(0,0,0,.15);
}
.pa-qr-missing {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    height:200px;
    gap:6px;
    color:var(--t2);
    font-size:12px;
}
.pa-qr-missing svg {
    width:32px;
    height:32px;
    opacity:.35;
}

/* Image lightbox */
.pa-lightbox-overlay {
    display:none;
    position:fixed;
    inset:0;
    z-index:10000;
    align-items:center;
    justify-content:center;
    background:rgba(8,11,20,.88);
    backdrop-filter:blur(4px);
    -webkit-backdrop-filter:blur(4px);
    padding:40px;
    cursor:zoom-out;
}
.pa-lightbox-overlay.open {
    display:flex;
}
.pa-lightbox-overlay img {
    max-width:min(90vw, 560px);
    max-height:85vh;
    border-radius:14px;
    background:#fff;
    padding:20px;
    box-shadow:0 20px 60px rgba(0,0,0,.5);
    cursor:default;
}
.pa-lightbox-close {
    position:fixed;
    top:22px;
    right:22px;
    width:40px;
    height:40px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.18);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:background .15s;
}
.pa-lightbox-close:hover {
    background:rgba(255,255,255,.18);
}
.pa-lightbox-close svg {
    width:18px;
    height:18px;
}

.pa-modal-overlay {
    display:none;
    position:fixed;
    inset:0;
    z-index:9999;
    align-items:center;
    justify-content:center;
    background:rgba(15,23,42,.18);
    backdrop-filter:blur(3px);
    -webkit-backdrop-filter:blur(3px);
    --p1:#1e293b;
    --p2:#263245;
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
}
html:not(.dark) .pa-modal-overlay {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
}
.pa-modal-overlay.open {
    display:flex;
}
.pa-modal {
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:14px;
    padding:26px;
    width:100%;
    max-width:460px;
    box-shadow:0 20px 60px rgba(0,0,0,.4);
}
.pa-modal h3 {
    font-size:15px;
    font-weight:750;
    color:var(--t1);
    margin-bottom:6px;
}
.pa-modal p {
    font-size:12.5px;
    color:var(--t2);
    margin-bottom:16px;
}
.pa-modal textarea {
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
.pa-modal textarea:focus {
    border-color:#7c3aed;
}
.pa-modal-footer {
    display:flex;
    justify-content:flex-end;
    gap:8px;
    margin-top:14px;
}
.pa-modal-btn {
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
.pa-modal-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.pa-modal-btn-danger {
    background:rgba(248,113,113,.15);
    color:#f87171;
    border:1px solid rgba(248,113,113,.3);
}
.pa-modal-btn-success {
    background:rgba(52,211,153,.15);
    color:#34d399;
    border:1px solid rgba(52,211,153,.3);
}
</style>

    {{-- Header --}}
    <div class="pa-header paa pa1">
        <div class="pa-header-left">
            <a href="{{ $listUrl }}" wire:navigate class="pa-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Payout Accounts
            </a>
            <div class="pa-header-title">
                <h1>Payout Account #{{ $a->id }}</h1>
                <p>Submitted {{ $a->created_at?->setTimezone(config('app.timezone'))->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>

        @if($a->status === 'pending')
        <div class="pa-header-actions">
            <button class="pa-btn pa-btn-reject" onclick="openRejectModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                Reject Account
            </button>
            <button class="pa-btn pa-btn-approve" onclick="openApproveModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                Verify Account
            </button>
        </div>
        @else
        <div class="pa-header-actions">
            <span class="pa-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }};font-size:13px;padding:7px 14px;">
                <span class="pa-dot" style="background:{{ $ss['color'] }}"></span>
                {{ $ss['label'] }}
            </span>
        </div>
        @endif
    </div>

    {{-- Instructor Information --}}
    <div class="pa-card paa pa2">
        <div class="pa-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
            <span class="pa-card-title">Instructor</span>
        </div>
        <div class="pa-card-body">
            <div class="pa-profile" style="margin-bottom:20px">
                <img src="{{ $avUrl }}" class="pa-avatar" alt="{{ $a->instructor?->name }}">
                <div class="pa-profile-info">
                    <span class="pa-profile-name">{{ $a->instructor?->name ?? '—' }}</span>
                    <span class="pa-profile-email">{{ $a->instructor?->email ?? '—' }}</span>
                </div>
            </div>
            <div class="pa-grid">
                <div class="pa-field">
                    <span class="pa-label">Instructor ID</span>
                    <span class="pa-value">#{{ $a->instructor_id }}</span>
                </div>
                <div class="pa-field">
                    <span class="pa-label">Submitted</span>
                    <span class="pa-value">{{ $a->created_at?->setTimezone(config('app.timezone'))->format('M d, Y') ?? '—' }}</span>
                </div>
                <div class="pa-field">
                    <span class="pa-label">Status</span>
                    <span class="pa-value">
                        <span class="pa-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="pa-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payout Details + QR --}}
    <div class="pa-card paa pa3">
        <div class="pa-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
            </svg>
            <span class="pa-card-title">Payout Destination</span>
        </div>
        <div class="pa-card-body" style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px;align-items:start">
            <div class="pa-grid">
                <div class="pa-field">
                    <span class="pa-label">Method</span>
                    <span class="pa-value">{{ $method }}</span>
                </div>
                <div class="pa-field">
                    <span class="pa-label">Account Name</span>
                    <span class="pa-value">{{ $a->account_name ?? '—' }}</span>
                </div>
                @if($a->account_number)
                <div class="pa-field">
                    <span class="pa-label">Account Number</span>
                    <span class="pa-value">{{ $a->account_number }}</span>
                </div>
                @endif
                @if($a->phone_number)
                <div class="pa-field">
                    <span class="pa-label">Phone Number</span>
                    <span class="pa-value">{{ $a->phone_number }}</span>
                </div>
                @endif
            </div>

            <div class="pa-qr-box">
                @if($qrUrl)
                    <img src="{{ $qrUrl }}" alt="Payout QR code" onclick="openLightbox('{{ $qrUrl }}')">
                @else
                    <div class="pa-qr-missing">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.625c.621 0 1.125.504 1.125 1.125v.375M3.75 4.5h16.5m-16.5 15v-.75A.75.75 0 0 0 3 18h-.75"/>
                        </svg>
                        No QR code uploaded
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Review Status --}}
    <div class="pa-card paa" style="animation-delay:.19s">
        <div class="pa-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <span class="pa-card-title">Review Status</span>
        </div>
        <div class="pa-card-body">
            <div class="pa-grid">
                <div class="pa-field">
                    <span class="pa-label">Status</span>
                    <span class="pa-value">
                        <span class="pa-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="pa-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </span>
                </div>
                <div class="pa-field">
                    <span class="pa-label">Reviewed By</span>
                    <span class="pa-value">{{ $a->reviewer?->name ?? '—' }}</span>
                </div>
                <div class="pa-field">
                    <span class="pa-label">Reviewed At</span>
                    <span class="pa-value">{{ $a->reviewed_at?->setTimezone(config('app.timezone'))->format('M d, Y H:i') ?? '—' }}</span>
                </div>
            </div>
            @if($a->rejection_reason)
            <div class="pa-field" style="margin-top:16px">
                <span class="pa-label">Rejection Reason</span>
                <span class="pa-value-long" style="color:#f87171;background:rgba(248,113,113,.07);border:1px solid rgba(248,113,113,.2);border-radius:8px;padding:10px 14px;margin-top:4px">{{ $a->rejection_reason }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Image lightbox (teleported to <body> so it centers on the real viewport).
         wire:ignore: without it, a wire:click-triggered re-render elsewhere on
         the page makes Livewire's DOM morph try to reconcile this block at its
         original position, which conflicts with Alpine's already-teleported copy. --}}
    <template x-teleport="body" wire:ignore>
    <div class="pa-lightbox-overlay" id="pa-lightbox" onclick="if(event.target===this)closeLightbox()">
        <div class="pa-lightbox-close" onclick="closeLightbox()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </div>
        <img id="pa-lightbox-img" src="" alt="Payout QR code (full size)">
    </div>
    </template>

    @if($a->status === 'pending')
    {{-- Approve Modal (teleported to <body> so it centers on the real viewport,
         not inside any transformed page wrapper). See wire:ignore note above. --}}
    <template x-teleport="body" wire:ignore>
    <div class="pa-modal-overlay" id="pa-approve-modal" onclick="if(event.target===this)closeApproveModal()">
        <div class="pa-modal">
            <h3>Verify Payout Account</h3>
            <p>Are you sure you want to verify <strong>{{ $a->instructor?->name }}</strong>'s payout account? They will be able to request payouts.</p>
            <div class="pa-modal-footer">
                <button class="pa-modal-btn pa-modal-btn-gray" onclick="closeApproveModal()">Cancel</button>
                <button class="pa-modal-btn pa-modal-btn-success" onclick="submitApprove()">Verify Account</button>
            </div>
        </div>
    </div>
    </template>

    {{-- Reject Modal — see wire:ignore note above. --}}
    <template x-teleport="body" wire:ignore>
    <div class="pa-modal-overlay" id="pa-reject-modal" onclick="if(event.target===this)closeRejectModal()">
        <div class="pa-modal">
            <h3>Reject Payout Account</h3>
            <p>Explain why <strong>{{ $a->instructor?->name }}</strong>'s payout account is being rejected. They will be notified.</p>
            <textarea id="pa-reject-reason" placeholder="e.g. QR code unreadable, name mismatch..."></textarea>
            <div class="pa-modal-footer">
                <button class="pa-modal-btn pa-modal-btn-gray" onclick="closeRejectModal()">Cancel</button>
                <button class="pa-modal-btn pa-modal-btn-danger" onclick="submitReject()">Reject Account</button>
            </div>
        </div>
    </div>
    </template>
    @endif

<script>
    function openLightbox(src) {
        document.getElementById('pa-lightbox-img').src = src;
        document.getElementById('pa-lightbox').classList.add('open');
    }
    function closeLightbox() {
        document.getElementById('pa-lightbox').classList.remove('open');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });

    function openApproveModal() {
        document.getElementById('pa-approve-modal').classList.add('open');
    }
    function closeApproveModal() {
        document.getElementById('pa-approve-modal').classList.remove('open');
    }
    function submitApprove() {
        closeApproveModal();
        @this.call('approve');
    }

    function openRejectModal() {
        document.getElementById('pa-reject-reason').value = '';
        document.getElementById('pa-reject-modal').classList.add('open');
        setTimeout(() => document.getElementById('pa-reject-reason').focus(), 100);
    }
    function closeRejectModal() {
        document.getElementById('pa-reject-modal').classList.remove('open');
    }
    function submitReject() {
        const reason = document.getElementById('pa-reject-reason').value.trim();
        if (!reason) { alert('Please provide a rejection reason.'); return; }
        closeRejectModal();
        @this.call('reject', reason);
    }
</script>
</div>
