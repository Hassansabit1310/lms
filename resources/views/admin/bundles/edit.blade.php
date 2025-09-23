<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">✏️ Edit Bundle</h1>
                        <p class="text-slate-200 text-lg">Update {{ $bundle->name }}</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">Admin Panel</div>
                                <div class="text-slate-200 text-sm">Bundle Management</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.bundles.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i> Back to Bundles
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('admin.bundles.show', $bundle) }}" class="text-indigo-600 hover:text-indigo-800">
                    View Bundle
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Bundle Information</h3>
                    <p class="text-sm text-gray-600 mt-1">Update the bundle details and course selection</p>
                </div>

                <form method="POST" action="{{ route('admin.bundles.update', $bundle) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Bundle Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $bundle->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Bundle Price ($)</label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   step="0.01" 
                                   min="0"
                                   value="{{ old('price', $bundle->price) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                                   required>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Brief description of the bundle...">{{ old('description', $bundle->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Long Description -->
                    <div>
                        <label for="long_description" class="block text-sm font-medium text-gray-700 mb-2">Detailed Description</label>
                        <textarea id="long_description" 
                                  name="long_description" 
                                  rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Detailed description, benefits, and what's included...">{{ old('long_description', $bundle->long_description) }}</textarea>
                        @error('long_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Bundle Image</label>
                        @if($bundle->image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $bundle->image) }}" 
                                     alt="Current bundle image" 
                                     class="w-32 h-32 object-cover rounded-lg border">
                                <p class="text-sm text-gray-500 mt-1">Current image</p>
                            </div>
                        @endif
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-sm text-gray-500 mt-1">Upload a new image to replace the current one (optional)</p>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Course Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Select Courses</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-4">Select at least 2 courses for this bundle. The first course will be marked as primary.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-64 overflow-y-auto">
                                @foreach($courses as $course)
                                    <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-white transition-colors cursor-pointer {{ in_array($course->id, $bundleCourseIds) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                        <input type="checkbox" 
                                               name="courses[]" 
                                               value="{{ $course->id }}"
                                               {{ in_array($course->id, $bundleCourseIds) ? 'checked' : '' }}
                                               class="mt-1 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <div class="flex-1">
                                            <div class="font-medium text-sm text-gray-900">{{ $course->title }}</div>
                                            <div class="text-sm text-gray-500">${{ number_format($course->price, 2) }}</div>
                                            @if($course->lessons_count ?? 0 > 0)
                                                <div class="text-xs text-gray-400">{{ $course->lessons_count }} lessons</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('courses')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Settings -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage</label>
                            <input type="number" 
                                   id="discount_percentage" 
                                   name="discount_percentage" 
                                   min="0" 
                                   max="100"
                                   value="{{ old('discount_percentage', $bundle->discount_percentage) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-sm text-gray-500 mt-1">Optional: override the calculated discount</p>
                        </div>

                        <div>
                            <label for="max_enrollments" class="block text-sm font-medium text-gray-700 mb-2">Max Enrollments</label>
                            <input type="number" 
                                   id="max_enrollments" 
                                   name="max_enrollments" 
                                   min="1"
                                   value="{{ old('max_enrollments', $bundle->max_enrollments) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-sm text-gray-500 mt-1">Leave empty for unlimited enrollments</p>
                        </div>
                    </div>

                    <!-- Availability Dates -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                            <input type="datetime-local" 
                                   id="available_from" 
                                   name="available_from"
                                   value="{{ old('available_from', $bundle->available_from?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="available_until" class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                            <input type="datetime-local" 
                                   id="available_until" 
                                   name="available_until"
                                   value="{{ old('available_until', $bundle->available_until?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active"
                                   {{ old('is_active', $bundle->is_active) ? 'checked' : '' }}
                                   class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Active (visible to users)</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_featured"
                                   {{ old('is_featured', $bundle->is_featured) ? 'checked' : '' }}
                                   class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Featured (highlighted on homepage)</span>
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.bundles.index') }}" 
                           class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Update Bundle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate pricing info when courses are selected
        document.addEventListener('DOMContentLoaded', function() {
            const courseCheckboxes = document.querySelectorAll('input[name="courses[]"]');
            const priceInput = document.getElementById('price');
            
            function updatePricing() {
                let totalOriginalPrice = 0;
                courseCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const courseCard = checkbox.closest('label');
                        const priceText = courseCard.querySelector('.text-gray-500').textContent;
                        const price = parseFloat(priceText.replace('$', '').replace(',', ''));
                        totalOriginalPrice += price;
                    }
                });
                
                // Show pricing info
                const existingInfo = document.getElementById('pricing-info');
                if (existingInfo) existingInfo.remove();
                
                if (totalOriginalPrice > 0) {
                    const info = document.createElement('div');
                    info.id = 'pricing-info';
                    info.className = 'mt-2 p-3 bg-blue-50 rounded-lg text-sm';
                    info.innerHTML = `
                        <div class="text-blue-800">
                            <strong>Selected courses total: $${totalOriginalPrice.toFixed(2)}</strong>
                        </div>
                        <div class="text-blue-600 text-xs mt-1">
                            Set your bundle price below to offer a discount
                        </div>
                    `;
                    document.querySelector('input[name="courses[]"]').closest('.bg-gray-50').appendChild(info);
                }
            }
            
            courseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updatePricing);
            });
            
            // Initial calculation
            updatePricing();
        });
    </script>
</x-app-layout>
