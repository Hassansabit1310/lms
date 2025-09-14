<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    📊 Enrollments Report
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Track course enrollments and student engagement</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enrollment Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Enrollments -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Enrollments</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($totalEnrollments ?? 0) }}</p>
                            <p class="text-sm text-blue-600">Current period</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Monthly Enrollments -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Monthly Enrollments</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($monthlyEnrollments ?? 0) }}</p>
                            <p class="text-sm text-purple-600">This month</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Yearly Enrollments -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Yearly Enrollments</p>
                            <p class="text-3xl font-bold text-slate-800">{{ number_format($yearlyEnrollments ?? 0) }}</p>
                            <p class="text-sm text-green-600">This year</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Recent Enrollments</h3>
                @if(isset($recentEnrollments) && $recentEnrollments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentEnrollments->take(10) as $enrollment)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr($enrollment->user->name ?? 'U', 0, 2) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-slate-800">{{ $enrollment->user->name ?? 'Unknown User' }}</p>
                                        <p class="text-xs text-gray-500">{{ $enrollment->course->title ?? 'Unknown Course' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</span>
                                    <p class="text-xs">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if(($enrollment->status ?? 'active') === 'completed') bg-green-100 text-green-800
                                            @elseif(($enrollment->status ?? 'active') === 'active') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($enrollment->status ?? 'Active') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(isset($recentEnrollments) && $recentEnrollments->hasPages())
                        <div class="mt-6">
                            {{ $recentEnrollments->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No Enrollments Found</h4>
                        <p class="text-gray-600">No enrollments found for the selected period.</p>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    ← Back to Dashboard
                </a>
                
                <div class="flex space-x-4">
                    <a href="{{ route('admin.reports.revenue') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        💰 Revenue Report
                    </a>
                    <a href="{{ route('admin.reports.users') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        👥 Users Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
