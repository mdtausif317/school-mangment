<?php

namespace App\Http\Middleware;

use App\Services\AccessMenuService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPageAccess
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->accessMenu->userHasPageAccess($user, $slug)) {
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}
