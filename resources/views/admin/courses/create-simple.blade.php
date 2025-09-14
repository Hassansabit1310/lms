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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Debug Info -->
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <strong>Debug Info:</strong>
                Categories count: {{ $categories ? $categories->count() : 'null' }}
                <br>Alpine.js test: <span x-data="{ test: 'Alpine.js is working!' }" x-text="test"></span>
            </div>

            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Course Information</h1>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Course Title -->
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Select a category</option>
                                @if($categories)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            @error('level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="6"
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Provide a detailed description of your course...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Free or Paid -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Course Type</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="is_free" 
                                           value="1" 
                                           class="text-purple-600 focus:ring-purple-500" 
                                           {{ old('is_free', '1') == '1' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Free Course</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="is_free" 
                                           value="0" 
                                           class="text-purple-600 focus:ring-purple-500" 
                                           {{ old('is_free') == '0' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Paid Course</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price (shown for paid courses) -->
                        <div id="price-field" style="{{ old('is_free', '1') == '1' ? 'display: none;' : '' }}">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Course Price *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', 0) }}"
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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Public)</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course Thumbnail -->
                        <div class="lg:col-span-2">
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
                            <input type="file" 
                                   id="thumbnail" 
                                   name="thumbnail" 
                                   accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.courses.index') }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </a>
                        
                        <button type="submit"
                                class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                            Create Course
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
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
    </script>
    @endpush
</x-app-layout>
