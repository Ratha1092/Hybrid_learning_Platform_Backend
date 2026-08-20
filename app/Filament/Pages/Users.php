<?php

namespace App\Filament\Pages;

use App\Domains\Users\Mail\AccountSuspendedMail;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Number;

class Users extends Page
{
    use HasDateRangePresets;

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

    public string $preset = 'all_time';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->preset   = request('preset', 'all_time');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');

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

        if ($user->hasRole('super-admin')) {
            Notification::make()->title('Super Admin accounts cannot be suspended')->danger()->send();
            return;
        }

        $user->update(['status' => 'suspended']);

        // Cut off any active session/token immediately rather than waiting for their token to expire
        $user->tokens()->delete();

        Mail::to($user->email)->send(new AccountSuspendedMail($user));

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

        if ($user->hasRole('super-admin')) {
            Notification::make()->title('Super Admin accounts cannot be removed')->danger()->send();
            return;
        }

        $user->delete();

        Notification::make()
            ->title('User removed')
            ->body("{$user->name} has been removed.")
            ->danger()
            ->send();
    }

    public function restoreUser(int $id): void
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return;
        }

        $user->restore();

        Notification::make()
            ->title('User restored')
            ->body("{$user->name} has been restored.")
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        [$from, $to] = static::resolvePreset($this->preset, 'all_time', $this->dateFrom ?: null, $this->dateTo ?: null);

        // Super-admin accounts are invisible to everyone except other super-admins.
        $viewerIsSuperAdmin = auth()->user()?->hasRole('super-admin') ?? false;
        $hideSuperAdmins = fn ($q) => $viewerIsSuperAdmin
            ? $q
            : $q->whereDoesntHave('roles', fn ($r) => $r->where('name', 'super-admin'));

        $base = fn() => static::applyDateRange($hideSuperAdmins(User::withoutTrashed()), 'created_at', $from, $to);

        $roleMeta = [
            'instructor' => ['label' => 'Instructor', 'color' => '#3b82f6'],
            'student'    => ['label' => 'Student',    'color' => '#10b981'],
        ];

        $trashedQuery = $hideSuperAdmins(User::onlyTrashed());

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

        $tabs[] = ['key' => 'trashed', 'label' => 'Deleted', 'count' => (clone $trashedQuery)->count(), 'color' => '#94a3b8'];

        $query = $tab === 'trashed' ? $trashedQuery : $base();

        if ($tab !== 'all' && $tab !== 'trashed' && array_key_exists($tab, $roleMeta)) {
            $query->role($tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $users      = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $activePreset      = $this->preset;
        $activePeriodLabel = $this->preset !== 'all_time'
            ? (static::dateRangePresetOptions()[$this->preset] ?? ucfirst($this->preset))
            : null;

        return compact('tabs', 'tab', 'search', 'users', 'total', 'totalPages', 'curPage', 'perPage', 'activePreset', 'activePeriodLabel');
    }
}
