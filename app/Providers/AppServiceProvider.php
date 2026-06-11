<?php

namespace App\Providers;

use App\View\Composers\SchoolLayoutComposer;
use App\View\Composers\SuperAdminLayoutComposer;
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
    }
}
