<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    🛠️ Admin Dashboard
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Manage your LMS platform with powerful admin tools</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <div style="background: linear-gradient(135deg, #334155 0%, #475569 50%, #64748B 100%) !important; border-radius: 1rem !important; padding: 2rem !important; position: relative !important; z-index: 100 !important;" class="shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 style="color: white !important; font-size: 2rem !important; font-weight: bold !important; margin-bottom: 0.5rem !important;">Welcome back, Admin! 👋</h1>
                            <p style="color: rgba(255, 255, 255, 0.8) !important; font-size: 1rem !important;">Manage your LMS platform with powerful admin tools</p>
                        </div>
                        <div class="hidden md:block">
                            <div style="width: 6rem !important; height: 6rem !important; background: rgba(255, 255, 255, 0.2) !important; border-radius: 50% !important;" class="flex items-center justify-center">
                                <svg style="width: 3rem !important; height: 3rem !important; color: white !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Users</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-sm text-green-600">+{{ $stats['new_users_this_month'] }} this month</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Courses -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Courses</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_courses']) }}</p>
                            <p class="text-sm text-purple-600">Across all categories</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Enrollments -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Enrollments</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_enrollments']) }}</p>
                            <p class="text-sm text-green-600">+{{ $stats['new_enrollments_this_month'] }} this month</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Revenue</p>
                            <p class="text-3xl font-bold text-slate-800">${{ number_format($stats['total_revenue'], 2) }}</p>
                            <p class="text-sm text-blue-600">From completed payments</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- H5P Content Library -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">H5P Content</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_h5p_content'] ?? 0) }}</p>
                            <p class="text-sm text-pink-600">Interactive content pieces</p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-puzzle-piece text-pink-600 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Lessons -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Lessons</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_lessons'] ?? 0) }}</p>
                            <p class="text-sm text-indigo-600">Across all courses</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Panels -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.courses.create') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-purple-700">Add New Course</span>
                        </a>

                        <a href="{{ route('admin.courses.index') }}" class="flex items-center p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-indigo-700">Manage Courses & Lessons</span>
                        </a>

                        <a href="{{ route('admin.categories.create') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.998 1.998 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-blue-700">Add Category</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-green-700">Manage Users</span>
                        </a>

                        <a href="{{ route('admin.h5p.index') }}" class="flex items-center p-3 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                            <div class="w-8 h-8 bg-pink-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-puzzle-piece text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-pink-700">H5P Content Library</span>
                        </a>

                        <a href="{{ route('admin.h5p.create') }}" class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-upload text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-orange-700">Upload H5P Content</span>
                        </a>

                        <a href="{{ route('admin.bundles.index') }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-yellow-700">Manage Bundles</span>
                        </a>

                        <a href="{{ route('admin.bundles.create') }}" class="flex items-center p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-amber-700">Create Bundle</span>
                        </a>

                        <a href="{{ route('admin.reports.revenue') }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-yellow-700">View Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Recent Enrollments</h3>
                    <div class="space-y-4">
                        @forelse($recent_enrollments->take(5) as $enrollment)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr($enrollment->user->name, 0, 2) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-slate-800">{{ $enrollment->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $enrollment->course->title }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent enrollments</p>
                        @endforelse
                    </div>
                    
                    @if($recent_enrollments->count() > 5)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.reports.enrollments') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                View all enrollments →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-800">Recent Users</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View all →</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 text-sm font-medium text-slate-600">User</th>
                                <th class="text-left py-2 text-sm font-medium text-slate-600">Role</th>
                                <th class="text-left py-2 text-sm font-medium text-slate-600">Joined</th>
                                <th class="text-left py-2 text-sm font-medium text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_users->take(5) as $user)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-slate-800">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($user->role === 'admin') bg-purple-100 text-purple-800
                                            @elseif($user->role === 'instructor') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
