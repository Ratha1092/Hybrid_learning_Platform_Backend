<?php

namespace App\Domains\Courses\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Courses\Models\Lesson;

class LessonResource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'file_path',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}