<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white min-h-32 flex items-center">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            🧩 H5P Content Library
                        </h2>
                        <p class="text-white/90 text-lg">Manage interactive H5P content</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            ← Dashboard
                        </a>
                        <a href="{{ route('admin.h5p.create') }}" 
                           class="bg-white text-indigo-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-semibold transition-colors">
                            📤 Upload H5P
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <i class="fas fa-puzzle-piece text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total H5P Content</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $h5pContents->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ready to Use</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $h5pContents->where('upload_status', 'completed')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Processing</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $h5pContents->where('upload_status', 'processing')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Failed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $h5pContents->where('upload_status', 'failed')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- H5P Content Grid -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">H5P Content Library</h3>
                        <div class="flex items-center space-x-4">
                            <!-- Search and filters can be added here -->
                            <span class="text-sm text-gray-500">{{ $h5pContents->total() }} items</span>
                        </div>
                    </div>
                </div>

                @if($h5pContents->count() > 0)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($h5pContents as $content)
                                <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                    <!-- Thumbnail -->
                                    <div class="h-48 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center relative">
                                        @if($content->thumbnail_path)
                                            <img src="{{ $content->getThumbnailUrl() }}" 
                                                 alt="{{ $content->title }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="text-center">
                                                <i class="fas fa-puzzle-piece text-4xl text-indigo-400 mb-2"></i>
                                                <p class="text-sm text-indigo-600 font-medium">{{ $content->content_type ?: 'H5P Content' }}</p>
                                            </div>
                                        @endif
                                        
                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            @if($content->upload_status === 'completed')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                                    ✅ Ready
                                                </span>
                                            @elseif($content->upload_status === 'processing')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                                    ⏳ Processing
                                                </span>
                                            @elseif($content->upload_status === 'failed')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                                    ❌ Failed
                                                </span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">
                                                    ⏸️ Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Content Info -->
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2 truncate">{{ $content->title }}</h4>
                                        
                                        @if($content->description)
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $content->description }}</p>
                                        @endif

                                        <!-- Metadata -->
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                            <span>{{ $content->getFormattedFileSize() }}</span>
                                            <span>{{ $content->created_at->diffForHumans() }}</span>
                                        </div>

                                        <!-- Usage Info -->
                                        @if($content->usages->count() > 0)
                                            <div class="text-xs text-indigo-600 mb-3">
                                                📚 Used in {{ $content->usages->count() }} lesson(s)
                                            </div>
                                        @endif

                                        <!-- Actions -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex space-x-2">
                                                @if($content->isReady())
                                                    <a href="{{ $content->getEmbedUrl() }}" 
                                                       target="_blank"
                                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                        👁️ Preview
                                                    </a>
                                                @endif
                                                
                                                <button onclick="copyEmbedCode('{{ $content->id }}')"
                                                        class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                                    📋 Copy ID
                                                </button>
                                            </div>

                                            <div class="flex space-x-1">
                                                @if($content->upload_status === 'failed')
                                                    <button onclick="retryProcessing('{{ $content->id }}')"
                                                            class="text-yellow-600 hover:text-yellow-800 p-1">
                                                        <i class="fas fa-redo text-sm"></i>
                                                    </button>
                                                @endif
                                                
                                                <button onclick="deleteContent('{{ $content->id }}')"
                                                        class="text-red-600 hover:text-red-800 p-1">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Error Message -->
                                        @if($content->upload_status === 'failed' && $content->error_message)
                                            <div class="mt-3 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-700">
                                                {{ $content->error_message }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $h5pContents->links() }}
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-puzzle-piece text-3xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No H5P Content Yet</h3>
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            Upload your first H5P package to start creating interactive content for your lessons.
                        </p>
                        <a href="{{ route('admin.h5p.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Upload H5P Package
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyEmbedCode(contentId) {
            navigator.clipboard.writeText(contentId).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'H5P Content ID copied to clipboard!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            });
        }

        function deleteContent(contentId) {
            if (confirm('Are you sure you want to delete this H5P content? This action cannot be undone.')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/h5p/${contentId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function retryProcessing(contentId) {
            if (confirm('Retry processing this H5P content?')) {
                fetch(`/admin/h5p/${contentId}/retry`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to retry processing: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
