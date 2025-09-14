<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;

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

        $stats = [
            'total_courses' => Course::count(),
            'total_students' => \App\Models\User::where('role', 'student')->count(),
            'total_instructors' => \App\Models\User::where('role', 'instructor')->count(),
            'total_enrollments' => \App\Models\Enrollment::count(),
        ];

        return view('home', compact('featuredCourses', 'categories', 'stats'));
    }
}
