@php
    use App\Filament\Pages\AuditLog as AuditLogPage;

    /** @var \App\Domains\Auth\Models\ActivityLog $log */

    $actions = AuditLogPage::ACTIONS;
    $label   = $actions[$log->action]['label'] ?? ucwords(str_replace(['.','_'], ' ', $log->action));
    $color   = $actions[$log->action]['color'] ?? '#64748b';

    $bgHex  = $log->user ? substr(md5($log->user->name ?? ''), 0, 6) : '64748b';
    $avUrl  = $log->user
        ? 'https://ui-avatars.com/api/?name=' . urlencode($log->user->name ?? '?') . '&background=' . $bgHex . '&color=fff&bold=true&size=128'
        : null;

    $statusBg = $color . '22';
@endphp

<div class="av">

<style>
.av,.av *,.av *::before,.av *::after{box-sizing:border-box;margin:0;padding:0}
.av{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:56px;display:grid;gap:20px;
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#6366f1;--accent2:#4f46e5;
    color:var(--t1);
}
html:not(.dark) .av{
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes avUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.ava{opacity:0;animation:avUp .38s cubic-bezier(.16,1,.3,1) forwards}
.av1{animation-delay:.04s}.av2{animation-delay:.09s}.av3{animation-delay:.14s}
.av4{animation-delay:.19s}.av5{animation-delay:.24s}.av6{animation-delay:.28s}

/* Header */
.av-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.av-page-title{font-size:clamp(22px,2.6vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.av-header-actions{display:flex;align-items:center;gap:8px}
.av-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.av-btn svg{width:14px;height:14px;flex-shrink:0}
.av-btn-gray{background:var(--p1);border:1px solid var(--bd2);color:var(--t2)}.av-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.av-btn-danger{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444}.av-btn-danger:hover{background:rgba(239,68,68,.18);border-color:#ef4444}

/* Hero */
.av-hero{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);padding:24px 28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.av-hero-avatar{width:64px;height:64px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid var(--bd2)}
.av-hero-avatar-ph{width:64px;height:64px;border-radius:50%;display:grid;place-items:center;flex-shrink:0;font-size:22px;font-weight:800;color:#fff}
.av-hero-info{flex:1;min-width:0}
.av-hero-name{font-size:18px;font-weight:780;color:var(--t1);letter-spacing:-.015em}
.av-hero-email{font-size:12px;color:var(--t2);margin-top:2px}
.av-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;border:1px solid;margin-top:10px}
.av-badge-dot{width:7px;height:7px;border-radius:50%}
.av-hero-stats{display:flex;gap:0;flex-shrink:0}
.av-stat{text-align:center;padding:0 20px;border-left:1px solid var(--bd)}
.av-stat-val{font-size:14px;font-weight:800;color:var(--t1);font-variant-numeric:tabular-nums}
.av-stat-label{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);margin-top:2px}

/* Cards */
.av-card{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);overflow:hidden}
.av-card-head{padding:18px 22px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:12px}
.av-card-icon{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;background:rgba(99,102,241,.1);color:var(--accent);flex-shrink:0}
.av-card-icon svg{width:16px;height:16px}
.av-card-title{font-size:13px;font-weight:750;color:var(--t1)}
.av-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}
.av-card-body{padding:22px}

/* Field rows */
.av-field{display:grid;grid-template-columns:160px 1fr;gap:8px 16px;padding:11px 0;border-bottom:1px solid var(--bd);align-items:start}
.av-field:last-child{border-bottom:none;padding-bottom:0}
.av-field:first-child{padding-top:0}
.av-field-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--t2);padding-top:2px}
.av-field-value{font-size:13px;color:var(--t1);font-weight:500;word-break:break-all}
.av-field-value.mono{font-family:'JetBrains Mono','Fira Code',monospace;font-size:12px}
.av-field-value.muted{color:var(--t2);font-style:italic}

