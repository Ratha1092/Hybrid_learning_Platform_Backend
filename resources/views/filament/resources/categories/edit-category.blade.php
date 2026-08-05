@php
    /** @var \App\Domains\Courses\Models\Category $record */
    $cat         = $record;
    $imageUrl    = $cat->image_url;
    $isFeatured  = $data['is_featured'] ?? $cat->is_featured;
    $iconVal     = $data['icon'] ?? $cat->icon ?? '';
@endphp

<div class="ec">

<style>
.ec,.ec *,.ec *::before,.ec *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.ec {
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
    --accent:#3b82f6;
    --accent2:#2563eb;
    color:var(--t1);
}
html:not(.dark) .ec {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.08);
}
@keyframes ecUp {
    from {
        opacity:0;
        transform:translateY(10px);
    }
    to {
        opacity:1;
        transform:none;
    }
}
.eca {
    opacity:0;
    animation:ecUp .38s cubic-bezier(.16,1,.3,1) forwards;
}
.ec1 {
    animation-delay:.04s;
}
.ec2 {
    animation-delay:.09s;
}
.ec3 {
    animation-delay:.14s;
}
.ec4 {
    animation-delay:.19s;
}
.ec5 {
    animation-delay:.24s;
}

/* Header */
.ec-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.ec-page-title {
    font-size:clamp(22px,2.6vw,30px);
    font-weight:800;
    letter-spacing:-.02em;
    color:var(--t1);
}
.ec-header-actions {
    display:flex;
    align-items:center;
    gap:8px;
}

