<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">💳 Manual Payment</h1>
                        <p class="text-slate-200 text-lg">Complete your purchase via bank transfer or mobile wallet</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">${{ number_format($item->price ?? 0, 2) }}</div>
                                <div class="text-slate-200 text-sm">{{ ucfirst($type) }} Price</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Item Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Purchase Summary</h2>
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $item->name ?? $item->title }}</h3>
                        @if($item->description ?? false)
                            <p class="text-gray-600 mb-4">{{ Str::limit($item->description, 200) }}</p>
                        @endif
                        
                        @if($type === 'bundle')
                            <div class="text-sm text-gray-500">
                                <span><i class="fas fa-book mr-1"></i>{{ $item->courses->count() }} courses</span>
                                <span class="ml-4"><i class="fas fa-play-circle mr-1"></i>{{ $item->total_lessons }} lessons</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900">${{ number_format($item->price, 2) }}</div>
                        @if($type === 'bundle' && $item->original_price > $item->price)
                            <div class="text-sm text-gray-500 line-through">${{ number_format($item->original_price, 2) }}</div>
                            <div class="text-sm text-green-600 font-semibold">Save {{ $item->savings_percentage }}%</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 p-8 mb-8">
                <h2 class="text-2xl font-bold text-blue-900 mb-6">📋 Payment Instructions</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Bank Transfer -->
                    @if(config('payments.manual.bank_transfer.enabled'))
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-university text-blue-600 mr-2"></i>
                            Bank Transfer
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Bank Name:</span>
                                <span class="text-gray-900">{{ config('payments.manual.bank_transfer.bank_name') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Account Name:</span>
                                <span class="text-gray-900">{{ config('payments.manual.bank_transfer.account_name') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Account Number:</span>
                                <span class="text-gray-900 font-mono">{{ config('payments.manual.bank_transfer.account_number') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Routing Number:</span>
                                <span class="text-gray-900 font-mono">{{ config('payments.manual.bank_transfer.routing_number') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">SWIFT Code:</span>
                                <span class="text-gray-900 font-mono">{{ config('payments.manual.bank_transfer.swift_code') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Mobile Wallet -->
                    @if(config('payments.manual.mobile_wallet.enabled'))
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-mobile-alt text-green-600 mr-2"></i>
                            Mobile Wallet
                        </h3>
                        <div class="space-y-3 text-sm">
                            @foreach(config('payments.manual.mobile_wallet.providers') as $provider)
                            <div>
                                <span class="font-semibold text-gray-700">{{ $provider['name'] }}:</span>
                                <span class="text-gray-900 font-mono">{{ $provider['number'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-semibold mb-1">Important Notes:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Please send exactly <strong>${{ number_format($item->price, 2) }}</strong></li>
                                @foreach(config('payments.manual.instructions.bank_transfer', []) as $instruction)
                                    <li>{{ $instruction }}</li>
                                @endforeach
                                <li>Your purchase will be activated after admin approval (usually within {{ config('payments.manual.processing_time', '24 hours') }})</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Submit Payment Details</h2>
                    <p class="text-gray-600 mt-2">After making the payment, fill in the details below to confirm your purchase</p>
                </div>

                <form method="POST" action="{{ route('payments.manual.submit') }}" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <input type="hidden" name="amount" value="{{ $item->price }}">

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Payment Method *</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method" value="bank_transfer" 
                                       class="sr-only peer" required onchange="toggleWalletProviders()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:border-blue-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-university text-blue-600 text-xl mr-3"></i>
                                        <div>
                                            <div class="font-semibold text-gray-900">Bank Transfer</div>
                                            <div class="text-sm text-gray-500">Traditional bank transfer</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method" value="mobile_wallet" 
                                       class="sr-only peer" required onchange="toggleWalletProviders()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:border-green-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-mobile-alt text-green-600 text-xl mr-3"></i>
                                        <div>
                                            <div class="font-semibold text-gray-900">Mobile Wallet</div>
                                            <div class="text-sm text-gray-500">bKash, Nagad, Rocket, etc.</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile Wallet Provider Selection -->
                    <div id="walletProviders" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Select Mobile Wallet Provider *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(config('payments.manual.mobile_wallet.providers') as $key => $provider)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="wallet_provider" value="{{ $key }}" 
                                       class="sr-only peer">
                                <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:border-green-300 text-center">
                                    @php
                                        $icons = [
                                            'bkash' => '📱',
                                            'nagad' => '🌟', 
                                            'rocket' => '🚀',
                                            'upay' => '💳'
                                        ];
                                    @endphp
                                    <div class="text-lg mb-1">{{ $icons[$key] ?? '📱' }}</div>
                                    <div class="font-semibold text-gray-900 text-sm">{{ $provider['name'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $provider['number'] }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('wallet_provider')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction ID -->
                    <div>
                        <label for="user_transaction_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Transaction ID *
                        </label>
                        <input type="text" 
                               id="user_transaction_id" 
                               name="user_transaction_id" 
                               value="{{ old('user_transaction_id') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Enter your transaction ID from receipt"
                               required>
                        <p class="text-sm text-gray-500 mt-1">
                            The transaction ID from your bank/mobile wallet receipt
                        </p>
                        @error('user_transaction_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender Name -->
                    <div>
                        <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Sender Name *
                        </label>
                        <input type="text" 
                               id="sender_name" 
                               name="sender_name" 
                               value="{{ old('sender_name', auth()->user()->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Name of the person who sent the money"
                               required>
                        @error('sender_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender Mobile -->
                    <div>
                        <label for="sender_mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            Sender Mobile Number *
                        </label>
                        <input type="text" 
                               id="sender_mobile" 
                               name="sender_mobile" 
                               value="{{ old('sender_mobile', auth()->user()->phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Mobile number used for payment"
                               required>
                        @error('sender_mobile')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Note -->
                    <div>
                        <label for="payment_note" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea id="payment_note" 
                                  name="payment_note" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Any additional information about your payment">{{ old('payment_note') }}</textarea>
                        @error('payment_note')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ $type === 'course' ? route('courses.show', $item) : route('bundles.show', $item) }}" 
                           class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-indigo-600 hover:to-blue-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold">
                            Submit Payment Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleWalletProviders() {
            const walletProviders = document.getElementById('walletProviders');
            const mobileWalletRadio = document.querySelector('input[name="payment_method"][value="mobile_wallet"]');
            const walletProviderInputs = document.querySelectorAll('input[name="wallet_provider"]');
            
            if (mobileWalletRadio.checked) {
                walletProviders.classList.remove('hidden');
                // Make wallet provider selection required
                walletProviderInputs.forEach(input => input.required = true);
            } else {
                walletProviders.classList.add('hidden');
                // Remove wallet provider requirement and clear selection
                walletProviderInputs.forEach(input => {
                    input.required = false;
                    input.checked = false;
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleWalletProviders();
        });
    </script>
</x-app-layout>
