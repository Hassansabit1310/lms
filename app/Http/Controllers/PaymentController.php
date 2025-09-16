<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Show payment form
     */
    public function showForm(Payment $payment): View
    {
        // Ensure user can only see their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if payment is still pending
        if ($payment->status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Payment is no longer pending.');
        }

        return view('payments.form', compact('payment'));
    }

    /**
     * Simulate payment completion (for testing)
     * In real implementation, this would be replaced by SSLCommerz integration
     */
    public function simulatePayment(Request $request, Payment $payment)
    {
        // Ensure user can only complete their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $action = $request->input('action');

        if ($action === 'success') {
            return app(EnrollmentController::class)->handlePaymentSuccess(
                $request->merge(['gateway_transaction_id' => 'MOCK_' . time()]), 
                $payment
            );
        } else {
            return app(EnrollmentController::class)->handlePaymentFailure($request, $payment);
        }
    }

    /**
     * Show user's payment history
     */
    public function history()
    {
        $user = auth()->user();
        
        $payments = $user->payments()
            ->with(['course', 'bundle', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.history', compact('payments'));
    }
}