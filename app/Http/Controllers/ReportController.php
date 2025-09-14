<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Payment;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Revenue report page
     */
    public function revenue(Request $request)
    {
        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // Revenue statistics
        $totalRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');

        $yearlyRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->sum('amount');

        // Revenue by course
        $revenueByCourse = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('course')
            ->get()
            ->groupBy('course_id')
            ->map(function ($payments) {
                return [
                    'course' => $payments->first()->course,
                    'total_revenue' => $payments->sum('amount'),
                    'total_sales' => $payments->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);

        // Daily revenue for chart
        $dailyRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date');

        return view('admin.reports.revenue', compact(
            'totalRevenue',
            'monthlyRevenue', 
            'yearlyRevenue',
            'revenueByCourse',
            'dailyRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Enrollments report page
     */
    public function enrollments(Request $request)
    {
        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // Enrollment statistics
        $totalEnrollments = Enrollment::whereBetween('created_at', [$startDate, $endDate])->count();
        $monthlyEnrollments = Enrollment::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $yearlyEnrollments = Enrollment::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->count();

        // Enrollments by course
        $enrollmentsByCourse = Enrollment::whereBetween('created_at', [$startDate, $endDate])
            ->with('course')
            ->get()
            ->groupBy('course_id')
            ->map(function ($enrollments) {
                return [
                    'course' => $enrollments->first()->course,
                    'total_enrollments' => $enrollments->count(),
                    'completion_rate' => $enrollments->where('status', 'completed')->count() / $enrollments->count() * 100,
                ];
            })
            ->sortByDesc('total_enrollments')
            ->take(10);

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Daily enrollments for chart
        $dailyEnrollments = Enrollment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as enrollments')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('enrollments', 'date');

        return view('admin.reports.enrollments', compact(
            'totalEnrollments',
            'monthlyEnrollments',
            'yearlyEnrollments',
            'enrollmentsByCourse',
            'recentEnrollments',
            'dailyEnrollments',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Users report page
     */
    public function users(Request $request)
    {
        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // User statistics
        $totalUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $monthlyUsers = User::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $yearlyUsers = User::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->count();

        // Users by role
        $usersByRole = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role');

        // Recent users
        $recentUsers = User::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Daily user registrations for chart
        $dailyUsers = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as users')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('users', 'date');

        return view('admin.reports.users', compact(
            'totalUsers',
            'monthlyUsers',
            'yearlyUsers',
            'usersByRole',
            'recentUsers',
            'dailyUsers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Courses report page
     */
    public function courses(Request $request)
    {
        // Course statistics
        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();
        $draftCourses = Course::where('status', 'draft')->count();

        // Most popular courses
        $popularCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(10)
            ->get();

        // Courses by category
        $coursesByCategory = Course::with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($courses) {
                return $courses->count();
            });

        return view('admin.reports.courses', compact(
            'totalCourses',
            'publishedCourses',
            'draftCourses',
            'popularCourses',
            'coursesByCategory'
        ));
    }
}
