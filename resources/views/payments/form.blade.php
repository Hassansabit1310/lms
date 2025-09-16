<x-app-layout>
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Complete Payment</h1>
                <p class="text-gray-600 mt-2">Secure payment processing</p>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Item:</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($payment->course)
                            {{ $payment->course->title }}
                        @elseif($payment->bundle)
                            {{ $payment->bundle->name }} (Bundle)
                        @elseif($payment->subscription)
                            {{ ucfirst($payment->subscription->plan_type) }} Subscription
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Amount:</span>
                    <span class="text-lg font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Transaction ID:</span>
                    <span class="text-xs text-gray-500">{{ $payment->transaction_id }}</span>
                </div>
            </div>

            <!-- Payment Form (Simulation for testing) -->
            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Payment Simulation</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This is a test environment. Click below to simulate payment success or failure.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('payments.simulate', $payment) }}" class="space-y-4">
                    @csrf
                    
                    <button type="submit" name="action" value="success" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-check mr-2"></i>
                        Simulate Successful Payment
                    </button>
                    
                    <button type="submit" name="action" value="failure" 
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Simulate Failed Payment
                    </button>
                </form>

                <div class="text-center">
                    <a href="{{ url()->previous() }}" class="text-gray-500 text-sm hover:text-gray-700">
                        ← Go back
                    </a>
                </div>
            </div>

            <!-- Security Note -->
            <div class="mt-6 text-center">
                <div class="flex items-center justify-center text-xs text-gray-500">
                    <i class="fas fa-lock mr-1"></i>
                    <span>Secured by SSL encryption</span>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
