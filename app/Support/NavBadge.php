<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Turns a sidebar count badge into a "new since you last viewed this page" indicator,
 * rather than a static total. Call markSeen() from the page's mount(), and countSince()
 * from getNavigationBadge().
 */
class NavBadge
{
    public static function markSeen(string $key): void
    {
        Cache::put(self::cacheKey($key), now(), now()->addYear());
    }

    public static function countSince(string $key, Closure $countQuery): int
    {
        return $countQuery(Cache::get(self::cacheKey($key)));
    }

    private static function cacheKey(string $key): string
    {
        return 'nav-seen:' . $key . ':' . (auth()->id() ?? 'guest');
    }
}
