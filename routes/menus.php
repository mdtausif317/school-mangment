<?php

/**
 * Auto-generated menu routes. Do not edit manually.
 * Updated when menus are added or edited in Menu Management.
 */

use App\Http\Controllers\School\PageController as SchoolPageController;
use App\Http\Controllers\SuperAdmin\PageController as SuperAdminPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/school-view', [App\Http\Controllers\SuperAdmin\PageController::class, 'show'])->defaults('slug', 'school-view')->name('school-view');
    Route::get('/users', [App\Http\Controllers\SuperAdmin\PageController::class, 'show'])->defaults('slug', 'users')->name('users');
});

