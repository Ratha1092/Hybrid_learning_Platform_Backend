<style>
:root {
    --nb-bg:     #1e293b;
    --nb-bg2:    #263245;
    --nb-bd:     rgba(255,255,255,.08);
    --nb-bd2:    rgba(255,255,255,.14);
    --nb-t1:     #e2e8f0;
    --nb-t2:     #64748b;
    --nb-sh:     0 16px 48px rgba(0,0,0,.45), 0 2px 8px rgba(0,0,0,.3);
}
html:not(.dark) {
    --nb-bg:     #ffffff;
    --nb-bg2:    #f8fafc;
    --nb-bd:     rgba(15,23,42,.09);
    --nb-bd2:    rgba(15,23,42,.15);
    --nb-t1:     #0f172a;
    --nb-t2:     #64748b;
    --nb-sh:     0 16px 48px rgba(15,23,42,.18), 0 2px 8px rgba(15,23,42,.08);
}

.nb-wrap { position:relative; display:inline-flex; align-items:center; }

/* Bell button */
.nb-btn {
    position:relative;
    width:36px; height:36px;
    border-radius:9px;
    border:1px solid var(--nb-bd2);
    background:var(--nb-bg);
    color:var(--nb-t2);
    cursor:pointer;
    display:inline-flex; align-items:center; justify-content:center;
    transition:background .15s, border-color .15s, color .15s;
    outline:none;
}
.nb-btn:hover { background:var(--nb-bg2); color:var(--nb-t1); border-color:var(--nb-bd2); }
.nb-btn svg  { width:17px; height:17px; }

.nb-badge {
    position:absolute; top:-5px; right:-5px;
    min-width:17px; height:17px;
    background:#f87171; color:#fff;
    font-size:10px; font-weight:800;
    border-radius:9px;
    display:flex; align-items:center; justify-content:center;
    padding:0 4px;
    border:2px solid var(--nb-bg);
    font-family:Inter, sans-serif;
    pointer-events:none;
}

/* Panel */
.nb-panel {
    position:absolute;
    top:calc(100% + 8px);
    right:0;
    width:360px;
    background:var(--nb-bg);
    border:1px solid var(--nb-bd2);
    border-radius:14px;
    box-shadow:var(--nb-sh);
    z-index:9999;
    overflow:hidden;
    font-family:Inter, ui-sans-serif, system-ui, sans-serif;
    font-size:13px;
}

/* Panel header */
.nb-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 16px 12px;
    border-bottom:1px solid var(--nb-bd);
}
.nb-head-left  { display:flex; align-items:center; gap:8px; }
.nb-head-title { font-size:14px; font-weight:750; color:var(--nb-t1); letter-spacing:-.01em; }
.nb-unread-pill {
    background:rgba(248,113,113,.15); color:#f87171;
    font-size:10.5px; font-weight:800;
    padding:2px 8px; border-radius:6px;
}
.nb-mark-all {
    font-size:11.5px; font-weight:650; color:#6366f1;
    background:none; border:none; cursor:pointer; padding:0;
    font-family:inherit;
    transition:opacity .15s;
}
.nb-mark-all:hover { opacity:.75; }

/* List */
.nb-list { max-height:360px; overflow-y:auto; }
.nb-list::-webkit-scrollbar { width:4px; }
.nb-list::-webkit-scrollbar-track { background:transparent; }
.nb-list::-webkit-scrollbar-thumb { background:var(--nb-bd2); border-radius:4px; }

/* Item */
.nb-item {
    display:flex; gap:12px;
    padding:13px 16px;
    border-bottom:1px solid var(--nb-bd);
    cursor:pointer;
    transition:background .13s;
    text-decoration:none;
    position:relative;
}
.nb-item:last-child { border-bottom:none; }
.nb-item:hover { background:var(--nb-bg2); }
.nb-item.unread { background:rgba(99,102,241,.04); }
.nb-item.unread::before {
    content:'';
    position:absolute; left:0; top:0; bottom:0;
    width:3px; border-radius:0 2px 2px 0;
    background:#6366f1;
}

.nb-icon {
    width:36px; height:36px; border-radius:9px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; margin-top:1px;
}
.nb-icon svg { width:17px; height:17px; }

.nb-content { flex:1; min-width:0; }
.nb-title   { font-size:12.5px; font-weight:680; color:var(--nb-t1); line-height:1.3; }
.nb-msg     { font-size:11.5px; color:var(--nb-t2); margin-top:3px; line-height:1.45; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.nb-time    { font-size:10.5px; color:var(--nb-t2); margin-top:5px; opacity:.7; }

/* Empty */
.nb-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:44px 20px; gap:10px; color:var(--nb-t2);
}
.nb-empty svg { width:36px; height:36px; opacity:.3; }
.nb-empty p   { font-size:12.5px; }

/* Footer */
.nb-foot {
    padding:10px 16px;
    border-top:1px solid var(--nb-bd);
    text-align:center;
}
.nb-foot a {
    font-size:12px; font-weight:650; color:#6366f1;
    text-decoration:none;
    transition:opacity .15s;
}
.nb-foot a:hover { opacity:.75; }
</style>

