<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @endpush

    @php
        $totalReviews = $course->reviews->count();
        $averageRating = $totalReviews > 0 ? $course->reviews->avg('rating') : 0;
        
        // Extract YouTube URL if present in description
        $description = $course->description;
        $youtubeUrl = null;
        if (preg_match('/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $description, $matches)) {
            $youtubeUrl = 'https://www.youtube.com/embed/' . $matches[1];
            $description = preg_replace('/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', '', $description);
        }
        $description = trim(strip_tags($description));
    @endphp

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 py-12">
                <!-- Left: Course Info (2/3) -->
                <div class="lg:col-span-2">
                    <!-- Instructor -->
                    @if($course->instructor)
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">{{ substr($course->instructor->name, 0, 1) }}</span>
                        </div>
                        <span class="text-white/90 text-sm">By {{ $course->instructor->name }}</span>
                    </div>
                    @else
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">?</span>
                        </div>
                        <span class="text-white/90 text-sm">By Unknown Instructor</span>
                    </div>
                    @endif

                    <!-- Course Title -->
                    <h1 class="text-white text-3xl lg:text-4xl font-bold leading-tight mb-4">
                        {{ $course->title }}
                    </h1>

                    <!-- Course Description -->
                    <p class="text-white/90 text-lg leading-relaxed mb-6">
                        @if($course->short_description)
                            {{ $course->short_description }}
                        @else
                            {{ Str::limit($description, 150) }}
                        @endif
                    </p>

                    <!-- Course Meta Stats -->
                    <div class="flex flex-wrap gap-4 mb-8">
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-2">
                            <i class="fas fa-clock text-white"></i>
                            <span class="text-white font-medium">{{ number_format(($course->duration_minutes ?? 0) / 60, 1) }} hours</span>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-2">
                            <i class="fas fa-users text-white"></i>
                            <span class="text-white font-medium">{{ $course->enrollments->count() }} students</span>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-2">
                            <i class="fas fa-signal text-white"></i>
                            <span class="text-white font-medium">{{ ucfirst($course->level) }}</span>
                        </div>
                        @if($totalReviews > 0)
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-2">
                            <div class="flex text-yellow-300">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($averageRating))
                                        <i class="fas fa-star text-xs"></i>
                                    @else
                                        <i class="far fa-star text-xs"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-white font-medium">{{ number_format($averageRating, 1) }} ({{ $totalReviews }})</span>
                        </div>
                        @endif
                    </div>

                    <!-- Price -->
                    <div class="text-white mb-6">
                        <span class="text-3xl font-bold">
                            @if($course->is_free)
                                Free
                            @else
                                ${{ number_format($course->price, 2) }}
                            @endif
                        </span>
                        @if(!$course->is_free && $course->original_price && $course->original_price > $course->price)
                        <span class="text-white/60 line-through ml-2">${{ number_format($course->original_price, 2) }}</span>
                        @endif
                    </div>

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-white/15 border border-white/25 text-white">
                            <i class="fas fa-layer-group"></i>
                            {{ ucfirst($course->level) }}
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-white/15 border border-white/25 text-white">
                            <i class="fas fa-code"></i>
                            {{ $course->category->name }}
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-white/15 border border-white/25 text-white">
                            <i class="fas fa-laptop-code"></i>
                            Development
                        </span>
                    </div>
                </div>

                <!-- Right: Action Buttons (1/3) -->
                <div class="lg:col-span-1">
                    <div class="space-y-4">
                        <button class="w-full bg-white font-bold py-4 px-6 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center gap-2" style="color: #0d9488;">
                            <i class="fas fa-shopping-cart" style="color: #0d9488;"></i>
                            <span style="color: #0d9488;">Add to cart</span>
                        </button>
                        <button class="w-full border-2 border-white text-white font-semibold py-4 px-6 rounded-xl hover:bg-white/10 transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="far fa-heart"></i>
                            Wishlist
                        </button>
                        <div class="bg-white/15 backdrop-blur-md rounded-xl p-6 text-center border border-white/20 shadow-lg">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <i class="fas fa-crown text-yellow-300"></i>
                                <span class="text-white font-bold text-lg">Subscribe @ 59%</span>
                            </div>
                            <div class="text-white/90 text-sm leading-relaxed">Get this course, plus 25,000+ of our top-rated courses, with Personal Plan.</div>
                            <button class="mt-4 w-full bg-yellow-400 text-gray-900 font-bold py-3 px-4 rounded-lg hover:bg-yellow-300 transition-colors">
                                Try Personal Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Content (2/3) -->
                <div class="lg:col-span-2">
                    <!-- Featured Video Player -->
                    @if($youtubeUrl)
                    <div class="mb-8">
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                            <!-- Video Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                            <i class="fas fa-play-circle text-red-500"></i>
                                            Course Preview
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ $course->title }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <i class="fab fa-youtube text-red-500"></i>
                                        <span>HD Quality</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Video Player -->
                            <div class="relative bg-black w-full" style="height: 400px; min-height: 400px;">
                                <iframe 
                                    src="{{ $youtubeUrl }}" 
                                    class="absolute inset-0 w-full h-full"
                                    allowfullscreen
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    style="border: none; width: 100%; height: 100%;">
                                </iframe>
                            </div>
                            
                            <!-- Video Footer -->
                            <div class="px-6 py-4 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <button class="flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                                            <i class="fas fa-bookmark"></i>
                                            Save for Later
                                        </button>
                                        <button class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-share"></i>
                                            Share
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-eye"></i>
                                        <span>{{ number_format($course->enrollments->count() * 12) }} views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Course Thumbnail if no video -->
                    @if($course->thumbnail)
                    <div class="bg-gray-100 rounded-lg overflow-hidden mb-8">
                        <div class="relative w-full h-96 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $course->thumbnail) }}');">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clipRule="evenodd"/>
                                    </svg>
                                    <p class="font-semibold">Course Preview</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-white">
                            <h3 class="font-semibold text-gray-900">Introduction</h3>
                            <p class="text-gray-600 text-sm">{{ $course->title }}</p>
                        </div>
                    </div>
                    @endif
                    @endif

                    <!-- Tags Row -->
                    <div class="flex flex-wrap gap-3 mb-8">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                            <i class="fab fa-python"></i>
                            Python
                        </span>
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium">
                            <i class="fas fa-code"></i>
                            Programming Language
                        </span>
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full font-medium">
                            <i class="fas fa-rocket"></i>
                            Development
                        </span>
                    </div>

                    <!-- Course Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>

                    <!-- Course Meta -->
                    <div class="flex flex-wrap gap-4 mb-8">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="font-semibold text-green-800">{{ $course->lessons->count() }} Lessons</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                            <i class="fas fa-clock text-blue-500"></i>
                            <span class="font-semibold text-blue-800">{{ number_format(($course->duration_minutes ?? 0) / 60, 1) }} hours</span>
                        </div>
                        @if($totalReviews > 0)
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex text-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($averageRating))
                                        <i class="fas fa-star text-xs"></i>
                                    @else
                                        <i class="far fa-star text-xs"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="font-semibold text-yellow-800">{{ number_format($averageRating, 1) }} ({{ $totalReviews }})</span>
                        </div>
                        @endif
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 border border-purple-200 rounded-lg">
                            <i class="fas fa-users text-purple-500"></i>
                            <span class="font-semibold text-purple-800">{{ number_format($course->enrollments->count()) }} enrolled</span>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 mb-8">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="showTab('overview')" id="overview-tab" class="border-b-2 border-teal-500 text-teal-600 py-2 px-2 font-medium text-sm whitespace-nowrap">
                                Overview
                            </button>
                            <button onclick="showTab('faq')" id="faq-tab" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-2 font-medium text-sm whitespace-nowrap">
                                FAQ
                            </button>
                            <button onclick="showTab('discussions')" id="discussions-tab" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-2 font-medium text-sm whitespace-nowrap">
                                Discussions
                            </button>
                            <button onclick="showTab('reviews')" id="reviews-tab" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-2 font-medium text-sm whitespace-nowrap">
                                Reviews
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div id="overview-content">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Become a Python Programmer and learn one of employer's most requested skills of 2023!</h2>
                        
                        <div class="prose prose-gray max-w-none">
                            <p class="text-gray-700 leading-relaxed mb-4">
                                {{ $description }}
                            </p>
                            
                            @if($course->learning_objectives && count($course->learning_objectives) > 0)
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">What you'll learn:</h3>
                            <ul class="space-y-2 mb-6">
                                @foreach($course->learning_objectives as $objective)
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-teal-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">{{ $objective }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>

                        <!-- Author Section -->
                        @if($course->instructor)
                        <div class="border-t pt-8 mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Author</h3>
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center">
                                    <span class="text-teal-600 font-bold text-xl">{{ substr($course->instructor->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $course->instructor->name }}</h4>
                                    <p class="text-gray-600 text-sm">Developer and Bootcamp Instructor</p>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="text-sm"></span>
                                            @endfor
                                        </div>
                                        <span class="text-gray-600 text-sm">(4.8)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div id="faq-content" class="hidden">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
                        <div class="space-y-4">
                            <div class="border rounded-lg p-4">
                                <h3 class="font-semibold text-gray-900 mb-2">Is this course suitable for beginners?</h3>
                                <p class="text-gray-700">Yes, this course is designed for complete beginners with no prior programming experience.</p>
                            </div>
                            <div class="border rounded-lg p-4">
                                <h3 class="font-semibold text-gray-900 mb-2">How long do I have access to the course?</h3>
                                <p class="text-gray-700">You have lifetime access to all course materials once enrolled.</p>
                            </div>
                        </div>
                    </div>

                    <div id="discussions-content" class="hidden">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Discussions</h2>
                        <p class="text-gray-600">Join the conversation with other students and instructors.</p>
                    </div>

                    <div id="reviews-content" class="hidden">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Student Reviews</h2>
                        @if($totalReviews > 0)
                            <div class="space-y-4">
                                @foreach($course->reviews->take(5) as $review)
                                <div class="border-b pb-4">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 text-sm font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $review->user->name }}</div>
                                            <div class="flex text-yellow-400 text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        
                                                    @else
                                                        
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600">No reviews yet. Be the first to review this course!</p>
                        @endif
                    </div>
                </div>

                <!-- Right Sidebar (1/3) -->
                <div class="lg:col-span-1">
                    <!-- Course Content -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-list-ul text-teal-600"></i>
                            <h3 class="font-bold text-gray-900 text-lg">Course content</h3>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-6 bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-folder text-blue-500"></i>
                                <span class="font-medium">{{ $course->lessons->count() }} sections</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-play-circle text-green-500"></i>
                                <span class="font-medium">{{ $course->lessons->count() }} lectures</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-clock text-orange-500"></i>
                                <span class="font-medium">{{ number_format(($course->duration_minutes ?? 0) / 60, 1) }}h {{ ($course->duration_minutes ?? 0) % 60 }}m total</span>
                            </div>
                        </div>

                        <!-- Course Sections -->
                        <div class="space-y-3">
                            @foreach($course->lessons->groupBy(function($lesson) { return 'Chapter ' . (floor(($loop->index ?? 0) / 5) + 1); }) as $chapterName => $lessons)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <button onclick="toggleChapter('chapter-{{ $loop->index }}')" class="w-full flex items-center justify-between p-3 text-left hover:bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $chapterName }}: {{ $lessons->first()->title ?? 'Getting Started' }}</div>
                                        <div class="text-sm text-gray-600">{{ $lessons->count() }} | {{ $lessons->count() * 5 }}min</div>
                                    </div>
                                    <svg id="chapter-{{ $loop->index }}-icon" class="w-5 h-5 text-gray-400 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd"/>
                                    </svg>
                                </button>
                                <div id="chapter-{{ $loop->index }}" class="hidden p-3">
                                    @foreach($lessons->take(5) as $lesson)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3 last:mb-0 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-play text-teal-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900 text-sm">{{ $lesson->title }}</div>
                                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $lesson->duration ?? '6' }}min
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-eye"></i>
                                                        Preview
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                            <h3 class="font-bold text-gray-900 text-lg">Requirements</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                </svg>
                                <span class="text-gray-700 text-sm">Access to a computer with an internet connection</span>
                            </div>
                            @if($course->prerequisites && count($course->prerequisites) > 0)
                                @foreach($course->prerequisites as $prerequisite)
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-teal-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700 text-sm">{{ $prerequisite }}</span>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- This course includes -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-4 mb-4">
                            <i class="fas fa-gift text-purple-600"></i>
                            <h3 class="font-bold text-gray-900 text-lg">This course includes</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-video text-red-500"></i>
                                <span class="text-gray-700 font-medium">{{ number_format(($course->duration_minutes ?? 0) / 60, 1) }} hours on-demand video</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <i class="fas fa-file-alt text-blue-500"></i>
                                <span class="text-gray-700 font-medium">{{ $course->lessons->count() }} articles</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <i class="fas fa-download text-green-500"></i>
                                <span class="text-gray-700 font-medium">130 downloadable resources</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <i class="fas fa-mobile-alt text-purple-500"></i>
                                <span class="text-gray-700 font-medium">Access on mobile and TV</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <i class="fas fa-closed-captioning text-indigo-500"></i>
                                <span class="text-gray-700 font-medium">Closed captions</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <i class="fas fa-certificate text-yellow-500"></i>
                                <span class="text-gray-700 font-medium">Certificate of completion</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('[id$="-content"]');
            contents.forEach(content => content.classList.add('hidden'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('[id$="-tab"]');
            tabs.forEach(tab => {
                tab.classList.remove('border-teal-500', 'text-teal-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.add('border-teal-500', 'text-teal-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
        }

        function toggleChapter(chapterId) {
            const chapter = document.getElementById(chapterId);
            const icon = document.getElementById(chapterId + '-icon');
            
            if (chapter.classList.contains('hidden')) {
                chapter.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                chapter.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
    @endpush
</x-app-layout>

