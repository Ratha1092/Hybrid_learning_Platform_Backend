@php
    $url       = fn(array $p) => url()->current() . '?' . http_build_query(array_merge(request()->query(), $p));
    $viewUrl   = fn($c) => route('filament.admin.resources.courses.view', ['record' => $c->id]);
    $editUrl   = fn($c) => route('filament.admin.resources.courses.edit', ['record' => $c->id]);
    $createUrl = route('filament.admin.resources.courses.create');

    $nameParts = explode(' ', trim($category->name ?? ''));
    $initials  = strtoupper(mb_substr($category->name ?? '?', 0, 2));
    $hue       = abs(crc32($category->name ?? '')) % 360;
    $catBg     = "hsl({$hue},55%,40%)";

    $statusStyles = [
        'published'      => ['bg' => 'rgba(52,211,153,.12)',  'color' => '#34d399', 'dot' => '#10b981', 'label' => 'Published'],
        'pending_review' => ['bg' => 'rgba(251,191,36,.12)',  'color' => '#fbbf24', 'dot' => '#f59e0b', 'label' => 'Pending Review'],
        'draft'          => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'dot' => '#94a3b8', 'label' => 'Draft'],
        'rejected'       => ['bg' => 'rgba(248,113,113,.12)', 'color' => '#f87171', 'dot' => '#ef4444', 'label' => 'Rejected'],
        'archived'       => ['bg' => 'rgba(148,163,184,.1)',  'color' => '#94a3b8', 'dot' => '#94a3b8', 'label' => 'Archived'],
    ];
@endphp

<style>
.cc,.cc *,.cc *::before,.cc *::after{box-sizing:border-box;margin:0;padding:0}
.cc{
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
    font-size:13px;line-height:1.5;
    padding-bottom:48px;display:grid;gap:20px;
    --bg:#0f172a;--p1:#1e293b;--p2:#263245;
    --bd:rgba(255,255,255,.07);--bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;--t2:#64748b;--t3:#334155;
    --sh:0 4px 24px rgba(0,0,0,.3);
    color:var(--t1);
}
html:not(.dark) .cc{
    --bg:#f1f5f9;--p1:#ffffff;--p2:#f8fafc;
    --bd:rgba(15,23,42,.13);--bd2:rgba(15,23,42,.20);
    --t1:#0f172a;--t2:#64748b;--t3:#cbd5e1;
    --sh:0 2px 16px rgba(15,23,42,.1);
}

@keyframes ccUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.cca{opacity:0;animation:ccUp .45s cubic-bezier(.16,1,.3,1) forwards}
.cc1{animation-delay:.04s}.cc2{animation-delay:.09s}.cc3{animation-delay:.14s}

/* Header */
.cc-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid var(--bd)}
.cc-header-left{display:flex;align-items:center;gap:14px}
.cc-cat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;flex-shrink:0}
.cc-header-text h1{font-size:clamp(20px,2.2vw,26px);font-weight:780;letter-spacing:-.018em;color:var(--t1);line-height:1.15}
.cc-header-meta{display:flex;align-items:center;gap:10px;margin-top:5px;flex-wrap:wrap}
.cc-meta-chip{display:inline-flex;align-items:center;gap:4px;font-size:11.5px;color:var(--t2)}
.cc-meta-sep{color:var(--t3)}
.cc-header-btns{display:flex;align-items:center;gap:8px;flex-shrink:0}
.cc-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:.02em;cursor:pointer;text-decoration:none;transition:opacity .18s,transform .15s;white-space:nowrap;border:none;font-family:inherit}
.cc-btn:hover{opacity:.85;transform:translateY(-1px)}
.cc-btn svg{width:13px;height:13px}
.cc-btn-gray{background:var(--p2);color:var(--t1);border:1px solid var(--bd2)}
.cc-btn-primary{background:#6d28d9;color:#fff}

/* Card */
.cc-card{background:var(--p1);border:1px solid var(--bd);border-radius:12px;overflow:hidden;box-shadow:var(--sh)}

/* Toolbar */
.cc-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border-bottom:1px solid var(--bd);flex-wrap:wrap}
.cc-search-box{display:flex;align-items:center;gap:6px;background:var(--p2);border:1px solid var(--bd2);border-radius:8px;padding:6px 12px}
.cc-search-box svg{width:14px;height:14px;color:var(--t2);flex-shrink:0}
.cc-search-box input{background:none;border:none;outline:none;color:var(--t1);font-size:12px;font-family:inherit;width:200px}
.cc-search-box input::placeholder{color:var(--t2)}
.cc-total-label{font-size:12px;color:var(--t2);font-weight:600}

