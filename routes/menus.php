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
});

Route::middleware(['auth', 'school_user'])->prefix('school')->name('school.')->group(function () {
    Route::get('/users-view', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'users-view')->middleware('page_access:users-view')->name('users-view');
    Route::get('/user-add', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'user-add')->middleware('page_access:user-add')->name('user-add');
    Route::get('/users', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'users')->middleware('page_access:users')->name('users');
});
