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
    Route::post('/courses/{course}/lessons/{lesson}/quiz/{quiz}/submit', [LessonController::class, 'submitQuiz'])->name('lessons.quiz.submit');
    
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
    
    // Add missing nested lesson routes
    Route::get('/courses/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('courses.lessons.edit');
    Route::patch('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('courses.lessons.update');
    Route::delete('/courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('courses.lessons.destroy');
    
    // H5P Content Management
    Route::get('/h5p', [H5PController::class, 'index'])->name('h5p.index');
    Route::get('/h5p/create', [H5PController::class, 'create'])->name('h5p.create');
    Route::post('/h5p', [H5PController::class, 'store'])->name('h5p.store');
    Route::get('/h5p/{h5pContent}/edit', [H5PController::class, 'edit'])->name('h5p.edit');
    Route::patch('/h5p/{h5pContent}', [H5PController::class, 'update'])->name('h5p.update');
    Route::delete('/h5p/{h5pContent}', [H5PController::class, 'destroy'])->name('h5p.destroy');
    Route::get('/h5p/available', [H5PController::class, 'getAvailableContent'])->name('h5p.available');
    Route::post('/h5p/{h5pContent}/retry', [H5PController::class, 'retryProcessing'])->name('h5p.retry');
    
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
});

// Bundle Routes
Route::get('/bundles', [BundleController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{bundle}', [BundleController::class, 'show'])->name('bundles.show');

// Payment Webhooks (for SSLCommerz integration)
Route::post('/payments/{payment}/success', [EnrollmentController::class, 'handlePaymentSuccess'])->name('payments.success');
Route::post('/payments/{payment}/failure', [EnrollmentController::class, 'handlePaymentFailure'])->name('payments.failure');

require __DIR__.'/auth.php';