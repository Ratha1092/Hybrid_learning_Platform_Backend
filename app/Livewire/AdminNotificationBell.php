<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class AdminNotificationBell extends Component
{
    public int $unreadCount = 0;

    public function boot(): void
    {
        $this->unreadCount = (int) (auth()->user()?->unreadNotifications()->count() ?? 0);
    }

    #[Computed]
    public function notifications()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->take(12)
            ->get();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => $this->notifications,
            'unreadCount'   => $this->unreadCount,
        ]);
    }
}
