<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #475569 0%, #6B7280 50%, #71717A 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">💳 Manual Payments</h1>
                        <p class="text-slate-200 text-lg">Review and approve manual payment submissions</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl px-6 py-4 border border-white/20">
                            <div class="text-white text-center">
                                <div class="text-2xl font-bold">Admin Panel</div>
                                <div class="text-slate-200 text-sm">Payment Management</div>
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
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                <form method="GET" action="{{ route('admin.payments.manual') }}" class="flex flex-wrap items-center gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Methods</option>
                            <option value="bank_transfer" {{ $paymentMethod === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_wallet" {{ $paymentMethod === 'mobile_wallet' ? 'selected' : '' }}>Mobile Wallet</option>
                        </select>
                    </div>
                    <div class="self-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            @php
                $pendingCount = \App\Models\Payment::pendingApproval()->count();
                $approvedToday = \App\Models\Payment::approved()->whereDate('approved_at', today())->count();
                $totalApproved = \App\Models\Payment::approved()->count();
                $rejectedToday = \App\Models\Payment::rejected()->whereDate('approved_at', today())->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending Approval</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Approved Today</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $approvedToday }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-thumbs-up text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Approved</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalApproved }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Rejected Today</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $rejectedToday }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ ucfirst($status) }} Payments 
                        ({{ $payments->total() }})
                    </h2>
                </div>

                @if($payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User & Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <!-- User & Item -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-start">
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="text-gray-600 font-semibold">{{ substr($payment->user->name, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="font-semibold text-gray-900">{{ $payment->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                                                    <div class="text-sm text-blue-600 mt-1">
                                                        {{ $payment->course ? $payment->course->title : $payment->bundle->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $payment->course ? 'Course' : 'Bundle' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Payment Details -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm">
                                                <div class="font-semibold text-gray-900">${{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-gray-500">
                                            @if($payment->payment_method === 'bank_transfer')
                                                <i class="fas fa-university mr-1"></i>Bank Transfer
                                            @else
                                                <i class="fas fa-mobile-alt mr-1"></i>{{ ucfirst($payment->wallet_provider ?? 'Mobile Wallet') }}
                                            @endif
                                        </div>
                                                <div class="text-xs text-gray-400">{{ $payment->created_at->format('M j, Y g:i A') }}</div>
                                            </div>
                                        </td>

                                        <!-- Transaction Info -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm">
                                                <div class="font-mono text-gray-900">{{ $payment->user_transaction_id }}</div>
                                                <div class="text-gray-500">{{ $payment->sender_name }}</div>
                                                <div class="text-gray-500">{{ $payment->sender_mobile }}</div>
                                                @if($payment->payment_note)
                                                    <div class="text-xs text-gray-400 mt-1 max-w-xs truncate" title="{{ $payment->payment_note }}">
                                                        Note: {{ $payment->payment_note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4">
                                            @if($payment->status === 'pending')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                                    Pending
                                                </span>
                                            @elseif($payment->status === 'approved')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                                    Approved
                                                </span>
                                                @if($payment->approvedBy)
                                                    <div class="text-xs text-gray-500 mt-1">by {{ $payment->approvedBy->name }}</div>
                                                @endif
                                            @elseif($payment->status === 'rejected')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                                    Rejected
                                                </span>
                                                @if($payment->approvedBy)
                                                    <div class="text-xs text-gray-500 mt-1">by {{ $payment->approvedBy->name }}</div>
                                                @endif
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4">
                                            @if($payment->status === 'pending')
                                                <div class="flex items-center space-x-2">
                                                    <button onclick="openApprovalModal({{ $payment->id }}, 'approve')" 
                                                            class="text-green-600 hover:text-green-800 font-medium text-sm">
                                                        <i class="fas fa-check mr-1"></i>Approve
                                                    </button>
                                                    <button onclick="openApprovalModal({{ $payment->id }}, 'reject')" 
                                                            class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                        <i class="fas fa-times mr-1"></i>Reject
                                                    </button>
                                                </div>
                                            @else
                                                <button onclick="viewPaymentDetails({{ $payment->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                                    <i class="fas fa-eye mr-1"></i>View Details
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">💳</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No {{ $status }} payments found</h3>
                        <p class="text-gray-600">
                            @if($status === 'pending')
                                All manual payments have been processed.
                            @else
                                No {{ $status }} payments to display.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Approve Payment</h3>
                <form id="approvalForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="admin_note" class="block text-sm font-medium text-gray-700 mb-2">
                            Admin Note <span id="noteRequired" class="text-red-500 hidden">*</span>
                        </label>
                        <textarea id="admin_note" 
                                  name="admin_note" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Add a note about this decision..."></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" 
                                onclick="closeApprovalModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-6 py-2 text-white rounded-lg transition-colors">
                            Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPaymentId = null;
        let currentAction = null;

        function openApprovalModal(paymentId, action) {
            currentPaymentId = paymentId;
            currentAction = action;
            
            const modal = document.getElementById('approvalModal');
            const form = document.getElementById('approvalForm');
            const title = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitBtn');
            const noteRequired = document.getElementById('noteRequired');
            const noteField = document.getElementById('admin_note');
            
            if (action === 'approve') {
                title.textContent = 'Approve Payment';
                submitBtn.textContent = 'Approve';
                submitBtn.className = 'px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors';
                form.action = `/admin/payments/${paymentId}/approve`;
                noteRequired.classList.add('hidden');
                noteField.required = false;
            } else {
                title.textContent = 'Reject Payment';
                submitBtn.textContent = 'Reject';
                submitBtn.className = 'px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors';
                form.action = `/admin/payments/${paymentId}/reject`;
                noteRequired.classList.remove('hidden');
                noteField.required = true;
            }
            
            modal.classList.remove('hidden');
        }

        function closeApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
            document.getElementById('admin_note').value = '';
        }

        function viewPaymentDetails(paymentId) {
            // You can implement a detailed view modal here
            alert('Payment details view coming soon!');
        }

        // Close modal when clicking outside
        document.getElementById('approvalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApprovalModal();
            }
        });
    </script>
</x-app-layout>
