<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'integer',
    ];

    /**
     * Get the user that owns the enrollment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the enrollment
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the course is completed
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Mark the enrollment as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);
    }

    /**
     * Update progress percentage
     */
    public function updateProgress(int $percentage): void
    {
        $this->update(['progress_percentage' => $percentage]);
        
        if ($percentage >= 100) {
            $this->markAsCompleted();
        }
    }

    /**
     * Scope for completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope for active enrollments
     */
    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }
}
