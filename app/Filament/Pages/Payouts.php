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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class Payouts extends Page
{
    protected string $view = 'filament.pages.payouts';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-arrow-up-on-square-stack';

    protected static ?string $navigationLabel = 'Payouts';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'payouts';

    /*
    | Authorization
    */

    public static function canAccess(): bool
    {
        return PanelAccess::can('payouts.view');
    }

    /*
    | Navigation badge
    */

    public static function getNavigationBadge(): ?string
    {
        $pending = PayoutRequest::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    /*
    | List state
    */

    public string $tab = 'all';

    public string $search = '';

    public int $page = 1;

    public int $perPage = 10;

    /*
    | Modal state
    */

    public ?int $selectedPayoutId = null;

    /**
     * null
     * details
     * approve
     * reject
     */
    public ?string $modal = null;

    public string $approveReference = '';

    public string $rejectReason = '';

    /*
    | Lifecycle
    */

    public function mount(): void
    {
        $this->tab = in_array(
            request('tab'),
            ['pending', 'approved', 'rejected'],
            true
        )
            ? request('tab')
            : 'all';
    }

    /*
    | Search / pagination
    */

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
        if (!in_array($tab, ['all', 'pending', 'approved', 'rejected'], true)) {
            return;
        }

        $this->tab = $tab;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    /*
    | Page heading
    */

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    /*
    | Open payout details
    */

    public function openDetails(int $id): void
    {
        $payout = PayoutRequest::find($id);

        if (!$payout) {
            Notification::make()
                ->title('Payout not found')
                ->danger()
                ->send();

            return;
        }

        $this->selectedPayoutId = $id;
        $this->approveReference = '';
        $this->rejectReason = '';
        $this->modal = 'details';
    }

    /*
    | Close modal
    */

    public function closeModal(): void
    {
        $this->modal = null;
        $this->selectedPayoutId = null;
        $this->approveReference = '';
        $this->rejectReason = '';
    }

    /*
    | Open approve modal
    */

    public function openApprove(int $id): void
    {
        if (!PanelAccess::can('payouts.update')) {
            return;
        }

        $payout = PayoutRequest::find($id);

        if (!$payout) {
            Notification::make()
                ->title('Payout not found')
                ->danger()
                ->send();

            return;
        }

        if ($payout->status !== 'pending') {
            Notification::make()
                ->title('Payout is no longer pending')
                ->warning()
                ->send();

            return;
        }

        $this->selectedPayoutId = $id;
        $this->approveReference = '';
        $this->modal = 'approve';
    }

    /*
    | Open reject modal
    */

    public function openReject(int $id): void
    {
        if (!PanelAccess::can('payouts.update')) {
            return;
        }

        $payout = PayoutRequest::find($id);

        if (!$payout) {
            Notification::make()
                ->title('Payout not found')
                ->danger()
                ->send();

            return;
        }

        if ($payout->status !== 'pending') {
            Notification::make()
                ->title('Payout is no longer pending')
                ->warning()
                ->send();

            return;
        }

        $this->selectedPayoutId = $id;
        $this->rejectReason = '';
        $this->modal = 'reject';
    }

    /*
    | Approve / complete payout
    |
    | IMPORTANT:
    |
    | The actual bank/KHQR transfer happens outside this application.
    |
    | The admin:
    |
    | 1. Opens payout
    | 2. Checks QR/account
    | 3. Sends money
    | 4. Enters transaction reference
    | 5. Confirms here
    |
    */

    public function approve(): void
    {
        if (!PanelAccess::can('payouts.update')) {
            return;
        }

        if (!$this->selectedPayoutId) {
            return;
        }

        $reference = trim($this->approveReference);

        /*

        | Require payment reference

        |
        | For a financial platform, do not allow an admin to mark a payout
        | as completed without some external payment reference.
        |
        */

        if ($reference === '') {
            Notification::make()
                ->title('Transaction reference is required')
                ->body('Enter the KHQR transaction ID or bank transfer reference before confirming the payout.')
                ->danger()
                ->send();

            return;
        }

        if (mb_strlen($reference) > 150) {
            Notification::make()
                ->title('Transaction reference is too long')
                ->body('The transaction reference cannot exceed 150 characters.')
                ->danger()
                ->send();

            return;
        }

        $payout = DB::transaction(function () use ($reference) {

            $payout = PayoutRequest::query()
                ->where('id', $this->selectedPayoutId)
                ->lockForUpdate()
                ->first();

            if (!$payout) {
                return null;
            }

            /*
    
            | Prevent double payment
    
            */

            if ($payout->status !== 'pending') {
                return null;
            }

            /*
    
            | Mark payout completed
    
            */

            $payout->update([
                'status' => 'approved',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'transaction_reference' => $reference,
            ]);

            /*
            | Complete the wallet debit transaction
            */

            WalletTransaction::query()
                ->where('payout_request_id', $payout->id)
                ->where('type', 'debit')
                ->update([
                    'status' => 'completed',
                ]);

            return $payout->fresh();
        });

        /*
        | Race condition / already processed
        */

        if (!$payout) {
            $this->closeModal();

            Notification::make()
                ->title('Payout was already processed')
                ->warning()
                ->send();

            return;
        }

        /*
        | Audit log
        */

        ActivityLogService::logChange(
            'payout.approved',
            $payout,
            [],
            [
                'transaction_reference' => $reference,
            ]
        );

        /*
        | Generate receipt
        */
        $receipt = app(PayoutReceiptService::class)
            ->issue($payout);

        /*
        | Notify instructor
        */
        if (Setting::get('payout_notification', true)) {

            $payout->instructor?->notify(
                new PayoutApprovedNotification(
                    $payout->id,
                    (float) $payout->amount,
                    $payout->currency,
                    $receipt->id
                )
            );

            app(PayoutReceiptService::class)
                ->sendByEmail($receipt);
        }

        $this->closeModal();

        Notification::make()
            ->title('Payout completed')
            ->body(
                'The payout was recorded successfully and the instructor receipt has been issued.'
            )
            ->success()
            ->send();
    }

    /*
    | Reject payout
    */
    public function reject(): void
    {
        if (!PanelAccess::can('payouts.update')) {
            return;
        }

        if (!$this->selectedPayoutId) {
            return;
        }

        $reason = trim($this->rejectReason);

        if ($reason === '') {
            Notification::make()
                ->title('Rejection reason is required')
                ->body('Explain why the payout request is being rejected.')
                ->danger()
                ->send();

            return;
        }

        if (mb_strlen($reason) > 1000) {
            Notification::make()
                ->title('Rejection reason is too long')
                ->body('The rejection reason cannot exceed 1000 characters.')
                ->danger()
                ->send();

            return;
        }

        $payout = DB::transaction(function () use ($reason) {

            $payout = PayoutRequest::query()
                ->where('id', $this->selectedPayoutId)
                ->lockForUpdate()
                ->first();

            if (!$payout) {
                return null;
            }

            /*
            | Prevent double processing
            */

            if ($payout->status !== 'pending') {
                return null;
            }

            /*
            | Reject payout
            */
            $payout->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'rejection_reason' => $reason,
            ]);

            /*
            | Cancel original wallet debit
            */

            WalletTransaction::query()
                ->where('payout_request_id', $payout->id)
                ->where('type', 'debit')
                ->update([
                    'status' => 'cancelled',
                ]);

            /*
            | Return money to instructor wallet
            */

            $wallet = InstructorWallet::query()
                ->where('instructor_id', $payout->instructor_id)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->increment(
                    'balance',
                    $payout->amount
                );
            }

            /*
            | Create wallet credit transaction
            */

            WalletTransaction::create([
                'instructor_id' => $payout->instructor_id,
                'amount' => $payout->amount,
                'type' => 'credit',
                'status' => 'completed',
                'payout_request_id' => $payout->id,
                'description' =>
                    'Payout request rejected — funds returned to wallet',
            ]);

            return $payout->fresh();
        });

        /*
        | Race condition / already processed
        */

        if (!$payout) {

            $this->closeModal();

            Notification::make()
                ->title('Payout was already processed')
                ->warning()
                ->send();

            return;
        }

        /*
        | Audit log
        */

        ActivityLogService::logChange(
            'payout.rejected',
            $payout,
            [],
            [
                'reason' => $reason,
            ]
        );

        /*
        | Notify instructor
        */

        if (Setting::get('payout_notification', true)) {
            $payout->instructor?->notify(
                new PayoutRejectedNotification(
                    $payout->id,
                    $reason
                )
            );
        }

        $this->closeModal();

        Notification::make()
            ->title('Payout rejected')
            ->body('The payout amount has been returned to the instructor wallet.')
            ->danger()
            ->send();
    }

    /*
    | View data
    */

    protected function getViewData(): array
    {
        $tab = $this->tab;

        $search = trim($this->search);

        $page = max(1, $this->page);

        $perPage = in_array(
            $this->perPage,
            [10, 25, 50],
            true
        )
            ? $this->perPage
            : 10;

        /*
        | Tabs
        */
        $base = fn () => PayoutRequest::query();
        $tabs = [
            [
                'key' => 'all',
                'label' => 'All',
                'count' => $base()->count(),
                'color' => '#2563eb',
            ],
            [
                'key' => 'pending',
                'label' => 'Pending',
                'count' => $base()
                    ->where('status', 'pending')
                    ->count(),
                'color' => '#f59e0b',
            ],
            [
                'key' => 'approved',
                'label' => 'Completed',
                'count' => $base()
                    ->where('status', 'approved')
                    ->count(),
                'color' => '#10b981',
            ],
            [
                'key' => 'rejected',
                'label' => 'Rejected',
                'count' => $base()
                    ->where('status', 'rejected')
                    ->count(),
                'color' => '#ef4444',
            ],
        ];

        /*
        | Main query
        */

        $query = PayoutRequest::query()
            ->with([
                'instructor:id,name,email',
                'payoutAccount',
                'receipt:id,payout_request_id,receipt_number',
            ]);

        /*
        | Filter
        */

        if (
            $tab !== 'all'
            && in_array(
                $tab,
                ['pending', 'approved', 'rejected'],
                true
            )
        ) {
            $query->where('status', $tab);
        }

        /*
        | Search
        */

        if ($search !== '') {
            $query->whereHas(
                'instructor',
                function ($q) use ($search) {

                    $q->where(
                        'name',
                        'ilike',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'ilike',
                        "%{$search}%"
                    );
                }
            );
        }

        $query->orderByDesc('id');

        /*
        | Pagination
        */

        $total = $query->count();

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        $curPage = min(
            $page,
            $totalPages
        );

        $payouts = $query
            ->skip(
                ($curPage - 1) * $perPage
            )
            ->take($perPage)
            ->get();

        /*
        | Selected payout
        */

        $selectedPayout = null;

        if ($this->selectedPayoutId) {

            $selectedPayout = PayoutRequest::query()
                ->with([
                    'instructor:id,name,email',
                    'payoutAccount',
                    'receipt:id,payout_request_id,receipt_number',
                ])
                ->find(
                    $this->selectedPayoutId
                );
        }

        /*
        | Permissions
        */

        $canUpdate = PanelAccess::can(
            'payouts.update'
        );

        $canDownload = PanelAccess::can(
            'payouts.download'
        );

        return compact(
            'tabs',
            'tab',
            'search',
            'payouts',
            'total',
            'totalPages',
            'curPage',
            'perPage',
            'selectedPayout',
            'canUpdate',
            'canDownload'
        );
    }
}