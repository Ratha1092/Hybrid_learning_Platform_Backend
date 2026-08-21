@php
    /** @var \App\Domains\Courses\Models\Category $category */
    $category = $record;

    $coursesCount     = $category->courses()->count();
    $publishedCount   = $category->courses()->where('is_published', true)->count();
    $enrollmentsCount = \App\Domains\Learning\Models\Enrollment::whereHas(
        'course', fn ($q) => $q->where('category_id', $category->id)
    )->count();

    $initials = strtoupper(mb_substr($category->name, 0, 2));
    $hue      = abs(crc32($category->name)) % 360;
    $avatarBg = "hsl({$hue},55%,42%)";

    $editUrl     = route('filament.admin.resources.categories.edit', ['record' => $category->id]);
    $coursesUrl  = route('filament.admin.pages.category-courses') . '?id=' . $category->id;
    $backUrl     = route('filament.admin.pages.categories');
@endphp

<div class="vc">

<style>
.vc, .vc *, .vc *::before, .vc *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
.vc {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
    font-size:13px;
    line-height:1.5;
    display:grid;
    gap:20px;
    padding-bottom:48px;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    --accent:#3b82f6;
    color:var(--t1);
}
html:not(.dark) .vc {
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.08);
    --bd2:rgba(15,23,42,.14);
    --t1:#0f172a;
    --t2:#64748b;
    --t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}

/* Top bar */
.vc-topbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    padding-bottom:20px;
    border-bottom:1px solid var(--bd);
}
.vc-topbar-left {
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}
.vc-btn {
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
    transition:all .15s;
    white-space:nowrap;
}
.vc-btn svg {
    width:14px;
    height:14px;
    flex-shrink:0;
}
.vc-btn-gray {
    background:var(--p2);
    border:1px solid var(--bd2);
    color:var(--t2);
}
.vc-btn-gray:hover {
    color:var(--t1);
    border-color:var(--accent);
}
.vc-btn-primary {
    background:var(--accent);
    color:#fff;
    border:1px solid transparent;
}
.vc-btn-primary:hover {
    opacity:.88;
}

/* Card */
.vc-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:14px;
    overflow:hidden;
    box-shadow:var(--sh);
}
.vc-card-header {
    padding:16px 20px;
    border-bottom:1px solid var(--bd);
    display:flex;
    align-items:center;
    gap:8px;
}
.vc-card-header-icon {
    width:32px;
    height:32px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(59,130,246,.12);
    flex-shrink:0;
}
.vc-card-header-icon svg {
    width:16px;
    height:16px;
    color:var(--accent);
}
.vc-card-title {
    font-size:13px;
    font-weight:750;
    color:var(--t1);
}
.vc-card-body {
    padding:20px;
}

/* Hero */
.vc-hero {
    display:grid;
    grid-template-columns:140px 1fr;
    gap:24px;
    align-items:start;
}
@media (max-width: 640px) {
    .vc-hero {
        grid-template-columns:1fr;
    }
}
.vc-avatar {
    width:100%;
    aspect-ratio:1;
    border-radius:14px;
    object-fit:cover;
    background:var(--p2);
    border:1px solid var(--bd2);
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}
.vc-avatar img {
    width:100%;
    height:100%;
    object-fit:cover;
}
.vc-avatar-init {
    font-size:34px;
    font-weight:800;
    color:#fff;
}
.vc-hero-meta {
    display:flex;
    flex-direction:column;
    gap:12px;
}
.vc-name {
    font-size:clamp(18px, 2.2vw, 26px);
    font-weight:800;
    color:var(--t1);
    line-height:1.2;
    letter-spacing:-.02em;
}
.vc-slug {
    font-size:12.5px;
    color:var(--t2);
    font-family:'JetBrains Mono', ui-monospace, monospace;
}
.vc-desc {
    font-size:13px;
    color:var(--t2);
    line-height:1.6;
}
.vc-badges {
    display:flex;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
}
.vc-badge {
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:4px 10px;
    border-radius:7px;
    font-size:11.5px;
    font-weight:700;
}
.vc-dot {
    width:5px;
    height:5px;
    border-radius:50%;
    flex-shrink:0;
}
.vc-meta-row {
    display:flex;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
}
.vc-meta-item {
    display:flex;
    align-items:center;
    gap:5px;
    font-size:12px;
    color:var(--t2);
}
.vc-meta-item svg {
    width:13px;
    height:13px;
    color:var(--t2);
}

/* Stats grid */
.vc-stats {
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:12px;
}
@media (max-width: 640px) {
    .vc-stats {
        grid-template-columns:1fr;
    }
}
.vc-stat {
    background:var(--p2);
    border:1px solid var(--bd);
    border-radius:10px;
    padding:14px 16px;
    display:flex;
    flex-direction:column;
    gap:4px;
}
.vc-stat-val {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}
.vc-stat-label {
    font-size:11px;
    font-weight:600;
    color:var(--t2);
    text-transform:uppercase;
    letter-spacing:.05em;
}
.vc-stat-link {
    text-decoration:none;
    transition:border-color .15s;
    cursor:pointer;
}
.vc-stat-link:hover {
    border-color:var(--accent);
}
.vc-stat-link .vc-stat-val {
    color:var(--accent);
}

