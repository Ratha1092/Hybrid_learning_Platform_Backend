<?php

namespace App\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class LocalAvatarProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = (string) Filament::getNameForDefaultAvatar($record);
        $initials = str($name)
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $segment): string => mb_substr($segment, 0, 1))
            ->join('');

        $initials = $initials !== '' ? mb_strtoupper($initials) : '?';
        $background = substr(md5($name ?: $initials), 0, 6);

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96"><rect width="96" height="96" rx="48" fill="#%s"/><text x="50%%" y="54%%" text-anchor="middle" dominant-baseline="middle" fill="#fff" font-family="Inter,Arial,sans-serif" font-size="34" font-weight="700">%s</text></svg>',
            $background,
            e($initials),
        );

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
