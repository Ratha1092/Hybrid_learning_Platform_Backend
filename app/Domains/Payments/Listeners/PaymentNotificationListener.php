<?php

namespace App\Domains\Payments\Listeners;

use App\Domains\Notifications\Notifications\AdminPaymentNotification;
use App\Domains\Notifications\Notifications\EnrollmentConfirmedNotification;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentNotificationListener implements ShouldQueue
{
    public string $queue = 'default';
    public int $tries = 3;

    public function handle(PaymentSuccessEvent $event): void
    {
        $order = $event->order->load(['user', 'items.course']);
        $courseTitle = $order->items->first()?->course_title
            ?? $order->items->first()?->course?->title
            ?? 'a course';
        $adminNotification = new AdminPaymentNotification($order);
        foreach (User::role(['super-admin', 'admin'])->get() as $admin) {
            $admin->notify($adminNotification);
        }
        if ($order->user) {
            $order->user->notify(new EnrollmentConfirmedNotification($order, $courseTitle));
        }
    }
}
