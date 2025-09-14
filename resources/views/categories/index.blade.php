<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    🎯 Explore Categories
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Discover courses organized by your interests and career goals</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Introduction Section -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 bg-slate-100 rounded-full text-slate-700 text-sm font-medium mb-6">
                    ✨ Choose your learning path
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                    Find Your Perfect <span class="text-slate-600 font-black">Learning Category</span>
                </h1>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                    Browse our comprehensive collection of course categories designed to help you master new skills and advance your career.
                </p>
            </div>

            @if($categories->count() > 0)
                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @php
                        $categoryIcons = ['💻', '🎨', '💼', '📊', '🚀', '🔬', '📱', '🎭', '🎵', '📸', '✍️', '🏃‍♂️', '🍳', '📚', '🌱', '🔧'];
                        $categoryColors = [
                            'from-blue-500 to-cyan-500',
                            'from-purple-500 to-pink-500', 
                            'from-green-500 to-emerald-500',
                            'from-orange-500 to-red-500',
                            'from-yellow-500 to-orange-500',
                            'from-indigo-500 to-purple-500',
                            'from-pink-500 to-rose-500',
                            'from-teal-500 to-cyan-500',
                            'from-violet-500 to-purple-500',
                            'from-amber-500 to-orange-500',
                            'from-lime-500 to-green-500',
                            'from-sky-500 to-blue-500',
                            'from-rose-500 to-pink-500',
                            'from-emerald-500 to-teal-500',
                            'from-cyan-500 to-blue-500',
                            'from-fuchsia-500 to-pink-500'
                        ];
                    @endphp
                    
                    @foreach($categories as $index => $category)
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="group relative bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-3 transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                        
                        <!-- Background Gradient Animation -->
                        <div class="absolute inset-0 bg-gradient-to-br {{ $categoryColors[$index % count($categoryColors)] }} opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        
                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br {{ $categoryColors[$index % count($categoryColors)] }} rounded-full opacity-20 group-hover:scale-125 transition-transform duration-500"></div>
                        
                        <div class="relative text-center">
                            <!-- Icon -->
                            <div class="w-20 h-20 bg-gradient-to-br {{ $categoryColors[$index % count($categoryColors)] }} rounded-3xl mx-auto mb-6 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-12 transition-all duration-300 shadow-lg">
                                {{ $categoryIcons[$index % count($categoryIcons)] }}
                            </div>
                            
                            <!-- Category Name -->
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:{{ $categoryColors[$index % count($categoryColors)] }} transition-all">
                                {{ $category->name }}
                            </h3>
                            
                            <!-- Description -->
                            @if($category->description)
                            <p class="text-slate-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
                                {{ $category->description }}
                            </p>
                            @endif
                            
                            <!-- Stats -->
                            <div class="flex items-center justify-center space-x-4 mb-4">
                                <div class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span>📚</span>
                                    <span>{{ $category->courses_count }} course{{ $category->courses_count !== 1 ? 's' : '' }}</span>
                                </div>
                                @if($category->children_count > 0)
                                <div class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span>🏷️</span>
                                    <span>{{ $category->children_count }} subcategories</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Subcategories Preview -->
                            @if($category->children->count() > 0)
                            <div class="flex flex-wrap justify-center gap-1 mb-4">
                                @foreach($category->children->take(3) as $child)
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 text-slate-600 dark:text-gray-300 px-2 py-1 rounded-full">
                                    {{ $child->name }}
                                </span>
                                @endforeach
                                @if($category->children->count() > 3)
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 text-slate-600 dark:text-gray-300 px-2 py-1 rounded-full">
                                    +{{ $category->children->count() - 3 }} more
                                </span>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Action Button -->
                            <div class="text-sm text-purple-600 dark:text-purple-400 group-hover:text-white transition-colors font-medium">
                                Explore Category →
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                
                <!-- Call to Action -->
                <div class="mt-20 text-center relative z-50">
                    <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 100%) !important; min-height: 200px !important; position: relative !important; z-index: 100 !important;" class="rounded-3xl p-12 text-white shadow-2xl">
                        <div class="max-w-3xl mx-auto">
                            <div class="text-5xl mb-4">🎓</div>
                            <h2 style="color: white !important; font-size: 2rem !important; font-weight: bold !important; margin-bottom: 1rem !important;" class="drop-shadow-lg">Ready to Start Learning?</h2>
                            <p style="color: white !important; font-size: 1.125rem !important; margin-bottom: 2rem !important;" class="drop-shadow-md">
                                Join thousands of students who are already advancing their careers with our expert-led courses.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('courses.index') }}" 
                                   style="background: white !important; color: #334155 !important; padding: 1rem 2rem !important; font-weight: bold !important; border-radius: 1rem !important; text-decoration: none !important;" class="hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                    🚀 Browse All Courses
                                </a>
                                @guest
                                <a href="{{ route('register') }}" 
                                   style="border: 2px solid white !important; color: white !important; padding: 1rem 2rem !important; font-weight: bold !important; border-radius: 1rem !important; text-decoration: none !important;" class="hover:bg-white hover:text-slate-700 transition-all duration-300">
                                    ✨ Sign Up Free
                                </a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl p-12 border border-gray-200/50 dark:border-gray-700/50 max-w-lg mx-auto">
                        <div class="text-8xl mb-6">📂</div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">No Categories Yet</h3>
                        <p class="text-slate-600 dark:text-gray-300 mb-8 text-lg">
                            We're organizing our courses into categories. Check back soon!
                        </p>
                        <a href="{{ route('courses.index') }}" 
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-2xl hover:from-pink-600 hover:to-red-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl space-x-2">
                            <span>📚 Browse All Courses</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
