<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Razorpay\Api\Errors\SignatureVerificationError;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions,
        protected RazorpayService $razorpay
    ) {}

    public function expired(): View
    {
        $school = auth()->user()->school;
        $pending = $school->subscriptionPayments()
            ->where('status', SubscriptionPayment::STATUS_PENDING)
            ->with('plan')
            ->latest()
            ->first();

        return view('school.subscription-expired', [
            'school' => $school,
            'plans' => $this->subscriptions->getActivePlans(),
            'pendingPayment' => $pending,
            'razorpayConfigured' => $this->razorpay->isConfigured(),
            'razorpayKey' => config('services.razorpay.key'),
        ]);
    }

    public function createOrder(Request $request): JsonResponse
    {
        if (! $this->razorpay->isConfigured()) {
            return response()->json(['message' => 'Razorpay is not configured. Contact super admin.'], 503);
        }

        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $school = auth()->user()->school;
        $plan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->findOrFail($validated['subscription_plan_id']);

        try {
            $payment = $this->subscriptions->prepareRazorpayPayment($school, $plan, auth()->user());

            if (! $payment->razorpay_order_id) {
                $order = $this->razorpay->createOrder($payment);
                $payment->update(['razorpay_order_id' => $order['id']]);
            }

            return response()->json([
                'key' => config('services.razorpay.key'),
                'order_id' => $payment->razorpay_order_id,
                'amount' => (int) round($payment->amount * 100),
                'currency' => 'INR',
                'payment_id' => $payment->id,
                'school_name' => $school->name,
                'plan_name' => $plan->name,
                'user_name' => auth()->user()->name,
                'user_email' => auth()->user()->email,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Could not start payment. Please try again.'], 500);
        }
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'exists:subscription_payments,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $school = auth()->user()->school;
        $payment = SubscriptionPayment::query()
            ->where('school_id', $school->id)
            ->where('status', SubscriptionPayment::STATUS_PENDING)
            ->findOrFail($validated['payment_id']);

        try {
            $this->razorpay->verifyPaymentSignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            $this->subscriptions->completeRazorpayPayment(
                $payment,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id']
            );
        } catch (SignatureVerificationError) {
            return redirect()->route('school.subscription.expired')
                ->with('error', 'Payment verification failed. Please try again or contact support.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('school.subscription.expired')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('school.subscription.expired')
                ->with('error', 'Payment could not be completed. Please contact support.');
        }

        return redirect()->route('school.dashboard')
            ->with('success', 'Payment successful! Your subscription is now active.');
    }
}
