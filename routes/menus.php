<?php

/**
 * Auto-generated menu routes. Do not edit manually.
 * Updated when menus are added or edited in Menu Management.
 */

use App\Http\Controllers\School\PageController as SchoolPageController;
use App\Http\Controllers\SuperAdmin\PageController as SuperAdminPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/super-dashboard', [App\Http\Controllers\SuperAdmin\PageController::class, 'show'])->defaults('slug', 'super-dashboard')->name('super-dashboard');
});

Route::middleware(['auth', 'school_user'])->prefix('school')->name('school.')->group(function () {
    Route::middleware('school_subscription')->group(function () {
    Route::get('/users-view', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'users-view')->middleware('page_access:users-view')->name('users-view');
    Route::get('/classes-view', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'classes-view')->middleware('page_access:classes-view')->name('classes-view');
    Route::get('/students-view', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'students-view')->middleware('page_access:students-view')->name('students-view');
    Route::get('/user-add', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'user-add')->middleware('page_access:user-add')->name('user-add');
    Route::get('/class-add', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'class-add')->middleware('page_access:class-add')->name('class-add');
    Route::get('/student-add', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'student-add')->middleware('page_access:student-add')->name('student-add');
    });
});
