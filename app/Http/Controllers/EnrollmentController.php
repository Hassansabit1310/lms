<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Bundle;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Enroll in a free course
     */
    public function enrollFreeCourse(Course $course): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to enroll in courses.');
        }

        // Check if course is actually free
        if (!$course->is_free) {
            return redirect()->back()->with('error', 'This course requires payment.');
        }

        // Check if already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('info', 'You are already enrolled in this course.');
        }

        // Enroll the user
        $user->enrollInCourse($course);

        return redirect()->route('courses.show', $course)->with('success', 'Successfully enrolled in the course!');
    }

    /**
     * Process course purchase and enrollment
     */
    public function purchaseCourse(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to purchase courses.');
        }

        // Check if course is purchasable
        if ($course->is_free) {
            return redirect()->route('enrollments.free', $course);
        }

        // Check if already purchased
        if ($user->payments()->where('course_id', $course->id)->whereIn('status', ['completed', 'success', 'approved'])->exists()) {
            return redirect()->route('courses.show', $course)->with('info', 'You have already purchased this course.');
        }

        // Check if user has active subscription
        if ($user->hasActiveSubscription()) {
            $user->enrollInCourse($course);
            return redirect()->route('courses.show', $course)->with('success', 'Course accessed via your subscription!');
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'gateway' => 'sslcommerz',
            'status' => 'pending',
        ]);

        // Redirect to payment gateway (placeholder for now)
        return $this->redirectToPaymentGateway($payment);
    }

    /**
     * Process bundle purchase and enrollment
     */
    public function purchaseBundle(Request $request, Bundle $bundle): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to purchase bundles.');
        }

        // Check if bundle is available
        if (!$bundle->isAvailable()) {
            return redirect()->back()->with('error', 'This bundle is not available for purchase.');
        }

        // Check if already purchased
        if ($user->hasPurchasedBundle($bundle)) {
            return redirect()->route('bundles.show', $bundle)->with('info', 'You have already purchased this bundle.');
        }

        // Check if user has active subscription
        if ($user->hasActiveSubscription()) {
            $user->enrollInBundle($bundle);
            return redirect()->route('bundles.show', $bundle)->with('success', 'Bundle courses accessed via your subscription!');
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'bundle_id' => $bundle->id,
            'amount' => $bundle->price,
            'gateway' => 'sslcommerz',
            'status' => 'pending',
        ]);

        // Redirect to payment gateway
        return $this->redirectToPaymentGateway($payment);
    }

    /**
     * Process subscription purchase
     */
    public function purchaseSubscription(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to subscribe.');
        }

        $validated = $request->validate([
            'type' => 'required|in:monthly,annual',
        ]);

        // Check if user already has active subscription
        if ($user->hasActiveSubscription()) {
            return redirect()->back()->with('info', 'You already have an active subscription.');
        }

        // Define subscription pricing
        $pricing = [
            'monthly' => 19.99,
            'annual' => 199.99, // Save 17%
        ];

        $amount = $pricing[$validated['type']];

        // Create subscription record
        $endDate = $validated['type'] === 'monthly' ? now()->addMonth() : now()->addYear();
        
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_type' => $validated['type'],
            'amount' => $amount,
            'start_date' => now(),
            'end_date' => $endDate,
            'status' => 'pending', // Will be activated after payment
        ]);

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'gateway' => 'sslcommerz',
            'status' => 'pending',
        ]);

        // Redirect to payment gateway
        return $this->redirectToPaymentGateway($payment);
    }

    /**
     * Handle successful payment (webhook/callback)
     */
    public function handlePaymentSuccess(Request $request, Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment, $request) {
            // Mark payment as successful
            $payment->markAsSuccessful(
                $request->get('gateway_transaction_id'),
                $request->all()
            );

            $user = $payment->user;

            if ($payment->course_id) {
                // Course purchase - enroll user
                $user->enrollInCourse($payment->course);
                
            } elseif ($payment->bundle_id) {
                // Bundle purchase - enroll in all courses
                $user->enrollInBundle($payment->bundle);
                
            } elseif ($payment->subscription_id) {
                // Subscription purchase - activate subscription
                $payment->subscription->update(['status' => 'active']);
                
                // Enroll in all premium courses
                $premiumCourses = Course::where('is_free', false)->where('status', 'published')->get();
                foreach ($premiumCourses as $course) {
                    $user->enrollInCourse($course);
                }
            }
        });

        // Redirect based on payment type
        if ($payment->course_id) {
            return redirect()->route('courses.show', $payment->course)->with('success', 'Payment successful! You now have access to the course.');
        } elseif ($payment->bundle_id) {
            return redirect()->route('bundles.show', $payment->bundle)->with('success', 'Payment successful! You now have access to all courses in the bundle.');
        } elseif ($payment->subscription_id) {
            return redirect()->route('dashboard')->with('success', 'Subscription activated! You now have access to all premium courses.');
        }

        return redirect()->route('dashboard')->with('success', 'Payment successful!');
    }

    /**
     * Handle failed payment
     */
    public function handlePaymentFailure(Request $request, Payment $payment): RedirectResponse
    {
        $payment->markAsFailed($request->all());

        return redirect()->back()->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Show user's enrollments
     */
    public function myEnrollments()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $enrollments = $user->enrollments()
            ->with(['course.category'])
            ->orderBy('enrolled_at', 'desc')
            ->paginate(12);

        $purchasedBundles = $user->purchasedBundles()
            ->with(['courses'])
            ->get();

        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        // Get payment history
        $payments = $user->payments()
            ->with(['course', 'bundle', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('enrollments.index', compact('enrollments', 'purchasedBundles', 'activeSubscription', 'payments'));
    }

    /**
     * Redirect to payment gateway (placeholder)
     * In a real implementation, this would integrate with SSLCommerz
     */
    private function redirectToPaymentGateway(Payment $payment): RedirectResponse
    {
        // For now, create a simple payment form
        return redirect()->route('payments.form', $payment);
    }
}