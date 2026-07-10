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
        'youtube'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
