<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">📋 Payment Status</h1>
                        <p class="text-slate-200 text-lg">Track your manual payment progress</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">
                                    @if($payment->status === 'pending')
                                        ⏳ Pending
                                    @elseif($payment->status === 'approved')
                                        ✅ Approved
                                    @elseif($payment->status === 'rejected')
                                        ❌ Rejected
                                    @endif
                                </div>
                                <div class="text-slate-200 text-sm">Payment Status</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Status Banner -->
            @if($payment->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-bold text-yellow-800 mb-2">Payment Under Review</h2>
                            <p class="text-yellow-700">
                                Your payment details have been submitted and are currently under review by our admin team. 
                                We typically process manual payments within 24 hours during business days.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($payment->status === 'approved')
                <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-bold text-green-800 mb-2">Payment Approved!</h2>
                            <p class="text-green-700 mb-4">
                                Congratulations! Your payment has been approved and you now have access to your purchase.
                            </p>
                            <div class="flex items-center space-x-4">
                                @if($payment->course)
                                    <a href="{{ route('courses.show', $payment->course) }}" 
                                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-play mr-2"></i>
                                        Start Learning
                                    </a>
                                @elseif($payment->bundle)
                                    <a href="{{ route('bundles.show', $payment->bundle) }}" 
                                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-box-open mr-2"></i>
                                        Access Bundle
                                    </a>
                                @endif
                                <a href="{{ route('enrollments.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-list mr-2"></i>
                                    My Courses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($payment->status === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-bold text-red-800 mb-2">Payment Rejected</h2>
                            <p class="text-red-700 mb-4">
                                Unfortunately, your payment could not be verified. Please check the rejection reason below and try again with correct details.
                            </p>
                            @if($payment->course)
                                <a href="{{ route('payments.manual.course', $payment->course) }}" 
                                   class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-redo mr-2"></i>
                                    Try Again
                                </a>
                            @elseif($payment->bundle)
                                <a href="{{ route('payments.manual.bundle', $payment->bundle) }}" 
                                   class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-redo mr-2"></i>
                                    Try Again
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Payment Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Payment Details</h2>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Purchase Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Item:</span>
                                    <div class="text-gray-900">
                                        {{ $payment->course ? $payment->course->title : $payment->bundle->name }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Type:</span>
                                    <div class="text-gray-900">{{ $payment->course ? 'Course' : 'Bundle' }}</div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Amount:</span>
                                    <div class="text-gray-900 font-semibold">${{ number_format($payment->amount, 2) }}</div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Payment Date:</span>
                                    <div class="text-gray-900">{{ $payment->created_at->format('M j, Y g:i A') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Method:</span>
                                    <div class="text-gray-900">
                                        @if($payment->payment_method === 'bank_transfer')
                                            <i class="fas fa-university mr-2"></i>Bank Transfer
                                        @else
                                            <i class="fas fa-mobile-alt mr-2"></i>{{ ucfirst($payment->wallet_provider ?? 'Mobile Wallet') }}
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Transaction ID:</span>
                                    <div class="text-gray-900 font-mono">{{ $payment->user_transaction_id }}</div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Sender Name:</span>
                                    <div class="text-gray-900">{{ $payment->sender_name }}</div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Sender Mobile:</span>
                                    <div class="text-gray-900">{{ $payment->sender_mobile }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payment->payment_note)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Note</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $payment->payment_note }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Response -->
            @if($payment->admin_note || $payment->approved_at)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-8 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Admin Response</h2>
                    </div>
                    <div class="p-8">
                        <div class="space-y-4">
                            @if($payment->approved_at)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Review Date:</span>
                                    <div class="text-gray-900">{{ $payment->approved_at->format('M j, Y g:i A') }}</div>
                                </div>
                            @endif
                            @if($payment->approvedBy)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Reviewed By:</span>
                                    <div class="text-gray-900">{{ $payment->approvedBy->name }}</div>
                                </div>
                            @endif
                            @if($payment->admin_note)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Admin Note:</span>
                                    <div class="bg-gray-50 rounded-lg p-4 mt-2">
                                        <p class="text-gray-700">{{ $payment->admin_note }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Need Help?</h3>
                <div class="text-blue-800 space-y-2">
                    <p>• If you have questions about your payment status, please contact our support team</p>
                    <p>• Include your transaction ID: <span class="font-mono bg-blue-100 px-2 py-1 rounded">{{ $payment->user_transaction_id }}</span></p>
                    <p>• For urgent matters, please call our support hotline</p>
                </div>
                <div class="mt-4">
                    <a href="mailto:support@yoursite.com" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