/* Table */
.cc-table{width:100%;border-collapse:collapse}
.cc-table thead tr{border-bottom:1px solid var(--bd)}
.cc-table th{padding:10px 12px;text-align:left;font-size:10.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);white-space:nowrap}
.cc-table th.th-c{text-align:center}
.cc-table tbody tr{border-bottom:1px solid var(--bd);transition:background .12s}
.cc-table tbody tr:last-child{border-bottom:none}
.cc-table tbody tr:hover{background:var(--p2)}
.cc-row-link { cursor:pointer; }
.cc-table td{padding:12px 12px;vertical-align:middle}

/* Cells */
.cc-id{font-size:11.5px;font-weight:700;color:var(--t2);white-space:nowrap}
.cc-course-cell{display:flex;align-items:center;gap:10px}
.cc-thumb{width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0}
.cc-thumb-init{width:40px;height:40px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0}
.cc-course-title{font-size:13px;font-weight:650;color:var(--t1);line-height:1.3;max-width:260px}
.cc-course-sub{font-size:11px;color:var(--t2);margin-top:2px;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cc-instructor-cell{display:flex;align-items:center;gap:8px;white-space:nowrap}
.cc-avatar{width:26px;height:26px;border-radius:50%;flex-shrink:0;object-fit:cover}
.cc-instructor-name{font-size:12.5px;color:var(--t1);font-weight:500}
.cc-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;font-size:11.5px;font-weight:700;white-space:nowrap}
.cc-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.cc-price{font-size:12.5px;font-weight:700;color:var(--t1);white-space:nowrap}
.cc-students{text-align:center}
.cc-students-val{display:inline-flex;align-items:center;justify-content:center;gap:5px;font-size:12.5px;font-weight:600;color:var(--t1)}
.cc-students-val svg{width:13px;height:13px;color:var(--t2)}
.cc-date{font-size:12px;color:var(--t2);white-space:nowrap}

/* Actions */
.cc-actions{display:flex;align-items:center;gap:4px;justify-content:flex-end}
.cc-act-btn{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:none;border:1px solid transparent;cursor:pointer;color:var(--t2);text-decoration:none;transition:background .15s,border-color .15s,color .15s}
.cc-act-btn:hover{background:var(--p2);border-color:var(--bd2);color:var(--t1)}
.cc-act-btn svg{width:14px;height:14px}

/* Empty */
.cc-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;gap:10px;color:var(--t2)}
.cc-empty svg{width:40px;height:40px;opacity:.35}
.cc-empty p{font-size:13px}

/* Footer */
.cc-footer{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--bd);flex-wrap:wrap;gap:10px}
.cc-footer-info{font-size:12px;color:var(--t2)}
.cc-pages{display:flex;align-items:center;gap:6px}
.cc-page-btn{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;color:var(--t2);background:none;border:1px solid transparent;transition:background .15s,border-color .15s,color .15s}
.cc-page-btn:not(.disabled):hover{background:var(--p2);border-color:var(--bd2);color:var(--t1)}
.cc-page-btn.active{background:#6d28d9;color:#fff;border-color:transparent}
.cc-page-btn.disabled{opacity:.35;pointer-events:none}
.cc-per-page{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--t2)}
.cc-per-page select{appearance:none;background:var(--p2);border:1px solid var(--bd2);border-radius:7px;padding:4px 22px 4px 9px;font-size:12px;font-weight:700;color:var(--t1);font-family:inherit;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 6px center;outline:none}
</style>

