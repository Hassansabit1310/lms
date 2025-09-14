<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <a href="{{ route('admin.categories.index') }}" class="text-pink-200 hover:text-white transition-colors">
                            ← Categories
                        </a>
                        <span class="text-pink-200">/</span>
                        <span class="text-white font-bold">{{ $category->name }}</span>
                    </div>
                    <h2 class="font-black text-4xl text-white leading-tight mb-2">
                        {{ $category->name }} Courses
                    </h2>
                    @if($category->description)
                    <p class="text-pink-100 text-lg">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="hidden md:block text-right">
                    <div class="text-white/80 text-sm">Total Courses</div>
                    <div class="text-white font-bold text-2xl">{{ $courses->total() }}</div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Subcategories -->
            @if($subcategories->count() > 0)
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                    🏷️ Subcategories in {{ $category->name }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($subcategories as $index => $subcategory)
                    @php
                        $colors = ['from-blue-500 to-cyan-500', 'from-purple-500 to-pink-500', 'from-green-500 to-emerald-500', 'from-orange-500 to-red-500'];
                    @endphp
                    <a href="{{ route('categories.show', $subcategory->slug) }}" 
                       class="group bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-4 text-center shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="w-12 h-12 bg-gradient-to-br {{ $colors[$index % count($colors)] }} rounded-xl mx-auto mb-3 flex items-center justify-center text-white text-lg group-hover:scale-110 transition-transform">
                            📁
                        </div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm mb-1 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                            {{ $subcategory->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $subcategory->courses_count }} courses
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Search and Filters -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-8 mb-10">
                <div class="mb-6 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">🔍 Find Courses in {{ $category->name }}</h3>
                    <p class="text-slate-600 dark:text-gray-300">Filter and sort to find the perfect course for your learning goals</p>
                </div>
                
                <form method="GET" action="{{ route('categories.show', $category->slug) }}" class="space-y-6">
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
                               placeholder="Search courses in {{ $category->name }}..."
                               class="w-full pl-12 pr-4 py-4 border border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-400 text-lg transition-all duration-200">
                    </div>

                    <!-- Filter Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Level Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">🎯 Level</label>
                            <select name="level" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>🌱 Beginner</option>
                                <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>🚀 Intermediate</option>
                                <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>⚡ Advanced</option>
                            </select>
                        </div>

                        <!-- Price Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">💰 Price</label>
                            <select name="price" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                                <option value="">All Prices</option>
                                <option value="free" {{ request('price') === 'free' ? 'selected' : '' }}>🎁 Free</option>
                                <option value="paid" {{ request('price') === 'paid' ? 'selected' : '' }}>💎 Paid</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">🔄 Sort By</label>
                            <select name="sort" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
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
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-2xl hover:from-pink-600 hover:to-red-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                            </svg>
                            <span>Apply Filters</span>
                        </button>
                        
                        @if(request()->hasAny(['search', 'level', 'price', 'sort']))
                        <a href="{{ route('categories.show', $category->slug) }}" class="px-8 py-4 border-2 border-gray-300 dark:border-gray-600 text-slate-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 flex items-center justify-center space-x-2">
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
                        📊 Showing <span class="font-bold text-purple-600 dark:text-purple-400">{{ $courses->firstItem() ?? 0 }} - {{ $courses->lastItem() ?? 0 }}</span> of <span class="font-bold text-pink-600 dark:text-pink-400">{{ $courses->total() }}</span> course{{ $courses->total() !== 1 ? 's' : '' }}
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
                            <div class="w-full h-56 bg-gradient-to-br from-purple-400 via-pink-500 to-red-500 flex items-center justify-center relative overflow-hidden">
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
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-purple-600 transition-colors">
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
                                   class="group/btn px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold hover:from-pink-600 hover:to-red-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center space-x-2">
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
                        <div class="text-8xl mb-6">📚</div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">No Courses Found</h3>
                        <p class="text-slate-600 dark:text-gray-300 mb-8 text-lg leading-relaxed">
                            No courses match your search criteria in this category. Try adjusting your filters or explore other categories.
                        </p>
                        <div class="space-y-4">
                            <a href="{{ route('categories.show', $category->slug) }}" 
                               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-2xl hover:from-pink-600 hover:to-red-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl space-x-2">
                                <span>🔄 Clear Filters</span>
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                or <a href="{{ route('admin.categories.index') }}" class="text-purple-600 dark:text-purple-400 hover:underline">browse other categories</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
