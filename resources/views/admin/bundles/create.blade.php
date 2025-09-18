<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">📦 Create Bundle</h1>
                        <p class="text-slate-200 text-lg">Create a new course bundle with discounted pricing</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">Admin Panel</div>
                                <div class="text-slate-200 text-sm">Bundle Creation</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.bundles.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i> Back to Bundles
                </a>
            </div>
        </div>

        <!-- Bundle Creation Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Bundle Information</h2>
                <p class="text-gray-600 text-sm mt-1">Create a bundle with multiple courses at a discounted price</p>
            </div>

            <form method="POST" action="{{ route('admin.bundles.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bundle Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Bundle Name *</label>
                        <input type="text" id="name" name="name" required 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="e.g., Web Development Complete Bundle">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Bundle Price *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required
                               value="{{ old('price') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="49.99">
                        @error('price')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Discount Percentage -->
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount %</label>
                        <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100"
                               value="{{ old('discount_percentage', 0) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="30">
                        @error('discount_percentage')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                              placeholder="Brief description of what's included in this bundle">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Long Description -->
                <div class="mt-6">
                    <label for="long_description" class="block text-sm font-medium text-gray-700 mb-2">Detailed Description</label>
                    <textarea id="long_description" name="long_description" rows="5"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                              placeholder="Detailed description of the bundle benefits and what students will learn">{{ old('long_description') }}</textarea>
                    @error('long_description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Selection -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Select Courses *</label>
                    <div class="text-sm text-gray-600 mb-4">Choose at least 2 courses to include in this bundle. The original price will be calculated automatically.</div>
                    
                    @if($courses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($courses as $course)
                        <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="courses[]" value="{{ $course->id }}" 
                                   class="mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                   {{ in_array($course->id, old('courses', [])) ? 'checked' : '' }}>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $course->title }}</div>
                                <div class="text-sm text-gray-500">{{ $course->category->name ?? 'Uncategorized' }}</div>
                                <div class="text-sm font-medium text-green-600">${{ number_format($course->price, 2) }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">No Courses Available</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>You need to create some published, paid courses before you can create bundles.</p>
                                    <a href="{{ route('admin.courses.create') }}" class="font-medium underline">Create a course first</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @error('courses')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bundle Image -->
                <div class="mt-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Bundle Image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-gray-500 text-sm mt-1">Upload a banner image for the bundle (optional)</p>
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Advanced Options -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Advanced Options</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Max Enrollments -->
                        <div>
                            <label for="max_enrollments" class="block text-sm font-medium text-gray-700 mb-2">Max Enrollments</label>
                            <input type="number" id="max_enrollments" name="max_enrollments" min="1"
                                   value="{{ old('max_enrollments') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Leave empty for unlimited">
                            <p class="text-gray-500 text-xs mt-1">Limit the number of people who can purchase this bundle</p>
                        </div>

                        <!-- Available From -->
                        <div>
                            <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                            <input type="datetime-local" id="available_from" name="available_from"
                                   value="{{ old('available_from') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <!-- Available Until -->
                        <div>
                            <label for="available_until" class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                            <input type="datetime-local" id="available_until" name="available_until"
                                   value="{{ old('available_until') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Status Options -->
                    <div class="mt-4 space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Active (visible to users)</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1"
                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Featured (highlighted on homepage)</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('admin.bundles.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Create Bundle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
