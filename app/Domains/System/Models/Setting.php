<?php

namespace App\Domains\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'description', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }

        $setting = static::allCached()->get($key);

        if (!$setting) {
            return $default;
        }

        return self::cast($setting->value, $setting->type);
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?string $type = null, ?string $description = null, ?bool $isPublic = null): void
    {
        $attributes = ['value' => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value, 'group' => $group];

        if ($type !== null) {
            $attributes['type'] = $type;
        }
        if ($description !== null) {
            $attributes['description'] = $description;
        }
        if ($isPublic !== null) {
            $attributes['is_public'] = $isPublic;
        }

        static::updateOrCreate(['key' => $key], $attributes);
        Cache::forget('settings.all');
        Cache::forget('settings.public');
    }

    public static function byGroup(string $group)
    {
        return static::where('group', $group)->orderBy('key')->get();
    }

    /**
     * All settings keyed by `key`, cached until the next write via set().
     */
    private static function allCached(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever('settings.all', fn () => static::all()->keyBy('key'));
    }

    /**
     * Flat key => casted value map of every public setting, for unauthenticated frontend consumption.
     */
    public static function allPublic(): array
    {
        return Cache::rememberForever('settings.public', fn () => static::allCached()
            ->filter(fn (self $setting) => $setting->is_public)
            ->mapWithKeys(fn (self $setting) => [$setting->key => self::cast($setting->value, $setting->type)])
            ->all());
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    private static function cast(?string $value, ?string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'json' => json_decode((string) $value, true),
            default => $value,
        };
    }
}
