<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessable_type',
        'assessable_id',
        'assessment_type',
        'score',
        'max_score',
        'grade',
        'is_passed',
        'feedback',
        'detailed_breakdown',
        'learning_analytics',
        'assessed_at',
        'graded_by',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_passed' => 'boolean',
        'detailed_breakdown' => 'array',
        'learning_analytics' => 'array',
        'assessed_at' => 'datetime',
    ];

    /**
     * Get the user who received this assessment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assessable model (quiz, lesson, etc.)
     */
    public function assessable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who graded this assessment
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Calculate letter grade based on score
     */
    public function calculateLetterGrade(): string
    {
        $percentage = $this->max_score > 0 ? ($this->score / $this->max_score) * 100 : 0;

        return match (true) {
            $percentage >= 97 => 'A+',
            $percentage >= 93 => 'A',
            $percentage >= 90 => 'A-',
            $percentage >= 87 => 'B+',
            $percentage >= 83 => 'B',
            $percentage >= 80 => 'B-',
            $percentage >= 77 => 'C+',
            $percentage >= 73 => 'C',
            $percentage >= 70 => 'C-',
            $percentage >= 67 => 'D+',
            $percentage >= 60 => 'D',
            default => 'F',
        };
    }

    /**
     * Get percentage score
     */
    public function getPercentageAttribute(): float
    {
        return $this->max_score > 0 ? ($this->score / $this->max_score) * 100 : 0;
    }

    /**
     * Scope for passed assessments
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope for failed assessments
     */
    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    /**
     * Scope by assessment type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Scope for user results
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}