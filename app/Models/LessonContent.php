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
     * Get the H5P content associated with this lesson content
     */
    public function h5pContent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\H5PContent::class, 'h5p_content_id');
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
            case 'runnable_code':
                return $this->renderRunnableCodeContent();
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
            $h5pContent = $this->h5pContent ?? \App\Models\H5PContent::find($this->h5p_content_id);
            
            if ($h5pContent && $h5pContent->isReady()) {
                return '<div class="h5p-content-wrapper bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="h5p-header bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-900">' . htmlspecialchars($h5pContent->title) . '</h4>
                                    <span class="text-xs text-gray-500 bg-green-100 px-2 py-1 rounded">Interactive Content</span>
                                </div>
                            </div>
                            <div class="h5p-content-frame" style="min-height: 400px;">
                                <iframe src="' . route('h5p.embed', $h5pContent) . '" 
                                        width="100%" 
                                        height="500" 
                                        frameborder="0" 
                                        allowfullscreen
                                        style="border: none;">
                                </iframe>
                            </div>
                        </div>';
            } else {
                return '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">H5P Content Not Available</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>The H5P content for this lesson is not ready or has been removed.</p>
                                    </div>
                                </div>
                            </div>
                        </div>';
            }
        }
        
        return '<div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                    <i class="fas fa-puzzle-piece text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-600">No H5P content configured for this section</p>
                </div>';
    }

    /**
     * Render Matter.js physics content
     */
    private function renderMatterJSContent(): string
    {
        // Secure canvas ID generation
        $canvasId = 'matter-canvas-' . preg_replace('/[^0-9]/', '', $this->id);
        $matterCode = $this->matter_js_code ?? $this->content_data['matter_js_code'] ?? '';
        $settings = $this->settings ?? [];
        
        // Validate and sanitize dimensions
        $width = max(200, min(1200, (int)($settings['width'] ?? 800)));
        $height = max(200, min(800, (int)($settings['height'] ?? 400)));
        
        // Sanitize Matter.js code for XSS protection
        $sanitizedMatterCode = $this->sanitizeMatterJSCode($matterCode);
        
        // Debug logging
        \Log::info('Matter.js rendering debug', [
            'content_id' => $this->id,
            'original_code_length' => strlen($matterCode),
            'sanitized_code_length' => strlen($sanitizedMatterCode),
            'code_preview' => substr($sanitizedMatterCode, 0, 200),
            'canvas_id' => $canvasId,
            'dimensions' => $width . 'x' . $height
        ]);
        
        // If sanitizer blocked the code, use default
        if (empty($sanitizedMatterCode) || strpos($sanitizedMatterCode, '// Code blocked') !== false) {
            \Log::warning('Matter.js code was blocked, using default animation', [
                'content_id' => $this->id,
                'original_preview' => substr($matterCode, 0, 300)
            ]);
            $sanitizedMatterCode = '';
        }
        
        return '<div class="matter-js-container mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-4">
                            <i class="fas fa-atom mr-2"></i>Physics Simulation
                        </h3>
                        <div class="matter-canvas-wrapper border-2 border-gray-300 rounded-lg bg-white mb-4">
                            <canvas id="' . $canvasId . '" 
                                    class="matter-canvas w-full" 
                                    width="' . $width . '" 
                                    height="' . $height . '"></canvas>
                        </div>
                        <div class="matter-controls flex space-x-2">
                            <button onclick="startMatterSimulation(\'' . $canvasId . '\')" 
                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-1"></i> Start
                            </button>
                            <button onclick="pauseMatterSimulation(\'' . $canvasId . '\')" 
                                    class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-pause mr-1"></i> Pause
                            </button>
                            <button onclick="resetMatterSimulation(\'' . $canvasId . '\')" 
                                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </button>
                        </div>
                    </div>
                    
                    <script>
                    // Matter.js simulation for canvas: ' . $canvasId . '
                    (function() {
                        let engine_' . $this->id . ', render_' . $this->id . ', runner_' . $this->id . ';
                        
                        window.startMatterSimulation = function(canvasId) {
                            if (canvasId === "' . $canvasId . '") {
                                if (!engine_' . $this->id . ') {
                                    initMatterPhysics_' . $this->id . '();
                                }
                                if (runner_' . $this->id . ') {
                                    Matter.Runner.start(runner_' . $this->id . ', engine_' . $this->id . ');
                                }
                            }
                        };
                        
                        window.pauseMatterSimulation = function(canvasId) {
                            if (canvasId === "' . $canvasId . '" && runner_' . $this->id . ') {
                                Matter.Runner.stop(runner_' . $this->id . ');
                            }
                        };
                        
                        window.resetMatterSimulation = function(canvasId) {
                            if (canvasId === "' . $canvasId . '" && engine_' . $this->id . ') {
                                Matter.World.clear(engine_' . $this->id . '.world);
                                Matter.Engine.clear(engine_' . $this->id . ');
                                initMatterPhysics_' . $this->id . '();
                            }
                        };
                        
                        function initMatterPhysics_' . $this->id . '() {
                            const canvas = document.getElementById("' . $canvasId . '");
                            if (!canvas) return;
                            
                            engine_' . $this->id . ' = Matter.Engine.create();
                            render_' . $this->id . ' = Matter.Render.create({
                                canvas: canvas,
                                engine: engine_' . $this->id . ',
                                options: {
                                    width: ' . $width . ',
                                    height: ' . $height . ',
                                    wireframes: false,
                                    background: "#f8fafc",
                                    showAngleIndicator: true,
                                    showVelocity: true
                                }
                            });
                            
                            // Make engine and render available globally for user code
                            window.currentEngine = engine_' . $this->id . ';
                            window.currentRender = render_' . $this->id . ';
                            
                            console.log("[Matter.js Debug] Initializing physics simulation for content block ' . $this->id . '");
                            console.log("[Matter.js Debug] Canvas ID:", "' . $canvasId . '");
                            console.log("[Matter.js Debug] Engine:", engine_' . $this->id . ');
                            console.log("[Matter.js Debug] Canvas dimensions:", ' . $width . ', ' . $height . ');
                            console.log("[Matter.js Debug] Using custom code:", ' . ($sanitizedMatterCode ? 'true' : 'false') . ');
                            
                            try {
                                ' . ($sanitizedMatterCode ? str_replace(['currentEngine', 'currentRender', 'engine_0', 'render_0'], ['engine_' . $this->id, 'render_' . $this->id, 'engine_' . $this->id, 'render_' . $this->id], $sanitizedMatterCode) : $this->getDefaultMatterJSCode($width, $height)) . '
                                console.log("[Matter.js Debug] Successfully added objects to world. Body count:", engine_' . $this->id . '.world.bodies.length);
                            } catch (error) {
                                console.error("[Matter.js Error] Failed to execute physics code:", error);
                                // Fallback to default animation
                                ' . $this->getDefaultMatterJSCode($width, $height) . '
                                console.log("[Matter.js Debug] Fallback animation loaded. Body count:", engine_' . $this->id . '.world.bodies.length);
                            }
                            
                            Matter.Render.run(render_' . $this->id . ');
                            runner_' . $this->id . ' = Matter.Runner.create();
                            Matter.Runner.run(runner_' . $this->id . ', engine_' . $this->id . ');
                        }
                        
                        // Auto-initialize when the page loads
                        document.addEventListener("DOMContentLoaded", function() {
                            setTimeout(initMatterPhysics_' . $this->id . ', 100);
                        });
                    })();
                    </script>
                </div>';
    }
    
    /**
     * Get default Matter.js code if none is provided
     */
    private function getDefaultMatterJSCode($width, $height): string
    {
        return '
            // Create ground
            const ground = Matter.Bodies.rectangle(' . ($width/2) . ', ' . ($height - 10) . ', ' . $width . ', 20, { 
                isStatic: true,
                render: { fillStyle: "#4f46e5" }
            });
            
            // Create walls
            const leftWall = Matter.Bodies.rectangle(10, ' . ($height/2) . ', 20, ' . $height . ', { 
                isStatic: true,
                render: { fillStyle: "#4f46e5" }
            });
            const rightWall = Matter.Bodies.rectangle(' . ($width - 10) . ', ' . ($height/2) . ', 20, ' . $height . ', { 
                isStatic: true,
                render: { fillStyle: "#4f46e5" }
            });
            
            // Create falling objects
            const balls = [];
            for (let i = 0; i < 5; i++) {
                const ball = Matter.Bodies.circle(
                    100 + i * 100, 
                    50, 
                    20, 
                    { 
                        restitution: 0.8,
                        render: { 
                            fillStyle: ["#ef4444", "#f97316", "#eab308", "#22c55e", "#3b82f6"][i]
                        }
                    }
                );
                balls.push(ball);
            }
            
            // Add some boxes
            const boxes = [];
            for (let i = 0; i < 3; i++) {
                const box = Matter.Bodies.rectangle(
                    150 + i * 150, 
                    100, 
                    40, 
                    40, 
                    { 
                        restitution: 0.4,
                        render: { 
                            fillStyle: ["#8b5cf6", "#ec4899", "#06b6d4"][i]
                        }
                    }
                );
                boxes.push(box);
            }
            
            // Add all bodies to the world
            Matter.World.add(engine_' . $this->id . '.world, [ground, leftWall, rightWall, ...balls, ...boxes]);
            
            // Mouse constraint for interaction
            const mouse = Matter.Mouse.create(render_' . $this->id . '.canvas);
            const mouseConstraint = Matter.MouseConstraint.create(engine_' . $this->id . ', {
                mouse: mouse,
                constraint: {
                    stiffness: 0.2,
                    render: {
                        visible: false
                    }
                }
            });
            
            Matter.World.add(engine_' . $this->id . '.world, mouseConstraint);
            render_' . $this->id . '.mouse = mouse;
        ';
    }
    
    /**
     * Sanitize Matter.js code to prevent XSS attacks while allowing legitimate physics code
     */
    private function sanitizeMatterJSCode($code): string
    {
        if (empty($code)) {
            return '';
        }
        
        // Only block the most dangerous patterns, be more permissive for legitimate code
        $criticalDangers = [
            '/<script[^>]*>/i',
            '/<\/script>/i',
            '/javascript\s*:/i',
            '/data\s*:/i',
            '/vbscript\s*:/i',
            '/eval\s*\(/i',
            '/Function\s*\(/i',
            '/\.innerHTML\s*=/i',
            '/\.outerHTML\s*=/i',
            '/document\s*\./i',
            '/window\s*\.(?!currentEngine|currentRender)/i', // Allow window.currentEngine/currentRender
            '/location\s*\./i',
            '/alert\s*\(/i',
            '/confirm\s*\(/i',
            '/prompt\s*\(/i',
            '/fetch\s*\(/i',
            '/XMLHttpRequest/i',
            '/\.createElement/i',
            '/\.appendChild/i',
            '/import\s*\(/i',
            '/require\s*\(/i',
            '/localStorage/i',
            '/sessionStorage/i'
        ];
        
        // Check for critical dangerous patterns only
        foreach ($criticalDangers as $pattern) {
            if (preg_match($pattern, $code)) {
                \Log::warning('Critical security pattern detected in Matter.js code', [
                    'content_id' => $this->id,
                    'pattern' => $pattern,
                    'code_preview' => substr($code, 0, 200)
                ]);
                
                return '// Code blocked for security: ' . str_replace(['/', 'i'], '', $pattern);
            }
        }
        
        // More lenient validation - allow JavaScript syntax but look for Matter.js usage
        $allowedPatterns = [
            '/Matter\./i',
            '/Bodies\./i', 
            '/World\./i',
            '/Engine\./i',
            '/Render\./i',
            '/Runner\./i',
            '/Mouse\./i',
            '/Constraint\./i',
            '/currentEngine/i',
            '/currentRender/i',
            '/Vector\./i',
            '/Events\./i',
            '/const\s+/i',
            '/let\s+/i',
            '/var\s+/i',
            '/function\s*\(/i',
            '/for\s*\(/i',
            '/if\s*\(/i'
        ];
        
        // Check if code looks like legitimate JavaScript/Matter.js (more lenient)
        $hasValidCode = false;
        foreach ($allowedPatterns as $pattern) {
            if (preg_match($pattern, $code)) {
                $hasValidCode = true;
                break;
            }
        }
        
        // Be more permissive - only block if code is very suspicious AND has no valid patterns
        if (!$hasValidCode && strlen(trim($code)) > 10) {
            // Check if it contains obvious Matter.js keywords even if pattern didn't match
            $matterKeywords = ['Matter', 'Bodies', 'World', 'Engine', 'Render', 'Runner', 'currentEngine', 'currentRender'];
            $foundKeyword = false;
            foreach ($matterKeywords as $keyword) {
                if (stripos($code, $keyword) !== false) {
                    $foundKeyword = true;
                    break;
                }
            }
            
            if (!$foundKeyword) {
                \Log::info('Matter.js code validation: no Matter.js keywords found', [
                    'content_id' => $this->id,
                    'code_preview' => substr($code, 0, 200)
                ]);
                // Allow it anyway for now, just log it
                return $code . ' // Note: No Matter.js keywords detected';
            }
        }
        
        // Only remove obvious XSS vectors, keep legitimate characters
        $code = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $code);
        $code = preg_replace('/javascript\s*:/i', '', $code);
        
        // Validate code length
        if (strlen($code) > 15000) {
            \Log::warning('Matter.js code too long', [
                'content_id' => $this->id,
                'code_length' => strlen($code)
            ]);
            return '// Code too long - maximum 15,000 characters allowed';
        }
        
        return $code;
    }

    /**
     * Render text content
     */
    private function renderTextContent(): string
    {
        $content = $this->content_data['content'] ?? '';
        // Allow basic HTML tags but escape dangerous content
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><code><pre>';
        $content = strip_tags($content, $allowedTags);
        // Remove dangerous attributes
        $content = preg_replace('/\s*on\w+\s*=\s*["\'][^"\'>]*["\']?/i', '', $content);
        return '<div class="prose max-w-none">' . $content . '</div>';
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
     * Render runnable code content with sandboxed iframe
     */
    private function renderRunnableCodeContent(): string
    {
        // Sanitize all code inputs for XSS protection
        $html = htmlspecialchars($this->content_data['html_code'] ?? '', ENT_QUOTES, 'UTF-8');
        $css = htmlspecialchars($this->content_data['css_code'] ?? '', ENT_QUOTES, 'UTF-8');
        $js = htmlspecialchars($this->content_data['js_code'] ?? '', ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($this->content_data['description'] ?? '', ENT_QUOTES, 'UTF-8');
        
        if (!$html && !$css && !$js) {
            return '<div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <p class="text-gray-600">No runnable code available</p>
                    </div>';
        }
        
        // Generate secure preview content
        $previewContent = $this->generateSecurePreviewContent(
            $this->content_data['html_code'] ?? '',
            $this->content_data['css_code'] ?? '',
            $this->content_data['js_code'] ?? ''
        );
        
        return '
        <div class="runnable-code-lesson mb-8">
                            ' . ($description ? '<div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-800">' . nl2br($description) . '</p>
            </div>' : '') . '
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Code Editors -->
                <div class="code-editors">
                    <div class="tabs mb-4">
                        <div class="flex border-b border-gray-200">
                            ' . ($html ? '<button class="tab-btn active" data-tab="html">HTML</button>' : '') . '
                            ' . ($css ? '<button class="tab-btn" data-tab="css">CSS</button>' : '') . '
                            ' . ($js ? '<button class="tab-btn" data-tab="js">JavaScript</button>' : '') . '
                        </div>
                    </div>
                    
                    ' . ($html ? '
                    <div class="tab-content active" id="html-tab">
                        <div class="code-editor-container">
                            <div class="flex items-center justify-between p-2 bg-gray-100 border-b">
                                <span class="text-sm font-medium text-gray-700">HTML</span>
                                <button class="copy-btn text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" data-target="html-code-' . $this->id . '">Copy</button>
                            </div>
                            <pre class="code-display"><code id="html-code-' . $this->id . '" class="language-html">' . htmlspecialchars($html) . '</code></pre>
                        </div>
                    </div>' : '') . '
                    
                    ' . ($css ? '
                    <div class="tab-content" id="css-tab">
                        <div class="code-editor-container">
                            <div class="flex items-center justify-between p-2 bg-gray-100 border-b">
                                <span class="text-sm font-medium text-gray-700">CSS</span>
                                <button class="copy-btn text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" data-target="css-code-' . $this->id . '">Copy</button>
                            </div>
                            <pre class="code-display"><code id="css-code-' . $this->id . '" class="language-css">' . htmlspecialchars($css) . '</code></pre>
                        </div>
                    </div>' : '') . '
                    
                    ' . ($js ? '
                    <div class="tab-content" id="js-tab">
                        <div class="code-editor-container">
                            <div class="flex items-center justify-between p-2 bg-gray-100 border-b">
                                <span class="text-sm font-medium text-gray-700">JavaScript</span>
                                <button class="copy-btn text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" data-target="js-code-' . $this->id . '">Copy</button>
                            </div>
                            <pre class="code-display"><code id="js-code-' . $this->id . '" class="language-javascript">' . htmlspecialchars($js) . '</code></pre>
                        </div>
                    </div>' : '') . '
                </div>
                
                <!-- Live Preview -->
                <div class="live-preview">
                    <div class="flex items-center justify-between p-2 bg-gray-100 border-b">
                        <span class="text-sm font-medium text-gray-700">Live Preview</span>
                        <button id="refresh-preview-' . $this->id . '" class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Refresh</button>
                    </div>
                    <div class="preview-container">
                        <iframe id="preview-frame-' . $this->id . '" 
                                class="w-full h-96 border-0"
                                sandbox="allow-scripts allow-same-origin"
                                srcdoc="' . htmlspecialchars($previewContent) . '">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Generate HTML content for the preview iframe
     */
    private function generatePreviewContent($html, $css, $js): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Preview</title>
    <style>
        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
        ' . $css . '
    </style>
</head>
<body>
    ' . $html . '
    <script>
        ' . $js . '
    </script>
</body>
</html>';
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
        $content = htmlspecialchars($this->content_data['content'] ?? '', ENT_QUOTES, 'UTF-8');
        return '<div class="interactive-content">' . $content . '</div>';
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
    
    /**
     * Generate secure preview content for iframe with XSS protection
     */
    private function generateSecurePreviewContent($html, $css, $js): string
    {
        // Sanitize HTML - allow only safe HTML tags
        $allowedTags = '<div><span><p><h1><h2><h3><h4><h5><h6><strong><em><ul><ol><li><br><img><a>';
        $html = strip_tags($html, $allowedTags);
        
        // Remove dangerous attributes from HTML
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\'>]*["\']?/i', '', $html);
        $html = preg_replace('/\s*javascript\s*:/i', '', $html);
        $html = preg_replace('/\s*data\s*:/i', '', $html);
        $html = preg_replace('/\s*vbscript\s*:/i', '', $html);
        
        // Sanitize CSS - remove dangerous CSS properties
        $css = preg_replace('/expression\s*\(/i', '', $css);
        $css = preg_replace('/javascript\s*:/i', '', $css);
        $css = preg_replace('/behavior\s*:/i', '', $css);
        $css = preg_replace('/@import/i', '', $css);
        $css = preg_replace('/url\s*\(/i', '', $css);
        
        // Sanitize JavaScript - remove dangerous functions
        $dangerousJS = [
            '/eval\s*\(/i',
            '/function\s*\(\s*\)\s*\{[^}]*document\./i',
            '/function\s*\(\s*\)\s*\{[^}]*window\./i',
            '/document\./i',
            '/window\./i',
            '/location\./i',
            '/alert\s*\(/i',
            '/confirm\s*\(/i',
            '/prompt\s*\(/i',
            '/fetch\s*\(/i',
            '/XMLHttpRequest/i',
            '/import\s*\(/i',
            '/require\s*\(/i',
            '/localStorage/i',
            '/sessionStorage/i',
            '/cookie/i'
        ];
        
        foreach ($dangerousJS as $pattern) {
            if (preg_match($pattern, $js)) {
                \Log::warning('Dangerous JavaScript detected in runnable code', [
                    'content_id' => $this->id,
                    'pattern' => $pattern
                ]);
                $js = '// Dangerous JavaScript blocked for security';
                break;
            }
        }
        
        // Ensure content length limits
        $html = substr($html, 0, 50000);
        $css = substr($css, 0, 20000);
        $js = substr($js, 0, 20000);
        
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Code Preview</title>
    <style>
        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
        ' . $css . '
    </style>
</head>
<body>
    ' . $html . '
    <script>
        // Disable dangerous functions
        window.eval = function() { console.warn("eval() disabled for security"); };
        window.Function = function() { console.warn("Function() constructor disabled for security"); };
        
        ' . $js . '
    </script>
</body>
</html>';
    }
}