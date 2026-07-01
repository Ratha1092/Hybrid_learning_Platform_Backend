@php
    $accent = '#ea580c';
@endphp

<div>
<style>
.rf, .rf *, .rf *::before, .rf *::after { box-sizing:border-box; margin:0; padding:0; }
.rf {
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
html:not(.dark) .rf {
    --bg:#f1f5f9; --p1:#ffffff; --p2:#f8fafc;
    --bd:rgba(15,23,42,.13); --bd2:rgba(15,23,42,.20);
    --t1:#0f172a; --t2:#64748b; --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}
@keyframes rfUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.rfa { opacity:0; animation:rfUp .45s cubic-bezier(.16,1,.3,1) forwards; }
.rf1{animation-delay:.04s} .rf2{animation-delay:.09s}

.rf-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--bd); }
.rf-header-text h1 { font-size:clamp(20px,2.2vw,26px); font-weight:780; letter-spacing:-.018em; color:var(--t1); line-height:1.15; }
.rf-header-text p { font-size:12px; color:var(--t2); margin-top:5px; }

.rf-card { background:var(--p1); border:1px solid var(--bd); border-radius:12px; overflow:hidden; box-shadow:var(--sh); }
.rf-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--bd); flex-wrap:wrap; }
.rf-tabs { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.rf-tab { display:inline-flex; align-items:center; gap:6px; padding:6px 13px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; color:var(--t2); background:none; font-family:inherit; border:1px solid transparent; transition:background .15s, color .15s, border-color .15s; }
.rf-tab:hover { background:var(--p2); color:var(--t1); }
.rf-tab-badge { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; border-radius:5px; font-size:10px; font-weight:800; }
.rf-search-box { display:flex; align-items:center; gap:6px; background:var(--p2); border:1px solid var(--bd2); border-radius:8px; padding:6px 12px; }
.rf-search-box svg { width:14px; height:14px; color:var(--t2); flex-shrink:0; }
.rf-search-box input { background:none; border:none; outline:none; color:var(--t1); font-size:12px; font-family:inherit; width:200px; }
.rf-search-box input::placeholder { color:var(--t2); }

.rf-table { width:100%; border-collapse:collapse; }
.rf-table thead tr { border-bottom:1px solid var(--bd); }
.rf-table th { padding:10px 12px; text-align:left; font-size:10.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--t2); white-space:nowrap; }
.rf-table tbody tr { border-bottom:1px solid var(--bd); transition:background .12s; }
.rf-table tbody tr:last-child { border-bottom:none; }
.rf-table tbody tr:hover { background:var(--p2); }
.rf-row-link { cursor:pointer; }
.rf-table td { padding:12px 12px; vertical-align:middle; }

.rf-id { font-size:11.5px; font-weight:700; color:var(--t2); white-space:nowrap; }
.rf-user-name { font-size:12.5px; color:var(--t1); font-weight:500; }
.rf-email { font-size:12px; color:var(--t2); }
.rf-amount { font-size:13px; color:var(--t1); font-weight:700; }
.rf-date { font-size:12px; color:var(--t2); white-space:nowrap; }
.rf-reason { font-size:12px; color:var(--t2); max-width:240px; }

.rf-actions { display:flex; align-items:center; gap:4px; justify-content:flex-end; }
.rf-act-btn { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; background:none; border:1px solid transparent; cursor:pointer; color:var(--t2); text-decoration:none; transition:background .15s, border-color .15s, color .15s; }
.rf-act-btn:hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.rf-act-btn svg { width:18px; height:18px; }
.rf-act-btn-refund { color:#f87171; }
.rf-act-btn-refund:hover { background:rgba(248,113,113,.12) !important; border-color:rgba(248,113,113,.3) !important; color:#f87171 !important; }

.rf-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; }
.rf-modal-overlay.open { display:flex; }
.rf-modal { background:var(--p1); border:1px solid var(--bd2); border-radius:14px; padding:26px; width:100%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,.4); }
.rf-modal h3 { font-size:15px; font-weight:750; color:var(--t1); margin-bottom:6px; }
.rf-modal p { font-size:12.5px; color:var(--t2); margin-bottom:16px; }
.rf-modal textarea { width:100%; background:var(--p2); border:1px solid var(--bd2); border-radius:9px; padding:10px 13px; color:var(--t1); font-size:13px; font-family:inherit; resize:vertical; min-height:90px; outline:none; }
.rf-modal textarea:focus { border-color:#ea580c; }
.rf-modal-footer { display:flex; justify-content:flex-end; gap:8px; margin-top:14px; }
.rf-modal-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s; }
.rf-modal-btn-gray { background:var(--p2); border:1px solid var(--bd2); color:var(--t2); }
.rf-modal-btn-danger { background:rgba(248,113,113,.15); color:#f87171; border:1px solid rgba(248,113,113,.3); }

.rf-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 24px; gap:10px; color:var(--t2); }
.rf-empty svg { width:40px; height:40px; opacity:.35; }
.rf-empty p { font-size:13px; }

.rf-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-top:1px solid var(--bd); flex-wrap:wrap; gap:10px; }
.rf-footer-info { font-size:12px; color:var(--t2); }
.rf-pages { display:flex; align-items:center; gap:6px; }
.rf-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; font-size:12px; font-weight:700; text-decoration:none; color:var(--t2); background:none; font-family:inherit; cursor:pointer; border:1px solid transparent; transition:background .15s, border-color .15s, color .15s; }
.rf-loading{opacity:.45;pointer-events:none;transition:opacity .1s}
.rf-page-btn:not(.disabled):hover { background:var(--p2); border-color:var(--bd2); color:var(--t1); }
.rf-page-btn.active { background:var(--accent); color:#fff; border-color:transparent; }
.rf-page-btn.disabled { opacity:.35; pointer-events:none; }
.rf-per-page { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--t2); }
.rf-per-page select { appearance:none; background:var(--p2); border:1px solid var(--bd2); border-radius:7px; padding:4px 22px 4px 9px; font-size:12px; font-weight:700; color:var(--t1); font-family:inherit; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 6px center; outline:none; }
</style>

