<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'answers',
        'score',
        'points_earned',
        'points_possible',
        'is_passed',
        'status',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'detailed_results',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'points_earned' => 'decimal:2',
        'points_possible' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'detailed_results' => 'array',
    ];

    /**
     * Get the quiz for this attempt
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user who made this attempt
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Submit the quiz attempt
     */
    public function submit(): void
    {
        $results = $this->quiz->calculateScore($this->answers);
        
        $this->update([
            'score' => $results['percentage'],
            'points_earned' => $results['points_earned'],
            'points_possible' => $results['points_possible'],
            'is_passed' => $results['is_passed'],
            'status' => 'completed',
            'submitted_at' => now(),
            'time_spent_seconds' => now()->diffInSeconds($this->started_at),
            'detailed_results' => $results['question_results'],
        ]);

        // Create assessment result
        AssessmentResult::create([
            'user_id' => $this->user_id,
            'assessable_type' => Quiz::class,
            'assessable_id' => $this->quiz_id,
            'assessment_type' => 'quiz',
            'score' => $this->score,
            'max_score' => 100,
            'is_passed' => $this->is_passed,
            'detailed_breakdown' => $this->detailed_results,
            'learning_analytics' => [
                'time_spent' => $this->time_spent_seconds,
                'attempt_number' => $this->attempt_number,
            ],
            'assessed_at' => now(),
        ]);
    }

    /**
     * Check if attempt is expired
     */
    public function isExpired(): bool
    {
        if (!$this->quiz->time_limit_minutes) {
            return false;
        }

        $timeLimit = $this->started_at->addMinutes($this->quiz->time_limit_minutes);
        return now()->gt($timeLimit);
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTime(): int
    {
        if (!$this->quiz->time_limit_minutes) {
            return -1; // No time limit
        }

        $endTime = $this->started_at->addMinutes($this->quiz->time_limit_minutes);
        $remaining = now()->diffInSeconds($endTime, false);
        
        return max(0, $remaining);
    }

    /**
     * Update answer for a question
     */
    public function updateAnswer(int $questionId, $answer): void
    {
        $answers = $this->answers ?? [];
        $answers[$questionId] = $answer;
        
        $this->update(['answers' => $answers]);
    }

    /**
     * Get answer for a question
     */
    public function getAnswer(int $questionId)
    {
        return $this->answers[$questionId] ?? null;
    }

    /**
     * Scope for completed attempts
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for passed attempts
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope for user attempts
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}