<div
    class="nb-wrap"
    x-data="{
        open: false,
        unread: {{ $unreadCount }},
        csrf: '{{ csrf_token() }}',
        post(url) {
            return fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json' } });
        },
        doMarkRead(id, el) {
            if (!el.classList.contains('unread')) return;
            el.classList.remove('unread');
            el.querySelector('.nb-dot')?.remove();
            this.unread = Math.max(0, this.unread - 1);
            this.post('/admin/notifications/' + id + '/mark-read');
        },
        doMarkAllRead() {
            document.querySelectorAll('#nb-list .nb-item.unread').forEach(el => {
                el.classList.remove('unread');
                el.querySelector('.nb-dot')?.remove();
            });
            this.unread = 0;
            this.post('/admin/notifications/mark-all-read');
        }
    }"
    @click.outside="open = false"
    wire:poll.30s
>
    {{-- Bell button --}}
    <button class="nb-btn" @click="open = !open" type="button">
        <template x-if="unread > 0">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M5.85 3.5a.75.75 0 0 0-1.117-1 9.719 9.719 0 0 0-2.348 4.876.75.75 0 0 0 1.479.248A8.219 8.219 0 0 1 5.85 3.5zM19.267 2.5a.75.75 0 1 0-1.118 1 8.22 8.22 0 0 1 1.987 4.124.75.75 0 0 0 1.48-.248A9.72 9.72 0 0 0 19.266 2.5z"/>
                <path fill-rule="evenodd" d="M12 2.25A6.75 6.75 0 0 0 5.25 9v.75a8.217 8.217 0 0 1-2.119 5.52.75.75 0 0 0 .298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 1 0 7.48 0 24.583 24.583 0 0 0 4.83-1.244.75.75 0 0 0 .298-1.205 8.217 8.217 0 0 1-2.118-5.52V9A6.75 6.75 0 0 0 12 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 0 0 4.496 0l.002.1a2.25 2.25 0 1 1-4.5 0z" clip-rule="evenodd"/>
            </svg>
        </template>
        <template x-if="unread === 0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
            </svg>
        </template>
        <span class="nb-badge" x-show="unread > 0" x-text="unread > 99 ? '99+' : unread"></span>
    </button>

    {{-- Dropdown panel --}}
    <div
        class="nb-panel"
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        style="display:none; transform-origin:top right"
    >
        {{-- Header --}}
        <div class="nb-head">
            <div class="nb-head-left">
                <span class="nb-head-title">Notifications</span>
                <span class="nb-unread-pill" x-show="unread > 0" x-text="unread + ' new'"></span>
            </div>
            <button class="nb-mark-all" x-show="unread > 0" @click="doMarkAllRead()" type="button">
                Mark all read
            </button>
        </div>

        {{-- List --}}
        <div class="nb-list" id="nb-list">
            @forelse ($notifications as $notification)
                @php
                    $data     = $notification->data;
                    $type     = $data['type'] ?? '';
                    $title    = $data['title'] ?? ucwords(str_replace('_', ' ', $type));
                    $message  = $data['message'] ?? $data['body'] ?? '';
                    $isUnread = is_null($notification->read_at);

                    $url = $data['action_url'] ?? null;

                    [$iconBg, $iconColor, $iconPath] = match(true) {
                        str_contains($type, 'course_approved')  => ['rgba(16,185,129,.15)',  '#10b981', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
                        str_contains($type, 'course_rejected')  => ['rgba(248,113,113,.15)', '#f87171', 'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
                        str_contains($type, 'course')           => ['rgba(245,158,11,.15)',  '#f59e0b', 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25'],
                        str_contains($type, 'instructor')       => ['rgba(99,102,241,.15)',  '#6366f1', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                        str_contains($type, 'payment')          => ['rgba(6,182,212,.15)',   '#06b6d4', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z'],
                        str_contains($type, 'order')            => ['rgba(168,85,247,.15)',  '#a855f7', 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z'],
                        default                                  => ['rgba(148,163,184,.12)', '#94a3b8', 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0'],
                    };
                @endphp

                <div
                    class="nb-item {{ $isUnread ? 'unread' : '' }}"
                    @click="doMarkRead('{{ $notification->id }}', $el); {{ $url ? "window.location.href='" . $url . "'" : '' }}"
                    style="cursor:pointer"
                >
                    <div class="nb-icon" style="background:{{ $iconBg }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                        </svg>
                    </div>
                    <div class="nb-content">
                        <div class="nb-title">{{ $title }}</div>
                        @if($message)
                            <div class="nb-msg">{{ $message }}</div>
                        @endif
                        <div class="nb-time">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                    @if($isUnread)
                        <div class="nb-dot" style="width:7px;height:7px;border-radius:50%;background:#6366f1;flex-shrink:0;margin-top:5px"></div>
                    @endif
                </div>
            @empty
                <div class="nb-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.417m1.105-6.275c.006.21.009.419.009.63 0 .625-.04 1.24-.117 1.843m0 0a24.207 24.207 0 0 1-1.72 5.327 3.001 3.001 0 0 0-2.87-3.952m4.59-1.375A11.952 11.952 0 0 0 12 5.25c-3.273 0-6.23 1.318-8.39 3.454M3 3l18 18"/>
                    </svg>
                    <p>No notifications yet</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->isNotEmpty())
        <div class="nb-foot">
            <a href="{{ route('filament.admin.pages.notifications') }}">View all notifications →</a>
        </div>
        @endif
    </div>
</div>
