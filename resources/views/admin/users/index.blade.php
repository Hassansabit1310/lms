<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-white/30 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                        <p class="text-slate-600">Manage user accounts and roles</p>
                    </div>
                    <a
                        href="{{ route('admin.users.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add User</span>
                    </a>
                </div>

                <!-- Search & Filters -->
                <form method="GET" class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Search Input -->
                        <div class="lg:col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search by name or email..."
                                    class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                            </div>
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <select
                                name="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select
                                name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">All Status</option>
                                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Sort Order -->
                        <div>
                            <select
                                name="sort"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Latest First</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email A-Z</option>
                                <option value="created" {{ request('sort') === 'created' ? 'selected' : '' }}>Newest</option>
                                <option value="enrollments" {{ request('sort') === 'enrollments' ? 'selected' : '' }}>Most Enrollments</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <input
                                type="date"
                                name="date_from"
                                value="{{ request('date_from') }}"
                                placeholder="From Date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <!-- Date To -->
                        <div>
                            <input
                                type="date"
                                name="date_to"
                                value="{{ request('date_to') }}"
                                placeholder="To Date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>

                        <a
                            href="{{ route('admin.users.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors inline-flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-white/30 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistics</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full w-fit
                                                @if($user->role === 'admin') bg-red-100 text-red-800
                                                @elseif($user->role === 'instructor') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($user->role ?? 'student') }}
                                            </span>
                                            
                                            @if($user->email_verified_at)
                                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full w-fit">Verified</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full w-fit">Unverified</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $user->enrollments_count ?? 0 }} courses</div>
                                        <div class="text-sm text-gray-500">{{ $user->subscriptions_count ?? 0 }} subscriptions</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $user->created_at->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <!-- View Button -->
                                            <a
                                                href="{{ route('admin.users.show', $user) }}"
                                                class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                                title="View Details"
                                            >
                                                View
                                            </a>

                                            <!-- Edit Button -->
                                            <a
                                                href="{{ route('admin.users.edit', $user) }}"
                                                class="text-green-600 hover:text-green-800 font-medium text-sm"
                                                title="Edit User"
                                            >
                                                Edit
                                            </a>

                                            <!-- Delete Button (only if not current user) -->
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm"
                                                        onclick="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone!')"
                                                        title="Delete User"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Quick Role Change -->
                                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <select
                                                name="role"
                                                onchange="if(confirm('Change role for {{ $user->name }}?')) this.form.submit()"
                                                class="text-xs px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            >
                                                <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                                <option value="instructor" {{ $user->role === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
