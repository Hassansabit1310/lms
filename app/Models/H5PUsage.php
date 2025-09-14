<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class H5PUsage extends Model
{
    use HasFactory;

    protected $table = 'h5_p_usages';

    protected $fillable = [
        'h5p_content_id',
        'lesson_content_id',
        'course_id',
    ];

    /**
     * Get the H5P content
     */
    public function h5pContent(): BelongsTo
    {
        return $this->belongsTo(H5PContent::class, 'h5p_content_id');
    }

    /**
     * Get the lesson content
     */
    public function lessonContent(): BelongsTo
    {
        return $this->belongsTo(LessonContent::class);
    }

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
