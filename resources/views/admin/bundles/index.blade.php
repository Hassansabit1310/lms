@extends('layouts.admin')

@section('title', 'Manage Bundles')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Course Bundles</h1>
                <p class="text-gray-600 mt-2">Manage your course bundle offerings</p>
            </div>
            <a href="{{ route('admin.bundles.create') }}" 
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Create Bundle
            </a>
        </div>

        <!-- Bundles Grid -->
        @if($bundles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($bundles as $bundle)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Bundle Image -->
                <div class="aspect-video bg-gray-100 relative">
                    @if($bundle->image)
                        <img src="{{ asset('storage/' . $bundle->image) }}" 
                             alt="{{ $bundle->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3">
                        @if($bundle->is_active)
                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">Active</span>
                        @else
                            <span class="bg-gray-500 text-white px-2 py-1 rounded-full text-xs font-medium">Inactive</span>
                        @endif
                    </div>
                    
                    @if($bundle->is_featured)
                        <div class="absolute top-3 right-3">
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">Featured</span>
                        </div>
                    @endif
                </div>

                <!-- Bundle Info -->
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $bundle->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($bundle->description, 100) }}</p>
                    
                    <!-- Bundle Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                        <div>
                            <span class="text-gray-500">Courses:</span>
                            <span class="font-medium text-gray-900">{{ $bundle->courses->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Sales:</span>
                            <span class="font-medium text-gray-900">{{ $bundle->payments_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Price:</span>
                            <span class="font-medium text-green-600">${{ number_format($bundle->price, 2) }}</span>
                        </div>
                        @if($bundle->original_price)
                        <div>
                            <span class="text-gray-500">Savings:</span>
                            <span class="font-medium text-red-600">{{ $bundle->savings_percentage }}%</span>
                        </div>
                        @endif
                    </div>

                    <!-- Course List -->
                    @if($bundle->courses->count() > 0)
                    <div class="mb-4">
                        <div class="text-xs text-gray-500 mb-2">Included Courses:</div>
                        <div class="space-y-1">
                            @foreach($bundle->courses->take(3) as $course)
                            <div class="text-xs text-gray-700 flex items-center">
                                <i class="fas fa-book text-gray-400 mr-2 text-xs"></i>
                                {{ $course->title }}
                            </div>
                            @endforeach
                            @if($bundle->courses->count() > 3)
                                <div class="text-xs text-gray-500">
                                    +{{ $bundle->courses->count() - 3 }} more courses
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.bundles.show', $bundle) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </a>
                            <a href="{{ route('admin.bundles.edit', $bundle) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Edit
                            </a>
                        </div>
                        <form method="POST" action="{{ route('admin.bundles.destroy', $bundle) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this bundle?')" 
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $bundles->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No bundles yet</h3>
            <p class="text-gray-600 mb-6">Start creating course bundles to offer better value to your students</p>
            <a href="{{ route('admin.bundles.create') }}" 
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Create Your First Bundle
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
