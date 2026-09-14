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

    public string $tab = 'all';
    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function mount(): void
    {
        $this->tab = in_array(request('tab'), ['pending', 'approved', 'rejected'], true)
            ? request('tab')
            : 'all';
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
        $this->page = 1;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
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
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $base = fn() => InstructorVerification::withoutTrashed();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                                 'color' => '#2563eb'],
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
                $q->whereHas('user', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) => $q2->where('email', 'ilike', "%{$search}%"));
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
