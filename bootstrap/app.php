<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'school_user' => \App\Http\Middleware\EnsureSchoolUser::class,
            'page_access' => \App\Http\Middleware\CheckPageAccess::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if ($user?->isSuperAdmin()) {
                return route('super-admin.dashboard');
            }

            if ($user?->isSchoolUser()) {
                return route('school.dashboard');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
