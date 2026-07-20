@php
    $backUrl = route('filament.admin.pages.coupons');
    $type = $data['type'] ?? 'percentage';
    $valueLabel = $type === 'fixed' ? 'Amount off ($)' : 'Percentage off (%)';
@endphp

<div class="cn">

<style>
.cn,.cn *,.cn *::before,.cn *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.cn {
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:56px;
    display:grid;
    gap:20px;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#6366f1;
    --accent2:#4f46e5;
    color:var(--t1);
}
html:not(.dark) .cn {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes cnUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.cna {
    opacity:0;
    animation:cnUp .38s cubic-bezier(.16,1,.3,1) forwards;
}
.cn1 {
    animation-delay:.04s;
}
.cn2 {
    animation-delay:.09s;
}
.cn3 {
    animation-delay:.14s;
}
.cn4 {
    animation-delay:.19s;
}
.cn5 {
    animation-delay:.24s;
}
.cn6 {
    animation-delay:.29s;
}

.cn-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.cn-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.cn-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:9px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    border:none;
    font-family:inherit;
    transition:all .15s;
    white-space:nowrap;
}
.cn-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.cn-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.cn-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.cn-btn-indigo {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.cn-btn-indigo:hover {
    background:var(--accent2);
}
.cn-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}

.cn-intro {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    padding:24px 28px;
    display:flex;
    align-items:center;
    gap:20px;
    flex-wrap:wrap;
}
.cn-intro-icon {
    width:64px;
    height:64px;
    border-radius:16px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(99,102,241,.12);
    color:var(--accent);
}
.cn-intro-icon svg {
    width:28px;
    height:28px;
}
.cn-intro-body {
    flex:1;
    min-width:0;
}
.cn-intro-title {
    font-size:18px;
    font-weight:780;
    color:var(--t1);
    letter-spacing:-.015em;
}
.cn-intro-desc {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
    line-height:1.6;
}
.cn-intro-steps {
    display:flex;
    gap:6px;
    margin-top:12px;
    flex-wrap:wrap;
}
.cn-step {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    border:1px solid;
    background:rgba(99,102,241,.08);
    border-color:rgba(99,102,241,.2);
    color:var(--accent);
}
.cn-step-num {
    width:16px;
    height:16px;
    border-radius:50%;
    background:var(--accent);
    color:#fff;
    font-size:9px;
    font-weight:800;
    display:grid;
    place-items:center;
    flex-shrink:0;
}

.cn-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    overflow:hidden;
}
.cn-card-head {
    padding:18px 22px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:12px;
}
.cn-card-icon {
    width:34px;
    height:34px;
    border-radius:9px;
    display:grid;
    place-items:center;
    background:rgba(99,102,241,.1);
    color:var(--accent);
    flex-shrink:0;
}
.cn-card-icon svg {
    width:16px;
    height:16px;
}
.cn-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.cn-card-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cn-card-body {
    padding:22px;
    display:grid;
    gap:18px;
}

.cn-label {
    display:block;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.cn-label span {
    color:#ef4444;
    margin-left:2px;
}
.cn-help {
    font-size:11.5px;
    color:var(--t2);
    margin-top:5px;
    line-height:1.5;
}
.cn-input {
    width:100%;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:9px;
    padding:10px 13px;
    font-size:13.5px;
    font-weight:550;
    color:var(--t1);
    font-family:inherit;
    outline:none;
    transition:border-color .15s,box-shadow .15s;
    -webkit-appearance:none;
}
.cn-input:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(99,102,241,.13);
}
.cn-input:disabled {
    opacity:.55;
    cursor:not-allowed;
}
textarea.cn-input {
    resize:vertical;
    min-height:80px;
}
select.cn-input {
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:18px;
    padding-right:36px;
    cursor:pointer;
}
.cn-error {
    display:block;
    font-size:11.5px;
    color:#ef4444;
    margin-top:5px;
}
.cn-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}
.cn-grid-3 {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:18px;
}
@media(max-width:640px) {
    .cn-grid-2,.cn-grid-3 {
        grid-template-columns:1fr;
    }
}

