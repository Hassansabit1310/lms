<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'watch_time_seconds',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'lesson_id' => 'integer',
        'watch_time_seconds' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the progress
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson that owns the progress
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Mark as started
     */
    public function markAsStarted(): void
    {
        if ($this->status === 'not_started') {
            $this->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Update course progress
        $this->updateCourseProgress();
    }

    /**
     * Update watch time
     */
    public function updateWatchTime(int $seconds): void
    {
        $this->increment('watch_time_seconds', $seconds);
    }

    /**
     * Check if completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if not started
     */
    public function isNotStarted(): bool
    {
        return $this->status === 'not_started';
    }

    /**
     * Update course progress based on lesson completion
     */
    private function updateCourseProgress(): void
    {
        $course = $this->lesson->course;
        $enrollment = $course->enrollments()->where('user_id', $this->user_id)->first();
        
        if ($enrollment) {
            $totalLessons = $course->lessons()->count();
            $completedLessons = Progress::where('user_id', $this->user_id)
                ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                ->where('status', 'completed')
                ->count();
            
            $percentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
            $enrollment->updateProgress($percentage);
        }
    }

    /**
     * Scope for completed progress
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for in progress
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for not started
     */
    public function scopeNotStarted($query)
    {
        return $query->where('status', 'not_started');
    }
}
