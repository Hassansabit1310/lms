<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- User Avatar -->
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-blue-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-slate-600">{{ $user->email }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 
                                       ($user->role === 'instructor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                @if($user->email_verified_at)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Verified</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Unverified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a
                            href="{{ route('admin.users.edit', $user) }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit User</span>
                        </a>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span>Back to Users</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?: 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <p class="mt-1">
                                    <span class="px-2 py-1 text-sm font-medium rounded-full
                                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 
                                           ($user->role === 'instructor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Account Status</label>
                                <p class="mt-1">
                                    @if($user->email_verified_at)
                                        <span class="px-2 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-full">Verified</span>
                                    @else
                                        <span class="px-2 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">Unverified</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Member Since</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        @if($user->bio)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Bio</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->bio }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Enrollments -->
                    @if($user->enrollments && $user->enrollments->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Enrollments</h3>
                            <div class="space-y-4">
                                @foreach($user->enrollments as $enrollment)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold">
                                                {{ substr($enrollment->course->title ?? 'Course', 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $enrollment->course->title ?? 'Course Title' }}</h4>
                                                <p class="text-sm text-gray-500">Enrolled {{ $enrollment->enrolled_at->format('M j, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->progress_percentage ?? 0 }}% Complete</div>
                                                @if($enrollment->completed_at)
                                                    <div class="text-sm text-green-600">Completed {{ $enrollment->completed_at->format('M j, Y') }}</div>
                                                @endif
                                            </div>
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Subscriptions -->
                    @if($user->subscriptions && $user->subscriptions->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscriptions</h3>
                            <div class="space-y-4">
                                @foreach($user->subscriptions as $subscription)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ ucfirst($subscription->plan_type ?? 'Plan') }} Subscription</h4>
                                            <p class="text-sm text-gray-500">
                                                {{ $subscription->start_date ? $subscription->start_date->format('M j, Y') : 'N/A' }}
                                                @if($subscription->end_date)
                                                    - {{ $subscription->end_date->format('M j, Y') }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-sm font-medium rounded-full
                                                {{ ($subscription->status ?? 'inactive') === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($subscription->status ?? 'inactive') }}
                                            </span>
                                            <div class="text-sm text-gray-500 mt-1">${{ number_format($subscription->amount ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Payment History -->
                    @if($user->payments && $user->payments->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Date</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Course</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Amount</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->payments->take(10) as $payment)
                                            <tr class="border-b border-gray-100">
                                                <td class="py-3 px-4 text-sm text-gray-900">
                                                    {{ $payment->created_at ? $payment->created_at->format('M j, Y') : 'N/A' }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-900">
                                                    {{ $payment->course->title ?? 'Subscription' }}
                                                </td>
                                                <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                                    ${{ number_format($payment->amount ?? 0, 2) }}
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                                        {{ ($payment->status ?? 'pending') === 'completed' ? 'bg-green-100 text-green-700' : 
                                                           (($payment->status ?? 'pending') === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                        {{ ucfirst($payment->status ?? 'pending') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Total Enrollments</span>
                                <span class="text-lg font-bold text-blue-600">{{ $user->enrollments->count() ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Active Subscriptions</span>
                                <span class="text-lg font-bold text-green-600">{{ $user->subscriptions->where('status', 'active')->count() ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Total Payments</span>
                                <span class="text-lg font-bold text-purple-600">${{ number_format($user->payments->where('status', 'completed')->sum('amount') ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Completed Courses</span>
                                <span class="text-lg font-bold text-indigo-600">{{ $user->enrollments->whereNotNull('completed_at')->count() ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a
                                href="{{ route('admin.users.edit', $user) }}"
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>Edit User</span>
                            </a>
                            
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <div class="flex space-x-2">
                                    <select
                                        name="role"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                                    >
                                        <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="instructor" {{ $user->role === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button
                                        type="submit"
                                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm"
                                        onclick="return confirm('Change role for {{ $user->name }}?')"
                                    >
                                        Update Role
                                    </button>
                                </div>
                            </form>

                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center space-x-2"
                                        onclick="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone!')"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Delete User</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div class="text-sm">
                                    <p class="text-gray-900">Account created</p>
                                    <p class="text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if($user->email_verified_at)
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <div class="text-sm">
                                        <p class="text-gray-900">Email verified</p>
                                        <p class="text-gray-500">{{ $user->email_verified_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif
                            @foreach($user->enrollments->take(3) as $enrollment)
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                    <div class="text-sm">
                                        <p class="text-gray-900">Enrolled in {{ $enrollment->course->title ?? 'course' }}</p>
                                        <p class="text-gray-500">{{ $enrollment->enrolled_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
