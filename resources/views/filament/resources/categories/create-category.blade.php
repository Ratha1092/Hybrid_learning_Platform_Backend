<div class="ec">

<style>
.ec,.ec *,.ec *::before,.ec *::after{box-sizing:border-box;margin:0;padding:0}
.ec{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;
    font-size:13px;line-height:1.5;padding-bottom:56px;display:grid;gap:20px;
    --p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.28);
    --accent:#3b82f6;--accent2:#2563eb;
    color:var(--t1);
}
html:not(.dark) .ec{
    --p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.08);--bd2:rgba(15,23,42,.14);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes ecUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.eca{opacity:0;animation:ecUp .38s cubic-bezier(.16,1,.3,1) forwards}
.ec1{animation-delay:.04s}.ec2{animation-delay:.09s}.ec3{animation-delay:.14s}.ec4{animation-delay:.19s}.ec5{animation-delay:.24s}

/* Header */
.ec-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.ec-page-title{font-size:clamp(22px,2.6vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--t1)}
.ec-header-actions{display:flex;align-items:center;gap:8px}

/* Buttons */
.ec-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;font-family:inherit;transition:all .15s;white-space:nowrap}
.ec-btn svg{width:14px;height:14px;flex-shrink:0}
.ec-btn-gray{background:var(--p1);border:1px solid var(--bd2);color:var(--t2)}.ec-btn-gray:hover{color:var(--t1);border-color:var(--accent)}
.ec-btn-amber{background:var(--accent);color:#fff;border:1px solid transparent}.ec-btn-amber:hover{background:var(--accent2)}
.ec-btn:disabled{opacity:.5;cursor:not-allowed}

/* Intro banner */
.ec-intro{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);padding:24px 28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.ec-intro-icon{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;flex-shrink:0;background:rgba(59,130,246,.12);color:var(--accent)}
.ec-intro-icon svg{width:28px;height:28px}
.ec-intro-body{flex:1;min-width:0}
.ec-intro-title{font-size:18px;font-weight:780;color:var(--t1);letter-spacing:-.015em}
.ec-intro-desc{font-size:12px;color:var(--t2);margin-top:4px;line-height:1.6}
.ec-intro-steps{display:flex;gap:6px;margin-top:12px;flex-wrap:wrap}
.ec-step{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;border:1px solid;background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2);color:var(--accent)}
.ec-step-num{width:16px;height:16px;border-radius:50%;background:var(--accent);color:#fff;font-size:9px;font-weight:800;display:grid;place-items:center;flex-shrink:0}

/* Card */
.ec-card{background:var(--p1);border:1px solid var(--bd);border-radius:14px;box-shadow:var(--sh);overflow:hidden}
.ec-card-head{padding:18px 22px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:12px}
.ec-card-icon{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;background:rgba(59,130,246,.1);color:var(--accent);flex-shrink:0}
.ec-card-icon svg{width:16px;height:16px}
.ec-card-title{font-size:13px;font-weight:750;color:var(--t1)}
.ec-card-sub{font-size:11.5px;color:var(--t2);margin-top:2px}
.ec-card-body{padding:22px;display:grid;gap:18px}

/* Fields */
.ec-label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--t2);margin-bottom:6px}
.ec-label span{color:#ef4444;margin-left:2px}
.ec-input{width:100%;background:var(--p2);border:1px solid var(--bd2);border-radius:9px;padding:10px 13px;font-size:13.5px;font-weight:550;color:var(--t1);font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s;-webkit-appearance:none}
.ec-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(59,130,246,.13)}
textarea.ec-input{resize:vertical;min-height:88px}
.ec-input-icon-wrap{position:relative}
.ec-input-prefix{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--t2);font-size:12px;pointer-events:none;font-family:ui-monospace,monospace}
.ec-error{display:block;font-size:11.5px;color:#ef4444;margin-top:5px}
.ec-helper{font-size:11.5px;color:var(--t2);margin-top:5px}
.ec-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:560px){.ec-grid-2{grid-template-columns:1fr}}

/* Toggle */
.ec-toggle-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 0}
.ec-toggle-info .ec-toggle-title{font-size:13px;font-weight:650;color:var(--t1)}
.ec-toggle-info .ec-toggle-desc{font-size:11.5px;color:var(--t2);margin-top:2px}
.ec-toggle-wrap{position:relative;width:44px;height:24px;flex-shrink:0}
.ec-toggle-wrap input{opacity:0;width:0;height:0;position:absolute}
.ec-toggle-track{position:absolute;inset:0;border-radius:12px;background:var(--t3);cursor:pointer;transition:background .2s}
.ec-toggle-wrap input:checked ~ .ec-toggle-track{background:var(--accent)}
.ec-toggle-thumb{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s;pointer-events:none}
.ec-toggle-wrap input:checked ~ .ec-toggle-track .ec-toggle-thumb{transform:translateX(20px)}

/* Image form override */
.ec-fi-wrap .fi-fo-field-wrp-label{font-size:11px!important;font-weight:700!important;text-transform:uppercase;letter-spacing:.05em;color:var(--t2)!important;margin-bottom:8px}
.ec-fi-wrap .fi-fo-field-wrp{background:transparent!important;border:none!important;padding:0!important;box-shadow:none!important}

