<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function getActivePlans(): Collection
    {
        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
    }

    public function getActiveSubscription(School $school): ?SchoolSubscription
    {
        $this->expireOutdatedSubscriptions($school);

        return $school->subscriptions()
            ->with('plan')
            ->where('status', SchoolSubscription::STATUS_ACTIVE)
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();
    }

    public function hasActiveSubscription(School $school): bool
    {
        return $this->getActiveSubscription($school) !== null;
    }

    public function activatePlan(
        School $school,
        SubscriptionPlan $plan,
        ?User $approvedBy = null,
        ?string $paymentReference = null,
        ?string $paymentMethod = null,
        bool $recordPayment = true
    ): SchoolSubscription {
        return DB::transaction(function () use ($school, $plan, $approvedBy, $paymentReference, $paymentMethod, $recordPayment) {
            $subscription = $this->createActiveSubscription($school, $plan);

            if ($recordPayment) {
                SubscriptionPayment::create([
                    'school_id' => $school->id,
                    'subscription_plan_id' => $plan->id,
                    'school_subscription_id' => $subscription->id,
                    'amount' => $plan->price,
                    'status' => SubscriptionPayment::STATUS_COMPLETED,
                    'payment_method' => $paymentMethod ?? 'manual',
                    'payment_reference' => $paymentReference,
                    'approved_by' => $approvedBy?->id,
                    'paid_at' => now(),
                ]);
            }

            return $subscription;
        });
    }

    protected function createActiveSubscription(School $school, SubscriptionPlan $plan): SchoolSubscription
    {
        $this->expireOutdatedSubscriptions($school);

        $school->subscriptions()
            ->where('status', SchoolSubscription::STATUS_ACTIVE)
            ->update(['status' => SchoolSubscription::STATUS_CANCELLED]);

        return SchoolSubscription::create([
            'school_id' => $school->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'status' => SchoolSubscription::STATUS_ACTIVE,
        ]);
    }

    public function prepareRazorpayPayment(School $school, SubscriptionPlan $plan, User $requestedBy): SubscriptionPayment
    {
        $pending = SubscriptionPayment::query()
            ->where('school_id', $school->id)
            ->where('status', SubscriptionPayment::STATUS_PENDING)
            ->first();

        if ($pending) {
            if ($pending->payment_method !== 'razorpay' || $pending->subscription_plan_id !== $plan->id) {
                throw new \InvalidArgumentException('A renewal is already in progress. Please wait or contact super admin.');
            }

            return $pending;
        }

        return SubscriptionPayment::create([
            'school_id' => $school->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => SubscriptionPayment::STATUS_PENDING,
            'payment_method' => 'razorpay',
            'requested_by' => $requestedBy->id,
        ]);
    }

    public function completeRazorpayPayment(
        SubscriptionPayment $payment,
        string $razorpayOrderId,
        string $razorpayPaymentId
    ): SchoolSubscription {
        if ($payment->status !== SubscriptionPayment::STATUS_PENDING) {
            throw new \InvalidArgumentException('This payment is already processed.');
        }

        if ($payment->razorpay_order_id && $payment->razorpay_order_id !== $razorpayOrderId) {
            throw new \InvalidArgumentException('Payment order mismatch.');
        }

        return DB::transaction(function () use ($payment, $razorpayOrderId, $razorpayPaymentId) {
            $subscription = $this->createActiveSubscription($payment->school, $payment->plan);

            $payment->update([
                'status' => SubscriptionPayment::STATUS_COMPLETED,
                'school_subscription_id' => $subscription->id,
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'payment_reference' => $razorpayPaymentId,
                'paid_at' => now(),
            ]);

            return $subscription;
        });
    }

    public function approvePayment(SubscriptionPayment $payment, User $superAdmin, ?string $reference = null): SchoolSubscription
    {
        if ($payment->status !== SubscriptionPayment::STATUS_PENDING) {
            throw new \InvalidArgumentException('This payment is not pending.');
        }

        return DB::transaction(function () use ($payment, $superAdmin, $reference) {
            $subscription = $this->createActiveSubscription($payment->school, $payment->plan);

            $payment->update([
                'status' => SubscriptionPayment::STATUS_COMPLETED,
                'school_subscription_id' => $subscription->id,
                'approved_by' => $superAdmin->id,
                'payment_reference' => $reference ?? $payment->payment_reference,
                'paid_at' => now(),
            ]);

            return $subscription;
        });
    }

    public function rejectPayment(SubscriptionPayment $payment, User $superAdmin, ?string $notes = null): void
    {
        $payment->update([
            'status' => SubscriptionPayment::STATUS_REJECTED,
            'approved_by' => $superAdmin->id,
            'notes' => $notes ?? $payment->notes,
        ]);
    }

    public function expireOutdatedSubscriptions(School $school): void
    {
        $school->subscriptions()
            ->where('status', SchoolSubscription::STATUS_ACTIVE)
            ->where('expires_at', '<=', now())
            ->update(['status' => SchoolSubscription::STATUS_EXPIRED]);
    }

    public function subscriptionStatusLabel(School $school): string
    {
        $active = $this->getActiveSubscription($school);

        if ($active) {
            return 'Active until '.$active->expires_at->format('M d, Y');
        }

        return 'Expired';
    }
}
