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
    Route::get('/classes-view', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'classes-view')->middleware('page_access:classes-view')->name('classes-view');
    Route::get('/reports-attendance', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'reports-attendance')->middleware('page_access:reports-attendance')->name('reports-attendance');
    Route::get('/class-add', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'class-add')->middleware('page_access:class-add')->name('class-add');
    Route::get('/students-view', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'students-view')->middleware('page_access:students-view')->name('students-view');
    Route::get('/reports-fees', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'reports-fees')->middleware('page_access:reports-fees')->name('reports-fees');
    Route::get('/student-add', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'student-add')->middleware('page_access:student-add')->name('student-add');
    Route::get('/attendance-manage', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'attendance-manage')->middleware('page_access:attendance-manage')->name('attendance-manage');
    Route::get('/fees-collect', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'fees-collect')->middleware('page_access:fees-collect')->name('fees-collect');
    Route::get('/reports', [App\Http\Controllers\School\PageController::class, 'show'])->defaults('slug', 'reports')->middleware('page_access:reports')->name('reports');
    });
});
