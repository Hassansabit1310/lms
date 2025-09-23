<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'price',
        'is_free',
        'category_id',
        'thumbnail',
        'slug',
        'status',
        'duration_minutes',
        'level',
        'learning_objectives',
        'prerequisites',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'category_id' => 'integer',
        'duration_minutes' => 'integer',
        'learning_objectives' => 'array',
        'prerequisites' => 'array',
    ];

    /**
     * Get the category that owns the course
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the lessons for the course
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get the enrollments for the course
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the enrolled users
     */
    public function enrolledUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')->withTimestamps();
    }

    /**
     * Get the reviews for the course
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the payments for the course
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the bundles that include this course
     */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'bundle_courses')
                    ->withPivot(['order', 'individual_price', 'is_primary'])
                    ->withTimestamps();
    }

    /**
     * Get active bundles that include this course
     */
    public function activeBundles(): BelongsToMany
    {
        return $this->bundles()->where('bundles.is_active', true);
    }

    /**
     * Get quizzes for this course
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get content locks for this course
     */
    public function contentLocks(): MorphMany
    {
        return $this->morphMany(ContentLock::class, 'lockable');
    }

    /**
     * Get content rules targeting this course
     */
    public function contentRules(): MorphMany
    {
        return $this->morphMany(ContentRule::class, 'target_content');
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the average rating for the course
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of lessons
     */
    public function getTotalLessonsAttribute(): int
    {
        return $this->lessons()->count();
    }

    /**
     * Get the free lessons for preview
     */
    public function getFreeLessonsAttribute()
    {
        return $this->lessons()->where('is_free', true);
    }

    /**
     * Scope for published courses
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for free courses
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for paid courses
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Check if user has access to this course
     */
    public function hasAccess($user): bool
    {
        if (!$user) {
            return $this->is_free;
        }

        // Admin has access to everything
        if ($user->hasRole('admin')) {
            return true;
        }

        // Free courses are accessible to everyone
        if ($this->is_free) {
            return true;
        }

        // Check if user has enrolled in this course (direct purchase)
        if ($this->enrollments()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Check if user has purchased a bundle containing this course
        $bundleAccess = $user->payments()
            ->whereIn('status', ['completed', 'success', 'approved'])
            ->whereNotNull('bundle_id')
            ->whereHas('bundle.courses', function ($query) {
                $query->where('courses.id', $this->id);
            })
            ->exists();

        if ($bundleAccess) {
            return true;
        }

        // Check if user has active subscription
        return $user->hasActiveSubscription();
    }
}
