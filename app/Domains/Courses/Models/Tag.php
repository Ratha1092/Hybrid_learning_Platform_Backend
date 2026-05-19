<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}