<?php

namespace App\Filament\Pages;

use App\Domains\System\Models\BlockedIp;
use App\Support\PanelAccess;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BlockedIps extends Page
{
    protected string  $view              = 'filament.pages.blocked-ips';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-no-symbol';
    protected static ?string $navigationLabel                 = 'Blocked IPs';
    protected static string|\UnitEnum|null $navigationGroup   = 'Security';
    protected static ?int    $navigationSort                  = 4;
    protected static ?string $slug                            = 'blocked-ips';

    public string $newIp     = '';
    public string $newReason = '';
    public string $newExpiry = '';

    public static function canAccess(): bool
    {
        return PanelAccess::can('system.view_security');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function blockIp(): void
    {
        if (! PanelAccess::can('system.view_security')) {
            return;
        }

        $ip = trim($this->newIp);

        if ($ip === '' || ! filter_var($ip, FILTER_VALIDATE_IP)) {
            Notification::make()->title('Invalid IP address.')->danger()->send();
            return;
        }

        BlockedIp::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason'     => trim($this->newReason) ?: null,
                'blocked_by' => auth()->id(),
                'expires_at' => $this->newExpiry !== '' ? $this->newExpiry : null,
            ]
        );

        $this->newIp     = '';
        $this->newReason = '';
        $this->newExpiry = '';

        Notification::make()->title("IP {$ip} blocked.")->success()->send();
    }

    public function unblock(int $id): void
    {
        BlockedIp::findOrFail($id)->delete();
        Notification::make()->title('IP unblocked.')->success()->send();
    }

    protected function getViewData(): array
    {
        $search  = trim(request('search', ''));
        $page    = max(1, (int) request('page', 1));
        $perPage = 30;

        $query = BlockedIp::with('blocker')->latest();

        if ($search !== '') {
            $query->where('ip_address', 'ilike', "%{$search}%")
                  ->orWhere('reason', 'ilike', "%{$search}%");
        }

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $blocked    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('blocked', 'total', 'totalPages', 'curPage', 'perPage', 'search');
    }
}
