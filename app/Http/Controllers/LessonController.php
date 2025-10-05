<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Display a listing of lessons for a course (Admin)
     */
    public function index(Course $course): View
    {
        // Authorization handled by role:admin middleware
        
        $lessons = $course->lessons()
            ->with(['progress', 'quizzes'])
            ->orderBy('order')
            ->paginate(15);
            
        return view('admin.lessons.index', compact('course', 'lessons'));
    }

    /**
     * Show the form for creating a new lesson
     */
    public function create(Course $course): View
    {
        // Authorization handled by role:admin middleware
        
        $nextOrder = $course->lessons()->max('order') + 1;
        
        return view('admin.lessons.create', compact('course', 'nextOrder'));
    }

    /**
     * Show the form for creating a new multi-content lesson
     */
    public function createMulti(Course $course): View
    {
        // Authorization handled by role:admin middleware
        
        $nextOrder = $course->lessons()->max('order') + 1;
        
        return view('admin.lessons.create-multi', compact('course', 'nextOrder'));
    }

    /**
     * Store a newly created lesson in storage
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        // Authorization handled by role:admin middleware
        
        // Check if this is a multi-content lesson or regular lesson
        if ($request->has('content_blocks')) {
            // Multi-content lesson validation
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_free' => 'boolean',
                'duration_minutes' => 'nullable|integer|min:0',
                'order' => 'nullable|integer|min:1',
                
                // Content blocks
                'content_blocks' => 'required|array|min:1',
                'content_blocks.*.type' => 'required|in:text,youtube,vimeo,h5p,code,runnable_code,matter_js,quiz',
                'content_blocks.*.content' => 'required|string',
                'content_blocks.*.order' => 'nullable|integer',
                'content_blocks.*.settings' => 'nullable|array',
            ]);
        } else {
            // Regular lesson validation (backward compatibility)
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:text,youtube,vimeo,h5p,code,pdf,quiz',
                'content' => 'required|string',
                'is_free' => 'boolean',
                'duration_minutes' => 'nullable|integer|min:0',
                'order' => 'nullable|integer|min:1',
            ]);
        }

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = $course->lessons()->max('order') + 1;
        }

        // Generate unique slug
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        while ($course->lessons()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;
        $validated['course_id'] = $course->id;

        if ($request->has('content_blocks')) {
            // Multi-content lesson creation
            $lessonData = collect($validated)->except('content_blocks')->toArray();
            
            // Add required fields for multi-content lessons
            $lessonData['type'] = 'text'; // Default type for multi-content lessons
            $lessonData['content'] = 'Multi-content lesson - see content blocks'; // Default content
            
            $lesson = $course->lessons()->create($lessonData);

            // Create content blocks
            foreach ($validated['content_blocks'] as $index => $blockData) {
                $this->createContentBlock($lesson, $blockData, $index + 1);
            }

            return redirect()
                ->route('admin.courses.lessons.index', $course)
                ->with('success', 'Multi-content lesson created successfully with ' . count($validated['content_blocks']) . ' content block(s)!');
        } else {
            // Regular lesson creation (backward compatibility)
            $lesson = $course->lessons()->create($validated);

            return redirect()
                ->route('admin.courses.lessons.index', $course)
                ->with('success', 'Lesson created successfully!');
        }
    }

    /**
     * Create a content block for a lesson
     */
    private function createContentBlock(Lesson $lesson, array $blockData, int $order): LessonContent
    {
        $contentData = [];
        $settings = $blockData['settings'] ?? [];
        
        switch ($blockData['type']) {
            case 'youtube':
            case 'vimeo':
                $contentData = [
                    'url' => $blockData['content'],
                    'video_type' => $blockData['type']
                ];
                break;
                
            case 'h5p':
                // Handle H5P content ID from library selection
                $h5pContentId = $blockData['content']; // This should be the H5P content ID
                
                // Validate that the H5P content exists and is ready
                $h5pContent = \App\Models\H5PContent::where('id', $h5pContentId)
                    ->where('upload_status', 'completed')
                    ->where('is_active', true)
                    ->first();
                
                if (!$h5pContent) {
                    throw new \Exception("H5P content with ID {$h5pContentId} not found or not ready");
                }
                
                $contentData = [
                    'h5p_content_id' => $h5pContentId,
                    'title' => $h5pContent->title,
                    'content_type' => $h5pContent->content_type,
                ];
                break;
                
            case 'code':
                $contentData = [
                    'code' => $blockData['content'],
                    'language' => $settings['language'] ?? 'javascript'
                ];
                break;
                
            case 'runnable_code':
                $contentData = [
                    'html_code' => substr($settings['html_code'] ?? '', 0, 50000),
                    'css_code' => substr($settings['css_code'] ?? '', 0, 20000),
                    'js_code' => substr($settings['js_code'] ?? '', 0, 20000),
                    'description' => substr(strip_tags($settings['description'] ?? ''), 0, 1000)
                ];
                break;
                
            case 'matter_js':
                $contentData = [
                    'matter_js_code' => substr($settings['matter_js_code'] ?? '', 0, 10000),
                    'width' => max(200, min(1200, (int)($settings['width'] ?? 800))),
                    'height' => max(200, min(800, (int)($settings['height'] ?? 400)))
                ];
                break;
                
            case 'text':
                $contentData = [
                    'content' => substr($blockData['content'], 0, 100000)
                ];
                break;
                
            case 'quiz':
                $contentData = [
                    'quiz_id' => $blockData['content'],
                    'title' => $settings['title'] ?? 'Quiz'
                ];
                break;
                
            default:
                $contentData = [
                    'content' => $blockData['content']
                ];
        }
        
        // Prepare lesson content data
        $lessonContentData = [
            'content_type' => $blockData['type'] === 'youtube' || $blockData['type'] === 'vimeo' ? 'video' : $blockData['type'],
            'content_data' => $contentData,
            'settings' => $settings,
            'order' => $order,
            'is_active' => true
        ];

        // For H5P content, also set the h5p_content_id field
        if ($blockData['type'] === 'h5p' && isset($contentData['h5p_content_id'])) {
            $lessonContentData['h5p_content_id'] = $contentData['h5p_content_id'];
        }

        $lessonContent = $lesson->contents()->create($lessonContentData);

        // For H5P content, create usage tracking record
        if ($blockData['type'] === 'h5p' && isset($contentData['h5p_content_id'])) {
            \App\Models\H5PUsage::create([
                'h5p_content_id' => $contentData['h5p_content_id'],
                'lesson_content_id' => $lessonContent->id,
                'course_id' => $lesson->course_id,
                'usage_type' => 'lesson_content',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $lessonContent;
    }

    /**
     * Display the specified lesson (Student View)
     */
    public function show(Course $course, Lesson $lesson): View
    {
        // Check if user has access to this lesson
        $user = Auth::user();
        
        if (!$lesson->hasAccess($user)) {
            abort(403, 'You do not have access to this lesson.');
        }

        // Load lesson with related data
        $lesson->load(['contents.h5pContent', 'quizzes.questions', 'progress']);
        
        // Get user's progress for this lesson
        $userProgress = $user ? $lesson->getProgressForUser($user) : null;
        
        // Get next and previous lessons
        $nextLesson = $course->lessons()
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();
            
        $previousLesson = $course->lessons()
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();

        return view('lessons.show', compact(
            'course', 
            'lesson', 
            'userProgress', 
            'nextLesson', 
            'previousLesson'
        ));
    }

    /**
     * Show the form for editing the specified lesson (with course context)
     */
    public function edit(Course $course, Lesson $lesson): View
    {
        // Authorization handled by role:admin middleware
        
        $lesson->load(['contents', 'quizzes']);
        
        return view('admin.lessons.edit-multi', compact('course', 'lesson'));
    }

    /**
     * Show the form for editing the specified lesson (shallow route)
     */
    public function editShallow(Lesson $lesson): View
    {
        // Authorization handled by role:admin middleware
        
        $lesson->load(['contents', 'quizzes', 'course']);
        $course = $lesson->course;
        
        return view('admin.lessons.edit-multi', compact('course', 'lesson'));
    }

    /**
     * Update the specified lesson in storage (with course context)
     */
    public function update(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        // Authorization handled by role:admin middleware

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:1',
            'content_blocks' => 'required|array|min:1',
            'content_blocks.*.type' => 'required|string|in:text,youtube,vimeo,h5p,code,runnable_code,matter_js,quiz',
            'content_blocks.*.content' => 'required|string',
            'content_blocks.*.order' => 'required|integer|min:1',
            'content_blocks.*.id' => 'nullable|integer|exists:lesson_contents,id',
        ]);

        // Update slug if title changed
        if ($lesson->title !== $validated['title']) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;

            while ($course->lessons()->where('slug', $slug)->where('id', '!=', $lesson->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        // Update lesson basic information
        $lesson->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration_minutes' => $validated['duration_minutes'],
            'order' => $validated['order'],
            'slug' => $validated['slug'] ?? $lesson->slug,
            'type' => 'text', // Set default type for multi-content lessons
            'content' => 'Multi-content lesson - see content blocks',
        ]);

        // Handle content blocks
        $existingContentIds = [];
        
        foreach ($validated['content_blocks'] as $index => $blockData) {
            $contentData = [
                'lesson_id' => $lesson->id,
                'content_type' => $blockData['type'],
                'order' => $blockData['order'],
                'is_active' => true,
            ];

            // Handle different content types
            switch ($blockData['type']) {
                case 'youtube':
                case 'vimeo':
                    $contentData['content_data'] = [
                        'url' => $blockData['content'],
                        'video_type' => $blockData['type']
                    ];
                    break;
                case 'text':
                    $contentData['content_data'] = [
                        'content' => $blockData['content']
                    ];
                    break;
                case 'code':
                case 'runnable_code':
                    $contentData['content_data'] = [
                        'code' => $blockData['content']
                    ];
                    break;
                case 'matter_js':
                    $contentData['content_data'] = [
                        'matter_js_code' => $blockData['content']
                    ];
                    $contentData['matter_js_code'] = $blockData['content'];
                    break;
                case 'h5p':
                    $contentData['content_data'] = [
                        'h5p_id' => $blockData['content']
                    ];
                    $contentData['h5p_content_id'] = $blockData['content'];
                    break;
                case 'quiz':
                    // Content should be JSON string with quiz_id
                    $quizData = is_string($blockData['content']) ? json_decode($blockData['content'], true) : $blockData['content'];
                    $contentData['content_data'] = $quizData; // Don't double-encode, let the model cast handle it
                    break;
                default:
                    $contentData['content_data'] = [
                        'content' => $blockData['content']
                    ];
                    break;
            }

            // Update existing content or create new
            if (!empty($blockData['id'])) {
                // Update existing content
                $existingContent = $lesson->contents()->find($blockData['id']);
                if ($existingContent) {
                    $existingContent->update($contentData);
                    $existingContentIds[] = $blockData['id'];
                }
            } else {
                // Create new content
                $newContent = $lesson->contents()->create($contentData);
                $existingContentIds[] = $newContent->id;
            }
        }

        // Remove content blocks that are no longer present
        $lesson->contents()->whereNotIn('id', $existingContentIds)->delete();

        return redirect()
            ->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson updated successfully!');
    }

    /**
     * Update the specified lesson in storage (shallow route)
     */
    public function updateShallow(Request $request, Lesson $lesson): RedirectResponse
    {
        // Authorization handled by role:admin middleware

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'content_blocks' => 'required|array|min:1',
            'content_blocks.*.type' => 'required|string|in:text,youtube,vimeo,h5p,code,runnable_code,matter_js,quiz',
            'content_blocks.*.content' => 'required|string',
            'content_blocks.*.order' => 'required|integer|min:1',
            'content_blocks.*.id' => 'nullable|integer|exists:lesson_contents,id',
        ]);

        // Update lesson basic information
        $lesson->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration_minutes' => $validated['duration_minutes'],
            'order' => $validated['order'],
            'is_published' => $validated['is_published'] ?? false,
            'type' => 'text', // Set default type for multi-content lessons
            'content' => 'Multi-content lesson - see content blocks',
        ]);

        // Handle content blocks (same logic as update method)
        $existingContentIds = [];
        
        foreach ($validated['content_blocks'] as $index => $blockData) {
            $contentData = [
                'lesson_id' => $lesson->id,
                'content_type' => $blockData['type'],
                'order' => $blockData['order'],
                'is_active' => true,
            ];

            // Handle different content types
            switch ($blockData['type']) {
                case 'youtube':
                case 'vimeo':
                    $contentData['content_data'] = [
                        'url' => $blockData['content'],
                        'video_type' => $blockData['type']
                    ];
                    break;
                case 'text':
                    $contentData['content_data'] = [
                        'content' => $blockData['content']
                    ];
                    break;
                case 'code':
                case 'runnable_code':
                    $contentData['content_data'] = [
                        'code' => $blockData['content']
                    ];
                    break;
                case 'matter_js':
                    $contentData['content_data'] = [
                        'matter_js_code' => $blockData['content']
                    ];
                    $contentData['matter_js_code'] = $blockData['content'];
                    break;
                case 'h5p':
                    $contentData['content_data'] = [
                        'h5p_id' => $blockData['content']
                    ];
                    $contentData['h5p_content_id'] = $blockData['content'];
                    break;
                case 'quiz':
                    // Content should be JSON string with quiz_id
                    $quizData = is_string($blockData['content']) ? json_decode($blockData['content'], true) : $blockData['content'];
                    $contentData['content_data'] = $quizData; // Don't double-encode, let the model cast handle it
                    break;
                default:
                    $contentData['content_data'] = [
                        'content' => $blockData['content']
                    ];
                    break;
            }

            // Update existing content or create new
            if (!empty($blockData['id'])) {
                // Update existing content
                $existingContent = $lesson->contents()->find($blockData['id']);
                if ($existingContent) {
                    $existingContent->update($contentData);
                    $existingContentIds[] = $blockData['id'];
                }
            } else {
                // Create new content
                $newContent = $lesson->contents()->create($contentData);
                $existingContentIds[] = $newContent->id;
            }
        }

        // Remove content blocks that are no longer present
        $lesson->contents()->whereNotIn('id', $existingContentIds)->delete();

        $course = $lesson->course;

        return redirect()
            ->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson updated successfully!');
    }

    /**
     * Remove the specified lesson from storage
     */
    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        // Authorization handled by role:admin middleware
        
        // Delete the lesson (cascading deletes will handle related records)
        $lesson->delete();
        
        return redirect()
            ->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson deleted successfully!');
    }

    /**
     * Mark lesson as completed for the current user
     */
    public function markCompleted(Course $course, Lesson $lesson): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            abort(403);
        }

        // Create or update progress
        Progress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
                'progress_percentage' => 100,
            ]
        );

        return redirect()
            ->route('lessons.show', [$course, $lesson])
            ->with('success', 'Lesson marked as completed!');
    }

    /**
     * Update lesson progress (AJAX endpoint)
     */
    public function updateProgress(Request $request, Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $status = $validated['progress_percentage'] >= 100 ? 'completed' : 'in_progress';
        $completedAt = $status === 'completed' ? now() : null;

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status' => $status,
                'progress_percentage' => $validated['progress_percentage'],
                'time_spent' => ($validated['time_spent'] ?? 0),
                'completed_at' => $completedAt,
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'message' => $status === 'completed' ? 'Lesson completed!' : 'Progress updated!'
        ]);
    }

    /**
     * Reorder lessons (AJAX endpoint for drag & drop)
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lesson_orders' => 'required|array',
            'lesson_orders.*.id' => 'required|exists:lessons,id',
            'lesson_orders.*.order' => 'required|integer|min:1',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        
        // Check authorization
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update lesson orders
        foreach ($validated['lesson_orders'] as $lessonData) {
            Lesson::where('id', $lessonData['id'])
                  ->where('course_id', $course->id)
                  ->update(['order' => $lessonData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson order updated successfully!'
        ]);
    }

    /**
     * Get available H5P content for lesson creation (AJAX)
     */
    public function getAvailableH5P()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $h5pContents = \App\Models\H5PContent::where('upload_status', 'completed')
            ->select('id', 'title', 'content_type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'h5p_contents' => $h5pContents
        ]);
    }

    /**
     * Show quiz for lesson (AJAX)
     */
    public function showQuiz(Course $course, Lesson $lesson, \App\Models\Quiz $quiz)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $quiz->load(['questions' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }]);

        return response()->json([
            'success' => true,
            'quiz' => $quiz,
            'questions' => $quiz->questions
        ]);
    }

    /**
     * Start a quiz attempt (AJAX)
     */
    public function startQuiz(Request $request, Course $course, Lesson $lesson, \App\Models\Quiz $quiz)
    {
        \Log::info('startQuiz called', [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id()
        ]);
        
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            \Log::warning('Access denied', ['user_id' => $user?->id, 'lesson_id' => $lesson->id]);
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check if user can take this quiz
        if (!$quiz->canUserTake($user)) {
            \Log::warning('User cannot take quiz', ['user_id' => $user->id, 'quiz_id' => $quiz->id]);
            return response()->json(['error' => 'You cannot take this quiz'], 403);
        }

        // Check if user has an active attempt
        $activeAttempt = $quiz->getActiveAttempt($user);
        if ($activeAttempt) {
            \Log::info('Returning active attempt', ['attempt_id' => $activeAttempt->id]);
            return response()->json([
                'success' => true,
                'attempt' => $activeAttempt,
                'questions' => $quiz->getQuestionsForUser($user),
                'time_remaining' => $activeAttempt->getRemainingTime(),
            ]);
        }

        // Start new attempt
        \Log::info('Starting new quiz attempt');
        $attempt = $quiz->startAttempt($user);
        
        return response()->json([
            'success' => true,
            'attempt' => $attempt,
            'questions' => $quiz->getQuestionsForUser($user),
            'time_remaining' => $attempt->getRemainingTime(),
        ]);
    }

    /**
     * Save quiz answer (AJAX)
     */
    public function saveQuizAnswer(Request $request, Course $course, Lesson $lesson, \App\Models\Quiz $quiz)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $validated = $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:quiz_questions,id',
            'answer' => 'required',
        ]);

        // Get the attempt
        $attempt = \App\Models\QuizAttempt::where('id', $validated['attempt_id'])
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Invalid attempt'], 400);
        }

        // Check if attempt is expired
        if ($attempt->isExpired()) {
            $attempt->update(['status' => 'time_expired']);
            return response()->json(['error' => 'Quiz time has expired'], 400);
        }

        // Save the answer
        $attempt->updateAnswer($validated['question_id'], $validated['answer']);

        return response()->json([
            'success' => true,
            'message' => 'Answer saved',
            'time_remaining' => $attempt->getRemainingTime(),
        ]);
    }

    /**
     * Submit quiz answers (AJAX)
     */
    public function submitQuiz(Request $request, Course $course, Lesson $lesson, \App\Models\Quiz $quiz)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check if user can take this quiz
        if (!$quiz->canUserTake($user)) {
            return response()->json(['error' => 'You cannot take this quiz'], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'attempt_id' => 'required|exists:quiz_attempts,id',
        ]);

        // Get the attempt
        $attempt = \App\Models\QuizAttempt::where('id', $validated['attempt_id'])
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Invalid attempt'], 400);
        }

        // Check if attempt is expired
        if ($attempt->isExpired()) {
            $attempt->update(['status' => 'time_expired']);
            return response()->json(['error' => 'Quiz time has expired'], 400);
        }

        // Update attempt with answers
        $attempt->update(['answers' => $validated['answers']]);

        // Calculate score using Quiz model method
        $results = $quiz->calculateScore($validated['answers']);

        // Update attempt with results
        $attempt->update([
            'score' => $results['percentage'],
            'points_earned' => $results['points_earned'],
            'points_possible' => $results['points_possible'],
            'is_passed' => $results['is_passed'],
            'status' => 'completed',
            'submitted_at' => now(),
            'time_spent_seconds' => now()->diffInSeconds($attempt->started_at),
            'detailed_results' => $results['question_results'],
        ]);

        // Create assessment result
        \App\Models\AssessmentResult::create([
            'user_id' => $user->id,
            'assessable_type' => \App\Models\Quiz::class,
            'assessable_id' => $quiz->id,
            'assessment_type' => 'quiz',
            'score' => $results['percentage'],
            'max_score' => 100,
            'is_passed' => $results['is_passed'],
            'detailed_breakdown' => $results['question_results'],
            'learning_analytics' => [
                'time_spent' => $attempt->time_spent_seconds,
                'attempt_number' => $attempt->attempt_number,
                'quiz_settings' => [
                    'time_limit' => $quiz->time_limit_minutes,
                    'passing_score' => $quiz->passing_score,
                    'randomized' => $quiz->randomize_questions,
                ]
            ],
            'assessed_at' => now(),
        ]);

        // Update lesson progress if quiz passed and is required
        if ($results['is_passed'] && $quiz->is_required) {
            \App\Models\Progress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'status' => 'completed',
                    'progress_percentage' => 100,
                    'completed_at' => now(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'attempt' => $attempt->fresh(),
            'results' => $results,
            'show_correct_answers' => $quiz->show_correct_answers,
            'can_retake' => $quiz->canUserTake($user),
            'attempts_remaining' => max(0, $quiz->max_attempts - $attempt->attempt_number),
        ]);
    }

    /**
     * Get available quizzes for lesson (AJAX)
     */
    public function getQuizzesForLesson(Request $request): \Illuminate\Http\JsonResponse
    {
        $lessonId = $request->get('lesson_id');
        $courseId = $request->get('course_id');
        
        $query = \App\Models\Quiz::where('is_active', true);
        
        if ($courseId) {
            // Get all quizzes for the course (both lesson-specific and course-level)
            $query->where('course_id', $courseId);
        }
        
        $quizzes = $query->select('id', 'title', 'description', 'quiz_type')
                        ->orderBy('title')
                        ->get();

        return response()->json(['quizzes' => $quizzes]);
    }

    /**
     * Get lesson analytics and statistics (Admin only)
     */
    public function analytics(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        // Basic statistics
        $totalStudents = $course->enrollments()->count();
        $studentsStarted = Progress::where('lesson_id', $lesson->id)->distinct('user_id')->count();
        $studentsCompleted = Progress::where('lesson_id', $lesson->id)
            ->where('status', 'completed')
            ->distinct('user_id')
            ->count();

        // Completion rate
        $completionRate = $totalStudents > 0 ? ($studentsCompleted / $totalStudents) * 100 : 0;
        $startRate = $totalStudents > 0 ? ($studentsStarted / $totalStudents) * 100 : 0;

        // Average time spent
        $avgTimeSpent = Progress::where('lesson_id', $lesson->id)
            ->where('time_spent', '>', 0)
            ->avg('time_spent') ?? 0;

        // Recent activity
        $recentProgress = Progress::where('lesson_id', $lesson->id)
            ->with('user:id,name,email')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Progress distribution
        $progressDistribution = Progress::where('lesson_id', $lesson->id)
            ->selectRaw('
                COUNT(CASE WHEN progress_percentage = 0 THEN 1 END) as not_started,
                COUNT(CASE WHEN progress_percentage > 0 AND progress_percentage < 100 THEN 1 END) as in_progress,
                COUNT(CASE WHEN progress_percentage = 100 THEN 1 END) as completed
            ')
            ->first();

        // Quiz statistics (if lesson has quizzes)
        $quizStats = [];
        if ($lesson->quizzes->count() > 0) {
            foreach ($lesson->quizzes as $quiz) {
                $attempts = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)->count();
                $avgScore = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)->avg('score_percentage') ?? 0;
                $passRate = $attempts > 0 
                    ? (\App\Models\QuizAttempt::where('quiz_id', $quiz->id)->where('score_percentage', '>=', $quiz->passing_score ?? 70)->count() / $attempts) * 100 
                    : 0;

                $quizStats[] = [
                    'quiz_id' => $quiz->id,
                    'quiz_title' => $quiz->title,
                    'total_attempts' => $attempts,
                    'average_score' => round($avgScore, 2),
                    'pass_rate' => round($passRate, 2),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'lesson' => $lesson,
            'analytics' => [
                'students' => [
                    'total_enrolled' => $totalStudents,
                    'started_lesson' => $studentsStarted,
                    'completed_lesson' => $studentsCompleted,
                ],
                'rates' => [
                    'start_rate' => round($startRate, 2),
                    'completion_rate' => round($completionRate, 2),
                ],
                'engagement' => [
                    'average_time_spent' => round($avgTimeSpent / 60, 2), // Convert to minutes
                ],
                'progress_distribution' => $progressDistribution,
                'recent_activity' => $recentProgress,
                'quiz_statistics' => $quizStats,
            ]
        ]);
    }

    /**
     * Export lesson analytics as CSV (Admin only)
     */
    public function exportAnalytics(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $progressData = Progress::where('lesson_id', $lesson->id)
            ->with('user:id,name,email')
            ->get();

        $csvData = [];
        $csvData[] = ['Student Name', 'Email', 'Status', 'Progress %', 'Time Spent (minutes)', 'Started At', 'Completed At'];

        foreach ($progressData as $progress) {
            $csvData[] = [
                $progress->user->name ?? 'Unknown',
                $progress->user->email ?? 'Unknown',
                ucfirst($progress->status),
                $progress->progress_percentage,
                round(($progress->time_spent ?? 0) / 60, 2),
                $progress->created_at->format('Y-m-d H:i:s'),
                $progress->completed_at ? $progress->completed_at->format('Y-m-d H:i:s') : 'Not completed',
            ];
        }

        $filename = "lesson-{$lesson->id}-analytics-" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