<div>
<div class="cc" id="cc-root">

    {{-- ── Header ── --}}
    <div class="cc-header cca cc1">
        <div class="cc-header-left">
            @if($category->image_url ?? null)
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" style="width:48px;height:48px;border-radius:12px;object-fit:cover;flex-shrink:0">
            @else
                <div class="cc-cat-icon" style="background:{{ $catBg }}">{{ $initials }}</div>
            @endif
            <div>
                <h1 class="cc-header-text" style="font-size:clamp(20px,2.2vw,26px);font-weight:780;letter-spacing:-.018em;color:var(--t1);line-height:1.15">
                    {{ $category->name }}
                </h1>
                <div class="cc-header-meta">
                    @if($category->description)
                        <span class="cc-meta-chip">{{ \Illuminate\Support\Str::limit($category->description, 60) }}</span>
                        <span class="cc-meta-sep">·</span>
                    @endif
                    <span class="cc-meta-chip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" style="width:12px;height:12px">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                        </svg>
                        {{ $category->courses_count }} {{ Str::plural('course', $category->courses_count) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="cc-header-btns">
            <a href="{{ $backUrl }}" class="cc-btn cc-btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Categories
            </a>
            <a href="{{ $createUrl }}" class="cc-btn cc-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Course
            </a>
        </div>
    </div>

    {{-- ── Table card ── --}}
    <div class="cc-card cca cc2">

        {{-- Toolbar --}}
        <div class="cc-toolbar">
            <span class="cc-total-label">
                @if($search)
                    {{ $total }} {{ Str::plural('result', $total) }} for "{{ $search }}"
                @else
                    {{ $total }} {{ Str::plural('course', $total) }} in this category
                @endif
            </span>
            <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center">
                <input type="hidden" name="id" value="{{ $category->id }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="cc-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search courses...">
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
        <table class="cc-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th class="th-c">Students</th>
                    <th>Created</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                @php
                    $thumbUrl    = $course->thumbnail ? \Storage::disk('public')->url($course->thumbnail) : null;
                    $thumbInitBg = '#' . substr(md5($course->title ?? ''), 0, 6);
                    $thumbInit   = strtoupper(substr($course->title ?? 'C', 0, 2));

                    $instName = $course->instructor?->name ?? '—';
                    $instBg   = substr(md5($instName), 0, 6);
                    $instAvatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($instName)
                                  . '&background=' . $instBg . '&color=fff&bold=true&size=64';

                    $statusKey   = $course->status ?? 'draft';
                    $statusStyle = $statusStyles[$statusKey] ?? ['bg' => 'rgba(148,163,184,.1)', 'color' => '#94a3b8', 'dot' => '#94a3b8', 'label' => ucfirst($statusKey)];
                @endphp
                <tr class="cc-row-link" onclick="window.location='{{ $viewUrl($course) }}'">
                    <td><span class="cc-id">#{{ $course->id }}</span></td>

                    <td style="min-width:240px">
                        <div class="cc-course-cell">
                            @if($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="cc-thumb">
                            @else
                                <div class="cc-thumb-init" style="background:{{ $thumbInitBg }}">{{ $thumbInit }}</div>
                            @endif
                            <div>
                                <div class="cc-course-title">{{ $course->title }}</div>
                                @if($course->short_description)
                                    <div class="cc-course-sub">{{ \Illuminate\Support\Str::limit($course->short_description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td style="min-width:140px">
                        <div class="cc-instructor-cell">
                            <img src="{{ $instAvatarUrl }}" class="cc-avatar" alt="{{ $instName }}">
                            <span class="cc-instructor-name">{{ $instName }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="cc-price">${{ number_format((float)$course->price, 2) }}</span>
                    </td>

                    <td>
                        <span class="cc-badge" style="background:{{ $statusStyle['bg'] }};color:{{ $statusStyle['color'] }}">
                            <span class="cc-dot" style="background:{{ $statusStyle['dot'] }}"></span>
                            {{ $statusStyle['label'] }}
                        </span>
                    </td>

                    <td class="cc-students">
                        <div class="cc-students-val">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z"/></svg>
                            {{ number_format($course->enrollments_count) }}
                        </div>
                    </td>

                    <td><span class="cc-date">{{ $course->created_at?->format('M d, Y') }}</span></td>

                    <td onclick="event.stopPropagation()">
                        <div class="cc-actions">
                            <a href="{{ $viewUrl($course) }}" class="cc-act-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                            </a>
                            <a href="{{ $editUrl($course) }}" class="cc-act-btn" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="cc-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                            <p>{{ $search ? 'No courses match "' . $search . '"' : 'No courses in this category yet.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="cc-footer">
            <div class="cc-footer-info">
                @if($total > 0)
                    Showing {{ ($curPage - 1) * $perPage + 1 }} to {{ min($curPage * $perPage, $total) }} of {{ number_format($total) }} courses
                @else
                    No results
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="cc-per-page">
                    Per page
                    <select onchange="window.location.href='{{ $url([]) }}&per_page=' + this.value + '&page=1'">
                        @foreach([10, 25, 50] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                @if($totalPages > 1)
                <div class="cc-pages">
                    <a href="{{ $url(['page' => max(1, $curPage - 1)]) }}" class="cc-page-btn {{ $curPage === 1 ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    @for($p = max(1, $curPage - 2); $p <= min($totalPages, $curPage + 2); $p++)
                        <a href="{{ $url(['page' => $p]) }}" class="cc-page-btn {{ $curPage === $p ? 'active' : '' }}">{{ $p }}</a>
                    @endfor
                    <a href="{{ $url(['page' => min($totalPages, $curPage + 1)]) }}" class="cc-page-btn {{ $curPage === $totalPages ? 'disabled' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
</div>
