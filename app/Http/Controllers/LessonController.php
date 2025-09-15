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
                'content_blocks.*.type' => 'required|in:text,youtube,vimeo,h5p,code,quiz',
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
                
            case 'text':
                $contentData = [
                    'content' => $blockData['content']
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
     * Show the form for editing the specified lesson
     */
    public function edit(Course $course, Lesson $lesson): View
    {
        // Authorization handled by role:admin middleware
        
        $lesson->load(['contents', 'quizzes']);
        
        return view('admin.lessons.edit', compact('course', 'lesson'));
    }

    /**
     * Update the specified lesson in storage
     */
    public function update(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        // Authorization handled by role:admin middleware
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:text,youtube,vimeo,h5p,code,pdf,quiz',
            'content' => 'required|string',
            'is_free' => 'boolean',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:1',
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

        // Update the lesson
        $lesson->update($validated);

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
     * Submit quiz answers (AJAX)
     */
    public function submitQuiz(Request $request, Course $course, Lesson $lesson, \App\Models\Quiz $quiz)
    {
        $user = Auth::user();
        
        if (!$user || !$lesson->hasAccess($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        // Create quiz attempt
        $attempt = \App\Models\QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'answers' => $validated['answers'],
            'time_spent' => $validated['time_spent'] ?? 0,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Calculate score
        $totalQuestions = $quiz->questions->where('is_active', true)->count();
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($quiz->questions->where('is_active', true) as $question) {
            $userAnswer = $validated['answers'][$question->id] ?? null;
            $points = $question->calculatePoints($userAnswer);
            
            $totalPoints += $question->points;
            $earnedPoints += $points;
            
            if ($points > 0) {
                $correctAnswers++;
            }
        }

        $scorePercentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;

        // Update attempt with score
        $attempt->update([
            'score_percentage' => $scorePercentage,
            'points_earned' => $earnedPoints,
            'points_total' => $totalPoints,
        ]);

        // Update lesson progress if quiz passed
        if ($scorePercentage >= ($quiz->passing_score ?? 70)) {
            Progress::updateOrCreate(
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
            'attempt' => $attempt,
            'score_percentage' => $scorePercentage,
            'passed' => $scorePercentage >= ($quiz->passing_score ?? 70),
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ]);
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
