<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'website',
        'twitter',
        'linkedin',
        'youtube'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
