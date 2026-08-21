@php
    $accent = '#9333ea';

    $viewUrl = fn($n) => route('filament.admin.resources.notifications.view', ['record' => $n->id]);

    $shortType = fn($type) => class_exists($type)
        ? (new \ReflectionClass($type))->getShortName()
        : (str_contains($type, '\\') ? substr($type, strrpos($type, '\\') + 1) : $type);
@endphp

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
    font-family:ui-monospace, 'Cascadia Code', monospace;
    font-size:11px;
    font-weight:700;
    color:var(--t2);
    white-space:nowrap;
}
.lp-type {
    font-size:12px;
    font-weight:650;
    color:var(--t1);
    max-width:220px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    display:block;
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
.lp-message {
    font-size:12px;
    color:var(--t2);
    max-width:260px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    display:block;
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
    width:30px;
    height:30px;
    border-radius:7px;
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
    width:14px;
    height:14px;
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

<div>
<div class="lp" id="lp-notifications" style="--accent:{{ $accent }}">

    {{-- Header --}}
    <div class="lp-header lpa lp1">
        <div class="lp-header-text">
            <h1>Notifications</h1>
            <p>View all system notifications sent to platform users.</p>
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
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search user or message...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="lp-loading" wire:target="selectTab,gotoPage,setPerPage,search">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Message</th>
                    <th>Read</th>
                    <th>Sent At</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                @php
                    $data    = is_array($notification->data) ? $notification->data : (json_decode($notification->data, true) ?? []);
                    $msg     = $data['title'] ?? $data['message'] ?? $data['body'] ?? 'Notification';
                    $msg     = \Illuminate\Support\Str::limit($msg, 60);
                    $typeName = $shortType($notification->type);
                    $isRead  = !is_null($notification->read_at);
                    $notifiable = $notification->notifiable;
                    $userName = ($notifiable instanceof \App\Domains\Users\Models\User)
                        ? $notifiable->name
                        : '—';
                    $bgHex = substr(md5($userName), 0, 6);
                    $avUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=' . $bgHex . '&color=fff&bold=true&size=64';
                    $shortId = substr($notification->id, 0, 8);
                @endphp
                <tr class="lp-row-link" wire:key="notification-row-{{ $notification->id }}" onclick="Livewire.navigate('{{ $viewUrl($notification) }}')">
                    <td><span class="lp-id">{{ $shortId }}…</span></td>

                    <td>
                        <span class="lp-type" title="{{ $notification->type }}">{{ $typeName }}</span>
                    </td>

                    <td>
                        @if($notifiable)
                        <div class="lp-user-cell">
                            <img src="{{ $avUrl }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0" alt="">
                            <span class="lp-user-name">{{ $userName }}</span>
                        </div>
                        @else
                            <span style="color:var(--t2)">—</span>
                        @endif
                    </td>

                    <td><span class="lp-message">{{ $msg }}</span></td>

                    <td>
                        @if($isRead)
                            <span class="lp-badge" style="background:rgba(52,211,153,.12);color:#34d399">
                                <span class="lp-dot" style="background:#34d399"></span>
                                Read
                            </span>
                        @else
                            <span class="lp-badge" style="background:rgba(248,113,113,.12);color:#f87171">
                                <span class="lp-dot" style="background:#f87171"></span>
                                New
                            </span>
                        @endif
                    </td>

                    <td><span class="lp-date">{{ $notification->created_at?->setTimezone(config('app.timezone'))->format('M d, Y H:i') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="lp-actions">
                            <a href="{{ $viewUrl($notification) }}" wire:navigate class="lp-act-btn" title="View">
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                            </svg>
                            <p>No notifications found.</p>
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
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} notifications
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

</div>
</div>
