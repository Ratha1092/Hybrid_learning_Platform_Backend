<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\InstructorVerification;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class Moderation extends Page
{
    protected string $view = 'filament.pages.moderation';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel = 'Moderation';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'moderation';

    public static function canAccess(): bool
    {
        return PanelAccess::can('courses.view') || PanelAccess::can('users.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $recentCourseRejection = Course::withoutGlobalScopes()
            ->where('status', Course::STATUS_REJECTED)
            ->where('updated_at', '>=', now()->subDay())
            ->exists();

        $recentVerificationRejection = InstructorVerification::withoutTrashed()
            ->rejected()
            ->where('reviewed_at', '>=', now()->subDay())
            ->exists();

        return ($recentCourseRejection || $recentVerificationRejection) ? '•' : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $tab     = request('tab', 'all');
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $entries = collect();

        if (PanelAccess::can('courses.view')) {
            $entries = $entries->merge(
                Course::withoutGlobalScopes()
                    ->where('status', Course::STATUS_REJECTED)
                    ->with('instructor:id,name,email')
                    ->get()
                    ->map(fn (Course $course) => [
                        'type'      => 'course',
                        'type_label' => 'Course',
                        'title'     => $course->title,
                        'subject'   => $course->instructor?->name ?? '—',
                        'reason'    => $course->rejection_reason,
                        'date'      => $course->updated_at,
                        'url'       => route('filament.admin.resources.courses.view', ['record' => $course->id]),
                    ])
            );
        }

        if (PanelAccess::can('users.view')) {
            $entries = $entries->merge(
                InstructorVerification::withoutTrashed()
                    ->rejected()
                    ->with('user:id,name,email')
                    ->get()
                    ->map(fn (InstructorVerification $verification) => [
                        'type'      => 'verification',
                        'type_label' => 'Instructor',
                        'title'     => 'Instructor verification',
                        'subject'   => $verification->user?->name ?? '—',
                        'reason'    => $verification->rejection_reason,
                        'date'      => $verification->reviewed_at ?? $verification->updated_at,
                        'url'       => route('filament.admin.resources.instructor-verifications.view', ['record' => $verification->id]),
                    ])
            );
        }

        $tabs = [
            ['key' => 'all',          'label' => 'All',         'count' => $entries->count(),                                         'color' => '#ea580c'],
            ['key' => 'course',       'label' => 'Courses',     'count' => $entries->where('type', 'course')->count(),               'color' => '#f87171'],
            ['key' => 'verification', 'label' => 'Instructors', 'count' => $entries->where('type', 'verification')->count(),         'color' => '#f87171'],
        ];

        if ($tab !== 'all' && in_array($tab, ['course', 'verification'])) {
            $entries = $entries->where('type', $tab);
        } else {
            $tab = 'all';
        }

        if ($search) {
            $entries = $entries->filter(fn (array $e) => str_contains(strtolower($e['title']), strtolower($search))
                || str_contains(strtolower($e['subject']), strtolower($search)));
        }

        $entries = $entries->sortByDesc(fn (array $e) => $e['date'])->values();

        $total      = $entries->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $items      = $entries->slice(($curPage - 1) * $perPage, $perPage)->values();

        return compact('tabs', 'tab', 'search', 'items', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
