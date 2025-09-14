<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                            🚀 Welcome Back, {{ auth()->user()->name }}!
                        </h2>
                        <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Continue your learning journey and achieve your goals</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="text-right">
                            <div style="color: rgba(255, 255, 255, 0.9) !important; font-size: 0.875rem !important;">Member since</div>
                            <div style="color: white !important; font-weight: bold !important; font-size: 1.125rem !important;">{{ auth()->user()->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Enrolled Courses -->
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 border border-white/30 shadow-lg hover:shadow-xl transition-all duration-300 group hover:bg-white/95">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-white text-xl">📚</span>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-gray-900">{{ $stats['total_enrollments'] ?? 0 }}</div>
                            <div class="text-sm text-slate-600">Enrolled</div>
                        </div>
                    </div>
                    <div class="text-slate-700 font-semibold">Total Courses</div>
                    <div class="text-xs text-gray-500 mt-1">Keep learning!</div>
                </div>

                <!-- Completed Courses -->
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 border border-white/30 shadow-lg hover:shadow-xl transition-all duration-300 group hover:bg-white/95">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-white text-xl">🏆</span>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-gray-900">{{ $stats['completed_courses'] ?? 0 }}</div>
                            <div class="text-sm text-slate-600">Completed</div>
                        </div>
                    </div>
                    <div class="text-slate-700 font-semibold">Achievements</div>
                    <div class="text-xs text-gray-500 mt-1">Great progress!</div>
                </div>

                <!-- In Progress -->
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 border border-white/30 shadow-lg hover:shadow-xl transition-all duration-300 group hover:bg-white/95">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-white text-xl">⚡</span>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-gray-900">{{ $stats['in_progress'] ?? 0 }}</div>
                            <div class="text-sm text-slate-600">Active</div>
                        </div>
                    </div>
                    <div class="text-slate-700 font-semibold">In Progress</div>
                    <div class="text-xs text-gray-500 mt-1">Keep going!</div>
                </div>

                <!-- Total Learning Time -->
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 border border-white/30 shadow-lg hover:shadow-xl transition-all duration-300 group hover:bg-white/95">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-white text-xl">⏱️</span>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-gray-900">{{ round(($stats['total_time'] ?? 0) / 60) }}h</div>
                            <div class="text-sm text-slate-600">Learning</div>
                        </div>
                    </div>
                    <div class="text-slate-700 font-semibold">Total Time</div>
                    <div class="text-xs text-gray-500 mt-1">Time invested</div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl p-8 border border-gray-200/50 dark:border-gray-700/50 shadow-xl text-center">
                <div class="text-6xl mb-4">🎉</div>
                <h3 class="text-3xl font-bold text-gray-900 mb-4">
                    Welcome to Your Learning Dashboard!
                </h3>
                <p class="text-xl text-slate-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Track your progress, discover new courses, and continue your learning journey with EduVerse.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('courses.index') }}" 
                       class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-2xl hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                        <span>🚀 Browse Courses</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="px-8 py-4 border-2 border-gray-600 dark:border-gray-400 text-slate-600 font-bold rounded-2xl hover:bg-gray-600 hover:text-white dark:hover:bg-gray-400 dark:hover:text-gray-900 transition-all duration-300 flex items-center justify-center space-x-2">
                        <span>⚙️ Edit Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
