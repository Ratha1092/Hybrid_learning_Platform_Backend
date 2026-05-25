<?php

namespace App\Filament\Auth\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.custom-login';

    protected function getViewData(): array
    {
        return [
            'platformName' => config('app.name', 'Hybrid Learning'),
            'platformInitials' => $this->getPlatformInitials(),
            'platformTagline' => 'Admin Console',
            'loginStats' => $this->getLoginStats(),
        ];
    }

    private function getLoginStats(): array
    {
        try {
            return Cache::remember('admin_login_stats', now()->addMinutes(5), function (): array {
                return [
                    [
                        'value' => $this->formatCompactNumber(
                            User::where('role', 'student')->count()
                        ),
                        'label' => 'Students',
                    ],
                    [
                        'value' => $this->formatCompactNumber(Course::count()),
                        'label' => 'Courses',
                    ],
                    [
                        'value' => $this->formatCompactNumber(
                            User::where('role', 'instructor')
                                ->where('instructor_status', User::INSTRUCTOR_VERIFIED)
                                ->count()
                        ),
                        'label' => 'Instructors',
                    ],
                ];
            });
        } catch (Throwable) {
            return [
                ['value' => '--', 'label' => 'Students'],
                ['value' => '--', 'label' => 'Courses'],
                ['value' => '--', 'label' => 'Instructors'],
            ];
        }
    }

    private function getPlatformInitials(): string
    {
        $words = preg_split('/\s+/', trim((string) config('app.name', 'Hybrid Learning')));

        $initials = collect($words)
            ->filter()
            ->take(3)
            ->map(fn (string $word): string => strtoupper(substr($word, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'HL';
    }

    private function formatCompactNumber(int $value): string
    {
        if ($value >= 1000000) {
            return round($value / 1000000, 1) . 'M';
        }

        if ($value >= 1000) {
            return round($value / 1000, 1) . 'K';
        }

        return (string) $value;
    }
}
