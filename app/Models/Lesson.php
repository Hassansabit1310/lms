<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $baseSlug = Str::slug($lesson->title);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure slug is unique within the course
                while (static::where('course_id', $lesson->course_id)
                           ->where('slug', $slug)
                           ->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $lesson->slug = $slug;
            }
        });

        static::updating(function ($lesson) {
            if ($lesson->isDirty('title') && empty($lesson->slug)) {
                $baseSlug = Str::slug($lesson->title);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure slug is unique within the course (excluding current lesson)
                while (static::where('course_id', $lesson->course_id)
                           ->where('slug', $slug)
                           ->where('id', '!=', $lesson->id)
                           ->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $lesson->slug = $slug;
            }
        });
    }

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'slug',
        'type',
        'content',
        'video_url',
        'video_duration',
        'is_free',
        'status',
        'order',
        'sort_orders',
        'duration_minutes',
    ];

    protected $casts = [
        'course_id' => 'integer',
        'is_free' => 'boolean',
        'order' => 'integer',
        'sort_orders' => 'integer',
        'duration_minutes' => 'integer',
        'video_duration' => 'integer',
    ];

    /**
     * Get the course that owns the lesson
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the progress records for the lesson
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Check if user has access to this lesson
     */
    public function hasAccess($user): bool
    {
        // Free lessons are always accessible
        if ($this->is_free) {
            return true;
        }

        if (!$user) {
            return false;
        }

        // Admin has access to everything
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if user has access to the course
        if (!$this->course->hasAccess($user)) {
            return false;
        }

        // Check prerequisites
        return $this->checkPrerequisites($user);
    }

    /**
     * Check if user meets prerequisites for this lesson
     */
    public function checkPrerequisites($user): bool
    {
        if (!$user) {
            return false;
        }

        // Admin bypasses prerequisites
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check content locks
        $contentLocks = $this->contentLocks()->active()->get();
        
        foreach ($contentLocks as $lock) {
            if (!$lock->isUnlockedFor($user)) {
                return false;
            }
        }

        // Check if previous lessons in order are completed
        $previousLessons = $this->course->lessons()
            ->where('order', '<', $this->order)
            ->where('is_free', false) // Only check non-free lessons
            ->get();

        foreach ($previousLessons as $previousLesson) {
            if (!$previousLesson->isCompletedByUser($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get reasons why lesson is locked for user
     */
    public function getLockReasons($user): array
    {
        $reasons = [];

        if (!$user) {
            return ['You must be logged in to access this lesson'];
        }

        if ($user->hasRole('admin')) {
            return [];
        }

        // Check course access
        if (!$this->course->hasAccess($user)) {
            $reasons[] = 'You must be enrolled in this course';
        }

        // Check content locks
        $contentLocks = $this->contentLocks()->active()->get();
        
        foreach ($contentLocks as $lock) {
            if (!$lock->isUnlockedFor($user)) {
                $reasons[] = $lock->getDescription();
            }
        }

        // Check previous lessons
        $incompletePrevious = $this->course->lessons()
            ->where('order', '<', $this->order)
            ->where('is_free', false)
            ->whereDoesntHave('progress', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'completed');
            })
            ->pluck('title')
            ->toArray();

        if (!empty($incompletePrevious)) {
            $reasons[] = 'Complete previous lessons: ' . implode(', ', $incompletePrevious);
        }

        return $reasons;
    }

    /**
     * Get the user's progress for this lesson
     */
    public function getProgressForUser($user)
    {
        if (!$user) {
            return null;
        }

        return $this->progress()->where('user_id', $user->id)->first();
    }

    /**
     * Check if lesson is completed by user
     */
    public function isCompletedByUser($user): bool
    {
        $progress = $this->getProgressForUser($user);
        return $progress && $progress->status === 'completed';
    }

    /**
     * Get lesson contents (H5P, Matter.js, etc.)
     */
    public function contents(): HasMany
    {
        return $this->hasMany(LessonContent::class)->orderBy('order');
    }

    /**
     * Get content locks for this lesson
     */
    public function contentLocks(): MorphMany
    {
        return $this->morphMany(ContentLock::class, 'lockable');
    }

    /**
     * Get content rules targeting this lesson
     */
    public function contentRules(): MorphMany
    {
        return $this->morphMany(ContentRule::class, 'target_content');
    }

    /**
     * Get quizzes attached to this lesson
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Check if lesson is locked for user
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
     * Get embedded content based on lesson type
     */
    public function getEmbeddedContentAttribute(): string
    {
        switch ($this->type) {
            case 'youtube':
                return $this->getYouTubeEmbed();
            case 'vimeo':
                return $this->getVimeoEmbed();
            case 'h5p':
                return $this->getH5PEmbed();
            case 'code':
                return $this->getCodeContent();
            case 'pdf':
                return $this->getPdfEmbed();
            case 'text':
            default:
                return $this->content;
        }
    }

    /**
     * Get YouTube embed HTML
     */
    private function getYouTubeEmbed(): string
    {
        // Extract video ID from URL
        $videoId = $this->extractYouTubeId($this->content);
        if (!$videoId) {
            return '<p>Invalid YouTube URL</p>';
        }

        return '<div class="video-responsive">
                    <iframe src="https://www.youtube.com/embed/' . $videoId . '?rel=0" 
                            frameborder="0" allowfullscreen></iframe>
                </div>';
    }

    /**
     * Get Vimeo embed HTML
     */
    private function getVimeoEmbed(): string
    {
        $videoId = $this->extractVimeoId($this->content);
        if (!$videoId) {
            return '<p>Invalid Vimeo URL</p>';
        }

        return '<div class="video-responsive">
                    <iframe src="https://player.vimeo.com/video/' . $videoId . '" 
                            frameborder="0" allowfullscreen></iframe>
                </div>';
    }

    /**
     * Get H5P embed HTML
     */
    private function getH5PEmbed(): string
    {
        // This would need H5P integration
        return '<div class="h5p-content">' . $this->content . '</div>';
    }

    /**
     * Get code content with syntax highlighting
     */
    private function getCodeContent(): string
    {
        return '<div class="code-content"><pre><code>' . htmlspecialchars($this->content) . '</code></pre></div>';
    }

    /**
     * Get PDF embed HTML
     */
    private function getPdfEmbed(): string
    {
        return '<div class="pdf-embed">
                    <iframe src="' . asset($this->content) . '" width="100%" height="600px"></iframe>
                </div>';
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeId($url): ?string
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extractVimeoId($url): ?string
    {
        preg_match('/vimeo\.com\/(?:channels\/[^\/]+\/)?(\d+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Scope for free lessons
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for premium lessons
     */
    public function scopePremium($query)
    {
        return $query->where('is_free', false);
    }
}
