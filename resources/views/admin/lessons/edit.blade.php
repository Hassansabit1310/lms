<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white min-h-32 flex items-center">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            ✏️ Edit Lesson
                        </h2>
                        <p class="text-white/90 text-lg">{{ $lesson->title }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            ← Back to Lessons
                        </a>
                        <a href="{{ route('lessons.show', [$course, $lesson]) }}" 
                           class="bg-white text-blue-600 hover:bg-gray-100 px-4 py-2 rounded-lg font-semibold transition-colors">
                            👁️ Preview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.courses.lessons.update', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data" x-data="lessonEditor()">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lesson Title *</label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title', $lesson->title) }}"
                                   x-model="formData.title"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter lesson title"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lesson Order</label>
                            <input type="number" 
                                   name="order" 
                                   value="{{ old('order', $lesson->order) }}"
                                   x-model="formData.order"
                                   min="1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="1">
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                            <input type="number" 
                                   name="duration_minutes" 
                                   value="{{ old('duration_minutes', $lesson->duration_minutes) }}"
                                   x-model="formData.duration_minutes"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="15">
                            @error('duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      x-model="formData.description"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Brief description of the lesson content">{{ old('description', $lesson->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Free Lesson Toggle -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_free" 
                                       value="1"
                                       {{ old('is_free', $lesson->is_free) ? 'checked' : '' }}
                                       x-model="formData.is_free"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Make this lesson free (accessible without enrollment)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Content Type Selection -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Content Type</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <label class="content-type-card" :class="formData.type === 'youtube' ? 'selected' : ''">
                            <input type="radio" name="type" value="youtube" x-model="formData.type" class="hidden" {{ $lesson->type === 'youtube' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 transition-colors">
                                <div class="text-center">
                                    <i class="fab fa-youtube text-red-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">YouTube</h3>
                                    <p class="text-sm text-gray-600 mt-1">Video from YouTube</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'vimeo' ? 'selected' : ''">
                            <input type="radio" name="type" value="vimeo" x-model="formData.type" class="hidden" {{ $lesson->type === 'vimeo' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                <div class="text-center">
                                    <i class="fab fa-vimeo text-blue-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">Vimeo</h3>
                                    <p class="text-sm text-gray-600 mt-1">Video from Vimeo</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'h5p' ? 'selected' : ''">
                            <input type="radio" name="type" value="h5p" x-model="formData.type" class="hidden" {{ $lesson->type === 'h5p' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 transition-colors">
                                <div class="text-center">
                                    <i class="fas fa-puzzle-piece text-green-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">H5P</h3>
                                    <p class="text-sm text-gray-600 mt-1">Interactive content</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'code' ? 'selected' : ''">
                            <input type="radio" name="type" value="code" x-model="formData.type" class="hidden" {{ $lesson->type === 'code' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-300 transition-colors">
                                <div class="text-center">
                                    <i class="fas fa-code text-yellow-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">Code</h3>
                                    <p class="text-sm text-gray-600 mt-1">Code examples</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'text' ? 'selected' : ''">
                            <input type="radio" name="type" value="text" x-model="formData.type" class="hidden" {{ $lesson->type === 'text' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-400 transition-colors">
                                <div class="text-center">
                                    <i class="fas fa-file-text text-gray-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">Text</h3>
                                    <p class="text-sm text-gray-600 mt-1">Written content</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'pdf' ? 'selected' : ''">
                            <input type="radio" name="type" value="pdf" x-model="formData.type" class="hidden" {{ $lesson->type === 'pdf' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-400 transition-colors">
                                <div class="text-center">
                                    <i class="fas fa-file-pdf text-red-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">PDF</h3>
                                    <p class="text-sm text-gray-600 mt-1">Document file</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'quiz' ? 'selected' : ''">
                            <input type="radio" name="type" value="quiz" x-model="formData.type" class="hidden" {{ $lesson->type === 'quiz' ? 'checked' : '' }}>
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <i class="fas fa-question-circle text-purple-500 text-3xl mb-3"></i>
                                    <h3 class="font-semibold text-gray-900">Quiz</h3>
                                    <p class="text-sm text-gray-600 mt-1">Assessment</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Content Input -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Lesson Content</h2>
                    
                    <!-- YouTube Content -->
                    <div x-show="formData.type === 'youtube'" class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">YouTube URL *</label>
                        <input type="url" 
                               name="content" 
                               value="{{ $lesson->type === 'youtube' ? old('content', $lesson->content) : '' }}"
                               x-model="formData.content"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="https://www.youtube.com/watch?v=..."
                               x-bind:required="formData.type === 'youtube'">
                        <p class="text-sm text-gray-600">Paste the full YouTube URL here</p>
                    </div>

                    <!-- Vimeo Content -->
                    <div x-show="formData.type === 'vimeo'" class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vimeo URL *</label>
                        <input type="url" 
                               name="content" 
                               value="{{ $lesson->type === 'vimeo' ? old('content', $lesson->content) : '' }}"
                               x-model="formData.content"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://vimeo.com/123456789"
                               x-bind:required="formData.type === 'vimeo'">
                        <p class="text-sm text-gray-600">Paste the full Vimeo URL here</p>
                    </div>

                    <!-- H5P Content -->
                    <div x-show="formData.type === 'h5p'" class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">H5P File</label>
                        <input type="file" 
                               name="h5p_file" 
                               accept=".h5p"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="text-sm text-gray-600">Upload an H5P interactive content file (.h5p)</p>
                        
                        @if($lesson->type === 'h5p' && $lesson->contents->where('content_type', 'h5p')->first())
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Current H5P file: {{ $lesson->contents->where('content_type', 'h5p')->first()->content_data['original_name'] ?? 'Unknown' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Code Content -->
                    <div x-show="formData.type === 'code'" class="space-y-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800 text-sm">
                                <strong>Note:</strong> For runnable HTML/CSS/JS code with live preview, use the Multi-Content Lesson creator and add a "Runnable Code" block.
                            </p>
                        </div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Code Content *</label>
                        <textarea name="content" 
                                  x-model="formData.content"
                                  rows="12"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent font-mono text-sm"
                                  placeholder="// Enter your code here..."
                                  x-bind:required="formData.type === 'code'">{{ $lesson->type === 'code' ? old('content', $lesson->content) : '' }}</textarea>
                    </div>

                    <!-- Text Content -->
                    <div x-show="formData.type === 'text'" class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Text Content *</label>
                        <textarea name="content" 
                                  x-model="formData.content"
                                  rows="12"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                  placeholder="Enter your lesson content here..."
                                  x-bind:required="formData.type === 'text'">{{ $lesson->type === 'text' ? old('content', $lesson->content) : '' }}</textarea>
                        <p class="text-sm text-gray-600">You can use basic formatting and line breaks</p>
                    </div>

                    <!-- PDF Content -->
                    <div x-show="formData.type === 'pdf'" class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">PDF URL *</label>
                        <input type="url" 
                               name="content" 
                               value="{{ $lesson->type === 'pdf' ? old('content', $lesson->content) : '' }}"
                               x-model="formData.content"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="https://example.com/document.pdf"
                               x-bind:required="formData.type === 'pdf'">
                        <p class="text-sm text-gray-600">URL to the PDF document</p>
                    </div>

                    <!-- Quiz Content -->
                    <div x-show="formData.type === 'quiz'" class="space-y-4">
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h3 class="font-semibold text-purple-900 mb-2">Quiz Configuration</h3>
                            <p class="text-purple-700 text-sm">Quiz functionality will be available in the next update. For now, you can create the lesson and add quiz content later.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                                Cancel
                            </a>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                ✅ Update Lesson
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function lessonEditor() {
            return {
                formData: {
                    title: '{{ old('title', $lesson->title) }}',
                    description: '{{ old('description', $lesson->description) }}',
                    type: '{{ old('type', $lesson->type) }}',
                    content: '{{ old('content', $lesson->content) }}',
                    is_free: {{ old('is_free', $lesson->is_free) ? 'true' : 'false' }},
                    order: {{ old('order', $lesson->order) }},
                    duration_minutes: {{ old('duration_minutes', $lesson->duration_minutes) ?? 'null' }}
                }
            }
        }

        // Update content type styling
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                document.querySelectorAll('.content-type-card').forEach(card => {
                    const input = card.querySelector('input[type="radio"]');
                    const container = card.querySelector('div');
                    
                    if (input.checked) {
                        container.classList.add('border-blue-500', 'bg-blue-50');
                        container.classList.remove('border-gray-200');
                    } else {
                        container.classList.remove('border-blue-500', 'bg-blue-50');
                        container.classList.add('border-gray-200');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
