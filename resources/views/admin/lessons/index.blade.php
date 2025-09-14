<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white min-h-32 flex items-center">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            📚 Course Lessons
                        </h2>
                        <p class="text-white/90 text-lg">{{ $course->title }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.courses.edit', $course) }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            ← Back to Course
                        </a>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
                               class="bg-white/90 text-purple-600 hover:bg-white px-3 py-2 rounded-lg font-semibold transition-colors text-sm">
                                ➕ Simple Lesson
                            </a>
                            <a href="{{ route('admin.courses.lessons.create-multi', $course) }}" 
                               class="bg-white text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg font-semibold transition-colors border-2 border-green-200 text-sm">
                                ✨ Multi-Content Lesson
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Course Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-book text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Lessons</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $lessons->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-clock text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Duration</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($course->lessons->sum('duration_minutes') / 60, 1) }}h</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-unlock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Free Lessons</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $course->lessons->where('is_free', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-question-circle text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">With Quizzes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $course->lessons->filter(function($lesson) { return $lesson->quizzes->count() > 0; })->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lessons List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Lessons</h3>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm text-gray-600 hover:text-gray-900 px-3 py-1 rounded-md hover:bg-gray-100">
                                <i class="fas fa-sort"></i> Reorder
                            </button>
                        </div>
                    </div>
                </div>

                @if($lessons->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($lessons as $lesson)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Order Number -->
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600">
                                        {{ $lesson->order }}
                                    </div>

                                    <!-- Lesson Info -->
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $lesson->title }}</h4>
                                            
                                            <!-- Type Badge -->
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @switch($lesson->type)
                                                    @case('youtube') bg-red-100 text-red-800 @break
                                                    @case('vimeo') bg-blue-100 text-blue-800 @break
                                                    @case('h5p') bg-green-100 text-green-800 @break
                                                    @case('quiz') bg-purple-100 text-purple-800 @break
                                                    @case('code') bg-yellow-100 text-yellow-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch
                                            ">
                                                @switch($lesson->type)
                                                    @case('youtube') <i class="fab fa-youtube mr-1"></i> YouTube @break
                                                    @case('vimeo') <i class="fab fa-vimeo mr-1"></i> Vimeo @break
                                                    @case('h5p') <i class="fas fa-puzzle-piece mr-1"></i> H5P @break
                                                    @case('quiz') <i class="fas fa-question-circle mr-1"></i> Quiz @break
                                                    @case('code') <i class="fas fa-code mr-1"></i> Code @break
                                                    @case('pdf') <i class="fas fa-file-pdf mr-1"></i> PDF @break
                                                    @default <i class="fas fa-file-text mr-1"></i> Text
                                                @endswitch
                                            </span>

                                            @if($lesson->is_free)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-unlock mr-1"></i> Free
                                                </span>
                                            @endif

                                            @if($lesson->quizzes->count() > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <i class="fas fa-question-circle mr-1"></i> {{ $lesson->quizzes->count() }} Quiz(es)
                                                </span>
                                            @endif
                                        </div>

                                        @if($lesson->description)
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $lesson->description }}</p>
                                        @endif

                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            @if($lesson->duration_minutes)
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $lesson->duration_minutes }} min
                                                </span>
                                            @endif
                                            
                                            <span class="flex items-center">
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $lesson->progress->where('status', 'completed')->count() }} completed
                                            </span>
                                            
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $lesson->created_at->format('M j, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('lessons.show', [$course, $lesson]) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                       title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" 
                                       class="text-gray-600 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this lesson?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($lessons->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            {{ $lessons->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No lessons yet</h3>
                        <p class="text-gray-600 mb-6">Get started by creating your first lesson for this course.</p>
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
                               class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Simple Lesson
                            </a>
                            <a href="{{ route('admin.courses.lessons.create-multi', $course) }}" 
                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-magic mr-2"></i>
                                Multi-Content Lesson
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
