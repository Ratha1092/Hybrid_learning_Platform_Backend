<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class AdminNotificationBell extends Component
{
    #[Computed]
    public function notifications()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->take(12)
            ->get();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => $this->notifications,
            'unreadCount'   => $this->unreadCount,
        ]);
    }
}
