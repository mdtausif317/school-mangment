<?php

namespace App\Providers;

use App\View\Composers\SchoolLayoutComposer;
use App\View\Composers\SuperAdminLayoutComposer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.super-admin', SuperAdminLayoutComposer::class);
        View::composer('layouts.school', SchoolLayoutComposer::class);

        Route::bind('user', function (string $value) {
            $auth = auth()->user();
            $schoolId = $auth?->school_id;

            if (! $schoolId) {
                abort(403);
            }

            return User::query()
                ->forSchool($schoolId)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('student', function (string $value) {
            $auth = auth()->user();
            $schoolId = $auth?->school_id;

            if (! $schoolId) {
                abort(403);
            }

            return Student::query()
                ->where('school_id', $schoolId)
                ->whereKey($value)
                ->firstOrFail();
        });
    }
}
