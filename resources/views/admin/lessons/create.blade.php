<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #059669 0%, #10B981 50%, #34D399 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    ✨ Create New Lesson
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">for {{ $course->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.courses.lessons.store', $course) }}" method="POST" enctype="multipart/form-data" x-data="lessonCreator()">
                @csrf
                
                <!-- Navigation -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.courses.edit', $course) }}" 
                               class="text-gray-500 hover:text-gray-700 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Create New Lesson</h1>
                                <p class="text-gray-600">Course: {{ $course->title }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Lesson {{ $nextOrder }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content Type Selection -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Choose Content Type</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <label class="content-type-card" :class="formData.type === 'youtube' ? 'selected' : ''">
                            <input type="radio" name="type" value="youtube" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">YouTube Video</h3>
                                    <p class="text-sm text-gray-600">Embed YouTube videos</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'vimeo' ? 'selected' : ''">
                            <input type="radio" name="type" value="vimeo" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197c1.185-1.044 2.351-2.084 3.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.1 3.447 3.838.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.473.06 3.628 1.664 3.493 4.797l-.013.01z"/>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">Vimeo Video</h3>
                                    <p class="text-sm text-gray-600">Embed Vimeo videos</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'h5p' ? 'selected' : ''">
                            <input type="radio" name="type" value="h5p" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">H5P Interactive</h3>
                                    <p class="text-sm text-gray-600">Interactive H5P content</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'code' ? 'selected' : ''">
                            <input type="radio" name="type" value="code" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">Code/Matter.js</h3>
                                    <p class="text-sm text-gray-600">Code examples & physics</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'text' ? 'selected' : ''">
                            <input type="radio" name="type" value="text" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">Text Content</h3>
                                    <p class="text-sm text-gray-600">Rich text and articles</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'pdf' ? 'selected' : ''">
                            <input type="radio" name="type" value="pdf" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">PDF Document</h3>
                                    <p class="text-sm text-gray-600">Upload PDF files</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'quiz' ? 'selected' : ''">
                            <input type="radio" name="type" value="quiz" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">Quiz/Assessment</h3>
                                    <p class="text-sm text-gray-600">Interactive quizzes</p>
                                </div>
                            </div>
                        </label>

                        <label class="content-type-card" :class="formData.type === 'interactive' ? 'selected' : ''">
                            <input type="radio" name="type" value="interactive" x-model="formData.type" class="hidden">
                            <div class="p-6 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">Interactive</h3>
                                    <p class="text-sm text-gray-600">Custom interactive content</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Lesson Information</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Lesson Title -->
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Lesson Title *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   x-model="formData.title"
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Enter a clear, descriptive lesson title...">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      x-model="formData.description"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Describe what students will learn in this lesson...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Lesson Order</label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', $nextOrder) }}"
                                   x-model="formData.order"
                                   min="1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                            <input type="number" 
                                   id="duration_minutes" 
                                   name="duration_minutes" 
                                   value="{{ old('duration_minutes') }}"
                                   x-model="formData.duration_minutes"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="e.g., 15">
                            @error('duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Free Preview -->
                        <div class="lg:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_free" 
                                       value="1" 
                                       x-model="formData.is_free"
                                       class="text-green-600 focus:ring-green-500 rounded" 
                                       {{ old('is_free') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Allow free preview (accessible without enrollment)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Content Area (Dynamic based on type) -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Lesson Content</h2>
                        <div class="text-sm text-gray-600 bg-blue-50 px-3 py-1 rounded-full">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Primary content type selected above
                        </div>
                    </div>
                    
                    <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            <strong>Pro Tip:</strong> After creating this lesson, you can add additional content types (H5P, quizzes, etc.) by editing the lesson.
                        </p>
                    </div>
                    
                    <!-- YouTube Content -->
                    <div x-show="formData.type === 'youtube'">
                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube URL *</label>
                            <input type="url" 
                                   id="youtube_url" 
                                   name="content" 
                                   x-model="formData.content"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <p class="mt-1 text-sm text-gray-500">Paste the YouTube video URL</p>
                        </div>
                    </div>

                    <!-- Vimeo Content -->
                    <div x-show="formData.type === 'vimeo'">
                        <div>
                            <label for="vimeo_url" class="block text-sm font-medium text-gray-700 mb-2">Vimeo URL *</label>
                            <input type="url" 
                                   id="vimeo_url" 
                                   name="content" 
                                   x-model="formData.content"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="https://vimeo.com/...">
                            <p class="mt-1 text-sm text-gray-500">Paste the Vimeo video URL</p>
                        </div>
                    </div>

                    <!-- H5P Content -->
                    <div x-show="formData.type === 'h5p'">
                        <div class="space-y-4">
                            <div>
                                <label for="h5p_file" class="block text-sm font-medium text-gray-700 mb-2">H5P Package *</label>
                                <input type="file" 
                                       id="h5p_file" 
                                       name="h5p_file" 
                                       accept=".h5p"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <p class="mt-1 text-sm text-gray-500">Upload an H5P package file (.h5p)</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="h5p_width" class="block text-sm font-medium text-gray-700 mb-2">Width</label>
                                    <input type="text" 
                                           id="h5p_width" 
                                           name="h5p_settings[width]" 
                                           value="100%"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="h5p_height" class="block text-sm font-medium text-gray-700 mb-2">Height</label>
                                    <input type="text" 
                                           id="h5p_height" 
                                           name="h5p_settings[height]" 
                                           value="400px"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code/Matter.js Content -->
                    <div x-show="formData.type === 'code'">
                        <div class="space-y-4">
                            <div>
                                <label for="code_type" class="block text-sm font-medium text-gray-700 mb-2">Code Type</label>
                                <select id="code_type" name="code_settings[type]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="matter_js">Matter.js Physics</option>
                                    <option value="javascript">JavaScript</option>
                                    <option value="html">HTML</option>
                                    <option value="css">CSS</option>
                                    <option value="python">Python</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="code_content" class="block text-sm font-medium text-gray-700 mb-2">Code Content *</label>
                                <textarea id="code_content" 
                                          name="content" 
                                          rows="15"
                                          x-model="formData.content"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm"
                                          placeholder="// Enter your code here..."></textarea>
                            </div>
                            <div x-show="formData.code_type === 'matter_js'" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Canvas Width</label>
                                    <input type="number" name="code_settings[width]" value="800" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Canvas Height</label>
                                    <input type="number" name="code_settings[height]" value="600" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text Content -->
                    <div x-show="formData.type === 'text'">
                        <div>
                            <label for="text_content" class="block text-sm font-medium text-gray-700 mb-2">Lesson Content *</label>
                            <div id="text-editor" class="bg-white border border-gray-300 rounded-lg min-h-[400px]"></div>
                            <textarea name="content" id="text_content" class="hidden"></textarea>
                        </div>
                    </div>

                    <!-- PDF Content -->
                    <div x-show="formData.type === 'pdf'">
                        <div>
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">PDF Document *</label>
                            <input type="file" 
                                   id="pdf_file" 
                                   name="pdf_file" 
                                   accept=".pdf"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500">Upload a PDF document (max 10MB)</p>
                        </div>
                    </div>

                    <!-- Quiz Content -->
                    <div x-show="formData.type === 'quiz'">
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-800 text-sm">
                                    <strong>Note:</strong> The quiz builder will be available after creating the lesson. 
                                    You can add questions and configure quiz settings in the lesson editor.
                                </p>
                            </div>
                            <div>
                                <label for="quiz_title" class="block text-sm font-medium text-gray-700 mb-2">Quiz Title</label>
                                <input type="text" 
                                       id="quiz_title" 
                                       name="quiz_settings[title]" 
                                       x-model="formData.title"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Quiz title (will use lesson title if empty)">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="quiz_time_limit" class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes)</label>
                                    <input type="number" 
                                           id="quiz_time_limit" 
                                           name="quiz_settings[time_limit]" 
                                           value="30"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="quiz_passing_score" class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%)</label>
                                    <input type="number" 
                                           id="quiz_passing_score" 
                                           name="quiz_settings[passing_score]" 
                                           value="70"
                                           min="0" max="100"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Content -->
                    <div x-show="formData.type === 'interactive'">
                        <div class="space-y-4">
                            <div>
                                <label for="interactive_type" class="block text-sm font-medium text-gray-700 mb-2">Interactive Type</label>
                                <select id="interactive_type" name="interactive_settings[type]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="drag_drop">Drag & Drop</option>
                                    <option value="timeline">Timeline</option>
                                    <option value="chart">Chart/Graph</option>
                                    <option value="simulation">Simulation</option>
                                    <option value="custom">Custom HTML/JS</option>
                                </select>
                            </div>
                            <div>
                                <label for="interactive_config" class="block text-sm font-medium text-gray-700 mb-2">Configuration (JSON)</label>
                                <textarea id="interactive_config" 
                                          name="content" 
                                          rows="10"
                                          x-model="formData.content"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm"
                                          placeholder='{"items": [], "config": {}}'></textarea>
                                <p class="mt-1 text-sm text-gray-500">Configure your interactive content using JSON format</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.courses.edit', $course) }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </a>
                        
                        <div class="flex space-x-3">
                            <button type="submit" name="action" value="save_draft"
                                    class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                                Save as Draft
                            </button>
                            <button type="submit" name="action" value="save_and_publish"
                                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                Create & Publish
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
        function lessonCreator() {
            return {
                formData: {
                    title: '',
                    description: '',
                    type: 'youtube',
                    content: '',
                    order: {{ $nextOrder }},
                    duration_minutes: '',
                    is_free: false
                },
                quill: null,

                init() {
                    this.$nextTick(() => {
                        this.initializeQuill();
                        this.updateContentTypeStyles();
                    });

                    // Watch for type changes
                    this.$watch('formData.type', () => {
                        this.updateContentTypeStyles();
                        if (this.formData.type === 'text' && this.quill) {
                            this.$nextTick(() => this.quill.focus());
                        }
                    });
                },

                initializeQuill() {
                    this.quill = new Quill('#text-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                ['blockquote', 'code-block'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                ['link', 'image', 'video'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'align': [] }],
                                ['clean']
                            ]
                        }
                    });

                    // Sync with hidden textarea
                    this.quill.on('text-change', () => {
                        document.getElementById('text_content').value = this.quill.root.innerHTML;
                        this.formData.content = this.quill.root.innerHTML;
                    });
                },

                updateContentTypeStyles() {
                    // Update visual selection of content type cards
                    document.querySelectorAll('.content-type-card').forEach(card => {
                        const input = card.querySelector('input[type="radio"]');
                        const cardDiv = card.querySelector('div');
                        
                        if (input.checked) {
                            cardDiv.classList.remove('border-gray-200');
                            cardDiv.classList.add('border-green-500', 'bg-green-50');
                        } else {
                            cardDiv.classList.add('border-gray-200');
                            cardDiv.classList.remove('border-green-500', 'bg-green-50');
                        }
                    });
                }
            }
        }

        // Additional styling and click handlers for content type selection
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .content-type-card.selected > div {
                    border-color: #10B981 !important;
                    background-color: #ECFDF5 !important;
                }
            `;
            document.head.appendChild(style);
            
            // Add click handlers to content type cards
            document.querySelectorAll('.content-type-card').forEach(card => {
                card.addEventListener('click', function() {
                    const input = this.querySelector('input[type="radio"]');
                    if (input) {
                        // Uncheck all other radio buttons
                        document.querySelectorAll('.content-type-card input[type="radio"]').forEach(radio => {
                            radio.checked = false;
                        });
                        
                        // Check this radio button
                        input.checked = true;
                        
                        // Trigger Alpine.js update
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        // Update styles immediately
                        updateContentTypeVisuals();
                    }
                });
            });
            
            // Function to update visual styles
            function updateContentTypeVisuals() {
                document.querySelectorAll('.content-type-card').forEach(card => {
                    const input = card.querySelector('input[type="radio"]');
                    const cardDiv = card.querySelector('div');
                    
                    if (input && input.checked) {
                        cardDiv.classList.remove('border-gray-200');
                        cardDiv.classList.add('border-green-500', 'bg-green-50');
                    } else {
                        cardDiv.classList.add('border-gray-200');
                        cardDiv.classList.remove('border-green-500', 'bg-green-50');
                    }
                });
            }
            
            // Initialize on page load
            setTimeout(updateContentTypeVisuals, 100);
        });
    </script>
    @endpush
</x-app-layout>