/* Fields */
.vc-field {
    display:flex;
    flex-direction:column;
    gap:4px;
    padding:12px 0;
    border-bottom:1px solid var(--bd);
}
.vc-field:last-child {
    border-bottom:none;
    padding-bottom:0;
}
.vc-field-label {
    font-size:10.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
}
.vc-field-value {
    font-size:13px;
    color:var(--t1);
    font-weight:500;
}
.vc-field-value.muted {
    color:var(--t2);
    font-style:italic;
}
</style>

    {{-- Top bar --}}
    <div class="vc-topbar">
        <div class="vc-topbar-left">
            <a href="{{ $backUrl }}" wire:navigate class="vc-btn vc-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Categories
            </a>
            <span style="font-size:12px;color:var(--t2)">Category #{{ $category->id }}</span>
        </div>
        <a href="{{ $editUrl }}" wire:navigate class="vc-btn vc-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
            Edit
        </a>
    </div>

    {{-- Hero card --}}
    <div class="vc-card">
        <div class="vc-card-body">
            <div class="vc-hero">
                <div class="vc-avatar" style="{{ ! $category->image_url ? 'background:' . $avatarBg : '' }}">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}">
                    @else
                        <span class="vc-avatar-init">{{ $initials }}</span>
                    @endif
                </div>
                <div class="vc-hero-meta">
                    <div class="vc-name">{{ $category->name }}</div>
                    <div class="vc-slug">/{{ $category->slug }}</div>
                    @if($category->description)
                    <div class="vc-desc">{{ $category->description }}</div>
                    @endif
                    <div class="vc-badges">
                        @if($category->is_featured)
                        <span class="vc-badge" style="background:rgba(251,191,36,.14);color:#fbbf24">
                            <span class="vc-dot" style="background:#fbbf24"></span>
                            Featured
                        </span>
                        @endif
                        @if($category->icon)
                        <span class="vc-badge" style="background:rgba(148,163,184,.12);color:#94a3b8">{{ $category->icon }}</span>
                        @endif
                    </div>
                    <div class="vc-meta-row">
                        <div class="vc-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25"/></svg>
                            Sort order: {{ $category->sort_order ?? 0 }}
                        </div>
                        <div class="vc-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            Created {{ $category->created_at?->setTimezone(config('app.timezone'))->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="vc-stats">
        <a href="{{ $coursesUrl }}" wire:navigate class="vc-stat vc-stat-link" title="View courses in this category">
            <div class="vc-stat-val">{{ number_format($coursesCount) }}</div>
            <div class="vc-stat-label">Courses</div>
        </a>
        <div class="vc-stat">
            <div class="vc-stat-val">{{ number_format($publishedCount) }}</div>
            <div class="vc-stat-label">Published</div>
        </div>
        <div class="vc-stat">
            <div class="vc-stat-val">{{ number_format($enrollmentsCount) }}</div>
            <div class="vc-stat-label">Enrollments</div>
        </div>
    </div>

    {{-- Details --}}
    <div class="vc-card">
        <div class="vc-card-header">
            <div class="vc-card-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
            </div>
            <span class="vc-card-title">Category Details</span>
        </div>
        <div class="vc-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:0">
            <div class="vc-field">
                <div class="vc-field-label">Name</div>
                <div class="vc-field-value">{{ $category->name }}</div>
            </div>
            <div class="vc-field">
                <div class="vc-field-label">Slug</div>
                <div class="vc-field-value">{{ $category->slug }}</div>
            </div>
            <div class="vc-field">
                <div class="vc-field-label">Icon</div>
                <div class="vc-field-value {{ !$category->icon ? 'muted' : '' }}">{{ $category->icon ?: 'No icon set' }}</div>
            </div>
            <div class="vc-field">
                <div class="vc-field-label">Featured</div>
                <div class="vc-field-value">{{ $category->is_featured ? 'Yes' : 'No' }}</div>
            </div>
            <div class="vc-field">
                <div class="vc-field-label">Created</div>
                <div class="vc-field-value">{{ $category->created_at?->setTimezone(config('app.timezone'))->format('M d, Y H:i') }}</div>
            </div>
            <div class="vc-field">
                <div class="vc-field-label">Updated</div>
                <div class="vc-field-value">{{ $category->updated_at?->setTimezone(config('app.timezone'))->format('M d, Y H:i') }}</div>
            </div>
            <div class="vc-field" style="grid-column:1/-1;border-bottom:none">
                <div class="vc-field-label">Description</div>
                <div class="vc-field-value {{ !$category->description ? 'muted' : '' }}" style="white-space:pre-line">{{ $category->description ?: 'No description' }}</div>
            </div>
        </div>
    </div>

</div>
