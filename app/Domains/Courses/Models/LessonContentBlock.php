<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonContentBlock extends Model
{
    public const TYPE_VIDEO = 'video';
    public const TYPE_TEXT = 'text';
    public const TYPE_IMAGE = 'image';
    public const TYPE_CODE = 'code';
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_EXTERNAL = 'external';

    protected $fillable = [
        'lesson_id', 'type', 'title', 'content', 'media_path',
        'media_url', 'language', 'metadata', 'order',
    ];

    protected $casts = ['metadata' => 'array', 'order' => 'integer'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
