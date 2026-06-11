<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function index(): View
    {
        return view('super-admin.super-dashboard');
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