/* Buttons */
.ec-btn {
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
.ec-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.ec-btn-gray {
    background:var(--p1);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.ec-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.ec-btn-amber {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.ec-btn-amber:hover {
    background:var(--accent2);
}
.ec-btn-outline {
    background:transparent;
    border:1px solid var(--bd2);
    color:var(--t2);
}
.ec-btn-outline:hover {
    border-color:#ef4444;
    color:#ef4444;
}
.ec-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
}

/* Card */
.ec-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    overflow:hidden;
}
.ec-card-head {
    padding:18px 22px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:12px;
}
.ec-card-icon {
    width:36px;
    height:36px;
    border-radius:10px;
    display:grid;
    place-items:center;
    background:rgba(59,130,246,.12);
    color:var(--accent);
    flex-shrink:0;
}
.ec-card-icon svg {
    width:18px;
    height:18px;
}
.ec-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.ec-card-sub {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.ec-card-body {
    padding:22px;
}

/* Hero */
.ec-hero {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    box-shadow:var(--sh);
    padding:24px 28px;
    display:flex;
    align-items:center;
    gap:22px;
    flex-wrap:wrap;
}
.ec-hero-icon {
    width:72px;
    height:72px;
    border-radius:18px;
    display:grid;
    place-items:center;
    flex-shrink:0;
    background:rgba(59,130,246,.14);
}
.ec-hero-icon svg {
    width:32px;
    height:32px;
    color:var(--accent);
}
.ec-hero-img {
    width:72px;
    height:72px;
    border-radius:18px;
    object-fit:cover;
    border:2px solid var(--bd2);
    flex-shrink:0;
}
.ec-hero-info {
    flex:1;
    min-width:0;
}
.ec-hero-name {
    font-size:20px;
    font-weight:780;
    color:var(--t1);
    letter-spacing:-.015em;
    line-height:1.2;
}
.ec-hero-slug {
    font-size:12px;
    color:var(--t2);
    margin-top:3px;
    font-family:ui-monospace,monospace;
}
.ec-hero-pills {
    display:flex;
    align-items:center;
    gap:6px;
    margin-top:10px;
    flex-wrap:wrap;
}
.ec-pill {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    border:1px solid;
}
.ec-pill-amber {
    background:rgba(245,158,11,.1);
    border-color:rgba(245,158,11,.3);
    color:#d97706;
}
.ec-pill-blue {
    background:rgba(59,130,246,.1);
    border-color:rgba(59,130,246,.25);
    color:#3b82f6;
}
.ec-pill-slate {
    background:rgba(100,116,139,.08);
    border-color:rgba(100,116,139,.2);
    color:var(--t2);
}
.ec-hero-stats {
    display:flex;
    gap:20px;
    flex-shrink:0;
}
.ec-stat {
    text-align:center;
    padding:0 12px;
    border-left:1px solid var(--bd);
}
.ec-stat-val {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.ec-stat-label {
    font-size:10px;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    margin-top:2px;
}

/* Fields */
.ec-grid {
    display:grid;
    gap:18px;
}
.ec-grid-2 {
    grid-template-columns:1fr 1fr;
}
@media(max-width:560px) {
    .ec-grid-2 {
        grid-template-columns:1fr;
    }
}
.ec-label {
    display:block;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2);
    margin-bottom:6px;
}
.ec-label span {
    color:#ef4444;
    margin-left:2px;
}
.ec-input {
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
.ec-input:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,130,246,.13);
}
html.dark .ec-input:hover {
    border-color:var(--accent);
}
textarea.ec-input {
    resize:vertical;
    min-height:88px;
}
.ec-input-icon-wrap {
    position:relative;
}
.ec-input-prefix {
    position:absolute;
    left:11px;
    top:50%;
    transform:translateY(-50%);
    color:var(--t2);
    font-size:12px;
    pointer-events:none;
    font-family:ui-monospace,monospace;
}
.ec-input-icon-wrap .ec-input {
    padding-left:130px;
}
.ec-error {
    display:block;
    font-size:11.5px;
    color:#ef4444;
    margin-top:5px;
}
.ec-helper {
    font-size:11.5px;
    color:var(--t2);
    margin-top:5px;
}

/* Toggle */
.ec-toggle-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:14px 0;
}
.ec-toggle-info .ec-toggle-title {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.ec-toggle-info .ec-toggle-desc {
    font-size:11.5px;
    color:var(--t2);
    margin-top:2px;
}
.ec-toggle-wrap {
    position:relative;
    width:44px;
    height:24px;
    flex-shrink:0;
}
.ec-toggle-wrap input {
    opacity:0;
    width:0;
    height:0;
    position:absolute;
}
.ec-toggle-track {
    position:absolute;
    inset:0;
    border-radius:12px;
    background:var(--t3);
    cursor:pointer;
    transition:background .2s;
}
.ec-toggle-wrap input:checked ~ .ec-toggle-track {
    background:var(--accent);
}
.ec-toggle-thumb {
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
.ec-toggle-wrap input:checked ~ .ec-toggle-track .ec-toggle-thumb {
    transform:translateX(20px);
}

/* Image preview */
.ec-image-preview {
    display:flex;
    align-items:center;
    gap:14px;
    flex-wrap:wrap;
    margin-bottom:18px;
}
.ec-image-thumb {
    width:80px;
    height:80px;
    border-radius:10px;
    object-fit:cover;
    border:2px solid var(--bd2);
    flex-shrink:0;
}
.ec-image-meta {
    flex:1;
    min-width:0;
}
.ec-image-name {
    font-size:13px;
    font-weight:650;
    color:var(--t1);
}
.ec-image-hint {
    font-size:11.5px;
    color:var(--t2);
    margin-top:3px;
}

/* Filament imageForm: strip field label chrome, keep filepond */
.ec-fi-wrap .fi-fo-field-wrp-label {
    font-size:11px!important;
    font-weight:700!important;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--t2)!important;
    margin-bottom:8px;
}
.ec-fi-wrap .fi-fo-field-wrp {
    background:transparent!important;
    border:none!important;
    padding:0!important;
    box-shadow:none!important;
}

/* Save bar */
.ec-save-bar {
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

{{-- Notifications --}}
@if (session('success'))
<div class="ec-card eca ec1" style="border-color:rgba(52,211,153,.25);background:rgba(52,211,153,.06)">
    <div style="display:flex;align-items:center;gap:10px;padding:14px 18px;color:#059669;font-weight:600;font-size:13px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif

{{-- Header --}}
<div class="ec-header eca ec1">
    <h1 class="ec-page-title">Edit Category</h1>
    <div class="ec-header-actions">
        <a href="{{ $backUrl }}" wire:navigate class="ec-btn ec-btn-gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to categories
        </a>
        @if($canDelete)
        <button type="button" wire:click="deleteCategory"
            wire:confirm="Delete the &quot;{{ addslashes($cat->name) }}&quot; category? This cannot be undone."
            class="ec-btn ec-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            Delete
        </button>
        @endif
    </div>
</div>

{{-- Hero --}}
<div class="ec-hero eca ec2">
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="{{ $cat->name }}" class="ec-hero-img">
    @else
        <div class="ec-hero-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/>
            </svg>
        </div>
    @endif
    <div class="ec-hero-info">
        <div class="ec-hero-name">{{ $cat->name }}</div>
        <div class="ec-hero-slug">/{{ $cat->slug }}</div>
        <div class="ec-hero-pills">
            @if ($cat->is_featured)
                <span class="ec-pill ec-pill-amber">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                    Featured
                </span>
            @endif
            @if ($iconVal)
                <span class="ec-pill ec-pill-slate">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    {{ $iconVal }}
                </span>
            @endif
            <span class="ec-pill ec-pill-blue">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                {{ $courseCount }} {{ Str::plural('course', $courseCount) }}
            </span>
        </div>
    </div>
    <div class="ec-hero-stats">
        <div class="ec-stat">
            <div class="ec-stat-val">{{ $cat->sort_order ?? 0 }}</div>
            <div class="ec-stat-label">Sort Order</div>
        </div>
        <div class="ec-stat">
            <div class="ec-stat-val">{{ $courseCount }}</div>
            <div class="ec-stat-label">Courses</div>
        </div>
    </div>
</div>

{{-- Details card --}}
<div class="ec-card eca ec3">
    <div class="ec-card-head">
        <div class="ec-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
        </div>
        <div>
            <div class="ec-card-title">Category Details</div>
            <div class="ec-card-sub">Edit name, slug, description and display settings</div>
        </div>
    </div>
    <div class="ec-card-body">
        <div class="ec-grid" style="gap:20px">

            {{-- Name + Slug --}}
            <div class="ec-grid ec-grid-2">
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
            <div class="ec-grid ec-grid-2">
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
                    <p class="ec-helper">Heroicon name, e.g. <code style="font-family:ui-monospace,monospace;background:var(--p2);padding:1px 5px;border-radius:4px;border:1px solid var(--bd2)">academic-cap</code></p>
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
            <div style="border-top:1px solid var(--bd);margin-top:4px;padding-top:4px">
                <div class="ec-toggle-row">
                    <div class="ec-toggle-info">
                        <div class="ec-toggle-title">Featured Category</div>
                        <div class="ec-toggle-desc">Display this category in featured sections and homepage</div>
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
</div>

{{-- Image card --}}
<div class="ec-card eca ec4">
    <div class="ec-card-head">
        <div class="ec-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
        </div>
        <div>
            <div class="ec-card-title">Category Image</div>
            <div class="ec-card-sub">Shown on category pages and course listings</div>
        </div>
    </div>
    <div class="ec-card-body">
        @if ($imageUrl)
            <div class="ec-image-preview">
                <img src="{{ $imageUrl }}" alt="{{ $cat->name }}" class="ec-image-thumb">
                <div class="ec-image-meta">
                    <div class="ec-image-name">Current image</div>
                    <div class="ec-image-hint">Upload a new image below to replace it</div>
                </div>
            </div>
        @endif

        {{-- imageForm: only the FileUpload field, shares data statePath --}}
        <div class="ec-fi-wrap">
            {{ $this->imageForm }}
        </div>
    </div>
</div>

{{-- Save bar --}}
<div class="ec-save-bar eca ec5">
    <button
        type="button"
        wire:click="save"
        wire:loading.attr="disabled"
        class="ec-btn ec-btn-amber"
    >
        <span wire:loading.remove wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </span>
        <span wire:loading wire:target="save">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;animation:spin .7s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        Save changes
    </button>
    <a href="{{ $backUrl }}" wire:navigate class="ec-btn ec-btn-gray">Cancel</a>
</div>

</div>
