<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentUser
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStudent() || ! $user->is_active) {
            return redirect()->route('student.login')
                ->with('error', 'Please log in with your student account.');
        }

        if (! $user->studentRecord) {
            auth()->logout();

            return redirect()->route('student.login')
                ->with('error', 'Student profile not linked. Contact your school admin.');
        }

        if (! $user->school?->portal_enabled || ! $user->school->is_active) {
            auth()->logout();

            return redirect()->route('student.login')
                ->with('error', 'School portal access is not enabled.');
        }

        if (! $this->subscriptions->hasActiveSubscription($user->school)) {
            return redirect()->route('school.subscription.expired');
        }

        return $next($request);
    }
}
