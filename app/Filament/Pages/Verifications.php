<?php

namespace App\Filament\Pages;

use App\Domains\Notifications\Notifications\InstructorApprovedNotification;
use App\Domains\Notifications\Notifications\InstructorRejectedNotification;
use App\Domains\Users\Models\InstructorProfile;
use App\Domains\Users\Models\InstructorVerification;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Verifications extends Page
{
    protected string $view = 'filament.pages.verifications';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Verifications';
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'instructor-verifications';

    public static function canAccess(): bool
    {
        return PanelAccess::can('users.view') || PanelAccess::can('instructor_verifications.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = InstructorVerification::pending()->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function approve(int $id): void
    {
        $verification = InstructorVerification::findOrFail($id);

        if ($verification->status !== 'pending') return;

        $verification->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $user = $verification->user;

        InstructorProfile::firstOrCreate(['user_id' => $verification->user_id]);

        $user->syncRoles(['instructor']);
        $user->notify(new InstructorApprovedNotification());

        Notification::make()
            ->title('Instructor Approved')
            ->body("{$user->name} has been approved as instructor.")
            ->success()
            ->send();

        $this->redirect(request()->fullUrl());
    }

    public function reject(int $id, string $reason): void
    {
        $verification = InstructorVerification::findOrFail($id);
        if ($verification->status !== 'pending') return;
        $verification->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        $user = $verification->user;
        $user->removeRole('instructor');
        $user->notify(new InstructorRejectedNotification($reason));

        Notification::make()
            ->title('Application Rejected')
            ->danger()
            ->send();

        $this->redirect(request()->fullUrl());
    }

    protected function getViewData(): array
    {
        $tab     = request('tab', 'all');
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;
        $base = fn() => InstructorVerification::withoutTrashed();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                                 'color' => '#ea580c'],
            ['key' => 'pending',  'label' => 'Pending',  'count' => $base()->where('status', 'pending')->count(),     'color' => '#fbbf24'],
            ['key' => 'approved', 'label' => 'Approved', 'count' => $base()->where('status', 'approved')->count(),    'color' => '#34d399'],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $base()->where('status', 'rejected')->count(),    'color' => '#f87171'],
        ];

        $query = InstructorVerification::withoutTrashed()->with('user:id,name,email');

        if ($tab !== 'all' && in_array($tab, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) => $q2->where('email', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');
        $total         = $query->count();
        $totalPages    = max(1, (int) ceil($total / $perPage));
        $curPage       = min($page, $totalPages);
        $verifications = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();
        return compact('tabs', 'tab', 'search', 'verifications', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
