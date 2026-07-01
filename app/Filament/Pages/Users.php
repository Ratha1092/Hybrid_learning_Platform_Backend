<?php

namespace App\Filament\Pages;

use App\Domains\Users\Models\User;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Number;

class Users extends Page
{
    protected string $view = 'filament.pages.users';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Users';
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'users';

    public static function canAccess(): bool
    {
        return PanelAccess::can('users.view');
    }

    public function mount(): void
    {
        NavBadge::markSeen('users');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('users', fn (?\Carbon\Carbon $since) => $since
            ? User::where('created_at', '>', $since)->count()
            : User::count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New users since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $tab = 'all';
    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function suspendUser(int $id): void
    {
        $user = User::withoutTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            Notification::make()->title('Cannot suspend yourself')->danger()->send();
            return;
        }

        if ($user->hasRole('super-admin') && ! auth()->user()?->hasRole('super-admin')) {
            Notification::make()->title('Insufficient permissions')->danger()->send();
            return;
        }

        $user->update(['status' => 'suspended']);

        Notification::make()
            ->title('User suspended')
            ->body("{$user->name} has been suspended.")
            ->warning()
            ->send();
    }

    public function unsuspendUser(int $id): void
    {
        $user = User::withoutTrashed()->findOrFail($id);
        $user->update(['status' => 'active']);

        Notification::make()
            ->title('User restored')
            ->body("{$user->name}'s account is active again.")
            ->success()
            ->send();
    }

    public function removeUser(int $id): void
    {
        $user = User::withoutTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            Notification::make()->title('Cannot remove yourself')->danger()->send();
            return;
        }

        if ($user->hasRole('super-admin') && ! auth()->user()?->hasRole('super-admin')) {
            Notification::make()->title('Insufficient permissions')->danger()->send();
            return;
        }

        $user->delete();

        Notification::make()
            ->title('User removed')
            ->body("{$user->name} has been removed.")
            ->danger()
            ->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $base = fn() => User::withoutTrashed();

        $roleMeta = [
            'super-admin'     => ['label' => 'Super Admin',     'color' => '#dc2626'],
            'admin'           => ['label' => 'Admin',           'color' => '#a855f7'],
            'finance-manager' => ['label' => 'Finance Manager', 'color' => '#0d9488'],
            'accountant'      => ['label' => 'Accountant',      'color' => '#0d9488'],
            'content-manager' => ['label' => 'Content Manager', 'color' => '#d97706'],
            'moderator'       => ['label' => 'Moderator',       'color' => '#d97706'],
            'support-staff'   => ['label' => 'Support Staff',   'color' => '#3b82f6'],
            'instructor'      => ['label' => 'Instructor',      'color' => '#3b82f6'],
            'student'         => ['label' => 'Student',         'color' => '#10b981'],
        ];

        $tabs = [
            ['key' => 'all', 'label' => 'All', 'count' => $base()->count(), 'color' => '#2563eb'],
        ];

        foreach ($roleMeta as $roleName => $meta) {
            $tabs[] = [
                'key'   => $roleName,
                'label' => $meta['label'],
                'count' => $base()->role($roleName)->count(),
                'color' => $meta['color'],
            ];
        }

        $query = User::withoutTrashed();

        if ($tab !== 'all' && array_key_exists($tab, $roleMeta)) {
            $query->role($tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $users      = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'users', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
