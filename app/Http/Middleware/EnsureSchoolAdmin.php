<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isSchoolUser() || $user->designation?->slug !== 'admin') {
            abort(403, 'Only school admin can access this page.');
        }

        if (! $user->school?->portal_enabled) {
            abort(403, 'School portal access is not enabled.');
        }

        return $next($request);
    }
}
