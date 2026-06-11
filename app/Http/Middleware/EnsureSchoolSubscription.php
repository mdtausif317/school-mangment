<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolSubscription
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $school = $user?->school;

        if (! $school) {
            return $next($request);
        }

        if ($this->subscriptions->hasActiveSubscription($school)) {
            return $next($request);
        }

        if ($request->routeIs('school.subscription.*')) {
            return $next($request);
        }

        return redirect()->route('school.subscription.expired');
    }
}
