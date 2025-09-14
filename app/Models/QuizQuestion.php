<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'question_type',
        'options',
        'correct_answers',
        'explanation',
        'points',
        'order',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'points' => 'decimal:2',
        'order' => 'integer',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the quiz this question belongs to
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Calculate points for user answer
     */
    public function calculatePoints($userAnswer): float
    {
        if ($userAnswer === null) {
            return 0;
        }

        return match ($this->question_type) {
            'multiple_choice' => $this->calculateMultipleChoicePoints($userAnswer),
            'true_false' => $this->calculateTrueFalsePoints($userAnswer),
            'short_answer' => $this->calculateShortAnswerPoints($userAnswer),
            'essay' => 0, // Requires manual grading
            'matching' => $this->calculateMatchingPoints($userAnswer),
            'fill_blank' => $this->calculateFillBlankPoints($userAnswer),
            'drag_drop' => $this->calculateDragDropPoints($userAnswer),
            default => 0,
        };
    }

    private function calculateMultipleChoicePoints($userAnswer): float
    {
        return in_array($userAnswer, $this->correct_answers) ? $this->points : 0;
    }

    private function calculateTrueFalsePoints($userAnswer): float
    {
        return $userAnswer === $this->correct_answers[0] ? $this->points : 0;
    }

    private function calculateShortAnswerPoints($userAnswer): float
    {
        foreach ($this->correct_answers as $correct) {
            if (strtolower(trim($userAnswer)) === strtolower(trim($correct))) {
                return $this->points;
            }
        }
        return 0;
    }

    private function calculateMatchingPoints($userAnswer): float
    {
        $correct = 0;
        $total = count($this->correct_answers);
        foreach ($userAnswer as $key => $value) {
            if (isset($this->correct_answers[$key]) && $this->correct_answers[$key] === $value) {
                $correct++;
            }
        }
        return $total > 0 ? ($correct / $total) * $this->points : 0;
    }

    private function calculateFillBlankPoints($userAnswer): float
    {
        $correct = 0;
        $total = count($this->correct_answers);
        foreach ($userAnswer as $index => $answer) {
            if (isset($this->correct_answers[$index]) && 
                strtolower(trim($answer)) === strtolower(trim($this->correct_answers[$index]))) {
                $correct++;
            }
        }
        return $total > 0 ? ($correct / $total) * $this->points : 0;
    }

    private function calculateDragDropPoints($userAnswer): float
    {
        return $userAnswer === $this->correct_answers ? $this->points : 0;
    }
}