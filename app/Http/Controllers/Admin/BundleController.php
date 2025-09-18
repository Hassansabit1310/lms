<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BundleController extends Controller
{
    /**
     * Display a listing of bundles
     */
    public function index(): View
    {
        $bundles = Bundle::with(['courses'])
            ->withCount(['payments' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.bundles.index', compact('bundles'));
    }

    /**
     * Show the form for creating a new bundle
     */
    public function create(): View
    {
        $courses = Course::where('status', 'published')
            ->where('is_free', false) // Only paid courses for bundles
            ->orderBy('title')
            ->get();

        return view('admin.bundles.create', compact('courses'));
    }

    /**
     * Store a newly created bundle
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'max_enrollments' => 'nullable|integer|min:1',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'courses' => 'required|array|min:2', // At least 2 courses for a bundle
            'courses.*' => 'exists:courses,id',
        ]);

        // Calculate original price from selected courses
        $selectedCourses = Course::whereIn('id', $validated['courses'])->get();
        $originalPrice = $selectedCourses->sum('price');

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('bundles', 'public');
            $validated['image'] = $imagePath;
        }

        // Create bundle
        $bundle = Bundle::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'long_description' => $validated['long_description'],
            'price' => $validated['price'],
            'original_price' => $originalPrice,
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'image' => $validated['image'] ?? null,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'max_enrollments' => $validated['max_enrollments'],
            'available_from' => $validated['available_from'],
            'available_until' => $validated['available_until'],
        ]);

        // Attach courses to bundle
        foreach ($selectedCourses as $index => $course) {
            $bundle->courses()->attach($course->id, [
                'order' => $index + 1,
                'individual_price' => $course->price,
                'is_primary' => $index === 0, // First course is primary
            ]);
        }

        return redirect()
            ->route('admin.bundles.index')
            ->with('success', 'Bundle created successfully!');
    }

    /**
     * Display the specified bundle
     */
    public function show(Bundle $bundle): View
    {
        $bundle->load(['courses', 'payments' => function ($query) {
            $query->where('status', 'completed')->with('user');
        }]);

        $stats = [
            'total_sales' => $bundle->payments()->where('status', 'completed')->sum('amount'),
            'total_enrollments' => $bundle->payments()->where('status', 'completed')->count(),
            'conversion_rate' => 0, // Would need tracking of views vs purchases
        ];

        return view('admin.bundles.show', compact('bundle', 'stats'));
    }

    /**
     * Show the form for editing the specified bundle
     */
    public function edit(Bundle $bundle): View
    {
        $bundle->load('courses');
        
        $courses = Course::where('status', 'published')
            ->where('is_free', false)
            ->orderBy('title')
            ->get();

        $bundleCourseIds = $bundle->courses->pluck('id')->toArray();

        return view('admin.bundles.edit', compact('bundle', 'courses', 'bundleCourseIds'));
    }

    /**
     * Update the specified bundle
     */
    public function update(Request $request, Bundle $bundle): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'max_enrollments' => 'nullable|integer|min:1',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'courses' => 'required|array|min:2',
            'courses.*' => 'exists:courses,id',
        ]);

        // Calculate original price from selected courses
        $selectedCourses = Course::whereIn('id', $validated['courses'])->get();
        $originalPrice = $selectedCourses->sum('price');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($bundle->image) {
                \Storage::delete('public/' . $bundle->image);
            }
            $imagePath = $request->file('image')->store('bundles', 'public');
            $validated['image'] = $imagePath;
        } else {
            $validated['image'] = $bundle->image;
        }

        // Update bundle
        $bundle->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'long_description' => $validated['long_description'],
            'price' => $validated['price'],
            'original_price' => $originalPrice,
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'image' => $validated['image'],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'max_enrollments' => $validated['max_enrollments'],
            'available_from' => $validated['available_from'],
            'available_until' => $validated['available_until'],
        ]);

        // Sync courses (remove old, add new with proper pivot data)
        $bundle->courses()->detach();
        foreach ($selectedCourses as $index => $course) {
            $bundle->courses()->attach($course->id, [
                'order' => $index + 1,
                'individual_price' => $course->price,
                'is_primary' => $index === 0,
            ]);
        }

        return redirect()
            ->route('admin.bundles.index')
            ->with('success', 'Bundle updated successfully!');
    }

    /**
     * Remove the specified bundle
     */
    public function destroy(Bundle $bundle): RedirectResponse
    {
        // Check if bundle has any completed payments
        if ($bundle->payments()->where('status', 'completed')->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete bundle with existing purchases.');
        }

        // Delete image if exists
        if ($bundle->image) {
            \Storage::delete('public/' . $bundle->image);
        }

        $bundle->delete();

        return redirect()
            ->route('admin.bundles.index')
            ->with('success', 'Bundle deleted successfully!');
    }
}