@php
    /** @var \Illuminate\Notifications\DatabaseNotification $n */
    $firstAction = $actions[0] ?? null;
    $isRead      = !is_null($n->read_at);

    $typeIcons = [
        'order'                    => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-1.684 2.032-3.37 2.532-5.085a.75.75 0 0 0-.504-.9l-9.375-2.813a.75.75 0 0 0-.9.504L4.875 9.75M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>',
        'enrollment_confirmed'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.63 48.63 0 0 1 12 20.904a48.63 48.63 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>',
        'instructor_verification'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
        'course'                   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>',
        'course_submitted'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>',
        'payment'                  => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>',
        'finance'                  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
        'system'                   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>',
    ];

    $iconPath = $typeIcons[$type] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>';
@endphp

<div class="nv">
<style>
.nv,.nv *,.nv *::before,.nv *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.nv {
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:48px;
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
html:not(.dark) .nv {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes nvUp {
    from {
        opacity:0;
        transform:translateY(12px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.nva {
    opacity:0;
    animation:nvUp .45s cubic-bezier(.16,1,.3,1) forwards;
}
.nv1 {
    animation-delay:.04s;
}
.nv2 {
    animation-delay:.09s;
}
.nv3 {
    animation-delay:.14s;
}
.nv4 {
    animation-delay:.16s;
}

.nv-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.nv-header-text h1 {
    font-size:clamp(18px,2vw,24px);
    font-weight:780;
    letter-spacing:-.018em;
    line-height:1.15;
}
.nv-header-text p {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
}
.nv-header-btns {
    display:flex;
    align-items:center;
    gap:8px;
    flex-shrink:0;
}
.nv-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    letter-spacing:.01em;
    cursor:pointer;
    text-decoration:none;
    transition:opacity .15s,transform .12s;
    white-space:nowrap;
    border:none;
    font-family:inherit;
}
.nv-btn:hover {
    opacity:.85;
    transform:translateY(-1px);
}
.nv-btn svg {
    width:13px;
    height:13px;
    flex-shrink:0;
}
.nv-btn-gray {
    background:var(--p2);
    color:var(--t1);
    border:1px solid var(--bd2);
}
.nv-btn-accent {
    color:#fff;
}

.nv-layout {
    display:grid;
    grid-template-columns:1fr 320px;
    gap:16px;
    align-items:start;
}
@media(max-width:800px) {
    .nv-layout {
        grid-template-columns:1fr;
    }
}

.nv-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    box-shadow:var(--sh);
}

/* Hero */
.nv-hero {
    display:flex;
    align-items:flex-start;
    gap:16px;
    padding:24px;
}
.nv-icon {
    width:52px;
    height:52px;
    border-radius:13px;
    display:grid;
    place-items:center;
    flex-shrink:0;
}
.nv-icon svg {
    width:24px;
    height:24px;
}
.nv-hero-body {
    flex:1;
    min-width:0;
}
.nv-type-chip {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    letter-spacing:.02em;
    margin-bottom:8px;
}
.nv-type-dot {
    width:6px;
    height:6px;
    border-radius:50%;
    flex-shrink:0;
}
.nv-title {
    font-size:18px;
    font-weight:780;
    letter-spacing:-.015em;
    line-height:1.25;
}
.nv-ago {
    font-size:11.5px;
    color:var(--t2);
    margin-top:5px;
}

/* Message */
.nv-section {
    padding:0 24px 24px;
}
.nv-section-label {
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    margin-bottom:8px;
}
.nv-divider {
    border:none;
    border-top:1px solid var(--bd);
    margin:0 24px;
}
.nv-message {
    font-size:14px;
    line-height:1.65;
    color:var(--t1);
    padding:20px 24px;
}

/* Action CTA */
.nv-action-card {
    padding:18px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
}
.nv-action-label-text {
    font-size:13px;
    font-weight:700;
    color:var(--t1);
}
.nv-action-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
    font-family:ui-monospace,'Cascadia Code',monospace;
    word-break:break-all;
}
.nv-cta-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:9px 16px;
    border-radius:8px;
    font-size:12.5px;
    font-weight:700;
    color:#fff;
    text-decoration:none;
    white-space:nowrap;
    flex-shrink:0;
    transition:opacity .15s,transform .12s;
}
.nv-cta-btn:hover {
    opacity:.85;
    transform:translateY(-1px);
}
.nv-cta-btn svg {
    width:13px;
    height:13px;
}

/* Sidebar cards */
.nv-meta-card {
    padding:18px 20px;
}
.nv-meta-title {
    font-size:11px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    margin-bottom:14px;
}
.nv-meta-row {
    display:flex;
    flex-direction:column;
    gap:12px;
}
.nv-meta-item {
}
.nv-meta-key {
    font-size:10.5px;
    font-weight:700;
    letter-spacing:.04em;
    text-transform:uppercase;
    color:var(--t2);
    margin-bottom:3px;
}
.nv-meta-val {
    font-size:12.5px;
    color:var(--t1);
    font-weight:500;
}
.nv-meta-val.mono {
    font-family:ui-monospace,'Cascadia Code',monospace;
    font-size:11px;
    color:var(--t2);
    word-break:break-all;
}

/* Status pill */
.nv-status {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:20px;
    font-size:11.5px;
    font-weight:700;
}
.nv-status-unread {
    background:rgba(251,191,36,.12);
    color:#d97706;
    border:1px solid rgba(251,191,36,.25);
}
.nv-status-read {
    background:rgba(34,197,94,.1);
    color:#16a34a;
    border:1px solid rgba(34,197,94,.2);
}
.nv-status-dot {
    width:6px;
    height:6px;
    border-radius:50%;
}
</style>

{{-- Header --}}
<div class="nv-header nva nv1">
    <div class="nv-header-text">
        <h1>Notification Detail</h1>
        <p>{{ $n->id }}</p>
    </div>
    <div class="nv-header-btns">
        <a href="{{ $backUrl }}" wire:navigate class="nv-btn nv-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back
        </a>
    </div>
</div>

<div class="nv-layout">

    {{-- Left: main content --}}
    <div style="display:grid;gap:14px">

        {{-- Hero: icon + type + title --}}
        <div class="nv-card nva nv2">
            <div class="nv-hero">
                <div class="nv-icon" style="background:{{ $typeConfig['bg'] }};color:{{ $typeConfig['color'] }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        {!! $iconPath !!}
                    </svg>
                </div>
                <div class="nv-hero-body">
                    <div class="nv-type-chip" style="background:{{ $typeConfig['bg'] }};color:{{ $typeConfig['color'] }}">
                        <span class="nv-type-dot" style="background:{{ $typeConfig['color'] }}"></span>
                        {{ $typeConfig['label'] }}
                    </div>
                    <div class="nv-title">{{ $data['title'] ?? 'Notification' }}</div>
                    <div class="nv-ago">Received {{ $n->created_at?->diffForHumans() ?? '—' }}</div>
                </div>
                <div class="nv-status {{ $isRead ? 'nv-status-read' : 'nv-status-unread' }}">
                    <span class="nv-status-dot" style="background:{{ $isRead ? '#22c55e' : '#f59e0b' }}"></span>
                    {{ $isRead ? 'Read' : 'Unread' }}
                </div>
            </div>

            <hr class="nv-divider">

            <div class="nv-message">{{ $data['message'] ?? '—' }}</div>
        </div>

        {{-- Action CTA --}}
        @if($firstAction)
        <div class="nv-card nva nv3">
            <div class="nv-action-card">
                <div>
                    <div class="nv-action-label-text">{{ $firstAction['label'] ?? 'View' }}</div>
                    <div class="nv-action-sub">{{ $firstAction['url'] ?? '' }}</div>
                </div>
                <a href="{{ $firstAction['url'] }}" wire:navigate class="nv-cta-btn" style="background:{{ $typeConfig['color'] }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    {{ $firstAction['label'] ?? 'Open' }}
                </a>
            </div>
        </div>
        @endif

    </div>

    {{-- Right: sidebar --}}
    <div style="display:grid;gap:14px">

        {{-- Status & timestamps --}}
        <div class="nv-card nva nv2">
            <div class="nv-meta-card">
                <div class="nv-meta-title">Status</div>
                <div class="nv-meta-row">
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Read Status</div>
                        <div class="nv-meta-val">
                            <span class="nv-status {{ $isRead ? 'nv-status-read' : 'nv-status-unread' }}">
                                <span class="nv-status-dot" style="background:{{ $isRead ? '#22c55e' : '#f59e0b' }}"></span>
                                {{ $isRead ? 'Read' : 'Unread' }}
                            </span>
                        </div>
                    </div>
                    @if($isRead)
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Read At</div>
                        <div class="nv-meta-val">{{ $n->read_at?->format('M d, Y · H:i') }}</div>
                    </div>
                    @endif
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Received</div>
                        <div class="nv-meta-val">{{ $n->created_at?->format('M d, Y · H:i') }}</div>
                    </div>
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Time Ago</div>
                        <div class="nv-meta-val">{{ $n->created_at?->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Technical info --}}
        <div class="nv-card nva nv3">
            <div class="nv-meta-card">
                <div class="nv-meta-title">Technical</div>
                <div class="nv-meta-row">
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Notification ID</div>
                        <div class="nv-meta-val mono">{{ $n->id }}</div>
                    </div>
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Type</div>
                        <div class="nv-meta-val mono">{{ class_basename($n->type) }}</div>
                    </div>
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Recipient</div>
                        <div class="nv-meta-val">{{ class_basename($n->notifiable_type) }} #{{ $n->notifiable_id }}</div>
                    </div>
                    @if(!empty($data['format']))
                    <div class="nv-meta-item">
                        <div class="nv-meta-key">Format</div>
                        <div class="nv-meta-val mono">{{ $data['format'] }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Extra actions if more than one --}}
        @if(count($actions) > 1)
        <div class="nv-card nva nv4">
            <div class="nv-meta-card">
                <div class="nv-meta-title">All Actions</div>
                <div style="display:grid;gap:8px;margin-top:4px">
                    @foreach($actions as $action)
                    <a href="{{ $action['url'] }}" wire:navigate class="nv-cta-btn" style="background:{{ $typeConfig['color'] }};justify-content:center">
                        {{ $action['label'] ?? 'Open' }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

</div>
