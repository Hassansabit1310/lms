<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Student Dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's enrollments with progress
        $enrollments = $user->enrollments()
            ->with(['course' => function($query) {
                $query->with('lessons');
            }])
            ->latest()
            ->get();

        // Calculate stats
        $stats = [
            'total_enrollments' => $enrollments->count(),
            'completed_courses' => $enrollments->where('completed_at', '!=', null)->count(),
            'in_progress' => $enrollments->where('completed_at', null)->count(),
            'total_time' => $enrollments->sum(function($enrollment) {
                return $enrollment->course->duration_minutes ?? 0;
            }),
        ];

        return view('dashboard', compact('enrollments', 'stats'));
    }

    /**
     * Admin Dashboard
     */
    public function admin()
    {
        // Admin statistics
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'new_enrollments_this_month' => Enrollment::whereMonth('created_at', now()->month)->count(),
        ];

        // Recent activities
        $recent_enrollments = Enrollment::with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get();

        $recent_users = User::latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_enrollments', 'recent_users'));
    }

    /**
     * Instructor Dashboard
     */
    public function instructor()
    {
        $user = Auth::user();
        
        // For now, since we don't have instructor_id in courses table,
        // we'll show general instructor stats and available courses
        $courses = Course::withCount(['enrollments', 'reviews'])
            ->with('reviews')
            ->latest()
            ->take(10)
            ->get();

        // Calculate stats (placeholder data for instructor)
        $stats = [
            'total_courses' => 0, // Will be updated when instructor functionality is fully implemented
            'total_students' => 0,
            'total_revenue' => 0,
            'average_rating' => 0,
        ];

        return view('instructor.dashboard', compact('courses', 'stats'));
    }

    /**
     * Users Management (Admin)
     */
    public function users()
    {
        $users = User::with(['enrollments', 'subscriptions'])
            ->withCount(['enrollments', 'subscriptions'])
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when(request('role'), function($query, $role) {
                $query->where('role', $role);
            })
            ->when(request('status'), function($query, $status) {
                if ($status === 'verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($status === 'unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(request('date_from'), function($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when(request('date_to'), function($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->when(request('sort'), function($query, $sort) {
                switch($sort) {
                    case 'name':
                        $query->orderBy('name');
                        break;
                    case 'email':
                        $query->orderBy('email');
                        break;
                    case 'created':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'enrollments':
                        $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
                        break;
                    default:
                        $query->latest();
                }
            }, function($query) {
                $query->latest();
            })
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show Create User Form (Admin)
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store New User (Admin)
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,student,instructor',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'email_verified_at' => $request->has('verified') ? now() : null,
        ]);

        // Assign Spatie role
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show Edit User Form (Admin)
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update User (Admin)
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,student,instructor',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Handle email verification
        if ($request->has('verified') && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        } elseif (!$request->has('verified') && $user->email_verified_at) {
            $updateData['email_verified_at'] = null;
        }

        $user->update($updateData);

        // Update Spatie role if changed
        if ($user->role !== $request->role) {
            $user->syncRoles([$request->role]);
            
            // Clear user sessions if role changed
            $this->clearUserSessions($user);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete User (Admin)
     */
    public function destroyUser(User $user)
    {
        // Prevent deletion of own account
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account!');
        }

        // Store user info for success message
        $userName = $user->name;
        
        // Delete user (will cascade to related records due to foreign keys)
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully!");
    }

    /**
     * Clear user sessions
     */
    private function clearUserSessions(User $user)
    {
        try {
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath)) {
                $files = glob($sessionPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $content = file_get_contents($file);
                        if (strpos($content, $user->id) !== false || strpos($content, $user->email) !== false) {
                            unlink($file);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Session clearing failed, but continue
        }
    }

    /**
     * Show User Details (Admin)
     */
    public function showUser(User $user)
    {
        $user->load(['enrollments.course', 'subscriptions', 'payments.course']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Update User Role (Admin)
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,student,instructor'
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;
        
        // Update database role column
        $user->update(['role' => $newRole]);
        
        // Sync Spatie role - remove all existing roles and assign the new one
        $user->syncRoles([$newRole]);
        
        // Refresh the user model to ensure changes are loaded
        $user->refresh();
        
        // Clear user sessions to force re-authentication with new role
        try {
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath)) {
                $files = glob($sessionPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $content = file_get_contents($file);
                        // Check if this session belongs to the user (basic check)
                        if (strpos($content, $user->id) !== false || strpos($content, $user->email) !== false) {
                            unlink($file);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Session clearing failed, but role update succeeded
        }
        
        // Log the change for debugging
        \Log::info("User role updated", [
            'user_id' => $user->id,
            'email' => $user->email,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'db_role_after' => $user->role,
            'spatie_roles_after' => $user->getRoleNames()->toArray()
        ]);

        return redirect()->back()->with('success', "User role updated from '{$oldRole}' to '{$newRole}' successfully!");
    }

    /**
     * Subscriptions Management (Admin)
     */
    public function subscriptions()
    {
        $subscriptions = Subscription::with('user')
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Cancel Subscription (Admin)
     */
    public function cancelSubscription(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now()
        ]);

        return redirect()->back()->with('success', 'Subscription cancelled successfully!');
    }

    /**
     * Enrollment Reports (Admin)
     */
    public function enrollmentReports()
    {
        $enrollments = Enrollment::with(['user', 'course'])
            ->when(request('from'), function($query, $from) {
                $query->whereDate('enrolled_at', '>=', $from);
            })
            ->when(request('to'), function($query, $to) {
                $query->whereDate('enrolled_at', '<=', $to);
            })
            ->latest('enrolled_at')
            ->paginate(50);

        // Monthly enrollment stats
        $monthly_stats = Enrollment::selectRaw('MONTH(enrolled_at) as month, COUNT(*) as count')
            ->whereYear('enrolled_at', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('admin.reports.enrollments', compact('enrollments', 'monthly_stats'));
    }

    /**
     * Revenue Reports (Admin)
     */
    public function revenueReports()
    {
        $payments = Payment::with(['user', 'course'])
            ->where('status', 'completed')
            ->when(request('from'), function($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when(request('to'), function($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest()
            ->paginate(50);

        // Monthly revenue stats
        $monthly_revenue = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return view('admin.reports.revenue', compact('payments', 'monthly_revenue'));
    }
}
