<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'website',
        'twitter',
        'linkedin',
        'youtube',
        'deleted_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (InstructorProfile $profile) {
            if (auth()->check()) {
                $profile->deleted_by = auth()->id();
                $profile->saveQuietly();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
