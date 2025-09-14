<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of courses (public)
     */
    public function index(Request $request)
    {
        $query = Course::with(['category', 'reviews']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categoryId = $request->get('category')) {
            $query->where('category_id', $categoryId);
        }

        // Level filter
        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        // Price filter
        if ($priceFilter = $request->get('price')) {
            if ($priceFilter === 'free') {
                $query->where('is_free', true);
            } elseif ($priceFilter === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'popular':
                $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $courses = $query->paginate(12);
        $categories = Category::whereNull('parent_id')->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        $course->load(['category', 'lessons', 'reviews.user', 'enrollments']);
        
        $userEnrollment = null;
        $userReview = null;
        $hasAccess = false;

        if (auth()->check()) {
            $user = auth()->user();
            $userEnrollment = $course->enrollments()->where('user_id', $user->id)->first();
            $userReview = $course->reviews()->where('user_id', $user->id)->first();
            $hasAccess = $course->hasAccess($user);
        }

        $freeLessons = $course->lessons()->where('is_free', true)->orderBy('order')->get();
        $averageRating = $course->reviews()->avg('rating') ?? 0;
        $totalReviews = $course->reviews()->count();

        return view('courses.show', compact(
            'course', 
            'userEnrollment', 
            'userReview', 
            'hasAccess', 
            'freeLessons',
            'averageRating',
            'totalReviews'
        ));
    }

    /**
     * Show the form for creating a new course (admin/instructor)
     */
    public function create()
    {
        $categories = Category::all();
        return view('courses.create', compact('categories'));
    }

    /**
     * Admin course index
     */
    public function adminIndex(Request $request)
    {
        $query = Course::with(['category', 'enrollments'])
            ->withCount(['lessons', 'enrollments']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $categories = \App\Models\Category::all();
        
        // Statistics
        $stats = [
            'total' => Course::count(),
            'published' => Course::where('status', 'published')->count(),
            'draft' => Course::where('status', 'draft')->count(),
            'revenue' => \App\Models\Payment::where('status', 'completed')->sum('amount')
        ];

        return view('admin.courses.index', compact('courses', 'categories', 'stats'));
    }

    /**
     * Show course creation form
     */
    public function adminCreate()
    {
        $categories = \App\Models\Category::all();
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'required|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'video_url' => 'nullable|url',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array', 
            'prerequisites.*' => 'nullable|string|max:255',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Ensure slug is unique
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;
        
        // Handle free courses - set price to 0 if free
        if ($validated['is_free']) {
            $validated['price'] = 0;
        } else {
            // Ensure price is set for paid courses
            $validated['price'] = $validated['price'] ?? 0;
        }

        // Process learning objectives - filter out empty values
        if (isset($validated['learning_objectives'])) {
            $validated['learning_objectives'] = array_filter($validated['learning_objectives'], function($objective) {
                return !empty(trim($objective));
            });
            // Re-index array to avoid gaps
            $validated['learning_objectives'] = array_values($validated['learning_objectives']);
        }

        // Process prerequisites - filter out empty values  
        if (isset($validated['prerequisites'])) {
            $validated['prerequisites'] = array_filter($validated['prerequisites'], function($prerequisite) {
                return !empty(trim($prerequisite));
            });
            // Re-index array to avoid gaps
            $validated['prerequisites'] = array_values($validated['prerequisites']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course = Course::create($validated);

        // Create first lesson if video URL is provided
        if ($request->filled('video_url')) {
            $videoUrl = $request->video_url;
            $lessonType = 'youtube';
            
            // Detect video type
            if (strpos($videoUrl, 'vimeo.com') !== false) {
                $lessonType = 'vimeo';
            }
            
            // Create the first lesson
            $course->lessons()->create([
                'title' => 'Introduction Video',
                'description' => 'Course introduction video',
                'slug' => Str::slug('Introduction Video'),
                'type' => $lessonType,
                'content' => $videoUrl,
                'order' => 1,
                'is_free' => true, // Make first video free as preview
                'duration_minutes' => null,
            ]);
        }

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Course created successfully!' . ($request->filled('video_url') ? ' First lesson was automatically created from your video URL.' : ''));
    }


    /**
     * Show the form for editing the course
     */
    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:courses,title,' . $course->id,
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'required|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array', 
            'prerequisites.*' => 'nullable|string|max:255',
        ]);

        // Generate unique slug (excluding current course)
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Ensure slug is unique (excluding current course)
        while (Course::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;

        // Handle free courses - set price to 0 if free
        if ($validated['is_free']) {
            $validated['price'] = 0;
        } else {
            // Ensure price is set for paid courses
            $validated['price'] = $validated['price'] ?? 0;
        }

        // Process learning objectives - filter out empty values
        if (isset($validated['learning_objectives'])) {
            $validated['learning_objectives'] = array_filter($validated['learning_objectives'], function($objective) {
                return !empty(trim($objective));
            });
            // Re-index array to avoid gaps
            $validated['learning_objectives'] = array_values($validated['learning_objectives']);
        }

        // Process prerequisites - filter out empty values  
        if (isset($validated['prerequisites'])) {
            $validated['prerequisites'] = array_filter($validated['prerequisites'], function($prerequisite) {
                return !empty(trim($prerequisite));
            });
            // Re-index array to avoid gaps
            $validated['prerequisites'] = array_values($validated['prerequisites']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        // Delete thumbnail
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }
}
