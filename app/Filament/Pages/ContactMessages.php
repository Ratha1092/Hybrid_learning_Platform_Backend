<?php

namespace App\Filament\Pages;

use App\Domains\System\Models\ContactMessage;
use App\Jobs\Mail\SendContactMessageReplyEmailJob;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ContactMessages extends Page
{
    protected string $view = 'filament.pages.contact-messages';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Contact Messages';
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'contact-messages';

    public static function canAccess(): bool
    {
        return PanelAccess::can('contact_messages.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::where('status', 'unread')->count();

        return $unread > 0 ? (string) $unread : null;
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

    public function markRead(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        if ($message->status !== 'unread') return;

        $message->update([
            'status'  => 'read',
            'read_at' => now(),
        ]);
    }

    public function reply(int $id, string $replyMessage): void
    {
        if (! PanelAccess::can('contact_messages.update')) {
            return;
        }

        $replyMessage = trim($replyMessage);
        if ($replyMessage === '') {
            return;
        }

        $message = ContactMessage::findOrFail($id);

        $message->update([
            'status'        => 'replied',
            'reply_message' => $replyMessage,
            'replied_by'    => auth()->id(),
            'replied_at'    => now(),
            'read_at'       => $message->read_at ?? now(),
        ]);

        SendContactMessageReplyEmailJob::dispatch($message->id);

        Notification::make()->title('Reply sent to ' . $message->email)->success()->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $base = fn () => ContactMessage::query();

        $tabs = [
            ['key' => 'all',     'label' => 'All',     'count' => $base()->count(),                             'color' => '#2563eb'],
            ['key' => 'unread',  'label' => 'Unread',  'count' => $base()->where('status', 'unread')->count(),  'color' => '#fbbf24'],
            ['key' => 'read',    'label' => 'Read',    'count' => $base()->where('status', 'read')->count(),    'color' => '#38bdf8'],
            ['key' => 'replied', 'label' => 'Replied', 'count' => $base()->where('status', 'replied')->count(), 'color' => '#34d399'],
        ];

        $query = ContactMessage::query()->with('replier:id,name');

        if ($tab !== 'all' && in_array($tab, ['unread', 'read', 'replied'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');
        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $messages   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'messages', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
