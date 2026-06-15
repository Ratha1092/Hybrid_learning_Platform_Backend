<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];
    protected $appends = ['image_url'];
    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $base = Str::slug($category->name);
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $category->slug = $slug;
            }
        });
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where(
            'is_featured',
            true
        );
    }
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return \Storage::disk(config('filament.default_filesystem_disk'))->url($this->image);
    }
}