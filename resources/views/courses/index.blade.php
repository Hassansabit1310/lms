<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    📚 Explore Our Course Library
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Discover thousands of courses taught by industry experts</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-300/50 p-8 mb-10">
                <div class="mb-6 text-center">
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">🔍 Find Your Perfect Course</h3>
                    <p class="text-slate-600">Use filters to discover courses that match your interests and skill level</p>
                </div>
                
                <form method="GET" action="{{ route('courses.index') }}" class="space-y-6">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search for courses, topics, or instructors..."
                               class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-slate-800 placeholder-slate-400 text-lg transition-all duration-200">
                    </div>

                    <!-- Filter Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">📂 Category</label>
                            <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-slate-800 transition-all duration-200">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Level Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">🎯 Level</label>
                            <select name="level" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-slate-800 transition-all duration-200">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>🌱 Beginner</option>
                                <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>🚀 Intermediate</option>
                                <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>⚡ Advanced</option>
                            </select>
                        </div>

                        <!-- Price Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">💰 Price</label>
                            <select name="price" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-slate-800 transition-all duration-200">
                                <option value="">All Prices</option>
                                <option value="free" {{ request('price') === 'free' ? 'selected' : '' }}>🎁 Free</option>
                                <option value="paid" {{ request('price') === 'paid' ? 'selected' : '' }}>💎 Paid</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">🔄 Sort By</label>
                            <select name="sort" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-slate-800 transition-all duration-200">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>⏰ Latest</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>🔥 Most Popular</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>⭐ Highest Rated</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>💲 Price: Low to High</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>💰 Price: High to Low</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-slate-600 to-gray-600 text-white font-bold rounded-2xl hover:from-slate-700 hover:to-gray-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                            </svg>
                            <span>Apply Filters</span>
                        </button>
                        
                        @if(request()->hasAny(['search', 'category', 'level', 'price', 'sort']))
                        <a href="{{ route('courses.index') }}" class="px-8 py-4 border-2 border-gray-300 dark:border-gray-600 text-slate-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>Clear Filters</span>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Results Info -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center px-6 py-3 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl border border-gray-200/50 dark:border-gray-700/50">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                        📊 Showing <span class="font-bold text-blue-600 dark:text-blue-400">{{ $courses->firstItem() ?? 0 }} - {{ $courses->lastItem() ?? 0 }}</span> of <span class="font-bold text-purple-600 dark:text-purple-400">{{ $courses->total() }}</span> course{{ $courses->total() !== 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            <!-- Course Grid -->
            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($courses as $course)
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl overflow-hidden transform hover:-translate-y-2 transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="relative overflow-hidden">
                            @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                            <div class="w-full h-56 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-black opacity-20"></div>
                                <div class="relative text-center">
                                    <div class="text-6xl mb-2">🎓</div>
                                    <div class="text-white font-bold text-lg">{{ Str::limit($course->title ?? 'Course', 20) }}</div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Course badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                @if($course->is_free ?? false)
                                <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                    🎁 FREE
                                </span>
                                @endif
                                <span class="px-3 py-1 bg-white/90 text-slate-800 text-xs font-bold rounded-full capitalize">
                                    @php
                                        $level = $course->level ?? 'beginner';
                                        $levelIcon = $level === 'beginner' ? '🌱' : ($level === 'intermediate' ? '🚀' : '⚡');
                                    @endphp
                                    {{ $levelIcon }} {{ ucfirst($level) }}
                                </span>
                            </div>
                            
                            <!-- Rating overlay -->
                            <div class="absolute top-4 right-4">
                                <div class="bg-white/90 backdrop-blur-sm rounded-xl px-3 py-1">
                                    @if(($course->reviews ?? collect())->count() > 0)
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400 text-sm">⭐</span>
                                        <span class="text-slate-800 text-sm font-bold">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                    </div>
                                    @else
                                    <span class="text-slate-600 text-xs">New</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                @if($course->category ?? false)
                                <span class="inline-flex items-center px-2 py-1 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full">
                                    📂 {{ $course->category->name }}
                                </span>
                                @endif
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $course->title ?? 'Awesome Course' }}
                            </h3>
                            
                            <p class="text-slate-600 dark:text-gray-300 mb-6 line-clamp-2 text-sm leading-relaxed">
                                {{ $course->short_description ?? $course->description ?? 'Learn amazing skills that will transform your career and unlock new opportunities.' }}
                            </p>
                            
                            <!-- Course Stats -->
                            <div class="flex items-center gap-4 mb-6 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <span>👥</span>
                                    <span>{{ rand(100, 2000) }} students</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span>⏱️</span>
                                    <span>{{ $course->duration_minutes ?? rand(60, 480) }}min</span>
                                </div>
                                @if(($course->reviews ?? collect())->count() > 0)
                                <div class="flex items-center gap-1">
                                    <span>💬</span>
                                    <span>{{ ($course->reviews ?? collect())->count() }} reviews</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="text-right">
                                    @if($course->is_free ?? false)
                                    <div class="text-2xl font-black text-green-600">FREE</div>
                                    <div class="text-xs text-gray-500 line-through">${{ number_format(rand(29, 99), 2) }}</div>
                                    @else
                                    <div class="text-2xl font-black text-gray-900 dark:text-white">
                                        ${{ number_format($course->price ?? rand(49, 199), 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">One-time payment</div>
                                    @endif
                                </div>
                                
                                <a href="{{ route('courses.show', $course->slug ?? '#') }}" 
                                   class="group/btn px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-bold hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center space-x-2">
                                    <span>View Course</span>
                                    <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-16 flex justify-center">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                        {{ $courses->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl p-12 border border-gray-200/50 dark:border-gray-700/50 max-w-lg mx-auto">
                        <div class="text-8xl mb-6">🔍</div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">No Courses Found</h3>
                        <p class="text-slate-600 dark:text-gray-300 mb-8 text-lg leading-relaxed">
                            We couldn't find any courses matching your search criteria. Try adjusting your filters or search terms.
                        </p>
                        <div class="space-y-4">
                            <a href="{{ route('courses.index') }}" 
                               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-2xl hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl space-x-2">
                                <span>🎯 Browse All Courses</span>
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                or try searching for something else
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
