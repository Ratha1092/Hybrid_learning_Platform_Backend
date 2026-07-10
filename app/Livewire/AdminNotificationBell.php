<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AdminNotificationBell extends Component
{
    public int $unreadCount = 0;
    public int $userId = 0;

    public function mount(): void
    {
        $this->userId = (int) auth()->id();
    }

    public function boot(): void
    {
        $this->unreadCount = (int) (auth()->user()?->unreadNotifications()->count() ?? 0);
    }

    #[On('echo-private:user.{userId},.notification.received')]
    public function onPusherNotification(array $notification): void
    {
        $this->unreadCount++;
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
