<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 50%, #A855F7 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    ✏️ Edit Course
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">{{ $course->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Tabs -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8" x-data="{ activeTab: 'details' }">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="activeTab = 'details'" 
                                :class="activeTab === 'details' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Course Details
                        </button>
                        <button @click="activeTab = 'lessons'" 
                                :class="activeTab === 'lessons' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Lessons ({{ $course->lessons->count() }})
                        </button>
                        <button @click="activeTab = 'students'" 
                                :class="activeTab === 'students' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Students ({{ $course->enrollments->count() }})
                        </button>
                        <button @click="activeTab = 'analytics'" 
                                :class="activeTab === 'analytics' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Analytics
                        </button>
                        <button @click="activeTab = 'settings'" 
                                :class="activeTab === 'settings' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Settings
                        </button>
                    </nav>
                </div>

                <!-- Course Details Tab -->
                <div x-show="activeTab === 'details'" class="p-6">
                    <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Course Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $course->title) }}"
                                           required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Short Description -->
                                <div>
                                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                                    <textarea id="short_description" 
                                              name="short_description" 
                                              rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('short_description', $course->short_description) }}</textarea>
                                    @error('short_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Full Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
                                    <div id="description-editor" class="bg-white border border-gray-300 rounded-lg min-h-[300px]"></div>
                                    <textarea name="description" id="description" class="hidden" required>{{ old('description', $course->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6">
                                <!-- Course Status -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-3">Publication Status</h3>
                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                        <option value="draft" {{ $course->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ $course->status === 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="archived" {{ $course->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                </div>

                                <!-- Course Thumbnail -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-3">Course Thumbnail</h3>
                                    @if($course->thumbnail)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                                 alt="{{ $course->title }}" 
                                                 class="w-full h-32 object-cover rounded-lg">
                                        </div>
                                    @endif
                                    <input type="file" name="thumbnail" accept="image/*" class="w-full">
                                    <p class="text-xs text-gray-500 mt-1">Recommended: 800x600 pixels</p>
                                </div>

                                <!-- Category -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-3">Category</h3>
                                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $course->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Pricing -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-3">Pricing</h3>
                                    <div class="space-y-3">
                                        <label class="flex items-center">
                                            <input type="radio" name="is_free" value="1" {{ $course->is_free ? 'checked' : '' }} class="text-purple-600">
                                            <span class="ml-2 text-sm">Free Course</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="is_free" value="0" {{ !$course->is_free ? 'checked' : '' }} class="text-purple-600">
                                            <span class="ml-2 text-sm">Paid Course</span>
                                        </label>
                                    </div>
                                    <div class="mt-3" id="price-field" style="{{ $course->is_free ? 'display: none;' : '' }}">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                                        <input type="number" name="price" value="{{ $course->price }}" step="0.01" min="0" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    </div>
                                </div>

                                <!-- Course Settings -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-3">Course Settings</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                                            <select name="level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                                <option value="beginner" {{ $course->level === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="intermediate" {{ $course->level === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                <option value="advanced" {{ $course->level === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                                            <input type="number" name="duration_minutes" value="{{ $course->duration_minutes }}" min="0" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="space-y-3">
                                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-colors">
                                        Update Course
                                    </button>
                                    <a href="{{ route('courses.show', $course) }}" target="_blank" 
                                       class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                                        Preview Course
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Lessons Tab -->
                <div x-show="activeTab === 'lessons'" class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Course Lessons</h3>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                               class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                📚 Manage All Lessons
                            </a>
                            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
                               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                ➕ Add Lesson
                            </a>
                        </div>
                    </div>

                    @if($course->lessons->count() > 0)
                        <div id="lessons-list" class="space-y-3">
                            @foreach($course->lessons->sortBy('order') as $lesson)
                                <div class="bg-gray-50 rounded-lg p-4 lesson-item" data-lesson-id="{{ $lesson->id }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="cursor-move text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $lesson->order }}. {{ $lesson->title }}</h4>
                                                <p class="text-sm text-gray-500">
                                                    <span class="capitalize">{{ $lesson->type }}</span>
                                                    @if($lesson->duration_minutes)
                                                        • {{ $lesson->duration_minutes }} min
                                                    @endif
                                                    @if($lesson->is_free)
                                                        • <span class="text-green-600">Free Preview</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.lessons.edit', $lesson) }}" 
                                               class="text-purple-600 hover:text-purple-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="deleteLesson({{ $lesson->id }})" 
                                                    class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No lessons yet</h3>
                            <p class="text-gray-500 mb-4">Create your first lesson to get started.</p>
                            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
                               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                Create First Lesson
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Students Tab -->
                <div x-show="activeTab === 'students'" class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Enrolled Students</h3>
                        <span class="text-sm text-gray-500">{{ $course->enrollments->count() }} total</span>
                    </div>

                    @if($course->enrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Activity</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($course->enrollments as $enrollment)
                                        <tr>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                        <span class="text-purple-600 font-semibold text-sm">
                                                            {{ substr($enrollment->user->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $enrollment->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                                        <div class="bg-purple-600 h-2 rounded-full" 
                                                             style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-700">{{ $enrollment->progress_percentage ?? 0 }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                {{ $enrollment->created_at->format('M j, Y') }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                {{ $enrollment->updated_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No students enrolled</h3>
                            <p class="text-gray-500">Students will appear here once they enroll in your course.</p>
                        </div>
                    @endif
                </div>

                <!-- Analytics Tab -->
                <div x-show="activeTab === 'analytics'" class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Course Analytics</h3>
                    
                    <!-- Analytics cards and charts would go here -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h4 class="text-sm font-medium text-blue-700 mb-2">Total Views</h4>
                            <p class="text-3xl font-bold text-blue-900">1,234</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-6">
                            <h4 class="text-sm font-medium text-green-700 mb-2">Completion Rate</h4>
                            <p class="text-3xl font-bold text-green-900">67%</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h4 class="text-sm font-medium text-purple-700 mb-2">Avg. Rating</h4>
                            <p class="text-3xl font-bold text-purple-900">4.8</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'" class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Course Settings</h3>
                    
                    <!-- Advanced settings form would go here -->
                    <div class="space-y-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-800 mb-2">Danger Zone</h4>
                            <p class="text-sm text-yellow-700 mb-4">These actions cannot be undone.</p>
                            <button onclick="deleteCourse()" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                Delete Course
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <script>
        // Initialize Quill editor
        const quill = new Quill('#description-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Set initial content
        quill.root.innerHTML = document.getElementById('description').value;

        // Sync with hidden textarea
        quill.on('text-change', () => {
            document.getElementById('description').value = quill.root.innerHTML;
        });

        // Price field visibility
        document.querySelectorAll('input[name="is_free"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const priceField = document.getElementById('price-field');
                if (this.value === '0') {
                    priceField.style.display = 'block';
                } else {
                    priceField.style.display = 'none';
                }
            });
        });

        // Sortable lessons
        if (document.getElementById('lessons-list')) {
            new Sortable(document.getElementById('lessons-list'), {
                handle: '.cursor-move',
                animation: 150,
                onEnd: function(evt) {
                    // Update lesson order
                    const lessonIds = Array.from(evt.to.children).map(item => 
                        item.getAttribute('data-lesson-id')
                    );
                    
                    fetch('{{ route("admin.lessons.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ lesson_ids: lessonIds })
                    });
                }
            });
        }

        function deleteLesson(lessonId) {
            if (confirm('Are you sure you want to delete this lesson?')) {
                fetch(`/admin/lessons/${lessonId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    location.reload();
                });
            }
        }

        function deleteCourse() {
            if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
                fetch('{{ route("admin.courses.destroy", $course) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    window.location.href = '{{ route("admin.courses.index") }}';
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
