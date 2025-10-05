<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\H5PController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Course;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// DEBUG: Admin access checker (REMOVE AFTER FIXING)
Route::get('/debug/admin-access', function() {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Not authenticated',
            'login_url' => route('login')
        ]);
    }
    
    return response()->json([
        'status' => 'success',
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'database_role' => $user->role,
            'spatie_roles' => $user->getRoleNames()->toArray(),
            'has_admin_role' => $user->hasRole('admin'),
            'is_admin_method' => $user->isAdmin(),
            'email_verified' => !is_null($user->email_verified_at),
        ],
        'middleware_requirements' => [
            'auth' => true,
            'verified' => !is_null($user->email_verified_at),
            'role:admin' => $user->hasRole('admin'),
        ],
        'admin_dashboard_url' => route('admin.dashboard'),
        'can_access_admin' => auth()->check() && $user->email_verified_at && $user->hasRole('admin'),
    ]);
})->middleware('auth')->name('debug.admin-access');

// EMERGENCY ADMIN FIX ROUTE (REMOVE AFTER FIXING)
Route::get('/emergency-admin-fix', function() {
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $user = auth()->user();
    
    try {
        // Step 1: Ensure roles exist
        if (!Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
            Spatie\Permission\Models\Role::create(['name' => 'admin']);
        }
        if (!Spatie\Permission\Models\Role::where('name', 'student')->exists()) {
            Spatie\Permission\Models\Role::create(['name' => 'student']);
        }
        if (!Spatie\Permission\Models\Role::where('name', 'instructor')->exists()) {
            Spatie\Permission\Models\Role::create(['name' => 'instructor']);
        }
        
        // Step 2: Fix current user
        $user->update([
            'role' => 'admin',
            'email_verified_at' => now()
        ]);
        
        // Step 3: Sync Spatie roles
        $user->syncRoles(['admin']);
        
        // Step 4: Verify the fix
        $user = $user->fresh();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Admin access fixed!',
            'user' => [
                'email' => $user->email,
                'database_role' => $user->role,
                'spatie_roles' => $user->getRoleNames()->toArray(),
                'has_admin_role' => $user->hasRole('admin'),
                'email_verified' => !is_null($user->email_verified_at),
            ],
            'next_step' => 'Try accessing /admin/dashboard now',
            'admin_url' => route('admin.dashboard')
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Fix failed: ' . $e->getMessage(),
            'suggestion' => 'Try the manual SQL approach'
        ]);
    }
})->middleware('auth')->name('emergency.admin.fix');

