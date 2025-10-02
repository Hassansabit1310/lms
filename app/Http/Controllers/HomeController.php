<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Bundle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // Cache homepage data for 10 minutes to improve performance
        $featuredCourses = Cache::remember('homepage.featured_courses', 600, function () {
            $query = Course::select(['id', 'title', 'slug', 'short_description', 'thumbnail', 'price', 'is_free', 'category_id', 'created_at'])
                ->with(['category:id,name,slug']);
                
            // Only filter by status if the column exists
            if (Schema::hasColumn('courses', 'status')) {
                $query->where('status', 'published');
            }
            
            return $query->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        });

        $categories = Cache::remember('homepage.categories', 1800, function () {
            // Start with basic columns that should always exist
            $baseColumns = ['id', 'name', 'slug'];
            
            // Check if optional columns exist before selecting them
            $columns = $baseColumns;
            if (Schema::hasColumn('categories', 'icon')) {
                $columns[] = 'icon';
            }
            if (Schema::hasColumn('categories', 'color')) {
                $columns[] = 'color';
            }
            
            return Category::select($columns)
                ->whereNull('parent_id')
                ->orderBy('id')
                ->limit(8)
                ->get();
        });

        

        $featuredBundles = Cache::remember('homepage.featured_bundles', 600, function () {
            // Check if Bundle model exists and has the methods
            if (!class_exists(Bundle::class)) {
                return collect([]);
            }
            
            try {
                return Bundle::select(['id', 'name', 'slug', 'description', 'price', 'original_price', 'created_at'])
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
            } catch (\Exception $e) {
                // Return empty collection if Bundle queries fail
                return collect([]);
            }
        });

        // Cache stats for 30 minutes since they don't change frequently
        $stats = Cache::remember('homepage.stats', 1800, function () {
            $stats = [];
            
            // Course count - check if status column exists
            if (Schema::hasColumn('courses', 'status')) {
                $stats['total_courses'] = Course::where('status', 'published')->count();
            } else {
                $stats['total_courses'] = Course::count();
            }
            
            // User counts - check if role column exists
            if (Schema::hasColumn('users', 'role')) {
                $stats['total_students'] = \App\Models\User::where('role', 'student')->count();
                $stats['total_instructors'] = \App\Models\User::where('role', 'instructor')->count();
            } else {
                $stats['total_students'] = \App\Models\User::count();
                $stats['total_instructors'] = 0;
            }
            
            // Enrollment count - check if table exists
            if (Schema::hasTable('enrollments')) {
                $stats['total_enrollments'] = \App\Models\Enrollment::count();
            } else {
                $stats['total_enrollments'] = 0;
            }
            
            // Bundle count - check if class and table exist
            if (class_exists(Bundle::class) && Schema::hasTable('bundles')) {
                if (Schema::hasColumn('bundles', 'is_active')) {
                    $stats['total_bundles'] = Bundle::where('is_active', true)->count();
                } else {
                    $stats['total_bundles'] = Bundle::count();
                }
            } else {
                $stats['total_bundles'] = 0;
            }
            
            return $stats;
        });

        return view('home', compact('featuredCourses', 'categories', 'featuredBundles', 'stats'));
    }
}