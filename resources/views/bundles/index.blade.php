<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">📦 Course Bundles</h1>
                        <p class="text-slate-200 text-lg">Get multiple courses at amazing discounts</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">Save Big</div>
                                <div class="text-slate-200 text-sm">Bundle Deals</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Learning Path</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Get access to multiple courses at incredible savings. Our expertly curated bundles provide comprehensive learning experiences.
                </p>
            </div>

            <!-- Bundle Grid -->
            @if($bundles->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($bundles as $bundle)
                        <div class="group relative">
                            <div class="bg-white rounded-3xl shadow-lg overflow-hidden transform group-hover:-translate-y-2 group-hover:shadow-2xl transition-all duration-300 border border-gray-200">
                                <!-- Bundle Header -->
                                <div class="relative p-8 bg-gradient-to-br from-purple-500 to-pink-600 text-white">
                                    @if($bundle->image)
                                        <div class="absolute inset-0">
                                            <img src="{{ asset('storage/' . $bundle->image) }}" 
                                                 alt="{{ $bundle->name }}" 
                                                 class="w-full h-full object-cover opacity-20">
                                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/80 to-pink-600/80"></div>
                                        </div>
                                    @endif
                                    
                                    <div class="relative">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold">
                                                📦 Bundle Deal
                                            </span>
                                            <span class="inline-flex items-center px-3 py-1 bg-green-500 rounded-full text-xs font-bold">
                                                💰 Save {{ $bundle->savings_percentage }}%
                                            </span>
                                        </div>
                                        
                                        <h3 class="text-2xl font-bold mb-3 line-clamp-2">{{ $bundle->name }}</h3>
                                        <p class="text-purple-100 mb-4 line-clamp-2">{{ $bundle->description }}</p>
                                        
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-3xl font-black">${{ number_format($bundle->price, 0) }}</div>
                                                <div class="text-sm text-purple-200 line-through">${{ number_format($bundle->original_price, 0) }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-purple-200">{{ $bundle->courses->count() }} courses</div>
                                                <div class="text-sm text-purple-200">{{ $bundle->total_lessons }} lessons</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bundle Content -->
                                <div class="p-6">
                                    <h4 class="font-bold text-gray-900 mb-4">What's included:</h4>
                                    <div class="space-y-3 mb-6">
                                        @foreach($bundle->courses->take(3) as $course)
                                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold text-gray-900 text-sm line-clamp-1">{{ $course->title }}</div>
                                                <div class="text-xs text-gray-500">${{ number_format($course->price, 0) }} value</div>
                                            </div>
                                        </div>
                                        @endforeach
                                        
                                        @if($bundle->courses->count() > 3)
                                        <div class="text-center py-2">
                                            <span class="text-sm text-purple-600 font-semibold">
                                                + {{ $bundle->courses->count() - 3 }} more course{{ $bundle->courses->count() - 3 > 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Features -->
                                    <div class="grid grid-cols-2 gap-4 mb-6 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-infinity text-purple-600 mr-2"></i>
                                            Lifetime access
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-certificate text-purple-600 mr-2"></i>
                                            Certificates
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-mobile-alt text-purple-600 mr-2"></i>
                                            Mobile friendly
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-users text-purple-600 mr-2"></i>
                                            Community access
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <a href="{{ route('bundles.show', $bundle) }}" 
                                       class="w-full group/btn relative px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold hover:from-pink-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 overflow-hidden">
                                        <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover/btn:scale-x-100 transition-transform origin-left duration-300"></div>
                                        <span class="relative">View Bundle Details</span>
                                        <svg class="w-5 h-5 group-hover/btn:translate-x-2 transition-all relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $bundles->links() }}
                </div>
            @else
                <!-- No Bundles -->
                <div class="max-w-2xl mx-auto text-center py-16">
                    <div class="bg-white rounded-3xl shadow-lg p-12">
                        <div class="text-6xl mb-6">📦</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">No Bundles Available Yet</h3>
                        <p class="text-gray-600 mb-8">
                            We're working on creating amazing course bundles for you. Check back soon for incredible deals!
                        </p>
                        <a href="{{ route('courses.index') }}" 
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-bold hover:from-pink-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            <span>Browse Individual Courses</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Why Choose Bundles -->
            <div class="mt-20">
                <div class="bg-white rounded-3xl shadow-lg p-12">
                    <div class="text-center mb-12">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Bundles?</h3>
                        <p class="text-lg text-gray-600">Get more value with our carefully curated course combinations</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-piggy-bank text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">Save Money</h4>
                            <p class="text-gray-600">Get multiple courses for less than buying them individually</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-route text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">Learning Path</h4>
                            <p class="text-gray-600">Follow a structured learning journey from beginner to expert</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-star text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">Curated Quality</h4>
                            <p class="text-gray-600">Expertly selected courses that complement each other perfectly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
