<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fallback Tailwind CSS for production issues -->
    <script>
        // Check if Tailwind styles are loaded by testing a common class
        document.addEventListener('DOMContentLoaded', function() {
            const testEl = document.createElement('div');
            testEl.className = 'bg-blue-500';
            testEl.style.position = 'absolute';
            testEl.style.visibility = 'hidden';
            document.body.appendChild(testEl);
            
            const computedStyle = window.getComputedStyle(testEl);
            const bgColor = computedStyle.backgroundColor;
            
            // If Tailwind isn't loaded (bg-blue-500 should be blue)
            if (!bgColor || bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
                console.warn('Tailwind CSS not loaded, adding fallback CSS');
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '/fallback-tailwind.css';
                document.head.appendChild(link);
                
                // Add a visual indicator in development
                setTimeout(() => {
                    const indicator = document.createElement('div');
                    indicator.innerHTML = '⚠️ Using Fallback CSS';
                    indicator.style.cssText = `
                        position: fixed; top: 10px; right: 10px; 
                        background: #ef4444; color: white; 
                        padding: 8px 12px; border-radius: 6px; 
                        font-size: 12px; z-index: 9999;
                        font-family: monospace;
                    `;
                    document.body.appendChild(indicator);
                    setTimeout(() => indicator.remove(), 10000);
                }, 500);
            }
            
            document.body.removeChild(testEl);
        });
    </script>
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            min-height: 100vh !important;
        }
        .hero-text {
            color: white !important;
            font-size: 4rem !important;
            font-weight: 900 !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3) !important;
        }
        .hero-badge {
            background-color: #ffffff !important;
            color: #4c1d95 !important;
            padding: 1rem 2rem !important;
            border-radius: 9999px !important;
            font-weight: 700 !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        .hero-button-primary {
            background-color: #ffffff !important;
            color: #4c1d95 !important;
            padding: 1rem 2rem !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
            border: none !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        .hero-button-secondary {
            background-color: transparent !important;
            color: white !important;
            padding: 1rem 2rem !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            border: 2px solid white !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    @include('layouts.navigation')
    
    <!-- SUPER SIMPLE TEST HERO SECTION -->
    <section class="hero-bg flex items-center justify-center">
            <div class="text-center">
                <!-- Professional Badge -->
                <div class="mb-8">
                    <span class="hero-badge">
                        ✨ Join {{ number_format($stats['total_students'] ?? 50000) }}+ learners worldwide
                    </span>
                </div>
                
                <!-- Professional Headlines -->
                <h1 class="hero-text mb-8">
                    <div>Learn</div>
                    <div style="color: #fbbf24;">Everything</div>
                    <div style="color: #a78bfa;">Anywhere</div>
                </h1>
                
                <!-- Professional Subtitle -->
                <p style="color: white; font-size: 1.25rem; margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    Transform your career with 
                    <span style="color: #fbbf24; font-weight: 700;">{{ number_format($stats['total_courses'] ?? 150) }}+ courses</span>
                    from world-class instructors. Start learning today!
                </p>
                
                <!-- Professional Buttons -->
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem;">
                    <a href="{{ route('courses.index') }}" class="hero-button-primary">
                        <span>🎯</span>
                        <span>Explore Courses</span>
                    </a>
                    
                    @guest
                    <a href="{{ route('register') }}" class="hero-button-secondary">
                        <span>🚀</span>
                        <span>Start Free</span>
                    </a>
                    @endguest
                </div>
                
                <!-- Professional Stats -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 2rem; max-width: 600px; margin: 0 auto;">
                    @php
                        $quickStats = [
                            ['icon' => '📚', 'number' => $stats['total_courses'] ?? 150, 'label' => 'Courses'],
                            ['icon' => '👥', 'number' => $stats['total_students'] ?? 50000, 'label' => 'Students'],
                            ['icon' => '🎓', 'number' => $stats['total_instructors'] ?? 200, 'label' => 'Instructors'],
                            ['icon' => '⭐', 'number' => '4.9', 'label' => 'Rating']
                        ];
                    @endphp
                    
                    @foreach($quickStats as $stat)
                    <div style="text-align: center; padding: 1.5rem; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">{{ $stat['icon'] }}</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.25rem;">{{ is_numeric($stat['number']) ? number_format($stat['number']) . '+' : $stat['number'] }}</div>
                        <div style="color: #fbbf24; font-size: 0.875rem; font-weight: 600;">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <!-- Rest of the page content -->
    <div class="pt-20">
        <!-- Animated Stats Section -->
        <section class="py-20 relative">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-100"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900  mb-6">
                        🌟 Join the <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Revolution</span>
                    </h2>
                    <p class="text-2xl text-slate-600  max-w-4xl mx-auto">
                        Millions of learners are already transforming their lives
                    </p>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="group text-center p-8 bg-white/95 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transform hover:-translate-y-4 hover:rotate-2 transition-all duration-500 border border-gray-300/50 ">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-lg">
                            <span class="text-3xl">📚</span>
                        </div>
                        <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-3 group-hover:scale-110 transition-transform">
                            {{ number_format($stats['total_courses'] ?? 0) }}+
                        </div>
                        <div class="text-slate-600  font-bold text-lg">Premium Courses</div>
                        <div class="text-sm text-slate-600 mt-2">Expert-crafted content</div>
                    </div>
                    
                    <div class="group text-center p-8 bg-white/80  backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transform hover:-translate-y-4 hover:-rotate-2 transition-all duration-500 border border-gray-300/50 ">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-lg">
                            <span class="text-3xl">👥</span>
                        </div>
                        <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600 mb-3 group-hover:scale-110 transition-transform">
                            {{ number_format($stats['total_students'] ?? 0) }}+
                        </div>
                        <div class="text-slate-600  font-bold text-lg">Happy Students</div>
                        <div class="text-sm text-slate-600 mt-2">Growing community</div>
                    </div>
                    
                    <div class="group text-center p-8 bg-white/95 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transform hover:-translate-y-4 hover:rotate-2 transition-all duration-500 border border-gray-300/50 ">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-lg">
                            <span class="text-3xl">🎓</span>
                        </div>
                        <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-3 group-hover:scale-110 transition-transform">
                            {{ number_format($stats['total_instructors'] ?? 0) }}+
                        </div>
                        <div class="text-slate-600  font-bold text-lg">Expert Instructors</div>
                        <div class="text-sm text-slate-600 mt-2">Industry leaders</div>
                    </div>
                    
                    <div class="group text-center p-8 bg-white/80  backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transform hover:-translate-y-4 hover:-rotate-2 transition-all duration-500 border border-gray-300/50 ">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-lg">
                            <span class="text-3xl">🏆</span>
                        </div>
                        <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600 mb-3 group-hover:scale-110 transition-transform">
                            {{ number_format($stats['total_enrollments'] ?? 0) }}+
                        </div>
                        <div class="text-slate-600  font-bold text-lg">Success Stories</div>
                        <div class="text-sm text-slate-600 mt-2">Completed courses</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Courses with 3D Effects -->
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-slate-50"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 rounded-full text-white text-lg font-bold mb-6 shadow-xl animate-pulse">
                        🔥 Hot & Trending
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900  mb-6">
                        ✨ Featured <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Courses</span>
                    </h2>
                    <p class="text-2xl text-slate-600  max-w-4xl mx-auto">
                        Handpicked by experts, loved by students worldwide
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @forelse($featuredCourses as $index => $course)
                    <div class="group perspective-1000">
                        <div class="relative preserve-3d group-hover:rotate-y-12 transition-transform duration-700">
                            <div class="bg-white/95  backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden transform group-hover:-translate-y-4 group-hover:rotate-1 transition-all duration-500 border border-gray-300/50 ">
                                <div class="relative overflow-hidden">
                                    @if($course->thumbnail ?? false)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover group-hover:scale-125 transition-transform duration-700">
                                    @else
                                    <div class="w-full h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center relative overflow-hidden">
                                        <div class="absolute inset-0 bg-black/20"></div>
                                        <div class="relative text-center">
                                            <div class="text-7xl mb-3 animate-bounce">🎓</div>
                                            <div class="text-white font-black text-xl">{{ Str::limit($course->title ?? 'Amazing Course', 25) }}</div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Floating badges -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        @if($course->is_free ?? rand(0,1))
                                        <span class="px-4 py-2 bg-green-500 text-white text-sm font-black rounded-full shadow-lg animate-pulse">
                                            🎁 FREE
                                        </span>
                                        @endif
                                        <span class="px-4 py-2 bg-white/95 text-slate-800 text-sm font-black rounded-full shadow-lg">
                                            @php
                                                $levels = ['beginner', 'intermediate', 'advanced'];
                                                $level = $course->level ?? $levels[array_rand($levels)];
                                                $icon = $level === 'beginner' ? '🌱' : ($level === 'intermediate' ? '🚀' : '⚡');
                                            @endphp
                                            {{ $icon }} {{ ucfirst($level) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Rating badge -->
                                    <div class="absolute top-4 right-4">
                                        <div class="bg-white/95 backdrop-blur-sm rounded-xl px-3 py-2 shadow-lg">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-yellow-400">⭐</span>
                                                <span class="text-slate-800 font-black">{{ number_format(rand(40, 50)/10, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-8">
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-bold rounded-full">
                                            📂 {{ $course->category->name ?? ['Programming', 'Design', 'Business', 'Marketing'][array_rand(['Programming', 'Design', 'Business', 'Marketing'])] }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-2xl font-black text-gray-900  mb-4 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                        {{ $course->title ?? 'Master Modern Web Development' }}
                                    </h3>
                                    
                                    <p class="text-slate-600  mb-6 line-clamp-3 leading-relaxed">
                                        {{ $course->short_description ?? $course->description ?? 'Transform your career with this comprehensive course designed by industry experts. Learn practical skills that you can apply immediately.' }}
                                    </p>
                                    
                                    <!-- Enhanced stats -->
                                    <div class="flex items-center gap-6 mb-6 text-sm text-slate-600 dark:text-gray-400">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">👥</span>
                                            <span class="font-semibold">{{ number_format(rand(1000, 5000)) }} students</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">⏱️</span>
                                            <span class="font-semibold">{{ rand(8, 25) }}h content</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            @if($course->is_free ?? rand(0,1))
                                            <div class="text-3xl font-black text-green-600">FREE</div>
                                            <div class="text-sm text-slate-600 line-through">${{ number_format(rand(49, 199)) }}</div>
                                            @else
                                            <div class="text-3xl font-black text-gray-900 ">
                                                ${{ number_format($course->price ?? rand(49, 199)) }}
                                            </div>
                                            <div class="text-sm text-slate-600">One-time payment</div>
                                            @endif
                                        </div>
                                        
                                        <a href="{{ route('courses.show', $course->slug ?? '#') }}" 
                                           class="group/btn relative px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-black hover:from-purple-600 hover:to-pink-600 transform hover:scale-110 hover:rotate-3 transition-all duration-300 shadow-2xl hover:shadow-3xl flex items-center space-x-2 overflow-hidden">
                                            <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover/btn:scale-x-100 transition-transform origin-left duration-300"></div>
                                            <span class="relative">View Course</span>
                                            <svg class="w-5 h-5 group-hover/btn:translate-x-2 group-hover/btn:scale-110 transition-all relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    @for($i = 0; $i < 6; $i++)
                    <div class="group perspective-1000">
                        <div class="relative preserve-3d group-hover:rotate-y-12 transition-transform duration-700">
                            <div class="bg-white/95  backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden transform group-hover:-translate-y-4 group-hover:rotate-1 transition-all duration-500">
                                @php
                                    $courseTitles = ['Master React Development', 'AI & Machine Learning', 'Digital Marketing Mastery', 'UI/UX Design Fundamentals', 'Python for Beginners', 'Data Science Bootcamp'];
                                    $courseDescs = [
                                        'Build modern web applications with React, Redux, and advanced JavaScript techniques',
                                        'Dive deep into artificial intelligence and machine learning algorithms',
                                        'Learn proven strategies to grow your business with digital marketing',
                                        'Design beautiful and user-friendly interfaces that convert',
                                        'Start your programming journey with Python from scratch',
                                        'Analyze data and extract insights using Python and SQL'
                                    ];
                                @endphp
                                <div class="w-full h-64 bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-black/20"></div>
                                    <div class="relative text-center">
                                        <div class="text-7xl mb-3 animate-bounce">{{ ['🚀', '🎨', '💻', '📊', '🔬', '⚡'][$i] }}</div>
                                        <div class="text-white font-black text-xl">{{ $courseTitles[$i] }}</div>
                                    </div>
                                </div>
                                
                                <div class="p-8">
                                    <h3 class="text-2xl font-black text-gray-900  mb-4">{{ $courseTitles[$i] }}</h3>
                                    <p class="text-slate-600  mb-6">{{ $courseDescs[$i] }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="text-3xl font-black text-gray-900 ">${{ rand(49, 199) }}</div>
                                        <a href="{{ route('courses.index') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-black transform hover:scale-110 transition-all duration-300">
                                            View Course
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                    @endforelse
                </div>

                <div class="text-center mt-16">
                    <a href="{{ route('courses.index') }}" 
                       class="group inline-flex items-center px-12 py-6 bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-100 text-white dark:text-gray-900 rounded-3xl font-black text-2xl hover:from-gray-700 hover:to-gray-500 transform hover:scale-110 hover:rotate-1 transition-all duration-300 shadow-2xl">
                        <span class="text-3xl mr-4">🎯</span>
                        <span>Explore All Courses</span>
                        <svg class="w-8 h-8 ml-4 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Bundles Section -->
        @if($featuredBundles->count() > 0)
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full text-white text-lg font-bold mb-6 shadow-xl animate-pulse">
                        📦 Special Bundles
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                        💎 Course <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">Bundles</span>
                    </h2>
                    <p class="text-2xl text-slate-600 max-w-4xl mx-auto">
                        Get multiple courses at incredible savings - handpicked combinations for maximum learning
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    @foreach($featuredBundles as $bundle)
                    <div class="group relative">
                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden transform group-hover:-translate-y-4 group-hover:rotate-1 transition-all duration-500 border border-gray-200">
                            <!-- Bundle Header -->
                            <div class="relative p-8 bg-gradient-to-br from-purple-500 to-pink-600 text-white">
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold">
                                        💰 Save {{ $bundle->savings_percentage }}%
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold">
                                        📦 Bundle Deal
                                    </span>
                                </div>
                                
                                <h3 class="text-2xl font-black mb-3 line-clamp-2">{{ $bundle->name }}</h3>
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
                            
                            <!-- Bundle Courses Preview -->
                            <div class="p-6">
                                <h4 class="font-bold text-gray-900 mb-4">What's included:</h4>
                                <div class="space-y-3 mb-6">
                                    @foreach($bundle->courses->take(3) as $course)
                                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-gray-900 text-sm line-clamp-1">{{ $course->title }}</div>
                                            <div class="text-xs text-gray-500">${{ number_format($course->price, 0) }} • {{ $course->lessons_count ?? rand(8, 25) }} lessons</div>
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
                                
                                <!-- Action Button -->
                                <a href="{{ route('bundles.show', $bundle) }}" 
                                   class="w-full group/btn relative px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl font-black hover:from-pink-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl flex items-center justify-center space-x-2 overflow-hidden">
                                    <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover/btn:scale-x-100 transition-transform origin-left duration-300"></div>
                                    <span class="relative">View Bundle Details</span>
                                    <svg class="w-5 h-5 group-hover/btn:translate-x-2 group-hover/btn:scale-110 transition-all relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- View All Bundles Button -->
                <div class="text-center mt-16">
                    <a href="{{ route('bundles.index') }}" 
                       class="group inline-flex items-center px-12 py-6 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-3xl font-black text-2xl hover:from-pink-600 hover:to-purple-600 transform hover:scale-110 hover:rotate-1 transition-all duration-300 shadow-2xl">
                        <span class="text-3xl mr-4">📦</span>
                        <span>Explore All Bundles</span>
                        <svg class="w-8 h-8 ml-4 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
        @endif

        <!-- Revolutionary Categories Section -->
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-slate-50"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900  mb-6">
                        🎯 Explore by <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Category</span>
                    </h2>
                    <p class="text-2xl text-slate-600  max-w-4xl mx-auto">
                        Discover your passion across diverse learning paths
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    @php
                        $demoCategories = [
                            ['name' => 'Programming', 'icon' => '💻', 'courses' => rand(50, 200), 'color' => 'from-blue-500 to-cyan-500'],
                            ['name' => 'Design', 'icon' => '🎨', 'courses' => rand(30, 150), 'color' => 'from-purple-500 to-pink-500'],
                            ['name' => 'Business', 'icon' => '💼', 'courses' => rand(40, 180), 'color' => 'from-green-500 to-emerald-500'],
                            ['name' => 'Marketing', 'icon' => '📊', 'courses' => rand(25, 120), 'color' => 'from-orange-500 to-red-500'],
                            ['name' => 'Data Science', 'icon' => '🔬', 'courses' => rand(35, 160), 'color' => 'from-yellow-500 to-orange-500'],
                            ['name' => 'Photography', 'icon' => '📸', 'courses' => rand(20, 100), 'color' => 'from-indigo-500 to-purple-500'],
                            ['name' => 'Music', 'icon' => '🎵', 'courses' => rand(15, 80), 'color' => 'from-pink-500 to-rose-500'],
                            ['name' => 'Languages', 'icon' => '🌍', 'courses' => rand(30, 140), 'color' => 'from-teal-500 to-cyan-500']
                        ];
                    @endphp
                    
                    @forelse($categories as $index => $category)
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="group relative bg-white/95  backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-6 hover:rotate-3 transition-all duration-500 border border-gray-300/50  overflow-hidden">
                        
                        <!-- Animated background -->
                        <div class="absolute inset-0 bg-gradient-to-br {{ $demoCategories[$index % count($demoCategories)]['color'] }} opacity-0 group-hover:opacity-20 transition-opacity duration-500"></div>
                        
                        <div class="relative text-center">
                            <div class="w-24 h-24 bg-gradient-to-br {{ $demoCategories[$index % count($demoCategories)]['color'] }} rounded-full mx-auto mb-6 flex items-center justify-center text-4xl group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-2xl">
                                {{ $demoCategories[$index % count($demoCategories)]['icon'] }}
                            </div>
                            
                            <h3 class="text-xl font-black text-gray-900  mb-3 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:{{ $demoCategories[$index % count($demoCategories)]['color'] }} transition-all">
                                {{ $category->name }}
                            </h3>
                            
                            <p class="text-slate-600  mb-4">
                                {{ $category->courses_count ?? rand(20, 150) }} courses
                            </p>
                            
                            <div class="text-sm text-purple-600 dark:text-purple-400 group-hover:text-white transition-colors font-bold">
                                Explore →
                            </div>
                        </div>
                    </a>
                    @empty
                    @foreach($demoCategories as $category)
                    <a href="{{ route('admin.categories.index') }}" 
                       class="group relative bg-white/95  backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-6 hover:rotate-3 transition-all duration-500 border border-gray-300/50  overflow-hidden">
                        
                        <div class="absolute inset-0 bg-gradient-to-br {{ $category['color'] }} opacity-0 group-hover:opacity-20 transition-opacity duration-500"></div>
                        
                        <div class="relative text-center">
                            <div class="w-24 h-24 bg-gradient-to-br {{ $category['color'] }} rounded-full mx-auto mb-6 flex items-center justify-center text-4xl group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-2xl">
                                {{ $category['icon'] }}
                            </div>
                            
                            <h3 class="text-xl font-black text-gray-900  mb-3">
                                {{ $category['name'] }}
                            </h3>
                            
                            <p class="text-slate-600  mb-4">
                                {{ $category['courses'] }} courses
                            </p>
                            
                            <div class="text-sm text-purple-600 dark:text-purple-400 group-hover:text-white transition-colors font-bold">
                                Explore →
                            </div>
                        </div>
                    </a>
                    @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Epic CTA Section -->
        @guest
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-700 via-gray-700 to-zinc-700"></div>
            <div class="absolute inset-0 bg-black/20"></div>
            
            <!-- Epic floating elements -->
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full animate-float"></div>
            <div class="absolute bottom-10 right-10 w-24 h-24 bg-yellow-400/20 rounded-full animate-bounce"></div>
            <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-green-400/15 rounded-full animate-ping"></div>
            
            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="mb-8">
                    <span class="inline-flex items-center px-6 py-3 bg-white/20 rounded-full text-white text-lg font-bold backdrop-blur-sm border border-white/30 animate-pulse">
                        🎁 Limited Time: Free Access
                    </span>
                </div>
                
                <h2 class="text-6xl md:text-8xl font-black text-white mb-8 leading-tight">
                    Ready to <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400 animate-pulse">Transform</span>
                    <br class="hidden sm:block"/>
                    Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-400">Future?</span>
                </h2>
                
                <p class="text-2xl md:text-3xl text-blue-100 mb-12 leading-relaxed max-w-4xl mx-auto">
                    Join <span class="font-black text-yellow-300">{{ number_format($stats['total_students'] ?? 0) }}+</span> students who chose to invest in themselves.
                    <br class="hidden sm:block"/>
                    <span class="font-bold text-yellow-300">Start your journey today</span> and unlock unlimited potential.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12">
                    <a href="{{ route('register') }}" 
                       class="group relative px-12 py-6 bg-white text-gray-900 rounded-3xl font-black text-2xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 hover:scale-110 transition-all duration-300 flex items-center space-x-4 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative flex items-center space-x-4 group-hover:text-white transition-colors">
                            <span class="text-3xl">🚀</span>
                            <span>Start Free Today</span>
                            <svg class="w-8 h-8 group-hover:translate-x-2 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </a>
                    
                    <div class="text-white text-lg">
                        ✨ <span class="font-bold">No credit card required</span> • Instant access
                    </div>
                </div>
                
                <div class="flex flex-wrap justify-center items-center gap-8 text-blue-200 text-lg">
                    <div class="flex items-center space-x-3 bg-white/10 px-6 py-3 rounded-full backdrop-blur-sm">
                        <span class="text-green-400 text-xl">✓</span>
                        <span class="font-bold">30-day money back guarantee</span>
                    </div>
                    <div class="flex items-center space-x-3 bg-white/10 px-6 py-3 rounded-full backdrop-blur-sm">
                        <span class="text-yellow-400 text-xl">⭐</span>
                        <span class="font-bold">4.9/5 average rating</span>
                    </div>
                    <div class="flex items-center space-x-3 bg-white/10 px-6 py-3 rounded-full backdrop-blur-sm">
                        <span class="text-blue-400 text-xl">🎓</span>
                        <span class="font-bold">Certificates included</span>
                    </div>
                </div>
            </div>
        </section>
        @endguest
    </div>
</body>
</html>
