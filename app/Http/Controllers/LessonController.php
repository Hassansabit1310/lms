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
                // Handle H5P file upload if present
                if (request()->hasFile('h5p_file')) {
                    $file = request()->file('h5p_file');
                    $path = $file->store('h5p-content', 'public');
                    $contentData = [
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
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
        
        return $lesson->contents()->create([
            'content_type' => $blockData['type'] === 'youtube' || $blockData['type'] === 'vimeo' ? 'video' : $blockData['type'],
            'content_data' => $contentData,
            'settings' => $settings,
            'order' => $order,
            'is_active' => true
        ]);
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
        $lesson->load(['contents', 'quizzes.questions', 'progress']);
        
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
}
