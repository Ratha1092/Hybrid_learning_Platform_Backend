<?php

namespace App\Filament\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Finance\Services\PayoutReceiptService;
use App\Domains\Notifications\Notifications\PayoutApprovedNotification;
use App\Domains\Notifications\Notifications\PayoutRejectedNotification;
use App\Domains\System\Models\Setting;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Payouts extends Page
{
    protected string $view = 'filament.pages.payouts';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';
    protected static ?string $navigationLabel = 'Payouts';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'payouts';

    public static function canAccess(): bool
    {
        return PanelAccess::can('payouts.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = PayoutRequest::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
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

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function approve(int $id, ?string $reference = null): void
    {
        if (!PanelAccess::can('payouts.update')) {
            return;
        }

        $reference = trim((string) $reference);

        $payout = DB::transaction(function () use ($id, $reference) {
            $payout = PayoutRequest::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($payout->status !== 'pending') {
                return null;
            }

            $payout->update([
                'status' => 'approved',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'transaction_reference' => $reference !== '' ? $reference : null,
            ]);

            WalletTransaction::where('payout_request_id', $payout->id)
                ->where('type', 'debit')
                ->update(['status' => 'completed']);

            return $payout;
        });

        if (!$payout) {
            return;
        }

        ActivityLogService::logChange('payout.approved', $payout);

        $receipt = app(PayoutReceiptService::class)->issue($payout->fresh());

        if (Setting::get('payout_notification', true)) {
            $payout->instructor?->notify(new PayoutApprovedNotification(
                $payout->id,
                (float) $payout->amount,
                $payout->currency,
                $receipt->id
            ));

            app(PayoutReceiptService::class)->sendByEmail($receipt);
        }

        Notification::make()
            ->title('Payout Approved')
            ->body('The payout request has been marked as approved and a receipt has been issued.')
            ->success()
            ->send();
    }

    public function reject(int $id, string $reason): void
    {
        if (!PanelAccess::can('payouts.update')) {
            return;
        }

        $reason = trim($reason);
        if ($reason === '') {
            Notification::make()->title('Rejection reason is required.')->danger()->send();
            return;
        }

        $payout = DB::transaction(function () use ($id, $reason) {
            $payout = PayoutRequest::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($payout->status !== 'pending') {
                return null;
            }

            $payout->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'rejection_reason' => $reason,
            ]);

            WalletTransaction::where('payout_request_id', $payout->id)
                ->where('type', 'debit')
                ->update(['status' => 'cancelled']);

            $wallet = InstructorWallet::where('instructor_id', $payout->instructor_id)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->increment('balance', $payout->amount);
            }

            WalletTransaction::create([
                'instructor_id' => $payout->instructor_id,
                'amount' => $payout->amount,
                'type' => 'credit',
                'status' => 'completed',
                'payout_request_id' => $payout->id,
                'description' => 'Payout request rejected — funds returned to wallet',
            ]);

            return $payout;
        });

        if (!$payout) {
            return;
        }

        ActivityLogService::logChange('payout.rejected', $payout, [], ['reason' => $reason]);

        if (Setting::get('payout_notification', true)) {
            $payout->instructor?->notify(new PayoutRejectedNotification($payout->id, $reason));
        }

        Notification::make()
            ->title('Payout Rejected')
            ->body('Funds have been returned to the instructor\'s wallet.')
            ->danger()
            ->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $base = fn () => PayoutRequest::query();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                              'color' => '#2563eb'],
            ['key' => 'pending',  'label' => 'Pending',  'count' => $base()->where('status', 'pending')->count(),  'color' => '#fbbf24'],
            ['key' => 'approved', 'label' => 'Approved', 'count' => $base()->where('status', 'approved')->count(), 'color' => '#34d399'],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $base()->where('status', 'rejected')->count(), 'color' => '#f87171'],
        ];

        $query = PayoutRequest::with(['instructor:id,name,email', 'payoutAccount', 'receipt:id,payout_request_id,receipt_number']);

        if ($tab !== 'all' && in_array($tab, ['pending', 'approved', 'rejected'])) {
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
        $payouts    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $canUpdate = PanelAccess::can('payouts.update');
        $canDownload = PanelAccess::can('payouts.download');

        return compact('tabs', 'tab', 'search', 'payouts', 'total', 'totalPages', 'curPage', 'perPage', 'canUpdate', 'canDownload');
    }
}