<div class="rf" id="rf-refunds" style="--accent:{{ $accent }};">

    {{-- Header --}}
    <div class="rf-header rfa rf1">
        <div class="rf-header-text">
            <h1>Refunds</h1>
            <p>Refund a paid order. Already-distributed instructor earnings are automatically reversed.</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="rf-card rfa rf2">

        {{-- Toolbar --}}
        <div class="rf-toolbar">
            <div class="rf-tabs">
                @foreach ($tabs as $t)
                @php
                    $isActive   = $tab === $t['key'];
                    $tabColor   = $t['color'];
                    $tabStyle   = $isActive ? "background:{$tabColor}1a;color:{$tabColor};border-color:{$tabColor}55;font-weight:700;" : '';
                    $badgeStyle = "background:{$tabColor}20;color:{$tabColor};";
                @endphp
                <button type="button" wire:click="selectTab('{{ $t['key'] }}')" class="rf-tab" style="{{ $tabStyle }}">
                    {{ $t['label'] }}
                    <span class="rf-tab-badge" style="{{ $badgeStyle }}">{{ $t['count'] }}</span>
                </button>
                @endforeach
            </div>

            <div class="rf-search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search order # or customer...">
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto" wire:loading.class="rf-loading" wire:target="selectTab,gotoPage,search,setPerPage">
        <table class="rf-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>{{ $tab === 'refunded' ? 'Refunded' : 'Paid' }}</th>
                    @if($tab === 'refunded')<th>Reason</th>@endif
                    @if($tab === 'eligible' && $canUpdate)<th style="text-align:right">Actions</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                @php $refund = $reasons->get($order->id); @endphp
                <tr
                    @if($tab === 'refunded' && $refund)
                        class="rf-row-link"
                        onclick="Livewire.navigate('{{ url('/admin/refunds/' . $refund->id) }}')"
                    @endif
                >
                    <td><span class="rf-id">{{ $order->order_number }}</span></td>

                    <td>
                        <div>
                            <div class="rf-user-name">{{ $order->user?->name ?? $order->customer_name ?? '—' }}</div>
                            <div class="rf-email">{{ $order->user?->email ?? $order->customer_email ?? '' }}</div>
                        </div>
                    </td>

                    <td><span class="rf-amount">{{ number_format($order->final_amount, 2) }} {{ $order->currency }}</span></td>

                    <td><span class="rf-date">{{ ($tab === 'refunded' ? $order->refunded_at : $order->paid_at)?->format('M d, Y') ?? '—' }}</span></td>

                    @if($tab === 'refunded')
                    <td><span class="rf-reason">{{ $refund?->reason ?? '—' }}</span></td>
                    @endif

                    @if($tab === 'eligible' && $canUpdate)
                    <td>
                        <div class="rf-actions">
                            <button onclick="openRefundModal({{ $order->id }}, '{{ addslashes($order->order_number) }}')"
                                    class="rf-act-btn rf-act-btn-refund" title="Refund">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="rf-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                            </svg>
                            <p>No orders found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="rf-footer">
            <div class="rf-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} orders
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="rf-per-page">
                    Per page
                    <select wire:change="setPerPage($event.target.value)">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="rf-pages">
                    <button type="button" wire:click="gotoPage({{ max(1, $curPage - 1) }})"
                       class="rf-page-btn {{ $curPage === 1 ? 'disabled' : '' }}" @disabled($curPage === 1)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <button type="button" wire:click="gotoPage({{ $p }})"
                           class="rf-page-btn {{ $curPage === $p ? 'active' : '' }}">
                            {{ $p }}
                        </button>
                    @endfor
                    <button type="button" wire:click="gotoPage({{ min($totalPages, $curPage + 1) }})"
                       class="rf-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}" @disabled($curPage === $totalPages)>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($canUpdate)
    {{-- Refund Modal --}}
    <div class="rf-modal-overlay" id="rf-refund-modal" onclick="if(event.target===this)closeRefundModal()">
        <div class="rf-modal">
            <h3>Refund Order</h3>
            <p id="rf-refund-name-text">Explain why this order is being refunded. This will mark the order, payment, and items as refunded.</p>
            <textarea id="rf-refund-reason" placeholder="e.g. Customer request, duplicate charge, course no longer available..."></textarea>
            <div class="rf-modal-footer">
                <button class="rf-modal-btn rf-modal-btn-gray" onclick="closeRefundModal()">Cancel</button>
                <button class="rf-modal-btn rf-modal-btn-danger" onclick="submitRefund()">Refund Order</button>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    var refundId = null;
    function openRefundModal(id, orderNumber) {
        refundId = id;
        document.getElementById('rf-refund-name-text').textContent = 'Explain why order ' + orderNumber + ' is being refunded. This will mark the order, payment, and items as refunded.';
        document.getElementById('rf-refund-reason').value = '';
        document.getElementById('rf-refund-modal').classList.add('open');
        setTimeout(() => document.getElementById('rf-refund-reason').focus(), 100);
    }
    function closeRefundModal() {
        document.getElementById('rf-refund-modal').classList.remove('open');
        refundId = null;
    }
    function submitRefund() {
        const reason = document.getElementById('rf-refund-reason').value.trim();
        if (!reason) {
            alert('Please provide a refund reason.');
            return;
        }
        const id = refundId;
        closeRefundModal();
        @this.call('refund', id, reason);
    }
</script>
</div>
