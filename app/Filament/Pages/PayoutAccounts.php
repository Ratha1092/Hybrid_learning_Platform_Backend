<?php

namespace App\Filament\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Finance\Models\InstructorPayoutAccount;
use App\Domains\Notifications\Notifications\PayoutAccountRejectedNotification;
use App\Domains\Notifications\Notifications\PayoutAccountVerifiedNotification;
use App\Domains\System\Models\Setting;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PayoutAccounts extends Page
{
    protected string $view = 'filament.pages.payout-accounts';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Payout Accounts';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'payout-accounts';

    public static function canAccess(): bool
    {
        return PanelAccess::can('payout_accounts.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = InstructorPayoutAccount::where('status', 'pending')->count();

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
        if (!PanelAccess::can('payout_accounts.approve')) {
            return;
        }

        $account = InstructorPayoutAccount::findOrFail($id);

        if ($account->status !== 'pending') {
            return;
        }

        $account->update([
            'status' => 'verified',
            'rejection_reason' => null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLogService::logChange('payout_account.verified', $account);

        if (Setting::get('payout_notification', true)) {
            $account->instructor->notify(new PayoutAccountVerifiedNotification());
        }

        Notification::make()
            ->title('Payout Account Verified')
            ->body("{$account->instructor->name}'s payout account has been verified.")
            ->success()
            ->send();
    }

    public function reject(int $id, string $reason): void
    {
        if (!PanelAccess::can('payout_accounts.reject')) {
            return;
        }

        $account = InstructorPayoutAccount::findOrFail($id);

        if ($account->status !== 'pending') {
            return;
        }

        $reason = trim($reason);
        if ($reason === '') {
            Notification::make()->title('Rejection reason is required.')->danger()->send();
            return;
        }

        $account->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLogService::logChange('payout_account.rejected', $account, [], ['reason' => $reason]);

        if (Setting::get('payout_notification', true)) {
            $account->instructor->notify(new PayoutAccountRejectedNotification($reason));
        }

        Notification::make()
            ->title('Payout Account Rejected')
            ->danger()
            ->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $base = fn () => InstructorPayoutAccount::query();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                              'color' => '#7c3aed'],
            ['key' => 'pending',  'label' => 'Pending',  'count' => $base()->where('status', 'pending')->count(),  'color' => '#fbbf24'],
            ['key' => 'verified', 'label' => 'Verified', 'count' => $base()->where('status', 'verified')->count(), 'color' => '#34d399'],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $base()->where('status', 'rejected')->count(), 'color' => '#f87171'],
        ];

        $query = InstructorPayoutAccount::with('instructor:id,name,email');

        if ($tab !== 'all' && in_array($tab, ['pending', 'verified', 'rejected'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->whereHas('instructor', fn ($q) => $q
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%"));
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $accounts   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $canUpdate = PanelAccess::can('payout_accounts.approve') || PanelAccess::can('payout_accounts.reject');

        return compact('tabs', 'tab', 'search', 'accounts', 'total', 'totalPages', 'curPage', 'perPage', 'canUpdate');
    }
}
