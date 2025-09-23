<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">📦 {{ $bundle->name }}</h1>
                        <p class="text-slate-200 text-lg">Bundle Details & Analytics</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">Admin Panel</div>
                                <div class="text-slate-200 text-sm">Bundle Management</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.bundles.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i> Back to Bundles
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.bundles.edit', $bundle) }}" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edit Bundle
                    </a>
                    <form method="POST" action="{{ route('admin.bundles.destroy', $bundle) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this bundle? This action cannot be undone.')" 
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    @if($bundle->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                            Inactive
                        </span>
                    @endif

                    @if($bundle->is_featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-star text-yellow-600 text-xs mr-1"></i>
                            Featured
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_sales'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Enrollments</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Courses Included</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bundle->courses->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Savings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $bundle->savings_percentage }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Bundle Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Bundle Information</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @if($bundle->image)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $bundle->image) }}" 
                                         alt="{{ $bundle->name }}" 
                                         class="w-full h-48 object-cover rounded-lg">
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Bundle Price</label>
                                    <p class="text-xl font-bold text-green-600">${{ number_format($bundle->price, 2) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Original Price</label>
                                    <p class="text-lg text-gray-600 line-through">${{ number_format($bundle->original_price, 2) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Discount Amount</label>
                                    <p class="text-lg font-semibold text-red-600">${{ number_format($bundle->discount_amount, 2) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Discount Percentage</label>
                                    <p class="text-lg font-semibold text-red-600">{{ $bundle->discount_percentage }}%</p>
                                </div>
                            </div>

                            @if($bundle->description)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Description</label>
                                    <p class="mt-1 text-gray-900">{{ $bundle->description }}</p>
                                </div>
                            @endif

                            @if($bundle->long_description)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Detailed Description</label>
                                    <div class="mt-1 text-gray-900 whitespace-pre-line">{{ $bundle->long_description }}</div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($bundle->max_enrollments)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Max Enrollments</label>
                                        <p class="text-gray-900">{{ $bundle->max_enrollments }}</p>
                                    </div>
                                @endif

                                @if($bundle->available_from)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Available From</label>
                                        <p class="text-gray-900">{{ $bundle->available_from->format('M j, Y g:i A') }}</p>
                                    </div>
                                @endif

                                @if($bundle->available_until)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Available Until</label>
                                        <p class="text-gray-900">{{ $bundle->available_until->format('M j, Y g:i A') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Included Courses -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Included Courses ({{ $bundle->courses->count() }})</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($bundle->courses as $course)
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h4>
                                                @if($course->pivot->is_primary)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Primary
                                                    </span>
                                                @endif
                                            </div>
                                            @if($course->description)
                                                <p class="mt-2 text-gray-600">{{ Str::limit($course->description, 150) }}</p>
                                            @endif
                                            <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
                                                <span><i class="fas fa-book-open mr-1"></i> {{ $course->lessons_count ?? 0 }} lessons</span>
                                                @if($course->duration_minutes)
                                                    <span><i class="fas fa-clock mr-1"></i> {{ $course->duration_minutes }} min</span>
                                                @endif
                                                <span><i class="fas fa-layer-group mr-1"></i> Order: {{ $course->pivot->order }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-6 text-right">
                                            <div class="text-lg font-semibold text-gray-900">${{ number_format($course->pivot->individual_price, 2) }}</div>
                                            <a href="{{ route('admin.courses.show', $course) }}" 
                                               class="text-sm text-indigo-600 hover:text-indigo-800">View Course</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('bundles.show', $bundle) }}" 
                               target="_blank"
                               class="w-full flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                View Public Page
                            </a>
                            <a href="{{ route('admin.bundles.edit', $bundle) }}" 
                               class="w-full flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Bundle
                            </a>
                            @if($bundle->is_active)
                                <button onclick="toggleBundleStatus(false)" 
                                        class="w-full flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                    <i class="fas fa-pause mr-2"></i>
                                    Deactivate
                                </button>
                            @else
                                <button onclick="toggleBundleStatus(true)" 
                                        class="w-full flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-play mr-2"></i>
                                    Activate
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Purchases -->
                    @if($bundle->payments->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Purchases</h3>
                            </div>
                            <div class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                                @foreach($bundle->payments->take(5) as $payment)
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $payment->user->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-green-600">${{ number_format($payment->amount, 2) }}</p>
                                                <p class="text-xs text-gray-500">{{ ucfirst($payment->status) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($bundle->payments->count() > 5)
                                <div class="p-4 border-t border-gray-200 text-center">
                                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">View All Purchases</a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Bundle Statistics -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Statistics</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Created</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bundle->created_at->format('M j, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Last Updated</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bundle->updated_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total Lessons</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bundle->total_lessons }}</span>
                            </div>
                            @if($bundle->total_duration > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Total Duration</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $bundle->total_duration }} min</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleBundleStatus(activate) {
            const action = activate ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${action} this bundle?`)) {
                // Create a form to toggle status
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.bundles.update", $bundle) }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                
                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'is_active';
                statusField.value = activate ? '1' : '0';
                
                // Keep other fields
                const nameField = document.createElement('input');
                nameField.type = 'hidden';
                nameField.name = 'name';
                nameField.value = '{{ $bundle->name }}';
                
                const priceField = document.createElement('input');
                priceField.type = 'hidden';
                priceField.name = 'price';
                priceField.value = '{{ $bundle->price }}';
                
                // Add course IDs
                @foreach($bundle->courses as $course)
                    const courseField{{ $course->id }} = document.createElement('input');
                    courseField{{ $course->id }}.type = 'hidden';
                    courseField{{ $course->id }}.name = 'courses[]';
                    courseField{{ $course->id }}.value = '{{ $course->id }}';
                    form.appendChild(courseField{{ $course->id }});
                @endforeach
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(statusField);
                form.appendChild(nameField);
                form.appendChild(priceField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
