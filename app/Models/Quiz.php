<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'course_id',
        'title',
        'description',
        'quiz_type',
        'settings',
        'max_attempts',
        'time_limit_minutes',
        'passing_score',
        'randomize_questions',
        'show_correct_answers',
        'is_required',
        'is_active',
        'order',
    ];

    protected $casts = [
        'settings' => 'array',
        'max_attempts' => 'integer',
        'time_limit_minutes' => 'integer',
        'passing_score' => 'decimal:2',
        'randomize_questions' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the lesson this quiz belongs to
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the course this quiz belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the questions for this quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    /**
     * Get all attempts for this quiz
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get content locks for this quiz
     */
    public function contentLocks(): MorphMany
    {
        return $this->morphMany(ContentLock::class, 'lockable');
    }

    /**
     * Get content rules targeting this quiz
     */
    public function contentRules(): MorphMany
    {
        return $this->morphMany(ContentRule::class, 'target_content');
    }

    /**
     * Get assessment results for this quiz
     */
    public function assessmentResults(): MorphMany
    {
        return $this->morphMany(AssessmentResult::class, 'assessable');
    }

    /**
     * Check if user can take this quiz
     */
    public function canUserTake(User $user): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if content is locked
        if ($this->isLockedFor($user)) {
            return false;
        }

        // Check max attempts
        $userAttempts = $this->attempts()
            ->where('user_id', $user->id)
            ->count();

        if ($this->max_attempts > 0 && $userAttempts >= $this->max_attempts) {
            return false;
        }

        // Check if user has access to lesson/course
        if ($this->lesson_id && !$this->lesson->hasAccess($user)) {
            return false;
        }

        if ($this->course_id && !$this->course->hasAccess($user)) {
            return false;
        }

        return true;
    }

    /**
     * Check if quiz is locked for user
     */
    public function isLockedFor(User $user): bool
    {
        return $this->contentLocks()
            ->active()
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get()
            ->some(fn($lock) => $lock->isLockedFor($user));
    }

    /**
     * Get user's best attempt
     */
    public function getBestAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('score', 'desc')
            ->first();
    }

    /**
     * Get user's latest attempt
     */
    public function getLatestAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->latest()
            ->first();
    }

    /**
     * Get user's active attempt (in progress)
     */
    public function getActiveAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
    }

    /**
     * Start new attempt for user
     */
    public function startAttempt(User $user): QuizAttempt
    {
        $attemptNumber = $this->attempts()
            ->where('user_id', $user->id)
            ->count() + 1;

        return $this->attempts()->create([
            'user_id' => $user->id,
            'attempt_number' => $attemptNumber,
            'answers' => [],
            'status' => 'in_progress',
            'started_at' => now(),
            'points_possible' => $this->getTotalPoints(),
        ]);
    }

    /**
     * Get total possible points
     */
    public function getTotalPoints(): float
    {
        return $this->questions()->sum('points');
    }

    /**
     * Get randomized questions for user
     */
    public function getQuestionsForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $questions = $this->questions()->active()->get();

        if ($this->randomize_questions) {
            return $questions->shuffle();
        }

        return $questions;
    }

    /**
     * Calculate score for answers
     */
    public function calculateScore(array $answers): array
    {
        $questions = $this->questions()->active()->get();
        $totalPoints = 0;
        $earnedPoints = 0;
        $results = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $points = $question->calculatePoints($userAnswer);
            
            $earnedPoints += $points;
            $totalPoints += $question->points;

            $results[$question->id] = [
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answers,
                'points_earned' => $points,
                'points_possible' => $question->points,
                'is_correct' => $points > 0,
            ];
        }

        $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;

        return [
            'points_earned' => $earnedPoints,
            'points_possible' => $totalPoints,
            'percentage' => $percentage,
            'is_passed' => $percentage >= $this->passing_score,
            'question_results' => $results,
        ];
    }

    /**
     * Generate quiz statistics
     */
    public function getStatistics(): array
    {
        $completedAttempts = $this->attempts()
            ->where('status', 'completed')
            ->get();

        if ($completedAttempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'completion_rate' => 0,
                'average_score' => 0,
                'pass_rate' => 0,
                'average_time' => 0,
            ];
        }

        $totalAttempts = $this->attempts()->count();
        $completedCount = $completedAttempts->count();
        $passedCount = $completedAttempts->where('is_passed', true)->count();

        return [
            'total_attempts' => $totalAttempts,
            'completed_attempts' => $completedCount,
            'completion_rate' => $totalAttempts > 0 ? ($completedCount / $totalAttempts) * 100 : 0,
            'average_score' => $completedAttempts->avg('score') ?? 0,
            'pass_rate' => $completedCount > 0 ? ($passedCount / $completedCount) * 100 : 0,
            'average_time' => $completedAttempts->avg('time_spent_seconds') ?? 0,
            'highest_score' => $completedAttempts->max('score') ?? 0,
            'lowest_score' => $completedAttempts->min('score') ?? 0,
        ];
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(): array
    {
        $statistics = $this->getStatistics();
        
        // Question difficulty analysis
        $questionAnalytics = $this->questions()->get()->map(function ($question) {
            $correctAnswers = QuizAttempt::whereJsonContains('detailed_results', [
                $question->id => ['is_correct' => true]
            ])->count();
            
            $totalAnswers = QuizAttempt::whereJsonLength('detailed_results->' . $question->id, '>', 0)->count();
            
            return [
                'question_id' => $question->id,
                'question_text' => $question->question,
                'difficulty' => $totalAnswers > 0 ? (1 - ($correctAnswers / $totalAnswers)) : 0,
                'correct_rate' => $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0,
            ];
        });

        return array_merge($statistics, [
            'question_analytics' => $questionAnalytics,
            'difficulty_distribution' => $this->getDifficultyDistribution($questionAnalytics),
        ]);
    }

    /**
     * Get difficulty distribution
     */
    private function getDifficultyDistribution($questionAnalytics): array
    {
        $easy = $questionAnalytics->where('difficulty', '<=', 0.3)->count();
        $medium = $questionAnalytics->where('difficulty', '>', 0.3)->where('difficulty', '<=', 0.7)->count();
        $hard = $questionAnalytics->where('difficulty', '>', 0.7)->count();

        return [
            'easy' => $easy,
            'medium' => $medium,
            'hard' => $hard,
        ];
    }

    /**
     * Export quiz data
     */
    public function export(): array
    {
        return [
            'quiz' => $this->toArray(),
            'questions' => $this->questions()->with('quiz')->get()->toArray(),
            'statistics' => $this->getStatistics(),
            'analytics' => $this->getAnalytics(),
        ];
    }

    /**
     * Duplicate quiz
     */
    public function duplicate(string $newTitle = null): self
    {
        $newQuiz = $this->replicate();
        $newQuiz->title = $newTitle ?? ($this->title . ' (Copy)');
        $newQuiz->save();

        // Duplicate questions
        foreach ($this->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->quiz_id = $newQuiz->id;
            $newQuestion->save();
        }

        return $newQuiz;
    }

    /**
     * Scope for active quizzes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for required quizzes
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for lesson quizzes
     */
    public function scopeForLesson($query, int $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    /**
     * Scope for course quizzes
     */
    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope by quiz type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('quiz_type', $type);
    }
}