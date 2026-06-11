<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
            }

            return redirect()->route('super-admin.login')
                ->with('error', 'Super admin access required.');
        }

        return $next($request);
    }
}
