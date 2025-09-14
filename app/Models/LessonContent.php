<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LessonContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'content_type',
        'content_data',
        'settings',
        'h5p_content_id',
        'matter_js_code',
        'interactive_config',
        'is_active',
        'order',
    ];

    protected $casts = [
        'content_data' => 'array',
        'settings' => 'array',
        'interactive_config' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the lesson that owns this content
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get content locks for this content
     */
    public function contentLocks(): MorphMany
    {
        return $this->morphMany(ContentLock::class, 'lockable');
    }

    /**
     * Get content rules targeting this content
     */
    public function contentRules(): MorphMany
    {
        return $this->morphMany(ContentRule::class, 'target_content');
    }

    /**
     * Scope for active content
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered content
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get rendered content based on type
     */
    public function getRenderedContentAttribute(): string
    {
        switch ($this->content_type) {
            case 'video':
                return $this->renderVideoContent();
            case 'h5p':
                return $this->renderH5PContent();
            case 'matter_js':
                return $this->renderMatterJSContent();
            case 'text':
                return $this->renderTextContent();
            case 'code':
                return $this->renderCodeContent();
            case 'quiz':
                return $this->renderQuizContent();
            case 'interactive':
                return $this->renderInteractiveContent();
            default:
                return $this->content_data['content'] ?? '';
        }
    }

    /**
     * Render video content (YouTube/Vimeo)
     */
    private function renderVideoContent(): string
    {
        $url = $this->content_data['url'] ?? '';
        $type = $this->content_data['video_type'] ?? 'youtube';
        
        if ($type === 'youtube') {
            $videoId = $this->extractYouTubeId($url);
            if ($videoId) {
                return '<div class="video-responsive"><iframe src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
            }
        } elseif ($type === 'vimeo') {
            $videoId = $this->extractVimeoId($url);
            if ($videoId) {
                return '<div class="video-responsive"><iframe src="https://player.vimeo.com/video/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
            }
        }
        
        return '<p class="text-red-600">Invalid video URL</p>';
    }

    /**
     * Render H5P content
     */
    private function renderH5PContent(): string
    {
        if ($this->h5p_content_id) {
            return '<div class="h5p-content" data-h5p-id="' . $this->h5p_content_id . '">Loading H5P content...</div>';
        }
        return '<p class="text-gray-600">H5P content not available</p>';
    }

    /**
     * Render Matter.js physics content
     */
    private function renderMatterJSContent(): string
    {
        $canvasId = 'matter-canvas-' . $this->id;
        return '<div class="matter-js-container">
                    <canvas id="' . $canvasId . '" class="matter-canvas"></canvas>
                    <div class="matter-controls">
                        <button onclick="startMatterSimulation(\'' . $canvasId . '\')" class="btn btn-primary">Start</button>
                        <button onclick="pauseMatterSimulation(\'' . $canvasId . '\')" class="btn btn-secondary">Pause</button>
                        <button onclick="resetMatterSimulation(\'' . $canvasId . '\')" class="btn btn-danger">Reset</button>
                    </div>
                </div>';
    }

    /**
     * Render text content
     */
    private function renderTextContent(): string
    {
        return '<div class="prose max-w-none">' . ($this->content_data['content'] ?? '') . '</div>';
    }

    /**
     * Render code content
     */
    private function renderCodeContent(): string
    {
        $language = $this->content_data['language'] ?? 'javascript';
        $code = $this->content_data['code'] ?? '';
        
        return '<div class="code-block">
                    <pre><code class="language-' . $language . '">' . htmlspecialchars($code) . '</code></pre>
                </div>';
    }

    /**
     * Render quiz content
     */
    private function renderQuizContent(): string
    {
        return '<div class="quiz-container" data-quiz-id="' . ($this->content_data['quiz_id'] ?? '') . '">
                    <p>Quiz: ' . ($this->content_data['title'] ?? 'Untitled Quiz') . '</p>
                </div>';
    }

    /**
     * Render interactive content
     */
    private function renderInteractiveContent(): string
    {
        return '<div class="interactive-content">' . ($this->content_data['content'] ?? '') . '</div>';
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeId($url): ?string
    {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extractVimeoId($url): ?string
    {
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}