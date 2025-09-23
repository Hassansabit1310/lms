<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the enrollments for the user
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the enrolled courses
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')->withTimestamps();
    }

    /**
     * Get the subscriptions for the user
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the payments for the user
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the progress records for the user
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Get the reviews for the user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get quiz attempts for this user
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get assessment results for this user
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     * Get content locks created by this user
     */
    public function contentLocks(): HasMany
    {
        return $this->hasMany(ContentLock::class);
    }

    /**
     * Get content rules created by this user
     */
    public function createdContentRules(): HasMany
    {
        return $this->hasMany(ContentRule::class, 'created_by');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is instructor
     */
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * Get active subscription
     */
    public function getActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();
    }

    /**
     * Check if user has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->getActiveSubscription() !== null;
    }

    /**
     * Check if user has access to course
     */
    public function hasAccessToCourse(Course $course): bool
    {
        return $course->hasAccess($this);
    }

    /**
     * Check if user has purchased a course
     */
    public function hasPurchasedCourse(Course $course): bool
    {
        return $this->payments()
                    ->where('course_id', $course->id)
                    ->whereIn('status', ['completed', 'success', 'approved'])
                    ->exists();
    }

    /**
     * Enroll in a course
     */
    public function enrollInCourse(Course $course): Enrollment
    {
        return $this->enrollments()->firstOrCreate([
            'course_id' => $course->id,
        ], [
            'enrolled_at' => now(),
        ]);
    }

    /**
     * Get user's progress for a specific course
     */
    public function getCourseProgress(Course $course): int
    {
        $enrollment = $this->enrollments()->where('course_id', $course->id)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    /**
     * Check if user has purchased a bundle
     */
    public function hasPurchasedBundle(Bundle $bundle): bool
    {
        return $this->payments()
                    ->where('bundle_id', $bundle->id)
                    ->whereIn('status', ['completed', 'success', 'approved'])
                    ->exists();
    }

    /**
     * Get user's purchased bundles
     */
    public function purchasedBundles()
    {
        return Bundle::whereIn('id', 
            $this->payments()
                 ->whereIn('status', ['completed', 'success', 'approved'])
                 ->whereNotNull('bundle_id')
                 ->pluck('bundle_id')
        );
    }

    /**
     * Enroll in all courses from a bundle
     */
    public function enrollInBundle(Bundle $bundle): void
    {
        foreach ($bundle->courses as $course) {
            $this->enrollInCourse($course);
        }
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for student users
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope for instructor users
     */
    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * Ensure database role and Spatie roles are synchronized
     */
    public function ensureRoleSync(): void
    {
        $dbRole = $this->role ?? 'student';
        $spatieRoles = $this->getRoleNames()->toArray();
        
        // Check if sync is needed
        $needsSync = false;
        
        if (empty($spatieRoles)) {
            $needsSync = true;
        } elseif (count($spatieRoles) > 1) {
            $needsSync = true;
        } elseif (!in_array($dbRole, $spatieRoles)) {
            $needsSync = true;
        }
        
        if ($needsSync) {
            // Validate and set default role if invalid
            if (!in_array($dbRole, ['admin', 'student', 'instructor'])) {
                $dbRole = 'student';
                $this->update(['role' => 'student']);
            }
            
            // Sync Spatie roles with database role
            $this->syncRoles([$dbRole]);
            
            \Log::info("Auto-synced user roles", [
                'user_id' => $this->id,
                'email' => $this->email,
                'synced_to' => $dbRole
            ]);
        }
    }

    // Removed automatic role sync to prevent interference with authentication
}
