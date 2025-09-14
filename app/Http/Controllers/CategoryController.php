<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->withCount('courses')
            ->with(['children' => function ($query) {
                $query->withCount('courses');
            }])
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $courses = Course::with(['category', 'reviews'])
            ->where('category_id', $category->id)
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when(request('level'), function ($query, $level) {
                $query->where('level', $level);
            })
            ->when(request('price'), function ($query, $price) {
                if ($price === 'free') {
                    $query->where('is_free', true);
                } elseif ($price === 'paid') {
                    $query->where('is_free', false);
                }
            })
            ->when(request('sort'), function ($query, $sort) {
                switch ($sort) {
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
                        $query->latest();
                }
            }, function ($query) {
                $query->latest();
            })
            ->paginate(12);

        $subcategories = Category::where('parent_id', $category->id)
            ->withCount('courses')
            ->get();

        return view('categories.show', compact('category', 'courses', 'subcategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
