@php
    $v       = $record->load(['user', 'reviewer']);
    $accent  = '#ea580c';

    $ss = match($v->status) {
        'pending'  => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'label' => 'Pending'],
        'approved' => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'label' => 'Approved'],
        'rejected' => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'label' => 'Rejected'],
        default    => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'label' => ucfirst($v->status ?? '—')],
    };

    $qual = ucfirst(str_replace('_', ' ', $v->qualification_type ?? '—'));

    $certUrl  = $v->certificate_file ? \Illuminate\Support\Facades\Storage::disk('public')->url($v->certificate_file) : null;
    $idUrl    = $v->identity_file    ? \Illuminate\Support\Facades\Storage::disk('public')->url($v->identity_file)    : null;

    $bgHex  = substr(md5($v->user?->name ?? ''), 0, 6);
    $avUrl  = 'https://ui-avatars.com/api/?name=' . urlencode($v->user?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=128';
    $listUrl = route('filament.admin.pages.instructor-verifications');
@endphp

<style>
.iv, .iv *, .iv *::before, .iv *::after { box-sizing:border-box; margin:0; padding:0; }
.iv {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    font-size:13px; line-height:1.5;
    padding-bottom:48px;
    display:grid; gap:20px;
    --bg:#0f172a; --p1:#1e293b; --p2:#263245;
    --bd:rgba(255,255,255,.07); --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0; --t2:#64748b; --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .iv {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.08); --bd2:rgba(15,23,42,.14);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes ivUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.iva { opacity:0; animation:ivUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.iv1{animation-delay:.04s} .iv2{animation-delay:.09s} .iv3{animation-delay:.14s} .iv4{animation-delay:.19s}

/* Header */
.iv-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.iv-header-left { display:flex; align-items:center; gap:12px; }
.iv-back { display:inline-flex; align-items:center; gap:5px; padding:7px 13px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; color:var(--t2); background:var(--p1); border:1px solid var(--bd2); transition:background .15s, color .15s; }
.iv-back:hover { background:var(--p2); color:var(--t1); }
.iv-back svg { width:14px; height:14px; }
.iv-header-title h1 { font-size:clamp(18px,2vw,24px); font-weight:780; letter-spacing:-.018em; color:var(--t1); }
.iv-header-title p { font-size:12px; color:var(--t2); margin-top:3px; }
.iv-header-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

/* Action buttons */
.iv-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s, transform .1s; }
.iv-btn:hover { opacity:.85; }
.iv-btn:active { transform:scale(.97); }
.iv-btn-approve { background:rgba(52,211,153,.15); color:#34d399; border:1px solid rgba(52,211,153,.3); }
.iv-btn-reject  { background:rgba(248,113,113,.15); color:#f87171; border:1px solid rgba(248,113,113,.3); }
.iv-btn svg { width:16px; height:16px; }

/* Cards */
.iv-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.iv-card-header { display:flex; align-items:center; gap:10px; padding:16px 20px; border-bottom:1px solid var(--bd); }
.iv-card-header svg { width:18px; height:18px; color:var(--t2); flex-shrink:0; }
.iv-card-title { font-size:13px; font-weight:750; color:var(--t1); letter-spacing:-.01em; }
.iv-card-body { padding:20px; }

/* Grid */
.iv-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:16px; }
.iv-grid-wide { display:grid; grid-template-columns:1fr; gap:16px; }

/* Fields */
.iv-field { display:flex; flex-direction:column; gap:4px; }
.iv-label { font-size:10.5px; font-weight:800; letter-spacing:.07em; text-transform:uppercase; color:var(--t2); }
.iv-value { font-size:13px; color:var(--t1); word-break:break-word; }
.iv-value a { color:#ea580c; text-decoration:none; }
.iv-value a:hover { text-decoration:underline; }
.iv-value-muted { color:var(--t2); font-style:italic; }
.iv-value-long { font-size:12.5px; line-height:1.7; color:var(--t1); white-space:pre-wrap; }

/* Profile section */
.iv-profile { display:flex; align-items:center; gap:16px; }
.iv-avatar { width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid var(--bd2); flex-shrink:0; }
.iv-profile-info { display:flex; flex-direction:column; gap:4px; }
.iv-profile-name { font-size:16px; font-weight:750; color:var(--t1); }
.iv-profile-email { font-size:12.5px; color:var(--t2); }

/* Badge */
.iv-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:700; white-space:nowrap; }
.iv-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }

/* Documents */
.iv-docs { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:16px; }
.iv-doc-box { border:1px solid var(--bd2); border-radius:10px; overflow:hidden; background:var(--p2); }
.iv-doc-box img { width:100%; height:160px; object-fit:cover; display:block; }
.iv-doc-label { padding:8px 12px; font-size:11.5px; font-weight:700; color:var(--t2); text-transform:uppercase; letter-spacing:.06em; border-top:1px solid var(--bd); display:flex; align-items:center; justify-content:space-between; }
.iv-doc-label a { color:#ea580c; font-size:11px; font-weight:700; text-decoration:none; }
.iv-doc-label a:hover { text-decoration:underline; }
.iv-doc-missing { display:flex; flex-direction:column; align-items:center; justify-content:center; height:160px; gap:6px; color:var(--t2); font-size:12px; }
.iv-doc-missing svg { width:32px; height:32px; opacity:.35; }

/* Modal */
.iv-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; }
.iv-modal-overlay.open { display:flex; }
.iv-modal { background:var(--p1); border:1px solid var(--bd2); border-radius:14px; padding:26px; width:100%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,.4); }
.iv-modal h3 { font-size:15px; font-weight:750; color:var(--t1); margin-bottom:6px; }
.iv-modal p { font-size:12.5px; color:var(--t2); margin-bottom:16px; }
.iv-modal textarea { width:100%; background:var(--p2); border:1px solid var(--bd2); border-radius:9px; padding:10px 13px; color:var(--t1); font-size:13px; font-family:inherit; resize:vertical; min-height:90px; outline:none; }
.iv-modal textarea:focus { border-color:#ea580c; }
.iv-modal-footer { display:flex; justify-content:flex-end; gap:8px; margin-top:14px; }
.iv-modal-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s; }
.iv-modal-btn-gray { background:var(--p2); border:1px solid var(--bd2); color:var(--t2); }
.iv-modal-btn-danger { background:rgba(248,113,113,.15); color:#f87171; border:1px solid rgba(248,113,113,.3); }
.iv-modal-btn-success { background:rgba(52,211,153,.15); color:#34d399; border:1px solid rgba(52,211,153,.3); }
</style>

<div>
<div class="iv" id="iv-root" style="--accent:{{ $accent }};">

    {{-- Header --}}
    <div class="iv-header iva iv1">
        <div class="iv-header-left" style="flex-direction:column;align-items:flex-start;gap:10px">
            <a href="{{ $listUrl }}" wire:navigate class="iv-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Verifications
            </a>
            <div class="iv-header-title">
                <h1>Instructor Verification #{{ $v->id }}</h1>
                <p>Submitted {{ $v->created_at?->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>

        @if($v->status === 'pending')
        <div class="iv-header-actions">
            <button class="iv-btn iv-btn-reject" onclick="openRejectModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                Reject Application
            </button>
            <button class="iv-btn iv-btn-approve" onclick="openApproveModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                Approve Application
            </button>
        </div>
        @else
        <div class="iv-header-actions">
            <span class="iv-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }};font-size:13px;padding:7px 14px;">
                <span class="iv-dot" style="background:{{ $ss['color'] }}"></span>
                {{ $ss['label'] }}
            </span>
        </div>
        @endif
    </div>

    {{-- Applicant Information --}}
    <div class="iv-card iva iv2">
        <div class="iv-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
            <span class="iv-card-title">Applicant Information</span>
        </div>
        <div class="iv-card-body">
            <div class="iv-profile" style="margin-bottom:20px">
                <img src="{{ $avUrl }}" class="iv-avatar" alt="{{ $v->user?->name }}">
                <div class="iv-profile-info">
                    <span class="iv-profile-name">{{ $v->user?->name ?? '—' }}</span>
                    <span class="iv-profile-email">{{ $v->user?->email ?? '—' }}</span>
                </div>
            </div>
            <div class="iv-grid">
                <div class="iv-field">
                    <span class="iv-label">User ID</span>
                    <span class="iv-value">#{{ $v->user_id }}</span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Identity ID</span>
                    <span class="iv-value">{{ $v->identity_id ?? '—' }}</span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Applied</span>
                    <span class="iv-value">{{ $v->created_at?->format('M d, Y') ?? '—' }}</span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Application Status</span>
                    <span class="iv-value">
                        <span class="iv-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="iv-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Professional Information --}}
    <div class="iv-card iva iv3">
        <div class="iv-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008z"/>
            </svg>
            <span class="iv-card-title">Professional Information</span>
        </div>
        <div class="iv-card-body" style="display:grid;gap:20px">
            <div class="iv-grid">
                <div class="iv-field">
                    <span class="iv-label">Qualification Type</span>
                    <span class="iv-value">{{ $qual }}</span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Institution / Organization</span>
                    <span class="iv-value">{{ $v->institution ?? '—' }}</span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Completion Year</span>
                    <span class="iv-value">{{ $v->completion_year ?? '—' }}</span>
                </div>
                @if($v->portfolio_url)
                <div class="iv-field">
                    <span class="iv-label">Portfolio URL</span>
                    <span class="iv-value">
                        <a href="{{ $v->portfolio_url }}" target="_blank" rel="noopener">{{ $v->portfolio_url }}</a>
                    </span>
                </div>
                @endif
            </div>
            <div class="iv-field">
                <span class="iv-label">Professional Bio</span>
                @if($v->bio)
                    <span class="iv-value-long">{{ $v->bio }}</span>
                @else
                    <span class="iv-value-muted">No bio provided.</span>
                @endif
            </div>
            <div class="iv-field">
                <span class="iv-label">Teaching Experience</span>
                @if($v->experience)
                    <span class="iv-value-long">{{ $v->experience }}</span>
                @else
                    <span class="iv-value-muted">No experience provided.</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="iv-card iva iv4">
        <div class="iv-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
            </svg>
            <span class="iv-card-title">Documents</span>
        </div>
        <div class="iv-card-body">
            <div class="iv-docs">
                {{-- Certificate --}}
                <div class="iv-doc-box">
                    @if($certUrl)
                        <img src="{{ $certUrl }}" alt="Certificate">
                    @else
                        <div class="iv-doc-missing">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                            </svg>
                            No file uploaded
                        </div>
                    @endif
                    <div class="iv-doc-label">
                        Certificate
                        @if($certUrl)
                        <a href="{{ $certUrl }}" target="_blank" rel="noopener">View full</a>
                        @endif
                    </div>
                </div>

                {{-- Identity Document --}}
                <div class="iv-doc-box">
                    @if($idUrl)
                        <img src="{{ $idUrl }}" alt="Identity Document">
                    @else
                        <div class="iv-doc-missing">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0z"/>
                            </svg>
                            No file uploaded
                        </div>
                    @endif
                    <div class="iv-doc-label">
                        ID Proof
                        @if($idUrl)
                        <a href="{{ $idUrl }}" target="_blank" rel="noopener">View full</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Status --}}
    <div class="iv-card iva" style="animation-delay:.24s">
        <div class="iv-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <span class="iv-card-title">Review Status</span>
        </div>
        <div class="iv-card-body">
            <div class="iv-grid">
                <div class="iv-field">
                    <span class="iv-label">Status</span>
                    <span class="iv-value">
                        <span class="iv-badge" style="background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">
                            <span class="iv-dot" style="background:{{ $ss['color'] }}"></span>
                            {{ $ss['label'] }}
                        </span>
                    </span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Reviewed By</span>
                    <span class="iv-value">{{ $v->reviewer?->name ?? '—' }}</span>
                </div>
                <div class="iv-field">
                    <span class="iv-label">Reviewed At</span>
                    <span class="iv-value">{{ $v->reviewed_at?->format('M d, Y H:i') ?? '—' }}</span>
                </div>
            </div>
            @if($v->rejection_reason)
            <div class="iv-field" style="margin-top:16px">
                <span class="iv-label">Rejection Reason</span>
                <span class="iv-value-long" style="color:#f87171;background:rgba(248,113,113,.07);border:1px solid rgba(248,113,113,.2);border-radius:8px;padding:10px 14px;margin-top:4px">{{ $v->rejection_reason }}</span>
            </div>
            @endif
        </div>
    </div>

    @if($v->status === 'pending')
    {{-- Approve Modal --}}
    <div class="iv-modal-overlay" id="iv-approve-modal" onclick="if(event.target===this)closeApproveModal()">
        <div class="iv-modal">
            <h3>Approve Application</h3>
            <p>Are you sure you want to approve <strong>{{ $v->user?->name }}</strong>'s application? They will be notified by email.</p>
            <div class="iv-modal-footer">
                <button class="iv-modal-btn iv-modal-btn-gray" onclick="closeApproveModal()">Cancel</button>
                <button class="iv-modal-btn iv-modal-btn-success" onclick="submitApprove()">Approve Application</button>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="iv-modal-overlay" id="iv-reject-modal" onclick="if(event.target===this)closeRejectModal()">
        <div class="iv-modal">
            <h3>Reject Application</h3>
            <p>Explain why <strong>{{ $v->user?->name }}</strong>'s application is being rejected. They will be notified.</p>
            <textarea id="iv-reject-reason" placeholder="e.g. Insufficient qualifications, invalid documents..."></textarea>
            <div class="iv-modal-footer">
                <button class="iv-modal-btn iv-modal-btn-gray" onclick="closeRejectModal()">Cancel</button>
                <button class="iv-modal-btn iv-modal-btn-danger" onclick="submitReject()">Reject Application</button>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    function openApproveModal() {
        document.getElementById('iv-approve-modal').classList.add('open');
    }
    function closeApproveModal() {
        document.getElementById('iv-approve-modal').classList.remove('open');
    }
    function submitApprove() {
        closeApproveModal();
        @this.call('approve');
    }

    function openRejectModal() {
        document.getElementById('iv-reject-reason').value = '';
        document.getElementById('iv-reject-modal').classList.add('open');
        setTimeout(() => document.getElementById('iv-reject-reason').focus(), 100);
    }
    function closeRejectModal() {
        document.getElementById('iv-reject-modal').classList.remove('open');
    }
    function submitReject() {
        const reason = document.getElementById('iv-reject-reason').value.trim();
        if (!reason) { alert('Please provide a rejection reason.'); return; }
        closeRejectModal();
        @this.call('reject', reason);
    }
</script>
</div>
