<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white min-h-32 flex items-center">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            ✏️ Edit H5P Content
                        </h2>
                        <p class="text-white/90 text-lg">{{ $h5pContent->title }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.h5p.index') }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            ← Back to Library
                        </a>
                        @if($h5pContent->isReady())
                            <a href="{{ $h5pContent->getEmbedUrl() }}" 
                               target="_blank"
                               class="bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2 rounded-lg font-semibold transition-colors">
                                👁️ Preview
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Content Information -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6">📝 Content Information</h3>
                
                <form action="{{ route('admin.h5p.update', $h5pContent) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title', $h5pContent->title) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   required>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                      placeholder="Describe what this H5P content is about...">{{ old('description', $h5pContent->description) }}</textarea>
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $h5pContent->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-medium text-gray-700">Active (available for use in lessons)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.h5p.index') }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </a>
                        
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Content
                        </button>
                    </div>
                </form>
            </div>

            <!-- Content Details -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6">📊 Content Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Content Type</label>
                            <p class="text-gray-900 font-medium">{{ $h5pContent->content_type ?: 'Unknown' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Version</label>
                            <p class="text-gray-900">{{ $h5pContent->version ?: 'Unknown' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Size</label>
                            <p class="text-gray-900">{{ $h5pContent->getFormattedFileSize() }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $h5pContent->upload_status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $h5pContent->upload_status === 'processing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $h5pContent->upload_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst($h5pContent->upload_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Usage Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="text-gray-900">{{ $h5pContent->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="text-gray-900">{{ $h5pContent->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Used in Lessons</label>
                            <p class="text-gray-900">{{ $h5pContent->usages->count() }} lesson(s)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Content ID</label>
                            <div class="flex items-center space-x-2">
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $h5pContent->id }}</code>
                                <button onclick="copyToClipboard('{{ $h5pContent->id }}')" 
                                        class="text-indigo-600 hover:text-indigo-800 text-sm">
                                    📋 Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Information -->
            @if($h5pContent->usages->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">📚 Usage in Lessons</h3>
                    
                    <div class="space-y-4">
                        @foreach($h5pContent->usages as $usage)
                            @if($usage->lessonContent && $usage->lessonContent->lesson)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $usage->lessonContent->lesson->title }}</h4>
                                        <p class="text-sm text-gray-600">Course: {{ $usage->lessonContent->lesson->course->title }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.courses.lessons.edit', [$usage->lessonContent->lesson->course, $usage->lessonContent->lesson]) }}" 
                                           class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            Edit Lesson
                                        </a>
                                        <a href="{{ route('lessons.show', [$usage->lessonContent->lesson->course, $usage->lessonContent->lesson]) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-800 text-sm">
                                            View Lesson
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Content ID copied to clipboard!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            });
        }
    </script>
    @endpush
</x-app-layout>
