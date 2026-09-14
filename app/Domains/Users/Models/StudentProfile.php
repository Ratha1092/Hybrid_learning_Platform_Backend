<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'learning_goals',
        'interests',
        'github',
        'linkedin',
        'deleted_by',
    ];

    protected $casts = [
        'interests' => 'array',
    ];

    protected static function booted(): void
    {
        static::deleting(function (StudentProfile $profile) {
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