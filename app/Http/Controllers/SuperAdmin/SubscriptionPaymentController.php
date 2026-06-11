<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPaymentController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function index(): View
    {
        return view('super-admin.payments', [
            'pendingPayments' => SubscriptionPayment::query()
                ->with(['school', 'plan', 'requester'])
                ->where('status', SubscriptionPayment::STATUS_PENDING)
                ->latest()
                ->get(),
            'recentPayments' => SubscriptionPayment::query()
                ->with(['school', 'plan'])
                ->whereIn('status', [SubscriptionPayment::STATUS_COMPLETED, SubscriptionPayment::STATUS_REJECTED])
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }

    public function assignSchool(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $plan = SubscriptionPlan::query()->findOrFail($validated['subscription_plan_id']);

        $this->subscriptions->activatePlan(
            $school,
            $plan,
            auth()->user(),
            $validated['payment_reference'] ?? null
        );

        return back()->with('success', "Subscription activated for \"{$school->name}\".");
    }

    public function approve(Request $request, SubscriptionPayment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->subscriptions->approvePayment(
                $payment,
                auth()->user(),
                $validated['payment_reference'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payment approved. School subscription is now active.');
    }

    public function reject(Request $request, SubscriptionPayment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptions->rejectPayment($payment, auth()->user(), $validated['notes'] ?? null);

        return back()->with('success', 'Payment request rejected.');
    }
}
