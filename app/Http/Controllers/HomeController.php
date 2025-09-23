<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Bundle;

class HomeController extends Controller
{
    public function index()
    {
        // Simplified version to avoid database issues during initial setup
        $featuredCourses = Course::with(['category', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->limit(8)
            ->get();

        // Get featured bundles for homepage
        $featuredBundles = Bundle::featured()
            ->available()
            ->with(['courses' => function($query) {
                $query->where('courses.status', 'published')
                      ->orderBy('bundle_courses.order')
                      ->limit(3); // Show only first 3 courses in the preview
            }])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $stats = [
            'total_courses' => Course::count(),
            'total_students' => \App\Models\User::where('role', 'student')->count(),
            'total_instructors' => \App\Models\User::where('role', 'instructor')->count(),
            'total_enrollments' => \App\Models\Enrollment::count(),
            'total_bundles' => Bundle::active()->count(),
        ];

        return view('home', compact('featuredCourses', 'categories', 'featuredBundles', 'stats'));
    }
}
