<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 50%, #A855F7 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    Quiz Management
                </h2>
                <p style="color: rgba(255, 255, 255, 0.9) !important; font-size: 1.1rem !important; font-weight: 500 !important;">
                    Create and manage interactive quizzes for your courses
                </p>
            </div>
        </div>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quiz Management</h1>
                <p class="text-gray-600 mt-2">Create and manage interactive quizzes for your courses</p>
            </div>
            <a href="{{ route('admin.quizzes.create') }}" 
               class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Quiz
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search quizzes..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <select id="course_id" 
                        name="course_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="quiz_type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select id="quiz_type" 
                        name="quiz_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Types</option>
                    <option value="multiple_choice" {{ request('quiz_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                    <option value="true_false" {{ request('quiz_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                    <option value="short_answer" {{ request('quiz_type') == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                    <option value="essay" {{ request('quiz_type') == 'essay' ? 'selected' : '' }}>Essay</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Quiz List -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        @if($quizzes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Quiz</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Course/Lesson</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Questions</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Settings</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($quizzes as $quiz)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $quiz->title }}</h3>
                                        @if($quiz->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($quiz->description, 100) }}</p>
                                        @endif
                                        <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
                                            <span><i class="fas fa-calendar mr-1"></i>{{ $quiz->created_at->format('M j, Y') }}</span>
                                            <span class="badge badge-{{ $quiz->quiz_type === 'multiple_choice' ? 'primary' : 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $quiz->quiz_type)) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($quiz->course)
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900">{{ $quiz->course->title }}</div>
                                            @if($quiz->lesson)
                                                <div class="text-gray-600">{{ $quiz->lesson->title }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">Standalone Quiz</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $quiz->questions_count ?? $quiz->questions->count() }} Questions</div>
                                        <div class="text-gray-600">{{ $quiz->getTotalPoints() }} Points</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs space-y-1">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-redo mr-1"></i>{{ $quiz->max_attempts }} attempts
                                        </div>
                                        @if($quiz->time_limit_minutes)
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-clock mr-1"></i>{{ $quiz->time_limit_minutes }}min limit
                                            </div>
                                        @endif
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-trophy mr-1"></i>{{ $quiz->passing_score }}% to pass
                                        </div>
                                        @if($quiz->is_required)
                                            <span class="badge badge-warning">Required</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge {{ $quiz->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors text-sm">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                        <a href="{{ route('admin.quizzes.preview', $quiz) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors text-sm">
                                            <i class="fas fa-search mr-1"></i>
                                            Preview
                                        </a>
                                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                                           class="text-green-600 hover:text-green-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.quizzes.duplicate', $quiz) }}" 
                                           class="text-purple-600 hover:text-purple-800" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        @if(!$quiz->lesson_id)
                                            @if($quiz->course_id)
                                                <button type="button" 
                                                        onclick="showAssignLessonModal({{ $quiz->id }}, {{ $quiz->course_id }})"
                                                        class="inline-flex items-center px-3 py-1 bg-teal-100 text-teal-700 rounded-md hover:bg-teal-200 transition-colors text-sm" 
                                                        title="Assign to Lesson">
                                                    <i class="fas fa-link mr-1"></i>
                                                    Assign to Lesson
                                                </button>
                                            @else
                                                <button type="button" 
                                                        onclick="showAssignCourseModal({{ $quiz->id }})"
                                                        class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-700 rounded-md hover:bg-amber-200 transition-colors text-sm" 
                                                        title="Assign to Course">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    Assign to Course
                                                </button>
                                            @endif
                                        @endif
                                        <button type="button" 
                                                onclick="toggleQuizStatus({{ $quiz->id }})"
                                                class="text-{{ $quiz->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $quiz->is_active ? 'orange' : 'green' }}-800" 
                                                title="{{ $quiz->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $quiz->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this quiz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($quizzes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $quizzes->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No quizzes found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first interactive quiz.</p>
                <a href="{{ route('admin.quizzes.create') }}" 
                   class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Your First Quiz
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Assign to Course Modal -->
<div id="assignCourseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Assign Quiz to Course</h3>
                <button type="button" onclick="hideAssignCourseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="assignCourseForm">
                <div class="mb-4">
                    <label for="course_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Course
                    </label>
                    <select id="course_select" name="course_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Loading courses...</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideAssignCourseModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Assign Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign to Lesson Modal -->
<div id="assignLessonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Assign Quiz to Lesson</h3>
                <button type="button" onclick="hideAssignLessonModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="assignLessonForm">
                <div class="mb-4">
                    <label for="lesson_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Lesson
                    </label>
                    <select id="lesson_select" name="lesson_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Loading lessons...</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideAssignLessonModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Assign Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
async function toggleQuizStatus(quizId) {
    try {
        const response = await fetch(`/admin/quizzes/${quizId}/toggle-active`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload the page to update the UI
            window.location.reload();
        } else {
            alert('Failed to update quiz status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update quiz status');
    }
}

let currentQuizId = null;

async function showAssignCourseModal(quizId) {
    currentQuizId = quizId;
    const modal = document.getElementById('assignCourseModal');
    const courseSelect = document.getElementById('course_select');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Load available courses
    try {
        courseSelect.innerHTML = '<option value="">Loading courses...</option>';
        
        // We'll need to create an endpoint for this, but for now let's use a simple approach
        const response = await fetch('/admin/api/courses', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            courseSelect.innerHTML = '<option value="">Select a course</option>';
            
            if (data.courses && data.courses.length > 0) {
                data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.title;
                    courseSelect.appendChild(option);
                });
            } else {
                courseSelect.innerHTML = '<option value="">No courses available</option>';
            }
        } else {
            throw new Error('Failed to load courses');
        }
    } catch (error) {
        console.error('Error loading courses:', error);
        // Fallback: Load courses from the page data if available
        courseSelect.innerHTML = '<option value="">Select a course</option>';
        @foreach($courses ?? [] as $course)
            const option{{ $course->id }} = document.createElement('option');
            option{{ $course->id }}.value = {{ $course->id }};
            option{{ $course->id }}.textContent = '{{ addslashes($course->title) }}';
            courseSelect.appendChild(option{{ $course->id }});
        @endforeach
    }
}

function hideAssignCourseModal() {
    document.getElementById('assignCourseModal').classList.add('hidden');
    currentQuizId = null;
}

async function showAssignLessonModal(quizId, courseId) {
    currentQuizId = quizId;
    const modal = document.getElementById('assignLessonModal');
    const lessonSelect = document.getElementById('lesson_select');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Load lessons for the course
    try {
        lessonSelect.innerHTML = '<option value="">Loading lessons...</option>';
        
        const response = await fetch(`{{ route('admin.api.lessons-for-course') }}?course_id=${courseId}`);
        const data = await response.json();
        
        lessonSelect.innerHTML = '<option value="">Select a lesson</option>';
        
        if (data.lessons && data.lessons.length > 0) {
            data.lessons.forEach(lesson => {
                const option = document.createElement('option');
                option.value = lesson.id;
                option.textContent = lesson.title;
                lessonSelect.appendChild(option);
            });
        } else {
            lessonSelect.innerHTML = '<option value="">No lessons available</option>';
        }
    } catch (error) {
        console.error('Error loading lessons:', error);
        lessonSelect.innerHTML = '<option value="">Error loading lessons</option>';
    }
}

function hideAssignLessonModal() {
    document.getElementById('assignLessonModal').classList.add('hidden');
    currentQuizId = null;
}

// Handle form submission
document.getElementById('assignLessonForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentQuizId) return;
    
    const formData = new FormData(this);
    const lessonId = formData.get('lesson_id');
    
    if (!lessonId) {
        alert('Please select a lesson');
        return;
    }
    
    try {
        const response = await fetch(`/admin/quizzes/${currentQuizId}/assign-lesson`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ lesson_id: lessonId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Quiz successfully assigned to lesson!');
            hideAssignLessonModal();
            window.location.reload();
        } else {
            alert(data.message || 'Failed to assign quiz to lesson');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to assign quiz to lesson');
    }
});

// Handle course assignment form submission
document.getElementById('assignCourseForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentQuizId) return;
    
    const formData = new FormData(this);
    const courseId = formData.get('course_id');
    
    if (!courseId) {
        alert('Please select a course');
        return;
    }
    
    try {
        const response = await fetch(`/admin/quizzes/${currentQuizId}/assign-course`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ course_id: courseId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Quiz successfully assigned to course!');
            hideAssignCourseModal();
            window.location.reload();
        } else {
            alert(data.message || 'Failed to assign quiz to course');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to assign quiz to course');
    }
});
</script>
</x-app-layout>
