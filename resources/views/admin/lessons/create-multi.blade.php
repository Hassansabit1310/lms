<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white min-h-32 flex items-center">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            ✅ Create Multi-Content Lesson (FIXED)
                        </h2>
                        <p class="text-white/90 text-lg">Course: {{ $course->title }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            ← Back to Lessons
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
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

            <form action="{{ route('admin.courses.lessons.store', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Lesson Basic Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">📚 Lesson Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lesson Title *</label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter a clear, descriptive lesson title...">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Describe what students will learn in this lesson...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lesson Order</label>
                            <input type="number" 
                                   name="order" 
                                   value="{{ old('order', $nextOrder) }}"
                                   min="1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                            <input type="number" 
                                   name="duration_minutes" 
                                   value="{{ old('duration_minutes') }}"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., 15">
                        </div>

                        <!-- Free Preview -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_free" 
                                       value="1" 
                                       {{ old('is_free') ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500 rounded">
                                <span class="ml-2 text-sm text-gray-700">Allow free preview (accessible without enrollment)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Content Blocks -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">🎯 Content Blocks</h2>
                        <button type="button" 
                                onclick="addContentBlock()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add Content Block
                        </button>
                    </div>

                    <!-- Content Blocks Container -->
                    <div id="contentBlocksContainer" class="space-y-6">
                        <!-- Initial Content Block -->
                        <div class="content-block border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm block-number">1</span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900">Content Block</h3>
                                    <select onchange="switchContentType(this, 0)" 
                                            class="px-3 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="youtube">📺 YouTube Video</option>
                                        <option value="vimeo">🎬 Vimeo Video</option>
                                        <option value="text">📝 Text Content</option>
                                        <option value="h5p">🧩 H5P Interactive</option>
                                        <option value="code">💻 Code Example</option>
                                        <option value="quiz">❓ Quiz</option>
                                    </select>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" 
                                            onclick="removeContentBlock(this)"
                                            class="text-red-500 hover:text-red-700 p-1 remove-btn" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" name="content_blocks[0][type]" value="youtube" class="block-type">
                            <input type="hidden" name="content_blocks[0][order]" value="1" class="block-order">

                            <!-- Content Input -->
                            <div class="content-input">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="content-label">YouTube URL</span> *
                                </label>
                                <input type="url" 
                                       name="content_blocks[0][content]"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                        </div>
                    </div>

                    <!-- Content Guidelines -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">💡 Content Creation Tips</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li><strong>Videos:</strong> Use engaging introductions and clear explanations</li>
                                        <li><strong>Text:</strong> Break content into digestible paragraphs</li>
                                        <li><strong>Code:</strong> Include comments for better understanding</li>
                                        <li><strong>Order:</strong> Structure content logically from introduction to practice</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </a>
                        
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <i class="fas fa-save mr-2"></i>Create Lesson
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let blockCounter = 1;

        function addContentBlock() {
            const container = document.getElementById('contentBlocksContainer');
            const newBlock = createContentBlock(blockCounter);
            container.appendChild(newBlock);
            blockCounter++;
            updateBlockNumbers();
            updateRemoveButtons();
        }

        function createContentBlock(index) {
            const html = `
                <div class="content-block border border-gray-200 rounded-lg p-6 bg-gray-50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm block-number">${index + 1}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900">Content Block</h3>
                            <select onchange="switchContentType(this, ${index})" 
                                    class="px-3 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="youtube">📺 YouTube Video</option>
                                <option value="vimeo">🎬 Vimeo Video</option>
                                <option value="text">📝 Text Content</option>
                                <option value="h5p">🧩 H5P Interactive</option>
                                <option value="code">💻 Code Example</option>
                                <option value="quiz">❓ Quiz</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    onclick="removeContentBlock(this)"
                                    class="text-red-500 hover:text-red-700 p-1 remove-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="content_blocks[${index}][type]" value="youtube" class="block-type">
                    <input type="hidden" name="content_blocks[${index}][order]" value="${index + 1}" class="block-order">

                    <!-- Content Input -->
                    <div class="content-input">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="content-label">YouTube URL</span> *
                        </label>
                        <input type="url" 
                               name="content_blocks[${index}][content]"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>
            `;
            
            const div = document.createElement('div');
            div.innerHTML = html;
            return div.firstElementChild;
        }

        function removeContentBlock(button) {
            const block = button.closest('.content-block');
            const container = document.getElementById('contentBlocksContainer');
            
            if (container.children.length > 1) {
                block.remove();
                updateBlockNumbers();
                updateRemoveButtons();
            }
        }

        function switchContentType(select, blockIndex) {
            const block = select.closest('.content-block');
            const contentInput = block.querySelector('.content-input');
            const typeInput = block.querySelector('.block-type');
            const label = block.querySelector('.content-label');
            const input = block.querySelector('input[name*="[content]"], textarea[name*="[content]"]');
            
            const selectedType = select.value;
            typeInput.value = selectedType;
            
            // Update label and input based on type
            let labelText = '';
            let placeholder = '';
            let inputType = 'input';
            let inputElement = '';
            
            switch (selectedType) {
                case 'youtube':
                    labelText = 'YouTube URL';
                    placeholder = 'https://www.youtube.com/watch?v=...';
                    inputElement = `<input type="url" name="content_blocks[${blockIndex}][content]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="${placeholder}">`;
                    break;
                case 'vimeo':
                    labelText = 'Vimeo URL';
                    placeholder = 'https://vimeo.com/...';
                    inputElement = `<input type="url" name="content_blocks[${blockIndex}][content]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="${placeholder}">`;
                    break;
                case 'text':
                    labelText = 'Text Content';
                    placeholder = 'Enter your text content here...';
                    inputElement = `<textarea name="content_blocks[${blockIndex}][content]" required rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="${placeholder}"></textarea>`;
                    break;
                case 'h5p':
                    labelText = 'H5P Interactive Content';
                    inputElement = `
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Select H5P Content</span>
                                <a href="{{ route('admin.h5p.create') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    📤 Upload New H5P
                                </a>
                            </div>
                            
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="h5p_source_${blockIndex}" value="library" class="text-indigo-600 focus:ring-indigo-500" checked onchange="toggleH5PSource(${blockIndex}, 'library')">
                                    <span class="ml-2 text-sm text-gray-700">Select from H5P Library</span>
                                </label>
                                <div class="ml-6">
                                    <button type="button" onclick="openH5PLibrary(${blockIndex})" class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm">
                                        <i class="fas fa-search mr-2"></i>Browse Library
                                    </button>
                                    <div id="selectedH5P_${blockIndex}" class="mt-2 hidden">
                                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-puzzle-piece text-indigo-600 text-lg"></i>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm font-medium text-indigo-800" id="h5pTitle_${blockIndex}"></p>
                                                    <p class="text-xs text-indigo-600" id="h5pType_${blockIndex}"></p>
                                                </div>
                                                <button type="button" onclick="clearH5PSelection(${blockIndex})" class="text-indigo-600 hover:text-indigo-800">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <label class="flex items-center">
                                    <input type="radio" name="h5p_source_${blockIndex}" value="manual" class="text-indigo-600 focus:ring-indigo-500" onchange="toggleH5PSource(${blockIndex}, 'manual')">
                                    <span class="ml-2 text-sm text-gray-700">Enter Content ID Manually</span>
                                </label>
                                <div class="ml-6">
                                    <input type="text" id="h5pManualId_${blockIndex}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Enter H5P content ID..." disabled>
                                </div>
                            </div>
                            
                            <input type="hidden" name="content_blocks[${blockIndex}][content]" id="h5pContentValue_${blockIndex}" required>
                        </div>
                    `;
                    break;
                case 'code':
                    labelText = 'Code';
                    placeholder = '// Enter your code here...';
                    inputElement = `<textarea name="content_blocks[${blockIndex}][content]" required rows="8" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm" placeholder="${placeholder}"></textarea>`;
                    break;
                case 'quiz':
                    labelText = 'Quiz Title';
                    placeholder = 'Enter quiz title...';
                    inputElement = `<input type="text" name="content_blocks[${blockIndex}][content]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="${placeholder}">`;
                    break;
            }
            
            label.textContent = labelText;
            contentInput.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="content-label">${labelText}</span> *
                </label>
                ${inputElement}
            `;
        }

        function updateBlockNumbers() {
            const blocks = document.querySelectorAll('.content-block');
            blocks.forEach((block, index) => {
                const numberSpan = block.querySelector('.block-number');
                const orderInput = block.querySelector('.block-order');
                const typeInput = block.querySelector('.block-type');
                const contentInput = block.querySelector('input[name*="[content]"], textarea[name*="[content]"]');
                const select = block.querySelector('select');
                
                numberSpan.textContent = index + 1;
                orderInput.value = index + 1;
                orderInput.name = `content_blocks[${index}][order]`;
                typeInput.name = `content_blocks[${index}][type]`;
                
                if (contentInput) {
                    contentInput.name = `content_blocks[${index}][content]`;
                }
                
                if (select) {
                    select.setAttribute('onchange', `switchContentType(this, ${index})`);
                }
            });
        }

        function updateRemoveButtons() {
            const blocks = document.querySelectorAll('.content-block');
            blocks.forEach((block) => {
                const removeBtn = block.querySelector('.remove-btn');
                removeBtn.style.display = blocks.length > 1 ? 'block' : 'none';
            });
        }

        // H5P Integration Functions
        function toggleH5PSource(blockIndex, source) {
            const manualInput = document.getElementById(`h5pManualId_${blockIndex}`);
            const contentValue = document.getElementById(`h5pContentValue_${blockIndex}`);
            
            if (source === 'manual') {
                manualInput.disabled = false;
                manualInput.addEventListener('input', function() {
                    contentValue.value = this.value;
                });
                // Clear library selection
                clearH5PSelection(blockIndex);
            } else {
                manualInput.disabled = true;
                manualInput.value = '';
            }
        }

        function openH5PLibrary(blockIndex) {
            // Create modal for H5P selection
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[80vh] overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Select H5P Content</h3>
                            <button onclick="closeH5PModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-96">
                        <div id="h5pLibraryContent" class="text-center">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600">Loading H5P content...</p>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            window.currentH5PModal = modal;
            window.currentH5PBlockIndex = blockIndex;
            
            // Load H5P content
            loadH5PContent();
        }

        function loadH5PContent() {
            fetch('{{ route("admin.h5p.available") }}')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('h5pLibraryContent');
                    
                    if (data.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-8">
                                <i class="fas fa-puzzle-piece text-4xl text-gray-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No H5P Content Available</h4>
                                <p class="text-gray-600 mb-4">Upload H5P packages to get started.</p>
                                <a href="{{ route('admin.h5p.create') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-upload mr-2"></i>Upload H5P Content
                                </a>
                            </div>
                        `;
                        return;
                    }
                    
                    container.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${data.map(content => `
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 cursor-pointer transition-colors" onclick="selectH5PContent(${content.id}, '${content.title}', '${content.content_type}')">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-puzzle-piece text-indigo-600 text-2xl"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h4 class="font-medium text-gray-900">${content.title}</h4>
                                            <p class="text-sm text-gray-600">${content.content_type || 'H5P Content'}</p>
                                            <p class="text-xs text-gray-500">${content.file_size}</p>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                })
                .catch(error => {
                    document.getElementById('h5pLibraryContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Error Loading Content</h4>
                            <p class="text-gray-600">Failed to load H5P content library.</p>
                        </div>
                    `;
                });
        }

        function selectH5PContent(contentId, title, contentType) {
            const blockIndex = window.currentH5PBlockIndex;
            const selectedDiv = document.getElementById(`selectedH5P_${blockIndex}`);
            const titleElement = document.getElementById(`h5pTitle_${blockIndex}`);
            const typeElement = document.getElementById(`h5pType_${blockIndex}`);
            const contentValue = document.getElementById(`h5pContentValue_${blockIndex}`);
            
            // Update UI
            titleElement.textContent = title;
            typeElement.textContent = contentType || 'H5P Content';
            selectedDiv.classList.remove('hidden');
            
            // Set the content value
            contentValue.value = contentId;
            
            // Close modal
            closeH5PModal();
        }

        function clearH5PSelection(blockIndex) {
            const selectedDiv = document.getElementById(`selectedH5P_${blockIndex}`);
            const contentValue = document.getElementById(`h5pContentValue_${blockIndex}`);
            
            selectedDiv.classList.add('hidden');
            contentValue.value = '';
        }

        function closeH5PModal() {
            if (window.currentH5PModal) {
                document.body.removeChild(window.currentH5PModal);
                window.currentH5PModal = null;
                window.currentH5PBlockIndex = null;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateRemoveButtons();
        });
    </script>
    @endpush
</x-app-layout>
