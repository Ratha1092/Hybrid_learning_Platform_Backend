<style>
.bi,
.bi *,
.bi *::before,
.bi *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.bi {
    font-family:Inter, ui-sans-serif, system-ui, sans-serif;
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
    --accent:#D7A441;
    color:var(--t1);
}
html:not(.dark) .bi {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f1f5f9;
    --bd:rgba(15,23,42,.13);
    --bd2:rgba(15,23,42,.20);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#94a3b8;
}
.bi-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}
.bi-header h1 {
    font-size:clamp(20px,2.2vw,26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}
.bi-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:5px;
}
.bi-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 14px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:1px solid var(--bd2);
    background:var(--p1);
    color:var(--t1);
}
.bi-btn:hover {
    background:var(--p2);
}
.bi-btn-danger {
    background:#ef444415;
    color:#ef4444;
    border-color:#ef444430;
}
.bi-btn-danger:hover {
    background:#ef444425;
}
.bi-btn-primary {
    background:var(--accent);
    color:#fff;
    border-color:transparent;
}
.bi-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:20px;
}
.bi-card h3 {
    font-size:13px;
    font-weight:700;
    color:var(--t1);
    margin-bottom:14px;
}
.bi-form {
    display:grid;
    grid-template-columns:1fr 1fr 180px auto;
    gap:8px;
    align-items:end;
}
@media(max-width:800px) {
    .bi-form {
        grid-template-columns:1fr 1fr;
    }
}
.bi-label {
    font-size:11px;
    font-weight:600;
    color:var(--t2);
    margin-bottom:4px;
}
.bi-input {
    background:var(--bg);
    border:1px solid var(--bd2);
    border-radius:8px;
    padding:8px 10px;
    font-size:12px;
    color:var(--t1);
    width:100%;
}
.bi-input::placeholder {
    color:var(--t2);
}
.bi-table-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow:hidden;
}
.bi-table {
    width:100%;
    border-collapse:collapse;
}
.bi-table th {
    padding:10px 14px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    border-bottom:1px solid var(--bd);
}
.bi-table td {
    padding:10px 14px;
    border-bottom:1px solid var(--bd);
    font-size:12.5px;
    color:var(--t1);
}
.bi-table tr:last-child td {
    border-bottom:none;
}
.bi-badge {
    display:inline-flex;
    padding:2px 9px;
    border-radius:99px;
    font-size:11px;
    font-weight:700;
}
.bi-pagination {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:12px 16px;
    font-size:12px;
    color:var(--t2);
    border-top:1px solid var(--bd);
}
.bi-empty {
    padding:32px;
    text-align:center;
    color:var(--t2);
}
.bi-warn {
    background:#f59e0b15;
    border:1px solid #f59e0b30;
    border-radius:10px;
    padding:12px 16px;
    font-size:12px;
    color:#f59e0b;
}
</style>

@php $url = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p)); @endphp

<div class="bi">
    <div class="bi-header">
        <div>
            <h1>Blocked IPs</h1>
            <p>IP addresses blocked from the platform. Add middleware <code>App\Http\Middleware\BlockBannedIps</code> to enforce blocks.</p>
        </div>
    </div>

    <div class="bi-warn">
        <strong>Note:</strong> Entries here are stored in the database. To actively block requests, register the <code>BlockBannedIps</code> middleware in your HTTP kernel or route groups.
    </div>

    {{-- Add form --}}
    <div class="bi-card">
        <h3>Block an IP</h3>
        <div class="bi-form">
            <div>
                <div class="bi-label">IP Address *</div>
                <input wire:model="newIp" type="text" placeholder="e.g. 192.168.1.1" class="bi-input">
            </div>
            <div>
                <div class="bi-label">Reason</div>
                <input wire:model="newReason" type="text" placeholder="Brute force, spam…" class="bi-input">
            </div>
            <div>
                <div class="bi-label">Expires At (optional)</div>
                <input wire:model="newExpiry" type="datetime-local" class="bi-input">
            </div>
            <div style="display:flex;align-items:flex-end">
                <button wire:click="blockIp" class="bi-btn bi-btn-primary" style="width:100%">Block IP</button>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="bi-table-card">
        @if($total === 0)
            <div class="bi-empty">No blocked IPs. The platform is wide open.</div>
        @else
            <table class="bi-table">
                <thead>
                    <tr><th>IP Address</th><th>Reason</th><th>Blocked By</th><th>Expires At</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($blocked as $entry)
                        <tr>
                            <td style="font-family:monospace;font-size:12px;font-weight:600">{{ $entry->ip_address }}</td>
                            <td style="color:var(--t2)">{{ $entry->reason ?? '—' }}</td>
                            <td style="font-size:12px">{{ $entry->blocker?->name ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--t2)">
                                {{ $entry->expires_at ? $entry->expires_at->format('Y-m-d H:i') : '∞ Permanent' }}
                            </td>
                            <td>
                                @if($entry->isExpired())
                                    <span class="bi-badge" style="background:#94a3b815;color:#94a3b8">Expired</span>
                                @else
                                    <span class="bi-badge" style="background:#ef444415;color:#ef4444">Blocked</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="unblock({{ $entry->id }})" wire:confirm="Unblock {{ $entry->ip_address }}?" class="bi-btn bi-btn-danger" style="padding:4px 10px;font-size:11px">
                                    Unblock
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="bi-pagination">
                <span>{{ number_format($total) }} blocked IPs</span>
                <div style="display:flex;gap:6px">
                    @if($curPage > 1)<a href="{{ $url(['page' => $curPage-1]) }}" class="bi-btn">Prev</a>@endif
                    @if($curPage < $totalPages)<a href="{{ $url(['page' => $curPage+1]) }}" class="bi-btn">Next</a>@endif
                </div>
            </div>
        @endif
    </div>
</div>
