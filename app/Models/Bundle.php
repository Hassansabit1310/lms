<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'long_description',
        'price',
        'original_price',
        'discount_percentage',
        'image',
        'is_active',
        'is_featured',
        'max_enrollments',
        'available_from',
        'available_until',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'max_enrollments' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Generate unique slug when creating
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }

    /**
     * Get the courses in this bundle
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'bundle_courses')
                    ->withPivot(['order', 'individual_price', 'is_primary'])
                    ->withTimestamps()
                    ->orderBy('bundle_courses.order');
    }

    /**
     * Get the payments for this bundle
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get active courses in bundle
     */
    public function activeCourses(): BelongsToMany
    {
        return $this->courses()->where('courses.status', 'published');
    }

    /**
     * Get the primary course (main course) in bundle
     */
    public function primaryCourse()
    {
        return $this->courses()->wherePivot('is_primary', true)->first();
    }

    /**
     * Calculate original price from all courses
     */
    public function calculateOriginalPrice(): float
    {
        return $this->courses()->sum('courses.price');
    }

    /**
     * Calculate discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->original_price) {
            return 0;
        }
        
        return $this->original_price - $this->price;
    }

    /**
     * Get savings percentage
     */
    public function getSavingsPercentageAttribute(): int
    {
        if (!$this->original_price || $this->original_price == 0) {
            return 0;
        }
        
        return round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    /**
     * Check if bundle is available for purchase
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->available_from && $now < $this->available_from) {
            return false;
        }

        if ($this->available_until && $now > $this->available_until) {
            return false;
        }

        return true;
    }

    /**
     * Check if bundle has enrollment limit
     */
    public function hasEnrollmentLimit(): bool
    {
        return !is_null($this->max_enrollments);
    }

    /**
     * Get current enrollment count
     */
    public function getCurrentEnrollments(): int
    {
        return $this->payments()->where('status', 'completed')->count();
    }

    /**
     * Check if enrollment limit is reached
     */
    public function isEnrollmentLimitReached(): bool
    {
        if (!$this->hasEnrollmentLimit()) {
            return false;
        }

        return $this->getCurrentEnrollments() >= $this->max_enrollments;
    }

    /**
     * Check if user has purchased this bundle
     */
    public function isPurchasedByUser($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->payments()
                    ->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->exists();
    }

    /**
     * Get total lesson count across all courses
     */
    public function getTotalLessonsAttribute(): int
    {
        return $this->courses()->withCount('lessons')->get()->sum('lessons_count');
    }

    /**
     * Get total duration across all courses (in minutes)
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->courses()->sum('duration_minutes') ?? 0;
    }

    /**
     * Scope for active bundles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured bundles
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for available bundles
     */
    public function scopeAvailable($query)
    {
        $now = now();
        
        return $query->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('available_from')
                          ->orWhere('available_from', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('available_until')
                          ->orWhere('available_until', '>=', $now);
                    });
    }
}