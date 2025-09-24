<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .video-responsive {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }
        .video-responsive iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .physics-canvas {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
        }
        .runnable-code-lesson .tab-btn {
            padding: 8px 16px;
            margin-right: 8px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            cursor: pointer;
            border-radius: 6px 6px 0 0;
            font-size: 14px;
            font-weight: 500;
        }
        .runnable-code-lesson .tab-btn.active {
            background: #ffffff;
            border-bottom: 1px solid #ffffff;
            color: #1f2937;
        }
        .runnable-code-lesson .tab-content {
            display: none;
        }
        .runnable-code-lesson .tab-content.active {
            display: block;
        }
        .runnable-code-lesson .code-display {
            background: #1f2937;
            color: #e5e7eb;
            padding: 16px;
            border-radius: 0 0 8px 8px;
            overflow-x: auto;
            font-size: 14px;
            line-height: 1.5;
        }
        .runnable-code-lesson .copy-btn:hover {
            background: #2563eb;
        }
        .runnable-code-lesson .preview-container {
            border: 1px solid #d1d5db;
            border-radius: 0 0 8px 8px;
            background: #ffffff;
        }
    </style>
    @endpush

    <!-- Course Navigation -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('courses.show', $course) }}" 
                       class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $lesson->title }}</h1>
                        <p class="text-sm text-gray-600">{{ $course->title }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if($previousLesson)
                        <a href="{{ route('lessons.show', [$course, $previousLesson]) }}" 
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </a>
                    @endif
                    
                    @if($nextLesson)
                        <a href="{{ route('lessons.show', [$course, $nextLesson]) }}" 
                           class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Lesson Content -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                        <!-- Content Header -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @switch($lesson->type)
                                            @case('youtube') bg-red-100 text-red-800 @break
                                            @case('vimeo') bg-blue-100 text-blue-800 @break
                                            @case('h5p') bg-green-100 text-green-800 @break
                                            @case('quiz') bg-purple-100 text-purple-800 @break
                                            @case('code') bg-yellow-100 text-yellow-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        @switch($lesson->type)
                                            @case('youtube') <i class="fab fa-youtube mr-1"></i> Video Lesson @break
                                            @case('vimeo') <i class="fab fa-vimeo mr-1"></i> Video Lesson @break
                                            @case('h5p') <i class="fas fa-puzzle-piece mr-1"></i> Interactive Content @break
                                            @case('quiz') <i class="fas fa-question-circle mr-1"></i> Quiz @break
                                            @case('code') <i class="fas fa-code mr-1"></i> Code Exercise @break
                                            @case('pdf') <i class="fas fa-file-pdf mr-1"></i> Document @break
                                            @default <i class="fas fa-file-text mr-1"></i> Reading
                                        @endswitch
                                    </span>
                                    
                                    @if($lesson->duration_minutes)
                                        <span class="text-sm text-gray-600">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $lesson->duration_minutes }} minutes
                                        </span>
                                    @endif
                                </div>
                                
                                @auth
                                    @if(!$userProgress || $userProgress->status !== 'completed')
                                        <form action="{{ route('lessons.complete', [$course, $lesson]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i> Mark Complete
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                                            <i class="fas fa-check-circle mr-2"></i> Completed
                                        </span>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <!-- Content Body -->
                        <div class="p-6">
                            @if($lesson->contents && $lesson->contents->count() > 0)
                                <!-- Multiple Content Blocks -->
                                @foreach($lesson->contents->sortBy('order') as $content)
                                    <div class="content-block mb-8 last:mb-0">
                                        {!! $content->rendered_content !!}
                                    </div>
                                @endforeach
                            @else
                                <!-- Legacy Single Content -->
                                @switch($lesson->type)
                                @case('youtube')
                                    @php
                                        $videoId = null;
                                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lesson->content, $matches)) {
                                            $videoId = $matches[1];
                                        }
                                    @endphp
                                    
                                    @if($videoId)
                                        <div class="video-responsive mb-6">
                                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}?rel=0" 
                                                    frameborder="0" 
                                                    allowfullscreen></iframe>
                                        </div>
                                    @else
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                            <p class="text-red-800">Invalid YouTube URL</p>
                                        </div>
                                    @endif
                                    @break

                                @case('vimeo')
                                    @php
                                        $videoId = null;
                                        if (preg_match('/vimeo\.com\/(\d+)/', $lesson->content, $matches)) {
                                            $videoId = $matches[1];
                                        }
                                    @endphp
                                    
                                    @if($videoId)
                                        <div class="video-responsive mb-6">
                                            <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                    frameborder="0" 
                                                    allowfullscreen></iframe>
                                        </div>
                                    @else
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                            <p class="text-red-800">Invalid Vimeo URL</p>
                                        </div>
                                    @endif
                                    @break

                                @case('h5p')
                                    <div class="h5p-content mb-6">
                                        @if($lesson->contents->where('content_type', 'h5p')->first())
                                            @php $h5pContent = $lesson->contents->where('content_type', 'h5p')->first(); @endphp
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                                                <i class="fas fa-puzzle-piece text-blue-600 text-4xl mb-4"></i>
                                                <h3 class="text-lg font-semibold text-blue-900 mb-2">Interactive H5P Content</h3>
                                                <p class="text-blue-700 mb-4">This lesson contains interactive H5P content.</p>
                                                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                                    Launch Interactive Content
                                                </button>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                                                <p class="text-gray-600">H5P content not available</p>
                                            </div>
                                        @endif
                                    </div>
                                    @break

                                @case('code')
                                    <div class="code-content mb-6">
                                        @if($lesson->contents->where('content_type', 'matterjs')->first())
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                                                <h3 class="text-lg font-semibold text-yellow-900 mb-4">
                                                    <i class="fas fa-atom mr-2"></i>Physics Simulation
                                                </h3>
                                                <canvas id="physics-canvas" class="physics-canvas w-full" height="400"></canvas>
                                                <div class="mt-4 flex space-x-2">
                                                    <button onclick="startSimulation()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                                        <i class="fas fa-play mr-1"></i> Start
                                                    </button>
                                                    <button onclick="pauseSimulation()" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                                                        <i class="fas fa-pause mr-1"></i> Pause
                                                    </button>
                                                    <button onclick="resetSimulation()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                                        <i class="fas fa-redo mr-1"></i> Reset
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                            <pre><code class="text-green-400 text-sm">{{ $lesson->content }}</code></pre>
                                        </div>
                                    </div>
                                    @break

                                @case('pdf')
                                    <div class="pdf-content mb-6">
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                                            <i class="fas fa-file-pdf text-red-600 text-4xl mb-4"></i>
                                            <h3 class="text-lg font-semibold text-red-900 mb-2">PDF Document</h3>
                                            <p class="text-red-700 mb-4">Click to view the PDF document</p>
                                            <a href="{{ $lesson->content }}" 
                                               target="_blank"
                                               class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-external-link-alt mr-1"></i> Open PDF
                                            </a>
                                        </div>
                                    </div>
                                    @break

                                @default
                                    <div class="prose prose-lg max-w-none">
                                        {!! nl2br(e($lesson->content)) !!}
                                    </div>
                                @endswitch
                            @endif

                            @if($lesson->description)
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">About this lesson</h3>
                                    <div class="prose max-w-none text-gray-700">
                                        {!! nl2br(e($lesson->description)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quiz Section -->
                    @if($lesson->quizzes->count() > 0)
                        @foreach($lesson->quizzes as $quiz)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                            <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
                                <h3 class="text-lg font-semibold text-purple-900">
                                    <i class="fas fa-question-circle mr-2"></i>{{ $quiz->title }}
                                </h3>
                                @if($quiz->description)
                                    <p class="text-purple-700 mt-1">{{ $quiz->description }}</p>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                    <i class="fas fa-clipboard-check text-blue-600 text-3xl mb-3"></i>
                                    <h4 class="text-lg font-semibold text-blue-900 mb-2">Quiz Available</h4>
                                    <p class="text-blue-700 mb-4">Test your knowledge with {{ $quiz->questions->count() }} questions</p>
                                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Start Quiz
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Progress Card -->
                    @auth
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Progress</h3>
                        
                        @if($userProgress)
                            <div class="flex items-center space-x-3 mb-3">
                                @if($userProgress->status === 'completed')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-green-900">Completed</p>
                                        <p class="text-sm text-green-700">{{ $userProgress->completed_at->format('M j, Y') }}</p>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-yellow-900">In Progress</p>
                                        <p class="text-sm text-yellow-700">{{ $userProgress->progress_percentage }}% complete</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play text-gray-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Not Started</p>
                                    <p class="text-sm text-gray-600">Ready to begin</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endauth

                    <!-- Course Navigation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Lessons</h3>
                        
                        <div class="space-y-2">
                            @foreach($course->lessons->take(10) as $courseLesson)
                                <a href="{{ route('lessons.show', [$course, $courseLesson]) }}" 
                                   class="block p-3 rounded-lg transition-colors
                                          {{ $courseLesson->id === $lesson->id 
                                             ? 'bg-teal-50 border border-teal-200' 
                                             : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold
                                                    {{ $courseLesson->id === $lesson->id 
                                                       ? 'bg-teal-100 text-teal-800' 
                                                       : 'bg-gray-100 text-gray-600' }}">
                                            {{ $courseLesson->order }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $courseLesson->title }}</p>
                                            @if($courseLesson->duration_minutes)
                                                <p class="text-xs text-gray-500">{{ $courseLesson->duration_minutes }} min</p>
                                            @endif
                                        </div>
                                        @auth
                                            @if($courseLesson->isCompletedByUser(auth()->user()))
                                                <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                            @endif
                                        @endauth
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.18.0/matter.min.js"></script>
    <script>
        // Matter.js Physics Simulation (placeholder)
        let engine, render, runner;
        
        function startSimulation() {
            if (!engine) {
                initPhysics();
            }
            if (runner) {
                Matter.Runner.start(runner, engine);
            }
        }
        
        function pauseSimulation() {
            if (runner) {
                Matter.Runner.stop(runner);
            }
        }
        
        function resetSimulation() {
            if (engine) {
                Matter.World.clear(engine.world);
                Matter.Engine.clear(engine);
                initPhysics();
            }
        }
        
        function initPhysics() {
            const canvas = document.getElementById('physics-canvas');
            if (!canvas) return;
            
            engine = Matter.Engine.create();
            render = Matter.Render.create({
                canvas: canvas,
                engine: engine,
                options: {
                    width: canvas.width,
                    height: canvas.height,
                    wireframes: false,
                    background: '#f9fafb'
                }
            });
            
            // Add some basic physics objects
            const ground = Matter.Bodies.rectangle(canvas.width/2, canvas.height-10, canvas.width, 20, { isStatic: true });
            const ball = Matter.Bodies.circle(100, 50, 20, { restitution: 0.8 });
            const box = Matter.Bodies.rectangle(300, 50, 40, 40, { restitution: 0.3 });
            
            Matter.World.add(engine.world, [ground, ball, box]);
            Matter.Render.run(render);
            
            runner = Matter.Runner.create();
            Matter.Runner.run(runner, engine);
        }
        
        // Initialize physics if canvas exists
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('physics-canvas');
            if (canvas) {
                canvas.width = canvas.offsetWidth;
                initPhysics();
            }
            
            // Initialize runnable code functionality
            initRunnableCode();
        });
        
        // Runnable Code functionality
        function initRunnableCode() {
            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tab = this.dataset.tab;
                    
                    // Remove active class from all tabs and contents
                    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(tab + '-tab').classList.add('active');
                });
            });
            
            // Copy functionality
            document.querySelectorAll('.copy-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const codeElement = document.getElementById(targetId);
                    const text = codeElement.textContent;
                    
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        this.classList.add('bg-green-500');
                        this.classList.remove('bg-blue-500');
                        
                        setTimeout(() => {
                            this.textContent = originalText;
                            this.classList.remove('bg-green-500');
                            this.classList.add('bg-blue-500');
                        }, 2000);
                    });
                });
            });
            
            // Refresh preview
            const refreshBtn = document.getElementById('refresh-preview');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    const iframe = document.getElementById('preview-frame');
                    if (iframe) {
                        // Reload the iframe by setting the srcdoc again
                        const currentSrcdoc = iframe.getAttribute('srcdoc');
                        iframe.setAttribute('srcdoc', '');
                        setTimeout(() => {
                            iframe.setAttribute('srcdoc', currentSrcdoc);
                        }, 100);
                    }
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
