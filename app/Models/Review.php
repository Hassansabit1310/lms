<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
        'rating' => 'integer',
    ];

    /**
     * Get the user that owns the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the review
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get star rating display
     */
    public function getStarRatingAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Scope for high ratings (4-5 stars)
     */
    public function scopeHighRating($query)
    {
        return $query->whereIn('rating', [4, 5]);
    }

    /**
     * Scope for low ratings (1-2 stars)
     */
    public function scopeLowRating($query)
    {
        return $query->whereIn('rating', [1, 2]);
    }

    /**
     * Scope for recent reviews
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for reviews with comments
     */
    public function scopeWithComments($query)
    {
        return $query->whereNotNull('comment')
                    ->where('comment', '!=', '');
    }
}
