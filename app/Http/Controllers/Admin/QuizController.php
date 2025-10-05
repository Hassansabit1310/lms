<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes
     */
    public function index(Request $request): View
    {
        $query = Quiz::with(['lesson', 'course', 'questions']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        // Filter by quiz type
        if ($request->filled('quiz_type')) {
            $query->where('quiz_type', $request->quiz_type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $quizzes = $query->orderBy('created_at', 'desc')->paginate(15);
        $courses = Course::orderBy('title')->get();
        $lessons = Lesson::orderBy('title')->get();

        return view('admin.quizzes.index', compact('quizzes', 'courses', 'lessons'));
    }

    /**
     * Show the form for creating a new quiz
     */
    public function create(Request $request): View
    {
        $courses = Course::orderBy('title')->get();
        $lessons = collect();
        
        // If course is specified, get its lessons
        if ($request->filled('course_id')) {
            $lessons = Lesson::where('course_id', $request->course_id)
                ->orderBy('order')
                ->get();
        }

        return view('admin.quizzes.create', compact('courses', 'lessons'));
    }

    /**
     * Store a newly created quiz
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'quiz_type' => 'required|in:multiple_choice,true_false,short_answer,essay,matching,fill_blank,drag_drop',
            'max_attempts' => 'required|integer|min:1|max:10',
            'time_limit_minutes' => 'nullable|integer|min:1|max:300',
            'passing_score' => 'required|numeric|min:0|max:100',
            'randomize_questions' => 'boolean',
            'show_correct_answers' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answers' => 'required|array',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.points' => 'required|numeric|min:0.1|max:100',
        ]);

        // Create quiz
        $quiz = Quiz::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'course_id' => $validated['course_id'],
            'lesson_id' => $validated['lesson_id'],
            'quiz_type' => $validated['quiz_type'],
            'settings' => [
                'instructions' => $validated['description'] ?? '',
                'show_progress' => true,
                'allow_review' => true,
            ],
            'max_attempts' => $validated['max_attempts'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'passing_score' => $validated['passing_score'],
            'randomize_questions' => $validated['randomize_questions'] ?? false,
            'show_correct_answers' => $validated['show_correct_answers'] ?? true,
            'is_required' => $validated['is_required'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'order' => 0,
        ]);

        // Create questions
        foreach ($validated['questions'] as $index => $questionData) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $questionData['question'],
                'question_type' => $questionData['question_type'],
                'options' => $questionData['options'] ?? [],
                'correct_answers' => $questionData['correct_answers'],
                'explanation' => $questionData['explanation'],
                'points' => $questionData['points'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        return redirect()
            ->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully with ' . count($validated['questions']) . ' questions!');
    }

    /**
     * Display the specified quiz
     */
    public function show(Quiz $quiz): View
    {
        $quiz->load(['questions', 'lesson', 'course', 'attempts.user']);
        
        $statistics = $quiz->getStatistics();
        
        return view('admin.quizzes.show', compact('quiz', 'statistics'));
    }

    /**
     * Show the form for editing the specified quiz
     */
    public function edit(Quiz $quiz): View
    {
        $quiz->load(['questions']);
        $courses = Course::orderBy('title')->get();
        $lessons = Lesson::orderBy('title')->get();

        return view('admin.quizzes.edit', compact('quiz', 'courses', 'lessons'));
    }

    /**
     * Update the specified quiz
     */
    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'quiz_type' => 'required|in:multiple_choice,true_false,short_answer,essay,matching,fill_blank,drag_drop',
            'max_attempts' => 'required|integer|min:1|max:10',
            'time_limit_minutes' => 'nullable|integer|min:1|max:300',
            'passing_score' => 'required|numeric|min:0|max:100',
            'randomize_questions' => 'boolean',
            'show_correct_answers' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $quiz->update($validated);

        return redirect()
            ->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified quiz
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully!');
    }

    /**
     * Preview quiz (read-only view)
     */
    public function preview(Quiz $quiz)
    {
        $quiz->load(['questions' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }, 'course', 'lesson']);

        return view('admin.quizzes.preview', compact('quiz'));
    }

    /**
     * Assign quiz to a lesson
     */
    public function assignToLesson(Request $request, Quiz $quiz): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        // Verify the lesson belongs to the same course as the quiz
        $lesson = Lesson::find($validated['lesson_id']);
        if ($lesson->course_id !== $quiz->course_id) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson must belong to the same course as the quiz.'
            ], 400);
        }

        $quiz->update(['lesson_id' => $validated['lesson_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz successfully assigned to lesson.',
            'lesson' => $lesson->only(['id', 'title'])
        ]);
    }

    /**
     * Assign quiz to a course
     */
    public function assignToCourse(Request $request, Quiz $quiz): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::find($validated['course_id']);
        
        $quiz->update([
            'course_id' => $validated['course_id'],
            'lesson_id' => null // Reset lesson if it was set
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz successfully assigned to course.',
            'course' => $course->only(['id', 'title'])
        ]);
    }

    /**
     * Get lessons for a course (AJAX)
     */
    public function getLessonsForCourse(Request $request): JsonResponse
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json(['lessons' => []]);
        }

        $lessons = Lesson::where('course_id', $courseId)
            ->orderBy('order')
            ->select('id', 'title', 'order')
            ->get();

        return response()->json(['lessons' => $lessons]);
    }

    /**
     * Duplicate a quiz
     */
    public function duplicate(Quiz $quiz): RedirectResponse
    {
        $newQuiz = $quiz->duplicate();

        return redirect()
            ->route('admin.quizzes.edit', $newQuiz)
            ->with('success', 'Quiz duplicated successfully! You can now edit the copy.');
    }

    /**
     * Toggle quiz active status
     */
    public function toggleActive(Quiz $quiz): JsonResponse
    {
        $quiz->update(['is_active' => !$quiz->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $quiz->is_active,
            'message' => $quiz->is_active ? 'Quiz activated' : 'Quiz deactivated'
        ]);
    }

    /**
     * Get quiz statistics (AJAX)
     */
    public function getStatistics(Quiz $quiz): JsonResponse
    {
        return response()->json([
            'success' => true,
            'statistics' => $quiz->getStatistics()
        ]);
    }

    /**
     * Export quiz results
     */
    public function exportResults(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with(['user'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'quiz_results_' . $quiz->id . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attempts) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Student Name',
                'Email',
                'Attempt Number',
                'Score (%)',
                'Points Earned',
                'Points Possible',
                'Passed',
                'Time Spent (minutes)',
                'Completed At'
            ]);

            foreach ($attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->attempt_number,
                    number_format($attempt->score, 2),
                    $attempt->points_earned,
                    $attempt->points_possible,
                    $attempt->is_passed ? 'Yes' : 'No',
                    round($attempt->time_spent_seconds / 60, 2),
                    $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}