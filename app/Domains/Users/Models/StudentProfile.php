<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'learning_goals',
        'interests',
        'github',
        'linkedin'
    ];

    protected $casts = [
        'interests' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}