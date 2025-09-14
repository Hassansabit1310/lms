<?php

use App\Http\Controllers\LessonController;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Debug routes for lesson testing
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Working multi-content lesson creator
    Route::get('/courses/{course}/lessons/create-multi-fixed', function(Course $course) {
        return view('admin.lessons.create-multi-fixed', [
            'course' => $course,
            'nextOrder' => $course->lessons()->max('order') + 1
        ]);
    })->name('courses.lessons.create-multi-fixed');
    
    // Debug POST route to see what data is being sent
    Route::post('/debug/lesson-data/{course}', function(Request $request, Course $course) {
        dd([
            'request_data' => $request->all(),
            'has_content_blocks' => $request->has('content_blocks'),
            'content_blocks' => $request->get('content_blocks'),
            'course' => $course->toArray(),
            'validation_errors' => null
        ]);
    })->name('debug.lesson-data');
    
});
