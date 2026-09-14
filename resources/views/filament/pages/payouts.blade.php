@php
    $accent = '#2563eb';

    $statusStyle = fn ($status) => match ($status) {
        'pending' => [
            'bg' => 'rgba(251,191,36,.12)',
            'color' => '#fbbf24',
            'label' => 'Pending',
        ],

        'approved' => [
            'bg' => 'rgba(52,211,153,.12)',
            'color' => '#34d399',
            'label' => 'Completed',
        ],

        'rejected' => [
            'bg' => 'rgba(248,113,113,.12)',
            'color' => '#f87171',
            'label' => 'Rejected',
        ],

        default => [
            'bg' => 'rgba(148,163,184,.1)',
            'color' => '#94a3b8',
            'label' => ucfirst($status ?? 'Unknown'),
        ],
    };

    $selectedAccount = $selectedPayout?->payoutAccount;
    $selectedDetails = $selectedPayout?->details ?? [];
    $currency = fn ($amount, $code) =>
        number_format((float) $amount, 2) . ' ' . strtoupper($code ?: 'USD');
@endphp

<div wire:poll.30s>
<div class="lp" id="lp-payouts" style="--accent:{{ $accent }}">

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
    --p3:#334155;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t1s:#f8fafc;
    --t2:#64748b;
    --t3:#334155;
    --input:#0f172a;
    --sh:0 4px 24px rgba(0,0,0,.3);
    --sh-modal:0 25px 80px rgba(0,0,0,.45);
    color:var(--t1);
}
html:not(.dark) .lp {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f8fafc;
    --p3:#e2e8f0;
    --bd:rgba(15,23,42,.10);
    --bd2:rgba(15,23,42,.18);
    --t1:#334155;
    --t1s:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --input:#ffffff;
    --sh:0 2px 16px rgba(15,23,42,.1);
    --sh-modal:0 25px 80px rgba(15,23,42,.20);
}
@keyframes lpUp {
    from { opacity:0; transform:translateY(12px); }
    to { opacity:1; transform:none; }
}
.lpa { opacity:0; animation:lpUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.lp1 { animation-delay:.04s; }
.lp2 { animation-delay:.09s; }

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
    color:var(--t1s);
    line-height:1.15;
}
.lp-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.lp-header-btns {
    display:flex;
    align-items:center;
    gap:10px;
    flex-shrink:0;
}
.lp-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    letter-spacing:.02em;
    cursor:pointer;
    text-decoration:none;
    transition:opacity .18s, transform .15s;
    white-space:nowrap;
    border:1px solid var(--bd2);
    background:var(--p2);
    color:var(--t1);
    font-family:inherit;
}
.lp-btn:hover { opacity:.85; transform:translateY(-1px); }

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
.lp-tabs { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
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
.lp-tab:hover { background:var(--p2); color:var(--t1s); }
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
.lp-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.lp-search-box input {
    background:none;
    border:none;
    outline:none;
    color:var(--t1s);
    font-size:12px;
    font-family:inherit;
    width:200px;
}
.lp-search-box input::placeholder { color:var(--t2); }

.lp-table { width:100%; border-collapse:collapse; }
.lp-table thead tr { border-bottom:1px solid var(--bd); }
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
.lp-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.lp-table tbody tr:last-child { border-bottom:none; }
.lp-table tbody tr:hover { background:var(--p2); }
.lp-table td { padding:12px; vertical-align:middle; }

.lp-id { font-size:11.5px; font-weight:700; color:var(--t2); white-space:nowrap; }
.lp-user-cell { display:flex; align-items:center; gap:8px; }
.lp-user-text { display:flex; flex-direction:column; gap:2px; }
.lp-user-name { font-size:12.5px; color:var(--t1s); font-weight:650; }
.lp-user-email { font-size:11px; color:var(--t2); }
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
.lp-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.lp-amount { font-size:12.5px; font-weight:700; color:var(--t1s); white-space:nowrap; }
.lp-method { font-size:11px; font-weight:700; color:var(--t2); text-transform:uppercase; }
.lp-date { font-size:12px; color:var(--t2); white-space:nowrap; }

.lp-actions { display:flex; align-items:center; gap:5px; justify-content:flex-end; }
.lp-act-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    height:30px;
    padding:0 10px;
    border-radius:7px;
    font-size:11.5px;
    font-weight:700;
    background:none;
    border:1px solid transparent;
    cursor:pointer;
    text-decoration:none;
    transition:background .15s, border-color .15s, color .15s;
    font-family:inherit;
}
.lp-act-view { color:#2563eb; background:rgba(37,99,235,.08); border-color:rgba(37,99,235,.16); }
.lp-act-view:hover { background:rgba(37,99,235,.16); }
.lp-act-approve { color:#059669; background:rgba(16,185,129,.08); border-color:rgba(16,185,129,.16); }
.dark .lp-act-approve { color:#34d399; }
.lp-act-approve:hover { background:rgba(16,185,129,.16); }
.lp-act-reject { color:#dc2626; background:rgba(239,68,68,.08); border-color:rgba(239,68,68,.16); }
.dark .lp-act-reject { color:#f87171; }
.lp-act-reject:hover { background:rgba(239,68,68,.16); }

.lp-empty {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:56px 24px;
    gap:6px;
    color:var(--t2);
}
.lp-empty strong { color:var(--t1s); font-size:13px; }

.lp-footer {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    border-top:1px solid var(--bd);
    flex-wrap:wrap;
    gap:10px;
}
.lp-footer-info { font-size:12px; color:var(--t2); }
.lp-pages { display:flex; align-items:center; gap:6px; }
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
    color:var(--t2);
    background:none;
    font-family:inherit;
    cursor:pointer;
    border:1px solid transparent;
    transition:background .15s, border-color .15s, color .15s;
}
.lp-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1s); }
.lp-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.lp-page-btn.disabled { opacity:.35; pointer-events:none; }
.lp-loading { opacity:.45; pointer-events:none; transition:opacity .1s; }

/* Modals — same token system as the rest of the page */
.lp-modal { position:fixed; inset:0; z-index:99999; display:flex; align-items:center; justify-content:center; padding:24px; }
.lp-modal-backdrop { position:absolute; inset:0; background:rgba(2,6,23,.68); }
html:not(.dark) .lp-modal-backdrop { background:rgba(15,23,42,.45); }
.lp-modal-panel {
    position:relative;
    z-index:1;
    width:100%;
    max-width:760px;
    max-height:calc(100vh - 48px);
    overflow-y:auto;
    border-radius:16px;
    border:1px solid var(--bd2);
    background:var(--p1);
    box-shadow:var(--sh-modal);
}
.lp-modal-header {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:15px;
    padding:20px 22px;
    border-bottom:1px solid var(--bd);
}
.lp-modal-title { color:var(--t1s); font-size:16px; font-weight:800; }
.lp-modal-subtitle { margin-top:4px; color:var(--t2); font-size:11px; }
.lp-close {
    width:32px; height:32px;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius:8px;
    border:1px solid var(--bd2);
    background:var(--p2);
    color:var(--t2);
    cursor:pointer;
    font-size:17px;
}
.lp-close:hover { color:var(--t1s); background:var(--p3); }
.lp-modal-body { padding:22px; }
.lp-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:18px; }
.lp-summary-card { padding:14px; border-radius:11px; border:1px solid var(--bd); background:var(--p2); }
.lp-summary-label { color:var(--t2); font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.07em; }
.lp-summary-value { margin-top:5px; color:var(--t1s); font-size:14px; font-weight:800; }
.lp-section { margin-top:15px; padding:17px; border-radius:12px; border:1px solid var(--bd); background:var(--p2); }
.lp-section-title { margin-bottom:14px; color:var(--t1s); font-size:12px; font-weight:800; }
.lp-info-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
.lp-info-label { color:var(--t2); font-size:9px; text-transform:uppercase; letter-spacing:.07em; font-weight:800; }
.lp-info-value { margin-top:4px; color:var(--t1); font-size:12px; word-break:break-word; }
.lp-destination { display:grid; grid-template-columns:1fr 240px; gap:22px; align-items:start; }
.lp-qr { width:100%; max-width:230px; margin:0 auto; display:block; border-radius:10px; padding:10px; background:white; border:1px solid var(--bd2); }
.lp-no-qr { display:flex; align-items:center; justify-content:center; min-height:180px; border-radius:10px; border:1px dashed var(--bd2); color:var(--t2); font-size:11px; text-align:center; }
.lp-warning { margin-top:15px; padding:12px 14px; border-radius:9px; border:1px solid rgba(251,191,36,.20); background:rgba(251,191,36,.08); color:#b45309; font-size:11px; line-height:1.5; }
.dark .lp-warning { color:#fbbf24; }
.lp-modal-footer { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:16px 22px; border-top:1px solid var(--bd); }
.lp-btn-modal { min-height:36px; padding:0 14px; border-radius:8px; border:1px solid transparent; font-size:11px; font-weight:800; cursor:pointer; font-family:inherit; }
.lp-btn-secondary { background:var(--p3); border-color:var(--bd2); color:var(--t1); }
html:not(.dark) .lp-btn-secondary { background:var(--p1); }
.lp-btn-secondary:hover { background:var(--p3); }
.lp-btn-success { background:#10b981; color:white; }
.lp-btn-success:hover { background:#059669; }
.lp-btn-danger { background:#dc2626; color:white; }
.lp-btn-danger:hover { background:#b91c1c; }
.lp-input-label { display:block; margin-bottom:7px; color:var(--t2); font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.07em; }
.lp-input, .lp-textarea {
    width:100%;
    border:1px solid var(--bd2);
    border-radius:9px;
    background:var(--input);
    color:var(--t1s);
    outline:none;
    font-size:12px;
    font-family:inherit;
}
.lp-input { height:42px; padding:0 12px; }
.lp-textarea { min-height:110px; padding:11px 12px; resize:vertical; }
.lp-input::placeholder, .lp-textarea::placeholder { color:var(--t2); }
.lp-input:focus, .lp-textarea:focus { border-color:rgba(37,99,235,.65); box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.lp-help { margin-top:7px; color:var(--t2); font-size:10px; line-height:1.5; }

@media(max-width:800px){
    .lp-summary{grid-template-columns:1fr}
    .lp-destination{grid-template-columns:1fr}
    .lp-info-grid{grid-template-columns:1fr}
    .lp-header{flex-direction:column}
}
</style>

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Instructor Payouts</h1>
            <p>Review payout requests, verify payment destinations, and record completed instructor payments.</p>
        </div>
        <div class="lp-header-btns">
            <button type="button" class="lp-btn" wire:click="$refresh">
                ↻ Refresh
            </button>
        </div>
    </div>

    {{-- Table card --}}
    <div class="lp-card lpa lp2">

        {{-- Toolbar --}}
        <div class="lp-toolbar">
            <div class="lp-tabs">
                @foreach($tabs as $t)
                    @php
                        $isActive = $tab === $t['key'];
                        $tabColor = $t['color'];
                        $tabStyle = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
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
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search instructor...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="lp-loading" wire:target="selectTab,gotoPage,search">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Instructor</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                    @php
                        $style = $statusStyle($payout->status);
                        $bgHex = substr(md5($payout->instructor?->name ?? ''), 0, 6);
                        $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($payout->instructor?->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                    @endphp
                    <tr wire:key="payout-{{ $payout->id }}">
                        <td><span class="lp-id">#{{ $payout->id }}</span></td>

                        <td>
                            <div class="lp-user-cell">
                                <img src="{{ $avUrl }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">
                                <div class="lp-user-text">
                                    <span class="lp-user-name">{{ $payout->instructor?->name ?? 'Unknown instructor' }}</span>
                                    <span class="lp-user-email">{{ $payout->instructor?->email ?? '—' }}</span>
                                </div>
                            </div>
                        </td>

                        <td><span class="lp-amount">{{ $currency($payout->amount, $payout->currency) }}</span></td>

                        <td><span class="lp-method">{{ strtoupper($payout->payment_method ?? '—') }}</span></td>

                        <td>
                            <span class="lp-badge" style="background:{{ $style['bg'] }};color:{{ $style['color'] }}">
                                <span class="lp-dot" style="background:{{ $style['color'] }}"></span>
                                {{ $style['label'] }}
                            </span>
                        </td>

                        <td><span class="lp-date">{{ optional($payout->requested_at ?? $payout->created_at)->format('M d, Y H:i') }}</span></td>

                        <td>
                            <div class="lp-actions">
                                <a
                                    href="{{ route('filament.admin.resources.payout-requests.view', ['record' => $payout->id]) }}"
                                    class="lp-act-btn lp-act-view"
                                    wire:navigate
                                >
                                    View
                                </a>
                                @if($canUpdate && $payout->status === 'pending')
                                    <button type="button" class="lp-act-btn lp-act-approve" wire:click="openApprove({{ $payout->id }})">
                                        Pay
                                    </button>
                                    <button type="button" class="lp-act-btn lp-act-reject" wire:click="openReject({{ $payout->id }})">
                                        Reject
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="lp-empty">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.35">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/>
                                </svg>
                                <strong>No payout requests found</strong>
                                <p>There are no payout requests matching the current filter.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Footer --}}
        <div class="lp-footer">
            <div class="lp-footer-info">
                Showing page {{ $curPage }} of {{ $totalPages }} · {{ $total }} total payouts
            </div>
            <div class="lp-pages">
                <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                   class="lp-page-btn {{ $curPage <= 1 ? 'disabled' : '' }}" @disabled($curPage <= 1)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
                @for($i = max(1, $curPage - 2); $i <= min($totalPages, $curPage + 2); $i++)
                    <button type="button" wire:click="gotoPage({{ $i }})"
                       class="lp-page-btn {{ $i === $curPage ? 'active' : '' }}">
                        {{ $i }}
                    </button>
                @endfor
                <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                   class="lp-page-btn {{ $curPage >= $totalPages ? 'disabled' : '' }}" @disabled($curPage >= $totalPages)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- DETAILS MODAL --}}
    @if($modal === 'details' && $selectedPayout)
        @php
            $detailStyle = $statusStyle($selectedPayout->status);
            $detailAccount = $selectedPayout->payoutAccount;
            $detailQr = $detailAccount?->qr_code_url;
        @endphp
        <div class="lp-modal">
            <div class="lp-modal-backdrop" wire:click="closeModal"></div>
            <div class="lp-modal-panel">
                <div class="lp-modal-header">
                    <div>
                        <div class="lp-modal-title">Payout #{{ $selectedPayout->id }}</div>
                        <div class="lp-modal-subtitle">Review payout details before processing.</div>
                    </div>
                    <button type="button" class="lp-close" wire:click="closeModal">×</button>
                </div>
                <div class="lp-modal-body">
                    <div class="lp-summary">
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Amount</div>
                            <div class="lp-summary-value">{{ $currency($selectedPayout->amount, $selectedPayout->currency) }}</div>
                        </div>
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Status</div>
                            <div class="lp-summary-value" style="color:{{ $detailStyle['color'] }}">{{ $detailStyle['label'] }}</div>
                        </div>
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Method</div>
                            <div class="lp-summary-value">{{ strtoupper($selectedPayout->payment_method ?? '—') }}</div>
                        </div>
                    </div>

                    <div class="lp-section">
                        <div class="lp-section-title">Instructor</div>
                        <div class="lp-info-grid">
                            <div>
                                <div class="lp-info-label">Name</div>
                                <div class="lp-info-value">{{ $selectedPayout->instructor?->name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="lp-info-label">Email</div>
                                <div class="lp-info-value">{{ $selectedPayout->instructor?->email ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="lp-info-label">Requested</div>
                                <div class="lp-info-value">{{ optional($selectedPayout->requested_at ?? $selectedPayout->created_at)->format('M d, Y H:i') }}</div>
                            </div>
                            <div>
                                <div class="lp-info-label">Source</div>
                                <div class="lp-info-value">{{ ucfirst(str_replace('_', ' ', $selectedPayout->source ?? 'manual')) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="lp-section">
                        <div class="lp-section-title">Payout Destination</div>
                        <div class="lp-destination">
                            <div>
                                <div class="lp-info-grid">
                                    <div>
                                        <div class="lp-info-label">Payment method</div>
                                        <div class="lp-info-value">{{ strtoupper($detailAccount?->method ?? $selectedPayout->payment_method ?? '—') }}</div>
                                    </div>
                                    <div>
                                        <div class="lp-info-label">Account name</div>
                                        <div class="lp-info-value">{{ $detailAccount?->account_name ?? ($selectedDetails['account_name'] ?? '—') }}</div>
                                    </div>
                                    <div>
                                        <div class="lp-info-label">Account number</div>
                                        <div class="lp-info-value">{{ $detailAccount?->account_number ?? ($selectedDetails['account_number'] ?? '—') }}</div>
                                    </div>
                                    <div>
                                        <div class="lp-info-label">Phone number</div>
                                        <div class="lp-info-value">{{ $detailAccount?->phone_number ?? ($selectedDetails['phone_number'] ?? '—') }}</div>
                                    </div>
                                </div>
                                <div class="lp-warning">
                                    Verify the recipient name and destination carefully before sending the payout.
                                    The external payment should be completed before marking this request as completed.
                                </div>
                            </div>
                            <div>
                                @if($detailQr)
                                    <img src="{{ $detailQr }}" alt="Payout QR code" class="lp-qr">
                                @else
                                    <div class="lp-no-qr">No QR code available</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($selectedPayout->transaction_reference)
                        <div class="lp-section">
                            <div class="lp-section-title">Transaction</div>
                            <div class="lp-info-grid">
                                <div>
                                    <div class="lp-info-label">Reference</div>
                                    <div class="lp-info-value">{{ $selectedPayout->transaction_reference }}</div>
                                </div>
                                <div>
                                    <div class="lp-info-label">Processed at</div>
                                    <div class="lp-info-value">{{ optional($selectedPayout->processed_at)->format('M d, Y H:i') ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedPayout->rejection_reason)
                        <div class="lp-section">
                            <div class="lp-section-title">Rejection reason</div>
                            <div class="lp-info-value">{{ $selectedPayout->rejection_reason }}</div>
                        </div>
                    @endif
                </div>
                <div class="lp-modal-footer">
                    <button type="button" class="lp-btn-modal lp-btn-secondary" wire:click="closeModal">Close</button>
                    @if($canUpdate && $selectedPayout->status === 'pending')
                        <button type="button" class="lp-btn-modal lp-btn-danger" wire:click="openReject({{ $selectedPayout->id }})">Reject</button>
                        <button type="button" class="lp-btn-modal lp-btn-success" wire:click="openApprove({{ $selectedPayout->id }})">Pay & Complete</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- APPROVE MODAL --}}
    @if($modal === 'approve' && $selectedPayout)
        @php
            $approveAccount = $selectedPayout->payoutAccount;
            $approveQr = $approveAccount?->qr_code_url;
        @endphp
        <div class="lp-modal">
            <div class="lp-modal-backdrop" wire:click="closeModal"></div>
            <div class="lp-modal-panel">
                <div class="lp-modal-header">
                    <div>
                        <div class="lp-modal-title">Complete Payout</div>
                        <div class="lp-modal-subtitle">Verify the payment destination before confirming.</div>
                    </div>
                    <button type="button" class="lp-close" wire:click="closeModal">×</button>
                </div>
                <div class="lp-modal-body">
                    <div class="lp-summary">
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Amount to pay</div>
                            <div class="lp-summary-value">{{ $currency($selectedPayout->amount, $selectedPayout->currency) }}</div>
                        </div>
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Instructor</div>
                            <div class="lp-summary-value">{{ $selectedPayout->instructor?->name ?? '—' }}</div>
                        </div>
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Method</div>
                            <div class="lp-summary-value">{{ strtoupper($selectedPayout->payment_method ?? '—') }}</div>
                        </div>
                    </div>

                    <div class="lp-section">
                        <div class="lp-section-title">Payment Destination</div>
                        <div class="lp-destination">
                            <div>
                                <div class="lp-info-grid">
                                    <div>
                                        <div class="lp-info-label">Account name</div>
                                        <div class="lp-info-value">{{ $approveAccount?->account_name ?? ($selectedPayout->details['account_name'] ?? '—') }}</div>
                                    </div>
                                    <div>
                                        <div class="lp-info-label">Account number</div>
                                        <div class="lp-info-value">{{ $approveAccount?->account_number ?? ($selectedPayout->details['account_number'] ?? '—') }}</div>
                                    </div>
                                    <div>
                                        <div class="lp-info-label">Phone</div>
                                        <div class="lp-info-value">{{ $approveAccount?->phone_number ?? ($selectedPayout->details['phone_number'] ?? '—') }}</div>
                                    </div>
                                    <div>
                                        <div class="lp-info-label">Instructor</div>
                                        <div class="lp-info-value">{{ $selectedPayout->instructor?->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($approveQr)
                                    <img src="{{ $approveQr }}" alt="Payout QR code" class="lp-qr">
                                @else
                                    <div class="lp-no-qr">No QR code available</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="lp-section">
                        <div class="lp-section-title">Payment confirmation</div>
                        <label class="lp-input-label">Transaction reference *</label>
                        <input
                            type="text"
                            class="lp-input"
                            wire:model="approveReference"
                            placeholder="e.g. KHQR transaction ID / bank transfer reference"
                            maxlength="150"
                        >
                        <div class="lp-help">
                            First complete the external payment. Then enter the transaction reference here.
                            This reference becomes part of the payout audit record.
                        </div>
                    </div>
                </div>
                <div class="lp-modal-footer">
                    <button type="button" class="lp-btn-modal lp-btn-secondary" wire:click="closeModal">Cancel</button>
                    <button
                        type="button"
                        class="lp-btn-modal lp-btn-success"
                        wire:click="approve"
                        wire:loading.attr="disabled"
                        wire:target="approve"
                    >
                        <span wire:loading.remove wire:target="approve">Confirm Payout</span>
                        <span wire:loading wire:target="approve">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- REJECT MODAL --}}
    @if($modal === 'reject' && $selectedPayout)
        <div class="lp-modal">
            <div class="lp-modal-backdrop" wire:click="closeModal"></div>
            <div class="lp-modal-panel">
                <div class="lp-modal-header">
                    <div>
                        <div class="lp-modal-title">Reject Payout</div>
                        <div class="lp-modal-subtitle">The payout amount will be returned to the instructor wallet.</div>
                    </div>
                    <button type="button" class="lp-close" wire:click="closeModal">×</button>
                </div>
                <div class="lp-modal-body">
                    <div class="lp-summary">
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Payout</div>
                            <div class="lp-summary-value">#{{ $selectedPayout->id }}</div>
                        </div>
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Instructor</div>
                            <div class="lp-summary-value">{{ $selectedPayout->instructor?->name ?? '—' }}</div>
                        </div>
                        <div class="lp-summary-card">
                            <div class="lp-summary-label">Amount</div>
                            <div class="lp-summary-value">{{ $currency($selectedPayout->amount, $selectedPayout->currency) }}</div>
                        </div>
                    </div>

                    <div class="lp-section">
                        <label class="lp-input-label">Rejection reason *</label>
                        <textarea
                            class="lp-textarea"
                            wire:model="rejectReason"
                            maxlength="1000"
                            placeholder="Explain why this payout cannot be processed..."
                        ></textarea>
                        <div class="lp-help">
                            The instructor will receive this reason with the payout rejection notification.
                        </div>
                    </div>
                </div>
                <div class="lp-modal-footer">
                    <button type="button" class="lp-btn-modal lp-btn-secondary" wire:click="closeModal">Cancel</button>
                    <button
                        type="button"
                        class="lp-btn-modal lp-btn-danger"
                        wire:click="reject"
                        wire:loading.attr="disabled"
                        wire:target="reject"
                    >
                        <span wire:loading.remove wire:target="reject">Reject Payout</span>
                        <span wire:loading wire:target="reject">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
</div>