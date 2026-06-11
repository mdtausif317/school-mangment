<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function index(): View
    {
        $activeSubscriptionQuery = fn ($q) => $q
            ->where('status', SchoolSubscription::STATUS_ACTIVE)
            ->where('expires_at', '>', now());

        return view('super-admin.super-dashboard', [
            'stats' => [
                'total_schools' => School::count(),
                'portal_enabled' => School::where('portal_enabled', true)->count(),
                'total_users' => User::query()->whereNotNull('school_id')->count(),
                'active_subscriptions' => SchoolSubscription::query()
                    ->where('status', SchoolSubscription::STATUS_ACTIVE)
                    ->where('expires_at', '>', now())
                    ->count(),
                'expired_portals' => School::query()
                    ->where('portal_enabled', true)
                    ->whereDoesntHave('subscriptions', $activeSubscriptionQuery)
                    ->count(),
                'pending_payments' => SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PENDING)->count(),
                'total_revenue' => SubscriptionPayment::where('status', SubscriptionPayment::STATUS_COMPLETED)->sum('amount'),
                'active_plans' => SubscriptionPlan::where('is_active', true)->count(),
            ],
            'recentSchools' => School::query()->withCount('users')->latest()->limit(5)->get(),
            'pendingPayments' => SubscriptionPayment::query()
                ->with(['school', 'plan', 'requester'])
                ->where('status', SubscriptionPayment::STATUS_PENDING)
                ->latest()
                ->limit(5)
                ->get(),
            'recentPayments' => SubscriptionPayment::query()
                ->with(['school', 'plan'])
                ->where('status', SubscriptionPayment::STATUS_COMPLETED)
                ->latest('paid_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function schoolView(): View
    {
        $schools = School::query()
            ->withCount('users')
            ->with(['subscriptions' => fn ($q) => $q->where('status', 'active')->latest('expires_at')])
            ->latest()
            ->get();

        $subscriptionLabels = $schools->mapWithKeys(function (School $school) {
            return [$school->id => $this->subscriptions->subscriptionStatusLabel($school)];
        });

        return view('super-admin.school-view', compact('schools', 'subscriptionLabels'));
    }
}
