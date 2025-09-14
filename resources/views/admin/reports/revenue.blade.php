<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    💰 Revenue Reports
                </h2>
                <p style="color: white !important; font-size: 1.125rem !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;">Track your LMS platform revenue and sales performance</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Revenue Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Revenue</p>
                            <p class="text-3xl font-bold text-slate-800">${{ number_format($totalRevenue ?? 0, 2) }}</p>
                            <p class="text-sm text-blue-600">Current period</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Monthly Revenue</p>
                            <p class="text-3xl font-bold text-slate-800">${{ number_format($monthlyRevenue ?? 0, 2) }}</p>
                            <p class="text-sm text-purple-600">This month</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Yearly Revenue -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Yearly Revenue</p>
                            <p class="text-3xl font-bold text-slate-800">${{ number_format($yearlyRevenue ?? 0, 2) }}</p>
                            <p class="text-sm text-orange-600">This year</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Filter by Date Range</h3>
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <button type="submit" 
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Apply Filter
                    </button>
                </form>
            </div>

            <!-- Top Performing Courses -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Top Revenue Generating Courses</h3>
                @if(isset($revenueByCourse) && $revenueByCourse->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-2 text-sm font-medium text-slate-600">Course</th>
                                    <th class="text-left py-2 text-sm font-medium text-slate-600">Revenue</th>
                                    <th class="text-left py-2 text-sm font-medium text-slate-600">Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByCourse as $data)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3">
                                            <p class="font-medium text-slate-800">{{ $data['course']->title ?? 'Unknown Course' }}</p>
                                        </td>
                                        <td class="py-3 text-green-600 font-semibold">
                                            ${{ number_format($data['total_revenue'], 2) }}
                                        </td>
                                        <td class="py-3 text-slate-600">
                                            {{ $data['total_sales'] }} sales
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No Revenue Data</h4>
                        <p class="text-gray-600">No payments found for the selected period.</p>
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
                    <a href="{{ route('admin.reports.enrollments') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        📊 Enrollments Report
                    </a>
                    <a href="{{ route('admin.reports.users') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        👥 Users Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
