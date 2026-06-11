<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\DesignationController as SchoolDesignationController;
use App\Http\Controllers\School\UserController as SchoolUserController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\MenuController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->isSchoolUser()) {
            return redirect()->route('school.dashboard');
        }
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/super-admin/login', [LoginController::class, 'showSuperAdminLogin'])->name('super-admin.login');
    Route::post('/super-admin/login', [LoginController::class, 'superAdminLogin']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools/{school}/access', [SchoolController::class, 'access'])->name('schools.access');
    Route::put('/schools/{school}/access', [SchoolController::class, 'updateAccess'])->name('schools.access.update');

    Route::get('/menu-add', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu', [MenuController::class, 'update'])->name('menu.update');
    Route::post('/menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');
    Route::post('/menu/display', [MenuController::class, 'updateDisplay'])->name('menu.display');
    Route::post('/menu/button', [MenuController::class, 'storeButton'])->name('menu.button.store');
    Route::delete('/menu/button', [MenuController::class, 'destroyButton'])->name('menu.button.destroy');
});

Route::middleware(['auth', 'school_user'])->prefix('school')->name('school.')->group(function () {
    Route::get('/dashboard', [SchoolDashboardController::class, 'index'])
        ->middleware('page_access:dashboard')
        ->name('dashboard');

    Route::middleware('school_admin')->group(function () {
        Route::get('/manage/users', [SchoolUserController::class, 'index'])->name('users.index');
        Route::get('/manage/users/create', [SchoolUserController::class, 'create'])->name('users.create');
        Route::post('/manage/users', [SchoolUserController::class, 'store'])->name('users.store');
        Route::get('/manage/users/{user}/access', [SchoolUserController::class, 'access'])->name('users.access');
        Route::put('/manage/users/{user}/access', [SchoolUserController::class, 'updateAccess'])->name('users.access.update');

        Route::get('/manage/designations', [SchoolDesignationController::class, 'index'])->name('designations.index');
        Route::get('/manage/designations/create', [SchoolDesignationController::class, 'create'])->name('designations.create');
        Route::post('/manage/designations', [SchoolDesignationController::class, 'store'])->name('designations.store');
    });
});

require __DIR__.'/menus.php';
