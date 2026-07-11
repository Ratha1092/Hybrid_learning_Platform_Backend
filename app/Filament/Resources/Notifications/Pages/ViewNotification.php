<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;
    protected string $view = 'filament.resources.notifications.view-notification';

    public function mount(int|string $record): void
    {
        parent::mount($record);
        if (is_null($this->record->read_at)) {
            $this->record->markAsRead();
        }
    }

    protected function getViewData(): array
    {
        $n       = $this->record;
        $data    = $n->data ?? [];
        $actions = $data['actions'] ?? [];
        $type    = $data['type'] ?? '';

        if (empty($actions) && filled($data['link'] ?? null)) {
            $actions = [[
                'label' => $data['action_text'] ?? 'Open',
                'url'   => $data['link'],
            ]];
        }

        $typeConfig = match ($type) {
            'order', 'enrollment_confirmed' => ['label' => 'Order',          'color' => '#22c55e', 'bg' => 'rgba(34,197,94,.12)'],
            'instructor_verification'       => ['label' => 'Instructor Verification', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.12)'],
            'course', 'course_submitted'    => ['label' => 'Course',         'color' => '#6366f1', 'bg' => 'rgba(99,102,241,.12)'],
            'payment'                       => ['label' => 'Payment',        'color' => '#10b981', 'bg' => 'rgba(16,185,129,.12)'],
            'finance'                       => ['label' => 'Finance',        'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.12)'],
            'system'                        => ['label' => 'System',         'color' => '#64748b', 'bg' => 'rgba(100,116,139,.12)'],
            default                         => ['label' => ucfirst(str_replace('_', ' ', $type ?: 'Notification')), 'color' => '#9333ea', 'bg' => 'rgba(147,51,234,.12)'],
        };

        $backUrl = route('filament.admin.pages.notifications');
        return compact('n', 'data', 'actions', 'type', 'typeConfig', 'backUrl');
    }
}
