<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function expired(): View
    {
        $school = auth()->user()->school;
        $pending = $school->subscriptionPayments()
            ->where('status', 'pending')
            ->with('plan')
            ->latest()
            ->first();

        return view('school.subscription-expired', [
            'school' => $school,
            'plans' => $this->subscriptions->getActivePlans(),
            'pendingPayment' => $pending,
        ]);
    }

    public function renew(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $school = auth()->user()->school;
        $plan = SubscriptionPlan::query()->findOrFail($validated['subscription_plan_id']);

        try {
            $this->subscriptions->requestRenewal(
                $school,
                $plan,
                auth()->user(),
                trim(($validated['notes'] ?? '').' Ref: '.($validated['payment_reference'] ?? ''))
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Renewal request submitted. Super admin will activate after payment verification.');
    }
}
