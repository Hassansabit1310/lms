<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white min-h-32 flex items-center">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            📤 Upload H5P Content
                        </h2>
                        <p class="text-white/90 text-lg">Add interactive H5P content to your library</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.h5p.index') }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            ← Back to Library
                        </a>
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

            <form action="{{ route('admin.h5p.store') }}" method="POST" enctype="multipart/form-data" id="h5pUploadForm">
                @csrf
                
                <!-- Upload Section -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">📁 H5P Package Upload</h3>
                    
                    <!-- Drag & Drop Upload Area -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">H5P Package File *</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors" 
                             id="dropZone">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="h5p_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a H5P file</span>
                                        <input id="h5p_file" name="h5p_file" type="file" accept=".h5p,application/zip" class="sr-only" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">H5P files up to 50MB</p>
                            </div>
                        </div>
                        
                        <!-- File Info Display -->
                        <div id="fileInfo" class="mt-4 hidden">
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-file-archive text-indigo-600 text-xl"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-indigo-800" id="fileName"></p>
                                        <p class="text-xs text-indigo-600" id="fileSize"></p>
                                    </div>
                                    <button type="button" onclick="clearFile()" class="text-indigo-600 hover:text-indigo-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">📝 Content Information</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Leave empty to use title from H5P package">
                            <p class="text-xs text-gray-500 mt-1">Optional: Override the title from the H5P package</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                      placeholder="Describe what this H5P content is about...">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Optional: Add a description to help identify this content</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Guidelines -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">💡 H5P Upload Guidelines</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li><strong>File Format:</strong> Only .h5p files are accepted</li>
                                    <li><strong>File Size:</strong> Maximum 50MB per file</li>
                                    <li><strong>Processing:</strong> Large files may take a few moments to process</li>
                                    <li><strong>Content Types:</strong> Interactive Videos, Course Presentations, Quizzes, and more</li>
                                    <li><strong>Reusability:</strong> Once uploaded, content can be used in multiple lessons</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.h5p.index') }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </a>
                        
                        <button type="submit" 
                                id="uploadButton"
                                disabled
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span id="uploadText">
                                <i class="fas fa-upload mr-2"></i>Upload H5P Content
                            </span>
                            <span id="uploadingText" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('h5p_file');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadButton = document.getElementById('uploadButton');
        const uploadForm = document.getElementById('h5pUploadForm');
        const uploadText = document.getElementById('uploadText');
        const uploadingText = document.getElementById('uploadingText');

        // Drag and drop functionality
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            // Validate file type
            if (!file.name.toLowerCase().endsWith('.h5p')) {
                alert('Please select a valid H5P file (.h5p extension)');
                return;
            }

            // Validate file size (50MB = 50 * 1024 * 1024 bytes)
            if (file.size > 50 * 1024 * 1024) {
                alert('File size must be less than 50MB');
                return;
            }

            // Update file input
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            // Show file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');
            
            // Enable upload button
            uploadButton.disabled = false;
        }

        function clearFile() {
            fileInput.value = '';
            fileInfo.classList.add('hidden');
            uploadButton.disabled = true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form submission
        uploadForm.addEventListener('submit', (e) => {
            uploadButton.disabled = true;
            uploadText.classList.add('hidden');
            uploadingText.classList.remove('hidden');
        });
    </script>
    @endpush
</x-app-layout>