/* Save bar */
.ec-save-bar{display:flex;align-items:center;gap:10px;padding-top:20px;border-top:1px solid var(--bd)}
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
</style>

{{-- Header --}}
<div class="ec-header eca ec1">
    <h1 class="ec-page-title">Create Category</h1>
    <div class="ec-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="ec-btn ec-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
            Back to Categories
        </a>
    </div>
</div>

{{-- Intro banner --}}
<div class="ec-intro eca ec2">
    <div class="ec-intro-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/>
        </svg>
    </div>
    <div class="ec-intro-body">
        <div class="ec-intro-title">New Category</div>
        <div class="ec-intro-desc">Create a new course category to organise your platform's content. Set a name, slug, description, and optional image before saving.</div>
        <div class="ec-intro-steps">
            <span class="ec-step"><span class="ec-step-num">1</span> Name &amp; slug</span>
            <span class="ec-step"><span class="ec-step-num">2</span> Description &amp; settings</span>
            <span class="ec-step"><span class="ec-step-num">3</span> Upload image</span>
        </div>
    </div>
</div>

{{-- Details card --}}
<div class="ec-card eca ec3">
    <div class="ec-card-head">
        <div class="ec-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
            </svg>
        </div>
        <div>
            <div class="ec-card-title">Category Details</div>
            <div class="ec-card-sub">Name, slug, description, and display settings</div>
        </div>
    </div>
    <div class="ec-card-body">

        {{-- Name + Slug --}}
        <div class="ec-grid-2">
            <div>
                <label class="ec-label" for="ec-name">Name <span>*</span></label>
                <input
                    id="ec-name"
                    type="text"
                    class="ec-input"
                    wire:model.live.debounce.400ms="data.name"
                    placeholder="Category name"
                    required
                >
                @error('data.name') <span class="ec-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="ec-label" for="ec-slug">Slug <span>*</span></label>
                <input
                    id="ec-slug"
                    type="text"
                    class="ec-input"
                    wire:model="data.slug"
                    placeholder="category-slug"
                    style="font-family:ui-monospace,monospace;font-size:12.5px"
                >
                @error('data.slug') <span class="ec-error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="ec-label" for="ec-desc">Description</label>
            <textarea
                id="ec-desc"
                class="ec-input"
                wire:model="data.description"
                placeholder="Brief description of this category…"
                rows="3"
            ></textarea>
            @error('data.description') <span class="ec-error">{{ $message }}</span> @enderror
        </div>

        {{-- Icon + Sort Order --}}
        <div class="ec-grid-2">
            <div>
                <label class="ec-label" for="ec-icon">Icon</label>
                <div class="ec-input-icon-wrap">
                    <span class="ec-input-prefix">heroicon-o-</span>
                    <input
                        id="ec-icon"
                        type="text"
                        class="ec-input"
                        wire:model="data.icon"
                        placeholder="academic-cap"
                        style="font-family:ui-monospace,monospace;font-size:12.5px;padding-left:118px"
                    >
                </div>
                <p class="ec-helper">e.g. <code style="font-family:ui-monospace,monospace;background:var(--p2);padding:1px 5px;border-radius:4px;border:1px solid var(--bd2)">academic-cap</code></p>
                @error('data.icon') <span class="ec-error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="ec-label" for="ec-sort">Sort Order</label>
                <input
                    id="ec-sort"
                    type="number"
                    class="ec-input"
                    wire:model="data.sort_order"
                    placeholder="0"
                    min="0"
                >
                <p class="ec-helper">Lower numbers appear first</p>
                @error('data.sort_order') <span class="ec-error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Featured toggle --}}
        <div style="border-top:1px solid var(--bd);padding-top:4px">
            <div class="ec-toggle-row">
                <div class="ec-toggle-info">
                    <div class="ec-toggle-title">Featured Category</div>
                    <div class="ec-toggle-desc">Display this category in featured sections and the homepage</div>
                </div>
                <label class="ec-toggle-wrap">
                    <input type="checkbox" wire:model="data.is_featured" value="1">
                    <div class="ec-toggle-track">
                        <div class="ec-toggle-thumb"></div>
                    </div>
                </label>
            </div>
        </div>

    </div>
</div>

{{-- Image card --}}
<div class="ec-card eca ec4">
    <div class="ec-card-head">
        <div class="ec-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
            </svg>
        </div>
        <div>
            <div class="ec-card-title">Category Image</div>
            <div class="ec-card-sub">Shown on category pages and course listings (optional)</div>
        </div>
    </div>
    <div class="ec-card-body">
        <div class="ec-fi-wrap">
            {{ $this->imageForm }}
        </div>
    </div>
</div>

{{-- Save bar --}}
<div class="ec-save-bar eca ec5">
    <button
        type="button"
        wire:click="create"
        wire:loading.attr="disabled"
        class="ec-btn ec-btn-amber"
    >
        <span wire:loading.remove wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
        </span>
        <span wire:loading wire:target="create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
        </span>
        Create Category
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="ec-btn ec-btn-gray">Cancel</a>
</div>

</div>
