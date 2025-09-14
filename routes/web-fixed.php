<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public course routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Course browsing and enrollment
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');
    
    // Lesson viewing
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/courses/{course}/lessons/{lesson}/progress', [LessonController::class, 'updateProgress'])->name('lessons.progress');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'markComplete'])->name('lessons.complete');
    
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
    
    // Debug route for testing
    Route::get('/debug/lesson-test/{course}', function(Course $course) {
        return view('admin.lessons.create-multi', [
            'course' => $course,
            'nextOrder' => $course->lessons()->max('order') + 1
        ]);
    })->name('debug.lesson-test');
    
    // Debug POST route to see what data is being sent
    Route::post('/debug/lesson-data/{course}', function(Request $request, Course $course) {
        dd([
            'request_data' => $request->all(),
            'has_content_blocks' => $request->has('content_blocks'),
            'content_blocks' => $request->get('content_blocks'),
            'validation_errors' => null
        ]);
    })->name('debug.lesson-data');
    Route::resource('courses.lessons', LessonController::class)->except(['index', 'show', 'create'])->shallow();
    Route::post('/lessons/reorder', [LessonController::class, 'reorder'])->name('lessons.reorder');
    
    // Category management - FIXED
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
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
    
    // H5P management
    Route::get('/h5p', [\App\Http\Controllers\H5PController::class, 'index'])->name('h5p.index');
    Route::get('/h5p/create', [\App\Http\Controllers\H5PController::class, 'create'])->name('h5p.create');
    Route::post('/h5p', [\App\Http\Controllers\H5PController::class, 'store'])->name('h5p.store');
    Route::get('/h5p/{h5p}', [\App\Http\Controllers\H5PController::class, 'show'])->name('h5p.show');
    Route::get('/h5p/{h5p}/edit', [\App\Http\Controllers\H5PController::class, 'edit'])->name('h5p.edit');
    Route::patch('/h5p/{h5p}', [\App\Http\Controllers\H5PController::class, 'update'])->name('h5p.update');
    Route::delete('/h5p/{h5p}', [\App\Http\Controllers\H5PController::class, 'destroy'])->name('h5p.destroy');
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

require __DIR__.'/auth.php';
