<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 140px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-bold">
                                📦 Bundle Deal
                            </span>
                            <span class="inline-flex items-center px-4 py-2 bg-green-500 rounded-full text-white text-sm font-bold">
                                💰 Save {{ $bundle->savings_percentage }}%
                            </span>
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-3">{{ $bundle->name }}</h1>
                        <p class="text-slate-200 text-lg mb-4">{{ $bundle->description }}</p>
                        <div class="flex items-center gap-6 text-slate-200">
                            <span><i class="fas fa-book mr-2"></i>{{ $bundle->courses->count() }} courses</span>
                            <span><i class="fas fa-play-circle mr-2"></i>{{ $bundle->total_lessons }} lessons</span>
                            @if($bundle->total_duration > 0)
                                <span><i class="fas fa-clock mr-2"></i>{{ floor($bundle->total_duration / 60) }}h {{ $bundle->total_duration % 60 }}m</span>
                            @endif
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-8 py-6 border border-white/20 text-center">
                            <div class="text-white">
                                <div class="text-3xl font-black mb-2">${{ number_format($bundle->price, 0) }}</div>
                                <div class="text-slate-200 text-sm line-through mb-1">${{ number_format($bundle->original_price, 0) }}</div>
                                <div class="text-green-300 text-sm font-bold">Save ${{ number_format($bundle->discount_amount, 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Bundle Description -->
                    @if($bundle->long_description)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">What You'll Learn</h2>
                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            {!! nl2br(e($bundle->long_description)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Bundle Image -->
                    @if($bundle->image)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <img src="{{ asset('storage/' . $bundle->image) }}" 
                             alt="{{ $bundle->name }}" 
                             class="w-full h-64 object-cover">
                    </div>
                    @endif

                    <!-- Courses in Bundle -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-8 border-b border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-900">Courses Included ({{ $bundle->courses->count() }})</h2>
                            <p class="text-gray-600 mt-2">All courses are included in this bundle at this special price</p>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($bundle->courses as $course)
                            <div class="p-8 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-4 mb-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">{{ $course->title }}</h3>
                                                @if($course->pivot->is_primary)
                                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                        ⭐ Primary Course
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($course->description)
                                            <p class="text-gray-600 mb-4 leading-relaxed">{{ Str::limit($course->description, 200) }}</p>
                                        @endif
                                        
                                        <div class="flex items-center gap-6 text-sm text-gray-500">
                                            <span><i class="fas fa-play-circle mr-2 text-purple-600"></i>{{ $course->lessons_count ?? 0 }} lessons</span>
                                            @if($course->duration_minutes)
                                                <span><i class="fas fa-clock mr-2 text-purple-600"></i>{{ floor($course->duration_minutes / 60) }}h {{ $course->duration_minutes % 60 }}m</span>
                                            @endif
                                            @if($course->level)
                                                <span><i class="fas fa-signal mr-2 text-purple-600"></i>{{ ucfirst($course->level) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-6 text-right">
                                        <div class="text-2xl font-bold text-gray-900">${{ number_format($course->pivot->individual_price, 0) }}</div>
                                        <div class="text-sm text-gray-500">Individual price</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">What's Included</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-infinity text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Lifetime Access</h4>
                                    <p class="text-gray-600">Access all courses forever, including future updates</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-certificate text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Certificates</h4>
                                    <p class="text-gray-600">Earn certificates for each completed course</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-mobile-alt text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Mobile Learning</h4>
                                    <p class="text-gray-600">Learn on any device, anywhere, anytime</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-users text-orange-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Community Access</h4>
                                    <p class="text-gray-600">Join our exclusive community of learners</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Purchase Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-8">
                        <div class="p-8">
                            <!-- Price Display -->
                            <div class="text-center mb-6">
                                <div class="text-4xl font-black text-gray-900 mb-2">${{ number_format($bundle->price, 0) }}</div>
                                <div class="text-lg text-gray-500 line-through mb-1">${{ number_format($bundle->original_price, 0) }}</div>
                                <div class="text-lg font-bold text-green-600">Save ${{ number_format($bundle->discount_amount, 0) }} ({{ $bundle->savings_percentage }}%)</div>
                            </div>

                            <!-- Purchase Actions -->
                            @auth
                            @if($hasPurchased)
                                <div class="text-center mb-6">
                                    <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Already Purchased
                                    </div>
                                </div>
                                <a href="{{ route('enrollments.index') }}" 
                                   class="w-full bg-gray-900 text-white text-center py-4 rounded-2xl font-bold text-lg hover:bg-gray-800 transition-colors block">
                                    View My Courses
                                </a>
                            @elseif($hasSubscription)
                                <div class="text-center mb-6">
                                    <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                        <i class="fas fa-crown mr-2"></i>
                                        Included in Subscription
                                    </div>
                                </div>
                                <a href="{{ route('enrollments.index') }}" 
                                   class="w-full bg-blue-600 text-white text-center py-4 rounded-2xl font-bold text-lg hover:bg-blue-700 transition-colors block">
                                    Access Now
                                </a>
                            @else
                                <div class="space-y-4">
                                    @auth
                                        @if(auth()->user()->hasPurchasedBundle($bundle))
                                            <!-- Already Purchased - Show Access Button -->
                                            <div class="w-full bg-green-500 text-white text-center py-4 rounded-2xl font-bold text-lg shadow-lg">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Bundle Purchased ✓
                                            </div>
                                            <a href="{{ route('courses.index') }}?bundle={{ $bundle->id }}" 
                                               class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-4 rounded-2xl font-bold text-lg hover:from-indigo-600 hover:to-blue-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl block">
                                                <i class="fas fa-play mr-2"></i>
                                                Start Learning
                                            </a>
                                        @else
                                            <!-- Manual Payment Button -->
                                            <a href="{{ route('payments.manual.bundle', $bundle) }}" 
                                               class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center py-4 rounded-2xl font-bold text-lg hover:from-pink-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl block">
                                                <i class="fas fa-credit-card mr-2"></i>
                                                Purchase via Bank Transfer / Mobile Wallet
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center py-4 rounded-2xl font-bold text-lg hover:from-pink-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl block">
                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                            Login to Purchase
                                        </a>
                                    @endauth
                                    
                                    <!-- Payment Methods Info -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">💳 Supported Payment Methods</h4>
                                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-university text-blue-600 mr-2"></i>
                                                Bank Transfer
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-mobile-alt text-green-600 mr-2"></i>
                                                bKash, Nagad, Rocket
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Quick approval within 24 hours • Secure payment process
                                        </p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-500">30-day money-back guarantee</p>
                                </div>
                            @endif
                            @else
                                <div class="space-y-4">
                                    <a href="{{ route('register') }}" 
                                       class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center py-4 rounded-2xl font-bold text-lg hover:from-pink-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl block">
                                        Sign Up to Purchase
                                    </a>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">
                                            Already have an account? 
                                            <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-800 font-medium">Log in</a>
                                        </p>
                                    </div>
                                </div>
                            @endauth

                            <!-- Bundle Stats -->
                            <div class="mt-8 space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Total lessons:</span>
                                    <span class="font-semibold">{{ $bundle->total_lessons }}</span>
                                </div>
                                @if($bundle->total_duration > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Total duration:</span>
                                    <span class="font-semibold">{{ floor($bundle->total_duration / 60) }}h {{ $bundle->total_duration % 60 }}m</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Access:</span>
                                    <span class="font-semibold">Lifetime</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Certificate:</span>
                                    <span class="font-semibold">Included</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Availability Info -->
                    @if($bundle->available_until || $bundle->max_enrollments)
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-amber-800 mb-3">⏰ Limited Time Offer</h3>
                        <div class="space-y-2 text-sm text-amber-700">
                            @if($bundle->available_until)
                                <p><strong>Offer ends:</strong> {{ $bundle->available_until->format('M j, Y g:i A') }}</p>
                            @endif
                            @if($bundle->max_enrollments)
                                <p><strong>Limited spots:</strong> {{ $bundle->max_enrollments - $bundle->getCurrentEnrollments() }} remaining</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Share -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Share this Bundle</h3>
                        <div class="flex items-center space-x-3">
                            <button onclick="copyToClipboard(window.location.href)" 
                                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors text-center">
                                <i class="fas fa-link mr-2"></i>Copy Link
                            </button>
                            <a href="https://twitter.com/intent/tweet?text=Check out this amazing course bundle!&url={{ urlencode(request()->url()) }}" 
                               target="_blank"
                               class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Create a temporary notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.textContent = 'Link copied to clipboard!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
        }
    </script>
</x-app-layout>
