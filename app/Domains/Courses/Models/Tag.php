<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'deleted_by',
    ];

    protected static function booted(): void
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $base = Str::slug($tag->name);
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $tag->slug = $slug;
            }
        });
        static::deleting(function (Tag $tag) {
            if (auth()->check()) {
                $tag->deleted_by = auth()->id();
                $tag->saveQuietly();
            }
        });
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}