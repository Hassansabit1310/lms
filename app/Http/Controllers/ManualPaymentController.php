<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Bundle;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ManualPaymentController extends Controller
{
    /**
     * Show manual payment form for course
     */
    public function courseForm(Course $course): View
    {
        // Check if user already has access
        if (auth()->user()->hasAccessToCourse($course)) {
            return redirect()
                ->route('courses.show', $course)
                ->with('info', 'You already have access to this course.');
        }

        return view('payments.manual-form', [
            'item' => $course,
            'type' => 'course'
        ]);
    }

    /**
     * Show manual payment form for bundle
     */
    public function bundleForm(Bundle $bundle): View
    {
        // Check if user already purchased this bundle
        if (auth()->user()->hasPurchasedBundle($bundle)) {
            return redirect()
                ->route('bundles.show', $bundle)
                ->with('info', 'You already have access to this bundle.');
        }

        return view('payments.manual-form', [
            'item' => $bundle,
            'type' => 'bundle'
        ]);
    }

    /**
     * Submit manual payment details
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:course,bundle',
            'item_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,mobile_wallet',
            'wallet_provider' => 'required_if:payment_method,mobile_wallet|in:bkash,nagad,rocket,upay',
            'user_transaction_id' => 'required|string|max:255',
            'sender_name' => 'required|string|max:255',
            'sender_mobile' => 'required|string|max:20',
            'payment_note' => 'nullable|string|max:1000',
        ], [
            'payment_method.required' => 'Please select a payment method.',
            'wallet_provider.required_if' => 'Please select a mobile wallet provider when using mobile wallet payment.',
            'user_transaction_id.required' => 'Transaction ID is required.',
            'sender_name.required' => 'Sender name is required.',
            'sender_mobile.required' => 'Sender mobile number is required.',
        ]);

        $user = Auth::user();

        // Verify the item exists and get correct amount
        if ($validated['type'] === 'course') {
            $item = Course::findOrFail($validated['item_id']);
            $courseId = $item->id;
            $bundleId = null;
            
            // Check if user already has access
            if ($user->hasAccessToCourse($item)) {
                return redirect()
                    ->route('courses.show', $item)
                    ->with('info', 'You already have access to this course.');
            }
        } else {
            $item = Bundle::findOrFail($validated['item_id']);
            $courseId = null;
            $bundleId = $item->id;
            
            // Check if user already purchased this bundle
            if ($user->hasPurchasedBundle($item)) {
                return redirect()
                    ->route('bundles.show', $item)
                    ->with('info', 'You already have access to this bundle.');
            }
        }

        // Verify amount matches
        if ($validated['amount'] != $item->price) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Payment amount does not match the item price.');
        }

        // Check if user already has a pending payment for this item
        $existingPayment = Payment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('bundle_id', $bundleId)
            ->where('status', 'pending')
            ->whereIn('payment_method', ['bank_transfer', 'mobile_wallet'])
            ->first();

        if ($existingPayment) {
            return redirect()
                ->route('payments.manual.status', $existingPayment)
                ->with('info', 'You already have a pending payment for this item.');
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'bundle_id' => $bundleId,
            'amount' => $validated['amount'],
            'gateway' => 'manual',
            'payment_method' => $validated['payment_method'],
            'wallet_provider' => $validated['wallet_provider'] ?? null,
            'user_transaction_id' => $validated['user_transaction_id'],
            'sender_name' => $validated['sender_name'],
            'sender_mobile' => $validated['sender_mobile'],
            'payment_note' => $validated['payment_note'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('payments.manual.status', $payment)
            ->with('success', 'Payment details submitted successfully! We will review and approve your payment within 24 hours.');
    }

    /**
     * Show payment status
     */
    public function status(Payment $payment): View
    {
        // Ensure user can only see their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load(['course', 'bundle', 'approvedBy']);

        return view('payments.manual-status', compact('payment'));
    }

    /**
     * Admin: List all manual payments for approval
     */
    public function adminIndex(Request $request): View
    {
        $status = $request->get('status', 'pending');
        $paymentMethod = $request->get('payment_method');

        $query = Payment::manualPayments()
            ->with(['user', 'course', 'bundle', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        if ($status === 'pending') {
            $query->pendingApproval();
        } elseif ($status === 'approved') {
            $query->approved();
        } elseif ($status === 'rejected') {
            $query->rejected();
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $payments = $query->paginate(20);

        return view('admin.payments.manual', compact('payments', 'status', 'paymentMethod'));
    }

    /**
     * Admin: Approve manual payment
     */
    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        if (!$payment->isManual() || !$payment->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'This payment cannot be approved.');
        }

        $payment->approve(auth()->user(), $request->admin_note);

        // Enroll user in course/bundle
        if ($payment->course_id) {
            $payment->user->enrollInCourse($payment->course);
        } elseif ($payment->bundle_id) {
            $payment->user->enrollInBundle($payment->bundle);
        }

        return redirect()
            ->back()
            ->with('success', 'Payment approved successfully and user has been enrolled.');
    }

    /**
     * Admin: Reject manual payment
     */
    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);

        if (!$payment->isManual() || !$payment->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'This payment cannot be rejected.');
        }

        $payment->reject(auth()->user(), $request->admin_note);

        return redirect()
            ->back()
            ->with('success', 'Payment rejected successfully.');
    }
}