.cn-toggle-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
}
.cn-toggle-info .cn-toggle-title {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.cn-toggle-info .cn-toggle-desc {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.cn-toggle-wrap {
    position:relative;
    width:44px;
    height:24px;
    flex-shrink:0;
}
.cn-toggle-wrap input {
    opacity:0;
    width:0;
    height:0;
    position:absolute;
}
.cn-toggle-track {
    position:absolute;
    inset:0;
    border-radius:12px;
    background:var(--t3);
    cursor:pointer;
    transition:background .2s;
}
.cn-toggle-wrap input:checked ~ .cn-toggle-track {
    background:var(--accent);
}
.cn-toggle-thumb {
    position:absolute;
    top:3px;
    left:3px;
    width:18px;
    height:18px;
    border-radius:50%;
    background:#fff;
    box-shadow:0 1px 4px rgba(0,0,0,.2);
    transition:transform .2s;
    pointer-events:none;
}
.cn-toggle-wrap input:checked ~ .cn-toggle-track .cn-toggle-thumb {
    transform:translateX(20px);
}

.cn-save-bar {
    display:flex;
    align-items:center;
    gap:10px;
    padding-top:20px;
    border-top:1px solid var(--bd);
}
@keyframes spin {
    from {
        transform:rotate(0deg);
    }
    to {
        transform:rotate(360deg);
    }
}
</style>

{{-- Header --}}
<div class="cn-header cna cn1">
    <h1 class="cn-page-title">Create Coupon</h1>
    <a href="{{ $backUrl }}" wire:navigate class="cn-btn cn-btn-gray">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        Back to coupons
    </a>
</div>

{{-- Intro banner --}}
<div class="cn-intro cna cn2">
    <div class="cn-intro-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
    </div>
    <div class="cn-intro-body">
        <div class="cn-intro-title">New discount code</div>
        <div class="cn-intro-desc">Set a redemption code, choose a percentage or fixed discount, and optionally cap how many times it can be used.</div>
        <div class="cn-intro-steps">
            <span class="cn-step"><span class="cn-step-num">1</span> Code &amp; status</span>
            <span class="cn-step"><span class="cn-step-num">2</span> Discount</span>
            <span class="cn-step"><span class="cn-step-num">3</span> Usage limits</span>
            <span class="cn-step"><span class="cn-step-num">4</span> Schedule (optional)</span>
        </div>
    </div>
</div>

{{-- Code & status --}}
<div class="cn-card cna cn3">
    <div class="cn-card-head">
        <div class="cn-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/></svg>
        </div>
        <div>
            <div class="cn-card-title">Code &amp; Status</div>
            <div class="cn-card-sub">What shoppers type at checkout</div>
        </div>
    </div>
    <div class="cn-card-body">
        <div class="cn-grid-2">
            <div>
                <label class="cn-label" for="cn-code">Coupon Code <span>*</span></label>
                <input id="cn-code" type="text" class="cn-input" wire:model="data.code" placeholder="e.g. SAVE20" style="text-transform:uppercase" required>
                <div class="cn-help">Always stored uppercase. Shoppers enter this at checkout.</div>
                @error('data.code') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
            <div class="cn-toggle-row" style="align-self:start;padding-top:24px">
                <div class="cn-toggle-info">
                    <div class="cn-toggle-title">Active</div>
                    <div class="cn-toggle-desc">Inactive coupons cannot be redeemed</div>
                </div>
                <label class="cn-toggle-wrap">
                    <input type="checkbox" wire:model="data.is_active" value="1">
                    <div class="cn-toggle-track"><div class="cn-toggle-thumb"></div></div>
                </label>
            </div>
        </div>
        <div>
            <label class="cn-label" for="cn-description">Description</label>
            <textarea id="cn-description" class="cn-input" wire:model="data.description" placeholder="Internal note — not shown to customers" rows="2"></textarea>
            @error('data.description') <span class="cn-error">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

{{-- Discount --}}
<div class="cn-card cna cn4">
    <div class="cn-card-head">
        <div class="cn-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div>
            <div class="cn-card-title">Discount</div>
            <div class="cn-card-sub">How much shoppers save</div>
        </div>
    </div>
    <div class="cn-card-body">
        <div class="cn-grid-3">
            <div>
                <label class="cn-label" for="cn-type">Discount Type <span>*</span></label>
                <select id="cn-type" class="cn-input" wire:model.live="data.type">
                    <option value="percentage">Percentage off</option>
                    <option value="fixed">Fixed amount off</option>
                </select>
                @error('data.type') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cn-label" for="cn-value">{{ $valueLabel }} <span>*</span></label>
                <input id="cn-value" type="number" step="0.01" min="0.01" class="cn-input" wire:model="data.value" placeholder="{{ $type === 'fixed' ? '10.00' : '20' }}" required>
                @error('data.value') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cn-label" for="cn-min-order">Minimum order amount ($)</label>
                <input id="cn-min-order" type="number" step="0.01" min="0" class="cn-input" wire:model="data.min_order_amount" placeholder="No minimum">
                @error('data.min_order_amount') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

{{-- Usage limits --}}
<div class="cn-card cna cn5">
    <div class="cn-card-head">
        <div class="cn-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        </div>
        <div>
            <div class="cn-card-title">Usage Limits</div>
            <div class="cn-card-sub">Control how many times this code can be redeemed</div>
        </div>
    </div>
    <div class="cn-card-body">
        <div class="cn-grid-2">
            <div>
                <label class="cn-label" for="cn-max-uses">Total redemption limit</label>
                <input id="cn-max-uses" type="number" min="1" class="cn-input" wire:model="data.max_uses" placeholder="Unlimited">
                <div class="cn-help">Leave blank for unlimited total uses.</div>
                @error('data.max_uses') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cn-label" for="cn-max-uses-per-user">Uses per customer <span>*</span></label>
                <input id="cn-max-uses-per-user" type="number" min="1" class="cn-input" wire:model="data.max_uses_per_user" placeholder="1" required>
                @error('data.max_uses_per_user') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

{{-- Schedule --}}
<div class="cn-card cna cn6">
    <div class="cn-card-head">
        <div class="cn-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div>
            <div class="cn-card-title">Schedule</div>
            <div class="cn-card-sub">Optionally limit when this coupon is valid</div>
        </div>
    </div>
    <div class="cn-card-body">
        <div class="cn-grid-2">
            <div>
                <label class="cn-label" for="cn-starts-at">Starts at</label>
                <input id="cn-starts-at" type="datetime-local" class="cn-input" wire:model="data.starts_at">
                <div class="cn-help">Leave blank to start immediately.</div>
                @error('data.starts_at') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="cn-label" for="cn-expires-at">Expires at</label>
                <input id="cn-expires-at" type="datetime-local" class="cn-input" wire:model="data.expires_at">
                <div class="cn-help">Leave blank for no expiry.</div>
                @error('data.expires_at') <span class="cn-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

{{-- Save bar --}}
<div class="cn-save-bar cna cn6">
    <button type="button" wire:click="create" wire:loading.attr="disabled" class="cn-btn cn-btn-indigo">
        <span wire:loading.remove wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </span>
        <span wire:loading wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Create Coupon
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="cn-btn cn-btn-gray">Cancel</a>
</div>

</div>
