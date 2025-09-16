<x-app-layout>
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Learning</h1>
            <p class="text-gray-600 mt-2">Track your progress and access your courses</p>
        </div>

        <!-- Active Subscription -->
        @if($activeSubscription)
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold">{{ ucfirst($activeSubscription->plan_type) }} Subscription</h3>
                    <p class="opacity-90">Access to all premium courses</p>
                    <p class="text-sm opacity-75 mt-1">
                        Expires: {{ $activeSubscription->end_date->format('M d, Y') }} 
                        ({{ $activeSubscription->days_remaining }} days remaining)
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">${{ number_format($activeSubscription->amount, 2) }}</div>
                    <div class="text-sm opacity-75">{{ $activeSubscription->plan_type }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Course Enrollments -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">My Courses</h2>
            </div>
            
            @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($enrollments as $enrollment)
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-video bg-gray-100 relative">
                        @if($enrollment->course->image)
                            <img src="{{ asset('storage/' . $enrollment->course->image) }}" 
                                 alt="{{ $enrollment->course->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-play-circle text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Progress Bar -->
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2">
                            <div class="bg-gray-300 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" 
                                     style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                            <div class="text-white text-xs mt-1">
                                {{ $enrollment->progress_percentage }}% complete
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $enrollment->course->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($enrollment->course->description, 100) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Enrolled: {{ $enrollment->enrolled_at->format('M d, Y') }}
                            </div>
                            <a href="{{ route('courses.show', $enrollment->course) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                Continue Learning
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $enrollments->links() }}
            </div>
            @else
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No courses yet</h3>
                <p class="text-gray-600 mb-6">Start learning by enrolling in your first course</p>
                <a href="{{ route('courses.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Courses
                </a>
            </div>
            @endif
        </div>

        <!-- Purchased Bundles -->
        @if($purchasedBundles->count() > 0)
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">My Bundles</h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                @foreach($purchasedBundles as $bundle)
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $bundle->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $bundle->courses->count() }} courses included</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600">${{ number_format($bundle->price, 2) }}</div>
                            @if($bundle->original_price)
                            <div class="text-sm text-gray-500 line-through">${{ number_format($bundle->original_price, 2) }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        @foreach($bundle->courses as $course)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-700">{{ $course->title }}</span>
                            <a href="{{ route('courses.show', $course) }}" 
                               class="text-blue-600 text-sm hover:text-blue-800">
                                View →
                            </a>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('bundles.show', $bundle) }}" 
                           class="text-blue-600 text-sm font-medium hover:text-blue-800">
                            View Bundle Details →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
</x-app-layout>
