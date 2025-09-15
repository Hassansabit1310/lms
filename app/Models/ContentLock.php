<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'lockable_type',
        'lockable_id',
        'user_id',
        'lock_type',
        'unlock_condition',
        'unlock_criteria',
        'locked_at',
        'unlocks_at',
        'is_active',
        'reason',
    ];

    protected $casts = [
        'unlock_criteria' => 'array',
        'locked_at' => 'datetime',
        'unlocks_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the lockable model (lesson, quiz, etc.)
     */
    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user this lock applies to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if content is locked for a specific user
     */
    public function isLockedFor(User $user): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Admin bypass
        if ($user->hasRole('admin')) {
            return false;
        }

        // Check if lock applies to this user
        if ($this->user_id && $this->user_id !== $user->id) {
            return false;
        }

        // Check time-based unlock
        if ($this->unlocks_at && now()->gte($this->unlocks_at)) {
            return false;
        }

        // Check condition-based unlock
        return !$this->checkUnlockConditions($user);
    }

    /**
     * Check if content is unlocked for a specific user (opposite of isLockedFor)
     */
    public function isUnlockedFor(User $user): bool
    {
        return !$this->isLockedFor($user);
    }

    /**
     * Get human-readable description of the lock
     */
    public function getDescription(): string
    {
        if ($this->reason) {
            return $this->reason;
        }

        return match ($this->unlock_condition) {
            'manual' => 'Content locked until manually unlocked by instructor',
            'time_based' => $this->unlocks_at 
                ? "Content unlocks on {$this->unlocks_at->format('M j, Y g:i A')}"
                : 'Content locked until specified time',
            'task_completion' => 'Complete required tasks to unlock this content',
            'payment' => 'Purchase required to unlock this content',
            'subscription' => 'Active subscription required to unlock this content',
            default => 'Content is currently locked',
        };
    }

    /**
     * Check if unlock conditions are met
     */
    public function checkUnlockConditions(User $user): bool
    {
        return match ($this->unlock_condition) {
            'manual' => false, // Always locked until manually unlocked
            'task_completion' => $this->checkTaskCompletion($user),
            'time_based' => $this->checkTimeBased(),
            'payment' => $this->checkPayment($user),
            'subscription' => $this->checkSubscription($user),
            default => false,
        };
    }

    /**
     * Check task completion conditions
     */
    private function checkTaskCompletion(User $user): bool
    {
        $criteria = $this->unlock_criteria ?? [];
        
        foreach ($criteria['required_tasks'] ?? [] as $task) {
            switch ($task['type']) {
                case 'lesson_completion':
                    if (!$this->isLessonCompleted($user, $task['lesson_id'])) {
                        return false;
                    }
                    break;
                    
                case 'quiz_passed':
                    if (!$this->isQuizPassed($user, $task['quiz_id'], $task['min_score'] ?? 70)) {
                        return false;
                    }
                    break;
                    
                case 'course_progress':
                    if (!$this->isCourseProgressMet($user, $task['course_id'], $task['min_progress'] ?? 50)) {
                        return false;
                    }
                    break;
                    
                case 'assessment_passed':
                    if (!$this->isAssessmentPassed($user, $task['assessment_id'])) {
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }

    /**
     * Check time-based conditions
     */
    private function checkTimeBased(): bool
    {
        if ($this->unlocks_at) {
            return now()->gte($this->unlocks_at);
        }
        
        $criteria = $this->unlock_criteria ?? [];
        
        if (isset($criteria['unlock_after_hours'])) {
            $unlockTime = $this->created_at->addHours($criteria['unlock_after_hours']);
            return now()->gte($unlockTime);
        }
        
        if (isset($criteria['unlock_after_days'])) {
            $unlockTime = $this->created_at->addDays($criteria['unlock_after_days']);
            return now()->gte($unlockTime);
        }
        
        return false;
    }

    /**
     * Check payment conditions
     */
    private function checkPayment(User $user): bool
    {
        $criteria = $this->unlock_criteria ?? [];
        
        if (isset($criteria['required_course_purchase'])) {
            $courseId = $criteria['required_course_purchase'];
            return $user->enrollments()->where('course_id', $courseId)->exists();
        }
        
        return false;
    }

    /**
     * Check subscription conditions
     */
    private function checkSubscription(User $user): bool
    {
        $criteria = $this->unlock_criteria ?? [];
        
        if (isset($criteria['required_subscription_type'])) {
            $requiredType = $criteria['required_subscription_type'];
            return $user->subscriptions()
                ->where('type', $requiredType)
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->exists();
        }
        
        return $user->hasActiveSubscription();
    }

    /**
     * Helper methods for checking specific conditions
     */
    private function isLessonCompleted(User $user, int $lessonId): bool
    {
        return $user->progress()
            ->where('lesson_id', $lessonId)
            ->where('status', 'completed')
            ->exists();
    }

    private function isQuizPassed(User $user, int $quizId, float $minScore): bool
    {
        return QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->where('is_passed', true)
            ->where('score', '>=', $minScore)
            ->exists();
    }

    private function isCourseProgressMet(User $user, int $courseId, float $minProgress): bool
    {
        $enrollment = $user->enrollments()->where('course_id', $courseId)->first();
        return $enrollment && $enrollment->progress_percentage >= $minProgress;
    }

    private function isAssessmentPassed(User $user, int $assessmentId): bool
    {
        return AssessmentResult::where('user_id', $user->id)
            ->where('assessable_id', $assessmentId)
            ->where('is_passed', true)
            ->exists();
    }

    /**
     * Get lock status for display
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        return match ($this->lock_type) {
            'hidden' => 'hidden',
            'locked' => 'locked',
            'preview_only' => 'preview',
            default => 'locked',
        };
    }

    /**
     * Get unlock progress for user
     */
    public function getUnlockProgress(User $user): array
    {
        if ($this->unlock_condition === 'manual') {
            return [
                'type' => 'manual',
                'message' => 'Content will be unlocked manually by instructor',
                'progress' => 0
            ];
        }

        if ($this->unlock_condition === 'time_based') {
            $total = $this->unlocks_at ? $this->unlocks_at->diffInSeconds($this->created_at) : 0;
            $elapsed = now()->diffInSeconds($this->created_at);
            $progress = $total > 0 ? min(100, ($elapsed / $total) * 100) : 0;
            
            return [
                'type' => 'time_based',
                'message' => $this->unlocks_at ? "Unlocks at {$this->unlocks_at->format('M j, Y g:i A')}" : 'Time-based unlock',
                'progress' => $progress
            ];
        }

        if ($this->unlock_condition === 'task_completion') {
            return $this->getTaskCompletionProgress($user);
        }

        return [
            'type' => $this->unlock_condition,
            'message' => 'Check requirements to unlock',
            'progress' => 0
        ];
    }

    /**
     * Get task completion progress
     */
    private function getTaskCompletionProgress(User $user): array
    {
        $criteria = $this->unlock_criteria ?? [];
        $tasks = $criteria['required_tasks'] ?? [];
        $completed = 0;
        $total = count($tasks);

        foreach ($tasks as $task) {
            $isCompleted = match ($task['type']) {
                'lesson_completion' => $this->isLessonCompleted($user, $task['lesson_id']),
                'quiz_passed' => $this->isQuizPassed($user, $task['quiz_id'], $task['min_score'] ?? 70),
                'course_progress' => $this->isCourseProgressMet($user, $task['course_id'], $task['min_progress'] ?? 50),
                'assessment_passed' => $this->isAssessmentPassed($user, $task['assessment_id']),
                default => false,
            };

            if ($isCompleted) {
                $completed++;
            }
        }

        $progress = $total > 0 ? ($completed / $total) * 100 : 0;

        return [
            'type' => 'task_completion',
            'message' => "Complete {$completed}/{$total} required tasks",
            'progress' => $progress,
            'tasks' => $this->getTaskDetails($user, $tasks)
        ];
    }

    /**
     * Get detailed task information
     */
    private function getTaskDetails(User $user, array $tasks): array
    {
        $details = [];

        foreach ($tasks as $task) {
            $isCompleted = match ($task['type']) {
                'lesson_completion' => $this->isLessonCompleted($user, $task['lesson_id']),
                'quiz_passed' => $this->isQuizPassed($user, $task['quiz_id'], $task['min_score'] ?? 70),
                'course_progress' => $this->isCourseProgressMet($user, $task['course_id'], $task['min_progress'] ?? 50),
                'assessment_passed' => $this->isAssessmentPassed($user, $task['assessment_id']),
                default => false,
            };

            $details[] = [
                'type' => $task['type'],
                'description' => $this->getTaskDescription($task),
                'completed' => $isCompleted,
                'data' => $task
            ];
        }

        return $details;
    }

    /**
     * Get human-readable task description
     */
    private function getTaskDescription(array $task): string
    {
        return match ($task['type']) {
            'lesson_completion' => "Complete lesson: " . (Lesson::find($task['lesson_id'])->title ?? 'Unknown Lesson'),
            'quiz_passed' => "Pass quiz with {$task['min_score']}% or higher",
            'course_progress' => "Achieve {$task['min_progress']}% progress in course",
            'assessment_passed' => "Pass assessment",
            default => 'Complete required task',
        };
    }

    /**
     * Manually unlock content
     */
    public function unlock(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Scope for active locks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for locks by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('lock_type', $type);
    }

    /**
     * Scope for user-specific locks
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        });
    }
}