// Vite asset serving for Railway (fallback)
Route::get('/build/assets/{file}', function ($file) {
    $path = public_path('build/assets/' . $file);
    if (!file_exists($path)) {
        abort(404);
    }
    
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    
    $mimeType = $mimeTypes[$extension] ?? 'text/plain';
    
    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('file', '.*');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// H5P embed route (public access for embedded content)
Route::get('/h5p/embed/{h5pContent}', [H5PController::class, 'embed'])->name('h5p.embed');

// H5P content data API (for rendering)
Route::get('/h5p/content-data/{h5pContent}', [H5PController::class, 'getContentData'])->name('h5p.content-data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public category routes (no auth required)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Public course routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Course browsing and enrollment
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');
    
    // Lesson viewing
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/courses/{course}/lessons/{lesson}/progress', [LessonController::class, 'updateProgress'])->name('lessons.progress');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'markCompleted'])->name('lessons.complete');
    
    // Quiz functionality within lessons
    Route::get('/courses/{course}/lessons/{lesson}/quiz/{quiz}', [LessonController::class, 'showQuiz'])->name('lessons.quiz.show');
    Route::post('/courses/{course}/lessons/{lesson}/quiz/{quiz}/start', function($courseId, $lessonId, $quizId) {
        try {
            $course = \App\Models\Course::findOrFail($courseId);
            $lesson = \App\Models\Lesson::findOrFail($lessonId);
            $quiz = \App\Models\Quiz::findOrFail($quizId);
            $user = auth()->user();
            
            if (!$user || !$lesson->hasAccess($user)) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            if (!$quiz->canUserTake($user)) {
                return response()->json(['error' => 'You cannot take this quiz'], 403);
            }

            // Check if user has an active attempt
            $activeAttempt = $quiz->getActiveAttempt($user);
            if ($activeAttempt) {
                return response()->json([
                    'success' => true,
                    'attempt' => $activeAttempt,
                    'questions' => $quiz->getQuestionsForUser($user),
                    'time_remaining' => $activeAttempt->getRemainingTime(),
                ]);
            }

            // Start new attempt
            $attempt = $quiz->startAttempt($user);
            
            return response()->json([
                'success' => true,
                'attempt' => $attempt,
                'questions' => $quiz->getQuestionsForUser($user),
                'time_remaining' => $attempt->getRemainingTime(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Quiz start error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to start quiz: ' . $e->getMessage()], 500);
        }
    })->name('lessons.quiz.start');
    // Save quiz answer route
    Route::post('/courses/{course}/lessons/{lesson}/quiz/{quiz}/save-answer', function($courseId, $lessonId, $quizId) {
        try {
            $course = \App\Models\Course::findOrFail($courseId);
            $lesson = \App\Models\Lesson::findOrFail($lessonId);
            $quiz = \App\Models\Quiz::findOrFail($quizId);
            $user = auth()->user();
            
            if (!$user || !$lesson->hasAccess($user)) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $request = request();
            $validated = $request->validate([
                'question_id' => 'required|exists:quiz_questions,id',
                'answer' => 'required',
                'attempt_id' => 'required|exists:quiz_attempts,id'
            ]);

            // Get the attempt and verify it belongs to the user
            $attempt = \App\Models\QuizAttempt::where('id', $validated['attempt_id'])
                ->where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->where('status', 'in_progress')
                ->firstOrFail();

            // Save or update the answer using QuizAttempt's updateAnswer method
            $attempt->updateAnswer($validated['question_id'], $validated['answer']);

            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Quiz save answer error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save answer: ' . $e->getMessage()], 500);
        }
    })->name('lessons.quiz.save-answer');

    // Submit quiz route
    Route::post('/courses/{course}/lessons/{lesson}/quiz/{quiz}/submit', function($courseId, $lessonId, $quizId) {
        try {
            $course = \App\Models\Course::findOrFail($courseId);
            $lesson = \App\Models\Lesson::findOrFail($lessonId);
            $quiz = \App\Models\Quiz::findOrFail($quizId);
            $user = auth()->user();
            
            if (!$user || !$lesson->hasAccess($user)) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $request = request();
            $validated = $request->validate([
                'attempt_id' => 'required|exists:quiz_attempts,id',
                'answers' => 'required|array'
            ]);

            // Get the attempt and verify it belongs to the user
            $attempt = \App\Models\QuizAttempt::where('id', $validated['attempt_id'])
                ->where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->first();
                
            if (!$attempt) {
                return response()->json(['error' => 'Quiz attempt not found. Please start the quiz again.'], 404);
            }
            
            if ($attempt->status !== 'in_progress') {
                return response()->json(['error' => 'Quiz attempt is not in progress. Status: ' . $attempt->status], 400);
            }

            // Update answers if provided
            if (!empty($validated['answers'])) {
                $currentAnswers = $attempt->answers ?? [];
                foreach ($validated['answers'] as $questionId => $answer) {
                    $currentAnswers[$questionId] = $answer;
                }
                $attempt->update(['answers' => $currentAnswers]);
            }
            
            // Submit the quiz (this will calculate score and update the attempt)
            $attempt->submit();

            // Refresh the attempt to get updated data
            $attempt->refresh();

            // Update lesson progress
            $progress = \App\Models\LessonProgress::firstOrCreate([
                'user_id' => $user->id,
                'lesson_id' => $lessonId
            ]);

            if (!$progress->completed_at) {
                $progress->update([
                    'completed_at' => now(),
                    'progress_percentage' => 100
                ]);
            }

            return response()->json([
                'success' => true,
                'results' => [
                    'score' => $attempt->points_earned,
                    'total_points' => $attempt->points_possible,
                    'percentage' => $attempt->score,
                    'passed' => $attempt->is_passed,
                    'questions' => $attempt->detailed_results ?? []
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Quiz submit error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to submit quiz: ' . $e->getMessage()], 500);
        }
    })->name('lessons.quiz.submit');
    
    // Payment routes
    Route::get('/courses/{course}/checkout', [PaymentController::class, 'checkout'])->name('courses.checkout');
    Route::post('/courses/{course}/payment', [PaymentController::class, 'processPayment'])->name('courses.payment');
    
    // Reviews
    Route::post('/courses/{course}/reviews', [CourseController::class, 'storeReview'])->name('courses.reviews.store');
    Route::patch('/courses/{course}/reviews/{review}', [CourseController::class, 'updateReview'])->name('courses.reviews.update');
    Route::delete('/courses/{course}/reviews/{review}', [CourseController::class, 'destroyReview'])->name('courses.reviews.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    
    // Course management
    Route::get('/courses', [CourseController::class, 'adminIndex'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'adminCreate'])->name('courses.create');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::resource('courses', CourseController::class)->except(['index', 'show', 'create']);
    Route::post('/courses/bulk-action', [CourseController::class, 'bulkAction'])->name('courses.bulk-action');
    Route::post('/courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');
    
    // Lesson management
    Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])->name('courses.lessons.index');
    Route::get('/courses/{course}/lessons/create', [LessonController::class, 'create'])->name('courses.lessons.create');
    Route::get('/courses/{course}/lessons/create-multi', [LessonController::class, 'createMulti'])->name('courses.lessons.create-multi');
    
    // Working multi-content lesson creator (FIXED VERSION)
    Route::get('/courses/{course}/lessons/create-multi-fixed', function(Course $course) {
        return view('admin.lessons.create-multi-fixed', [
            'course' => $course,
            'nextOrder' => $course->lessons()->max('order') + 1
        ]);
    })->name('courses.lessons.create-multi-fixed');
    
    // WORKING multi-content lesson creator (FINAL VERSION)
    Route::get('/courses/{course}/lessons/create-multi-working', function(Course $course) {
        return view('admin.lessons.create-multi-working', [
            'course' => $course,
            'nextOrder' => $course->lessons()->max('order') + 1
        ]);
    })->name('courses.lessons.create-multi-working');
    
    // Multi-content lesson creator
    Route::get('/courses/{course}/lessons/create-multi', function(Course $course) {
        return view('admin.lessons.create-multi', [
            'course' => $course,
            'nextOrder' => $course->lessons()->max('order') + 1
        ]);
    })->name('courses.lessons.create-multi');
    
    Route::resource('courses.lessons', LessonController::class)->except(['index', 'show', 'create'])->shallow();
    
    // Override the shallow routes to use the correct methods
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'editShallow'])->name('lessons.edit');
    Route::patch('/lessons/{lesson}', [LessonController::class, 'updateShallow'])->name('lessons.update');
    
    // Add missing nested lesson routes
    Route::get('/courses/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('courses.lessons.edit');
    Route::patch('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('courses.lessons.update');
    Route::delete('/courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('courses.lessons.destroy');
    
    // Bundle management (admin only)
    Route::get('/bundles', [\App\Http\Controllers\Admin\BundleController::class, 'index'])->name('bundles.index');
    Route::get('/bundles/create', [\App\Http\Controllers\Admin\BundleController::class, 'create'])->name('bundles.create');
    Route::post('/bundles', [\App\Http\Controllers\Admin\BundleController::class, 'store'])->name('bundles.store');
    Route::get('/bundles/{bundle}', [\App\Http\Controllers\Admin\BundleController::class, 'show'])->name('bundles.show');
    Route::get('/bundles/{bundle}/edit', [\App\Http\Controllers\Admin\BundleController::class, 'edit'])->name('bundles.edit');
    Route::patch('/bundles/{bundle}', [\App\Http\Controllers\Admin\BundleController::class, 'update'])->name('bundles.update');
    Route::delete('/bundles/{bundle}', [\App\Http\Controllers\Admin\BundleController::class, 'destroy'])->name('bundles.destroy');

    // H5P Content Management
    Route::get('/h5p', [H5PController::class, 'index'])->name('h5p.index');
    Route::get('/h5p/create', [H5PController::class, 'create'])->name('h5p.create');
    Route::post('/h5p', [H5PController::class, 'store'])->name('h5p.store');
    Route::get('/h5p/{h5pContent}/edit', [H5PController::class, 'edit'])->name('h5p.edit');
    Route::patch('/h5p/{h5pContent}', [H5PController::class, 'update'])->name('h5p.update');
    Route::delete('/h5p/{h5pContent}', [H5PController::class, 'destroy'])->name('h5p.destroy');
    Route::get('/h5p/available', [H5PController::class, 'getAvailableContent'])->name('h5p.available');
    Route::post('/h5p/{h5pContent}/retry', [H5PController::class, 'retryProcessing'])->name('h5p.retry');
    
    // Quiz Management
    Route::resource('quizzes', \App\Http\Controllers\Admin\QuizController::class);
    Route::get('/quizzes/{quiz}/duplicate', [\App\Http\Controllers\Admin\QuizController::class, 'duplicate'])->name('quizzes.duplicate');
    Route::post('/quizzes/{quiz}/toggle-active', [\App\Http\Controllers\Admin\QuizController::class, 'toggleActive'])->name('quizzes.toggle-active');
    Route::get('/quizzes/{quiz}/statistics', [\App\Http\Controllers\Admin\QuizController::class, 'getStatistics'])->name('quizzes.statistics');
    Route::get('/quizzes/{quiz}/export-results', [\App\Http\Controllers\Admin\QuizController::class, 'exportResults'])->name('quizzes.export-results');
    Route::get('/quizzes/{quiz}/preview', [\App\Http\Controllers\Admin\QuizController::class, 'preview'])->name('quizzes.preview');
    Route::post('/quizzes/{quiz}/assign-lesson', [\App\Http\Controllers\Admin\QuizController::class, 'assignToLesson'])->name('quizzes.assign-lesson');
    Route::post('/quizzes/{quiz}/assign-course', [\App\Http\Controllers\Admin\QuizController::class, 'assignToCourse'])->name('quizzes.assign-course');
    
    // API routes for AJAX calls
    Route::get('/api/lessons-for-course', [\App\Http\Controllers\Admin\QuizController::class, 'getLessonsForCourse'])->name('api.lessons-for-course');
    Route::get('/api/quizzes-for-lesson', [\App\Http\Controllers\LessonController::class, 'getQuizzesForLesson'])->name('api.quizzes-for-lesson');
    
    Route::post('/lessons/reorder', [LessonController::class, 'reorder'])->name('lessons.reorder');
    Route::get('/h5p/available-for-lessons', [LessonController::class, 'getAvailableH5P'])->name('admin.h5p.available');
    
    // Lesson analytics routes (within admin group)
    Route::get('/courses/{course}/lessons/{lesson}/analytics', [LessonController::class, 'analytics'])->name('admin.lessons.analytics');
    Route::get('/courses/{course}/lessons/{lesson}/analytics/export', [LessonController::class, 'exportAnalytics'])->name('admin.lessons.analytics.export');
    
    // Category management (admin only)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Reports
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/enrollments', [ReportController::class, 'enrollments'])->name('reports.enrollments');
    Route::get('/reports/users', [ReportController::class, 'users'])->name('reports.users');
    Route::get('/reports/courses', [ReportController::class, 'courses'])->name('reports.courses');
    
    // User management
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    Route::get('/users/create', [DashboardController::class, 'createUser'])->name('users.create');
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [DashboardController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [DashboardController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/users/{user}/role', [DashboardController::class, 'updateUserRole'])->name('users.role');
    
    // Payment management
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    
    // Manual Payment Admin Routes
    Route::get('/payments/manual/list', [\App\Http\Controllers\ManualPaymentController::class, 'adminIndex'])->name('payments.manual');
    Route::post('/payments/{payment}/approve', [\App\Http\Controllers\ManualPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [\App\Http\Controllers\ManualPaymentController::class, 'reject'])->name('payments.reject');
    
});

// Instructor routes (for course creators)
Route::middleware(['auth', 'verified', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    // Instructor dashboard
    Route::get('/dashboard', [DashboardController::class, 'instructor'])->name('dashboard');
    
    // Course management for instructors
    Route::resource('courses', CourseController::class)->except(['index']);
    
    // Lesson management for instructors
    Route::get('/courses/{course}/lessons', [LessonController::class, 'instructorIndex'])->name('courses.lessons.index');
    Route::resource('courses.lessons', LessonController::class)->except(['show'])->shallow();
});

// Enrollment and Purchase Routes
Route::middleware(['auth'])->group(function () {
    // Course Enrollment
    Route::post('/courses/{course}/enroll-free', [EnrollmentController::class, 'enrollFreeCourse'])->name('enrollments.free');
    Route::post('/courses/{course}/purchase', [EnrollmentController::class, 'purchaseCourse'])->name('enrollments.purchase.course');
    
    // Bundle Purchase
    Route::post('/bundles/{bundle}/purchase', [EnrollmentController::class, 'purchaseBundle'])->name('enrollments.purchase.bundle');
    
    // Subscription Purchase
    Route::post('/subscription/purchase', [EnrollmentController::class, 'purchaseSubscription'])->name('enrollments.purchase.subscription');
    
    // User Enrollments
    Route::get('/my-enrollments', [EnrollmentController::class, 'myEnrollments'])->name('enrollments.index');
    
    // Payment Management
    Route::get('/payments/{payment}/form', [PaymentController::class, 'showForm'])->name('payments.form');
    Route::post('/payments/{payment}/simulate', [PaymentController::class, 'simulatePayment'])->name('payments.simulate');
    Route::get('/payment-history', [PaymentController::class, 'history'])->name('payments.history');
    
    // Manual Payment Routes
    Route::get('/payments/manual/course/{course}', [\App\Http\Controllers\ManualPaymentController::class, 'courseForm'])->name('payments.manual.course');
    Route::get('/payments/manual/bundle/{bundle}', [\App\Http\Controllers\ManualPaymentController::class, 'bundleForm'])->name('payments.manual.bundle');
    Route::post('/payments/manual/submit', [\App\Http\Controllers\ManualPaymentController::class, 'submit'])->name('payments.manual.submit');
    Route::get('/payments/{payment}/status', [\App\Http\Controllers\ManualPaymentController::class, 'status'])->name('payments.manual.status');
});

// Bundle Routes
Route::get('/bundles', [BundleController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{bundle}', [BundleController::class, 'show'])->name('bundles.show');

// Payment Webhooks (for SSLCommerz integration)
Route::post('/payments/{payment}/success', [EnrollmentController::class, 'handlePaymentSuccess'])->name('payments.success');
Route::post('/payments/{payment}/failure', [EnrollmentController::class, 'handlePaymentFailure'])->name('payments.failure');

require __DIR__.'/auth.php';