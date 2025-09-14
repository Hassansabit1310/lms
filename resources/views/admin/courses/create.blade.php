<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 50%, #A855F7 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    ✨ Create New Course
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Build an engaging learning experience for your students</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" x-data="courseCreator()">
                @csrf
                
                <!-- Progress Steps -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Course Creation Wizard</h1>
                        <a href="{{ route('admin.courses.index') }}" 
                           class="text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Step Indicator - Simplified -->
                    <div class="w-full max-w-3xl mx-auto mb-8 px-8">
                        <!-- Step Progress -->
                        <div class="flex items-center justify-between mb-6">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-purple-600 border-2 border-purple-600 flex items-center justify-center font-bold text-lg step-indicator shadow-lg" data-step="0" style="color: white !important;">
                                    <span style="color: white !important; font-weight: bold;">1</span>
                                </div>
                                <div class="text-purple-600 font-semibold text-sm mt-2 step-label" data-step="0">Basic Info</div>
                            </div>
                            
                            <!-- Connection Line 1-2 -->
                            <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                            
                            <!-- Step 2 -->
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-400 border-2 border-gray-400 flex items-center justify-center font-bold text-lg step-indicator shadow-lg" data-step="1" style="color: white !important;">
                                    <span style="color: white !important; font-weight: bold;">2</span>
                                </div>
                                <div class="text-gray-500 text-sm mt-2 step-label" data-step="1">Content</div>
                            </div>
                            
                            <!-- Connection Line 2-3 -->
                            <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                            
                            <!-- Step 3 -->
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-400 border-2 border-gray-400 flex items-center justify-center font-bold text-lg step-indicator shadow-lg" data-step="2" style="color: white !important;">
                                    <span style="color: white !important; font-weight: bold;">3</span>
                                </div>
                                <div class="text-gray-500 text-sm mt-2 step-label" data-step="2">Pricing</div>
                            </div>
                            
                            <!-- Connection Line 3-4 -->
                            <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                            
                            <!-- Step 4 -->
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-400 border-2 border-gray-400 flex items-center justify-center font-bold text-lg step-indicator shadow-lg" data-step="3" style="color: white !important;">
                                    <span style="color: white !important; font-weight: bold;">4</span>
                                </div>
                                <div class="text-gray-500 text-sm mt-2 step-label" data-step="3">SEO</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Basic Information -->
                <div x-show="currentStep === 0" class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Course Information</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Course Title -->
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   x-model="formData.title"
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Enter an engaging course title...">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="lg:col-span-2">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                            <textarea id="short_description" 
                                      name="short_description" 
                                      rows="3"
                                      x-model="formData.short_description"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="A brief, compelling summary of your course...">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="category_id" 
                                    name="category_id" 
                                    x-model="formData.category_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                            <select id="level" 
                                    name="level" 
                                    x-model="formData.level"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            @error('level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course Thumbnail -->
                        <div>
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input type="file" 
                                           id="thumbnail" 
                                           name="thumbnail" 
                                           accept="image/*"
                                           @change="previewImage($event)"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                                <div x-show="imagePreview" class="w-24 h-24">
                                    <img :src="imagePreview" alt="Preview" class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                                </div>
                            </div>
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Video Link (for quick lesson creation) -->
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Video URL <span class="text-gray-500">(Optional - creates first lesson)</span>
                            </label>
                            <input type="url" 
                                   id="video_url" 
                                   name="video_url" 
                                   value="{{ old('video_url') }}"
                                   x-model="formData.video_url"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                            <p class="mt-1 text-sm text-gray-500">Add a YouTube or Vimeo URL to automatically create the first lesson</p>
                            @error('video_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 2: Course Content & Description -->
                <div x-show="currentStep === 1" class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Course Content & Description</h2>
                    
                    <!-- Full Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
                        <div id="description-editor" class="bg-white border border-gray-300 rounded-lg min-h-[300px]"></div>
                        <textarea name="description" id="description" class="hidden" required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Learning Objectives -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Learning Objectives</label>
                        <div id="learning-objectives-container">
                            <div class="flex items-center space-x-2 mb-2 objective-row">
                                <input type="text" 
                                       name="learning_objectives[0]" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="What will students learn?"
                                       value="{{ old('learning_objectives.0') }}">
                                <button type="button" 
                                        onclick="removeObjectiveRow(this)"
                                        class="text-red-600 hover:text-red-800 remove-btn"
                                        style="display: none;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="addObjectiveRow()"
                                class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                            + Add Objective
                        </button>
                        @error('learning_objectives')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prerequisites -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prerequisites</label>
                        <div id="prerequisites-container">
                            <div class="flex items-center space-x-2 mb-2 prerequisite-row">
                                <input type="text" 
                                       name="prerequisites[0]" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="What should students know beforehand?"
                                       value="{{ old('prerequisites.0') }}">
                                <button type="button" 
                                        onclick="removePrerequisiteRow(this)"
                                        class="text-red-600 hover:text-red-800 remove-btn"
                                        style="display: none;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="addPrerequisiteRow()"
                                class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                            + Add Prerequisite
                        </button>
                        @error('prerequisites')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Step 3: Pricing & Access -->
                <div x-show="currentStep === 2" class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Pricing & Access Settings</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Free or Paid -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Course Type</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="is_free" 
                                           value="1" 
                                           x-model="formData.is_free"
                                           class="text-purple-600 focus:ring-purple-500" 
                                           {{ old('is_free') == '1' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Free Course</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="is_free" 
                                           value="0" 
                                           x-model="formData.is_free"
                                           class="text-purple-600 focus:ring-purple-500" 
                                           {{ old('is_free') == '0' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Paid Course</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price (shown only for paid courses) -->
                        <div x-show="formData.is_free === '0'">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Course Price *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price') }}"
                                       x-model="formData.price"
                                       step="0.01" 
                                       min="0"
                                       class="w-full pl-7 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration (minutes)</label>
                            <input type="number" 
                                   id="duration_minutes" 
                                   name="duration_minutes" 
                                   value="{{ old('duration_minutes') }}"
                                   x-model="formData.duration_minutes"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="e.g., 120">
                            @error('duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Initial Status</label>
                            <select id="status" 
                                    name="status" 
                                    x-model="formData.status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Public)</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 4: SEO & Metadata -->
                <div x-show="currentStep === 3" class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">SEO & Metadata</h2>
                    
                    <div class="space-y-6">
                        <!-- SEO Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">SEO Title</label>
                            <input type="text" 
                                   id="meta_title" 
                                   name="meta_title" 
                                   value="{{ old('meta_title') }}"
                                   x-model="formData.meta_title"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Optimized title for search engines...">
                            <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters</p>
                        </div>

                        <!-- SEO Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">SEO Description</label>
                            <textarea id="meta_description" 
                                      name="meta_description" 
                                      rows="3"
                                      x-model="formData.meta_description"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Compelling description for search results...">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                        </div>

                        <!-- Course Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Course URL Slug</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    {{ url('/courses') }}/
                                </span>
                                <input type="text" 
                                       id="slug" 
                                       name="slug" 
                                       value="{{ old('slug') }}"
                                       x-model="formData.slug"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="course-url-slug">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from title</p>
                        </div>

                        <!-- Tags -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                            <input type="text" 
                                   id="tags" 
                                   name="tags" 
                                   value="{{ old('tags') }}"
                                   x-model="formData.tags"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="web development, programming, javascript (comma-separated)">
                            <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.courses.index') }}" 
                               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                                Cancel
                            </a>
                            
                            <button type="button" 
                                    x-show="currentStep > 0"
                                    @click="previousStep()"
                                    class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                                Previous
                            </button>
                        </div>
                        
                        <div class="flex space-x-3">
                            <!-- Next Step Button -->
                            <button type="button" 
                                    onclick="nextStep()"
                                    id="next-btn"
                                    class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold"
                                    style="display: inline-block !important; color: white !important; background-color: #7C3AED !important;">
                                <span style="color: white !important; font-weight: bold;">➡️ Next Step</span>
                            </button>
                            
                            <!-- Always show save button -->
                            <button type="submit" 
                                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                ✅ Create Course
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
    <script>
        // Global variables for step management
        let currentStep = 0;
        const totalSteps = 4;
        let quill = null;

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeQuill();
            updateStepDisplay();
            setupEventListeners();
        });

        function initializeQuill() {
            if (typeof Quill !== 'undefined') {
                quill = new Quill('#description-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'indent': '-1'}, { 'indent': '+1' }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });

                // Set initial content if any
                const hiddenTextarea = document.getElementById('description');
                if (hiddenTextarea && hiddenTextarea.value) {
                    quill.root.innerHTML = hiddenTextarea.value;
                }

                // Sync with hidden textarea
                quill.on('text-change', () => {
                    document.getElementById('description').value = quill.root.innerHTML;
                });
            }
        }

        function setupEventListeners() {
            // Price field visibility
            document.querySelectorAll('input[name="is_free"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const priceField = document.querySelector('#price-field, [x-show*="is_free"]');
                    const priceInput = document.getElementById('price');
                    
                    if (this.value === '0') {
                        if (priceField) priceField.style.display = 'block';
                    } else {
                        if (priceField) priceField.style.display = 'none';
                        if (priceInput) priceInput.value = '0';
                    }
                });
            });
            
            // Initialize remove button visibility
            updateRemoveButtons();
        }

        // Learning Objectives Management
        function addObjectiveRow() {
            const container = document.getElementById('learning-objectives-container');
            const rowCount = container.querySelectorAll('.objective-row').length;
            
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-2 mb-2 objective-row';
            newRow.innerHTML = `
                <input type="text" 
                       name="learning_objectives[${rowCount}]" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="What will students learn?">
                <button type="button" 
                        onclick="removeObjectiveRow(this)"
                        class="text-red-600 hover:text-red-800 remove-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(newRow);
            updateRemoveButtons();
        }

        function removeObjectiveRow(button) {
            const row = button.closest('.objective-row');
            row.remove();
            
            // Reindex the remaining inputs
            const container = document.getElementById('learning-objectives-container');
            const inputs = container.querySelectorAll('input[name^="learning_objectives"]');
            inputs.forEach((input, index) => {
                input.name = `learning_objectives[${index}]`;
            });
            
            updateRemoveButtons();
        }

        // Prerequisites Management
        function addPrerequisiteRow() {
            const container = document.getElementById('prerequisites-container');
            const rowCount = container.querySelectorAll('.prerequisite-row').length;
            
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-2 mb-2 prerequisite-row';
            newRow.innerHTML = `
                <input type="text" 
                       name="prerequisites[${rowCount}]" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="What should students know beforehand?">
                <button type="button" 
                        onclick="removePrerequisiteRow(this)"
                        class="text-red-600 hover:text-red-800 remove-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(newRow);
            updateRemoveButtons();
        }

        function removePrerequisiteRow(button) {
            const row = button.closest('.prerequisite-row');
            row.remove();
            
            // Reindex the remaining inputs
            const container = document.getElementById('prerequisites-container');
            const inputs = container.querySelectorAll('input[name^="prerequisites"]');
            inputs.forEach((input, index) => {
                input.name = `prerequisites[${index}]`;
            });
            
            updateRemoveButtons();
        }

        // Update remove button visibility
        function updateRemoveButtons() {
            // Objectives
            const objectiveRows = document.querySelectorAll('.objective-row');
            objectiveRows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-btn');
                if (removeBtn) {
                    removeBtn.style.display = objectiveRows.length > 1 ? 'block' : 'none';
                }
            });
            
            // Prerequisites
            const prerequisiteRows = document.querySelectorAll('.prerequisite-row');
            prerequisiteRows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-btn');
                if (removeBtn) {
                    removeBtn.style.display = prerequisiteRows.length > 1 ? 'block' : 'none';
                }
            });
        }

        function nextStep() {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                updateStepDisplay();
            }
        }

        function previousStep() {
            if (currentStep > 0) {
                currentStep--;
                updateStepDisplay();
            }
        }

        function updateStepDisplay() {
            console.log('Updating step display, currentStep:', currentStep);
            
            // Update step indicators
            document.querySelectorAll('.step-indicator').forEach((indicator) => {
                const stepNumber = parseInt(indicator.getAttribute('data-step'));
                const span = indicator.querySelector('span');
                
                if (stepNumber <= currentStep) {
                    // Active/completed steps - purple
                    indicator.classList.remove('bg-gray-400', 'border-gray-400');
                    indicator.classList.add('bg-purple-600', 'border-purple-600');
                    indicator.style.backgroundColor = '#7C3AED';
                    indicator.style.borderColor = '#7C3AED';
                    if (span) {
                        span.style.color = 'white';
                        span.style.fontWeight = 'bold';
                    }
                } else {
                    // Inactive steps - gray
                    indicator.classList.remove('bg-purple-600', 'border-purple-600');
                    indicator.classList.add('bg-gray-400', 'border-gray-400');
                    indicator.style.backgroundColor = '#9CA3AF';
                    indicator.style.borderColor = '#9CA3AF';
                    if (span) {
                        span.style.color = 'white';
                        span.style.fontWeight = 'bold';
                    }
                }
            });

            // Update step labels
            document.querySelectorAll('.step-label').forEach((label) => {
                const stepNumber = parseInt(label.getAttribute('data-step'));
                if (stepNumber <= currentStep) {
                    label.classList.remove('text-gray-500');
                    label.classList.add('text-purple-600', 'font-semibold');
                } else {
                    label.classList.remove('text-purple-600', 'font-semibold');
                    label.classList.add('text-gray-500');
                }
            });

            // Show/hide step content
            document.querySelectorAll('[x-show]').forEach(element => {
                const showCondition = element.getAttribute('x-show');
                let shouldShow = false;

                if (showCondition.includes('currentStep === 0')) shouldShow = currentStep === 0;
                else if (showCondition.includes('currentStep === 1')) shouldShow = currentStep === 1;
                else if (showCondition.includes('currentStep === 2')) shouldShow = currentStep === 2;
                else if (showCondition.includes('currentStep === 3')) shouldShow = currentStep === 3;
                else if (showCondition.includes('currentStep > 0')) shouldShow = currentStep > 0;

                element.style.display = shouldShow ? 'block' : 'none';
            });

            // Update Next button text and visibility
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.querySelector('button[onclick="previousStep()"]');
            
            if (nextBtn) {
                if (currentStep < totalSteps - 1) {
                    nextBtn.style.display = 'inline-block';
                    nextBtn.style.color = 'white';
                    nextBtn.style.backgroundColor = '#7C3AED';
                    nextBtn.innerHTML = '<span style="color: white !important; font-weight: bold;">➡️ Next Step</span>';
                } else {
                    nextBtn.style.display = 'none';
                }
            }

            // Update Previous button visibility
            if (prevBtn) {
                prevBtn.style.display = currentStep > 0 ? 'inline-block' : 'none';
            }

            console.log('Step display updated');
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const preview = document.querySelector('[x-show="imagePreview"] img');
                    const container = document.querySelector('[x-show="imagePreview"]');
                    if (preview && container) {
                        preview.src = e.target.result;
                        container.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // Alpine.js compatibility functions
        function courseCreator() {
            return {
                currentStep: 0,
                steps: [
                    { title: 'Basic Info' },
                    { title: 'Content' },
                    { title: 'Pricing' },
                    { title: 'SEO' }
                ],
                formData: {
                    title: '',
                    short_description: '',
                    category_id: '',
                    level: 'beginner',
                    is_free: '1',
                    price: '',
                    duration_minutes: '',
                    status: 'draft',
                    meta_title: '',
                    meta_description: '',
                    slug: '',
                    tags: '',
                    video_url: ''
                },
                imagePreview: null,
                quill: null,

                init() {
                    // Sync with global functions
                    this.currentStep = currentStep;
                },

                nextStep() { nextStep(); },
                previousStep() { previousStep(); },
                previewImage(event) { previewImage(event); }
            }
        }
    </script>
    @endpush
</x-app-layout>