/* JSON diff blocks */
.av-diff{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:640px){.av-diff{grid-template-columns:1fr}}
.av-diff-block{border-radius:10px;overflow:hidden;border:1px solid var(--bd2)}
.av-diff-head{padding:8px 14px;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;border-bottom:1px solid var(--bd2)}
.av-diff-head.old{background:rgba(239,68,68,.08);color:#f87171}
.av-diff-head.new{background:rgba(16,185,129,.08);color:#34d399}
.av-json{font-family:'JetBrains Mono','Fira Code','Cascadia Code',monospace;font-size:12px;color:#94a3b8;background:var(--p2);padding:14px;white-space:pre-wrap;word-break:break-all;line-height:1.7;min-height:60px;max-height:320px;overflow-y:auto}

/* Data block */
.av-data-block{background:var(--p2);border:1px solid var(--bd2);border-radius:10px;overflow:hidden}
.av-data-pre{font-family:'JetBrains Mono','Fira Code','Cascadia Code',monospace;font-size:12px;color:#94a3b8;padding:16px;white-space:pre-wrap;word-break:break-all;line-height:1.7;max-height:400px;overflow-y:auto}
</style>

{{-- Header --}}
<div class="av-header ava av1">
    <h1 class="av-page-title">Audit Log Entry</h1>
    <div class="av-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="av-btn av-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Audit Log
        </a>
        @if ($canReset)
        <button type="button" wire:click="mountAction('resetToOldValues')" class="av-btn av-btn-danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
            Reset Changes
        </button>
        @endif
    </div>
</div>

{{-- Hero --}}
<div class="av-hero ava av2">
    @if ($avUrl)
        <img src="{{ $avUrl }}" class="av-hero-avatar" alt="">
    @else
        <div class="av-hero-avatar-ph" style="background:#{{ $bgHex }}">
            {{ $log->user ? strtoupper(substr($log->user->name ?? '?', 0, 1)) : '?' }}
        </div>
    @endif

    <div class="av-hero-info">
        @if ($log->user)
            <div class="av-hero-name">{{ $log->user->name }}</div>
            <div class="av-hero-email">{{ $log->user->email }}</div>
        @else
            <div class="av-hero-name" style="color:var(--t2);font-style:italic">Guest / Unauthenticated</div>
        @endif
        <div>
            <span class="av-badge" style="background:{{ $statusBg }};border-color:{{ $color }}22;color:{{ $color }}">
                <span class="av-badge-dot" style="background:{{ $color }}"></span>
                {{ $label }}
            </span>
        </div>
    </div>

    <div class="av-hero-stats">
        <div class="av-stat">
            <div class="av-stat-val">{{ $log->created_at?->format('M d, Y') }}</div>
            <div class="av-stat-label">Date</div>
        </div>
        <div class="av-stat">
            <div class="av-stat-val">{{ $log->created_at?->format('H:i:s') }}</div>
            <div class="av-stat-label">Time</div>
        </div>
        <div class="av-stat">
            <div class="av-stat-val">#{{ $log->id }}</div>
            <div class="av-stat-label">Entry ID</div>
        </div>
    </div>
</div>

{{-- Event Details card --}}
<div class="av-card ava av3">
    <div class="av-card-head">
        <div class="av-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        </div>
        <div>
            <div class="av-card-title">Event Details</div>
            <div class="av-card-sub">Action, subject, and network info</div>
        </div>
    </div>
    <div class="av-card-body">
        <div class="av-field">
            <div class="av-field-label">Action</div>
            <div class="av-field-value">
                <span style="display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $statusBg }};color:{{ $color }}">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $color }};display:inline-block"></span>
                    {{ $label }}
                </span>
            </div>
        </div>
        <div class="av-field">
            <div class="av-field-label">Raw Action</div>
            <div class="av-field-value mono">{{ $log->action }}</div>
        </div>
        @if ($log->subject_type)
        <div class="av-field">
            <div class="av-field-label">Subject Type</div>
            <div class="av-field-value mono">{{ class_basename($log->subject_type) }}</div>
        </div>
        @endif
        @if ($log->subject_id)
        <div class="av-field">
            <div class="av-field-label">Subject ID</div>
            <div class="av-field-value mono">#{{ $log->subject_id }}</div>
        </div>
        @endif
        <div class="av-field">
            <div class="av-field-label">IP Address</div>
            <div class="av-field-value mono">{{ $log->ip_address ?? '—' }}</div>
        </div>
        <div class="av-field">
            <div class="av-field-label">User Agent</div>
            <div class="av-field-value {{ $log->user_agent ? '' : 'muted' }}">{{ $log->user_agent ?? '—' }}</div>
        </div>
        <div class="av-field">
            <div class="av-field-label">Timestamp</div>
            <div class="av-field-value">{{ $log->created_at?->format('M d, Y \a\t H:i:s') }} ({{ $log->created_at?->diffForHumans() }})</div>
        </div>
    </div>
</div>

{{-- User Details card (only if user exists) --}}
@if ($log->user)
<div class="av-card ava av4">
    <div class="av-card-head">
        <div class="av-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        </div>
        <div>
            <div class="av-card-title">User</div>
            <div class="av-card-sub">Account that triggered this event</div>
        </div>
    </div>
    <div class="av-card-body">
        <div class="av-field">
            <div class="av-field-label">Name</div>
            <div class="av-field-value">{{ $log->user->name }}</div>
        </div>
        <div class="av-field">
            <div class="av-field-label">Email</div>
            <div class="av-field-value">{{ $log->user->email }}</div>
        </div>
        <div class="av-field">
            <div class="av-field-label">User ID</div>
            <div class="av-field-value mono">#{{ $log->user->id }}</div>
        </div>
        <div class="av-field">
            <div class="av-field-label">Roles</div>
            <div class="av-field-value">
                @forelse ($log->user->getRoleNames() as $role)
                    <span style="display:inline-flex;margin-right:6px;margin-bottom:4px;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:700;background:rgba(99,102,241,.12);color:var(--accent);border:1px solid rgba(99,102,241,.2)">{{ $role }}</span>
                @empty
                    <span class="av-field-value muted">No roles</span>
                @endforelse
            </div>
        </div>
        <div class="av-field">
            <div class="av-field-label">Profile</div>
            <div class="av-field-value">
                <a href="{{ route('filament.admin.resources.users.edit', $log->user) }}" wire:navigate
                   style="color:var(--accent);text-decoration:none;font-size:12px;font-weight:600">
                    View user profile →
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Changes card (old/new values) --}}
@if (!empty($log->old_values) || !empty($log->new_values))
<div class="av-card ava av5">
    <div class="av-card-head">
        <div class="av-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
        </div>
        <div>
            <div class="av-card-title">Changes</div>
            <div class="av-card-sub">Before and after values for this event</div>
        </div>
    </div>
    <div class="av-card-body">
        <div class="av-diff">
            <div class="av-diff-block">
                <div class="av-diff-head old">Before</div>
                <pre class="av-json">@if(!empty($log->old_values)){{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}@else—@endif</pre>
            </div>
            <div class="av-diff-block">
                <div class="av-diff-head new">After</div>
                <pre class="av-json">@if(!empty($log->new_values)){{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}@else—@endif</pre>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Extra Data card --}}
@if (!empty($log->data))
<div class="av-card ava av6">
    <div class="av-card-head">
        <div class="av-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/></svg>
        </div>
        <div>
            <div class="av-card-title">Extra Data</div>
            <div class="av-card-sub">Additional context attached to this event</div>
        </div>
    </div>
    <div class="av-card-body" style="padding:0">
        <div class="av-data-block" style="border:none;border-radius:0">
            <pre class="av-data-pre">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endif

</div>
