<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSchoolUser() || ! $user->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Please log in to continue.');
        }

        if (! $user->school?->portal_enabled) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'School portal access is not enabled. Contact super admin.');
        }

        return $next($request);
    }
}
