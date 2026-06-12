<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\School\ClassController as SchoolClassController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\DesignationController as SchoolDesignationController;
use App\Http\Controllers\School\StudentController as SchoolStudentController;
use App\Http\Controllers\School\UserController as SchoolUserController;
use App\Http\Controllers\School\SubscriptionController as SchoolSubscriptionController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\MenuController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\Student\PortalController as StudentPortalController;
use App\Http\Controllers\SuperAdmin\SubscriptionPaymentController;
use App\Http\Controllers\SuperAdmin\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->isStudent()) {
            $school = $user->school;
            if ($school && ! app(\App\Services\SubscriptionService::class)->hasActiveSubscription($school)) {
                return redirect()->route('school.subscription.expired');
            }

            return redirect()->route('student.dashboard');
        }

        if ($user->isSchoolUser()) {
            $school = $user->school;
            if ($school && ! app(\App\Services\SubscriptionService::class)->hasActiveSubscription($school)) {
                return redirect()->route('school.subscription.expired');
            }

            return redirect()->route('school.dashboard');
        }
    }

    return redirect()->route('school.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/school/login', [LoginController::class, 'showSchoolLogin'])->name('school.login');
    Route::post('/school/login', [LoginController::class, 'schoolLogin']);
    Route::get('/super-admin/login', [LoginController::class, 'showSuperAdminLogin'])->name('super-admin.login');
    Route::post('/super-admin/login', [LoginController::class, 'superAdminLogin']);
    Route::get('/student/login', [LoginController::class, 'showStudentLogin'])->name('student.login');
    Route::post('/student/login', [LoginController::class, 'studentLogin']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/school-view', [SuperAdminDashboardController::class, 'schoolView'])->name('school-view');
    Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools/{school}/access', [SchoolController::class, 'access'])->name('schools.access');
    Route::put('/schools/{school}/access', [SchoolController::class, 'updateAccess'])->name('schools.access.update');
    Route::put('/schools/{school}/id-card', [SchoolController::class, 'updateIdCard'])->name('schools.id-card.update');
    Route::post('/id-card/preview', [SchoolController::class, 'previewIdCard'])->name('id-card.preview');

    Route::get('/menu-add', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu', [MenuController::class, 'update'])->name('menu.update');
    Route::post('/menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');
    Route::post('/menu/display', [MenuController::class, 'updateDisplay'])->name('menu.display');
    Route::post('/menu/button', [MenuController::class, 'storeButton'])->name('menu.button.store');
    Route::delete('/menu/button', [MenuController::class, 'destroyButton'])->name('menu.button.destroy');

    Route::get('/plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [SubscriptionPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('plans.update');

    Route::get('/payments', [SubscriptionPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/approve', [SubscriptionPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [SubscriptionPaymentController::class, 'reject'])->name('payments.reject');
    Route::post('/schools/{school}/subscription', [SubscriptionPaymentController::class, 'assignSchool'])->name('schools.subscription.assign');
});

Route::middleware(['auth', 'school_user'])->prefix('school')->name('school.')->group(function () {
    Route::get('/subscription/expired', [SchoolSubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::post('/subscription/razorpay/order', [SchoolSubscriptionController::class, 'createOrder'])->name('subscription.razorpay.order');
    Route::post('/subscription/razorpay/verify', [SchoolSubscriptionController::class, 'verify'])->name('subscription.razorpay.verify');

    Route::middleware('school_subscription')->group(function () {
        Route::get('/dashboard', [SchoolDashboardController::class, 'index'])
            ->middleware('page_access:dashboard')
            ->name('dashboard');

        Route::middleware('school_admin')->group(function () {
            Route::post('/user-add', [SchoolUserController::class, 'store'])
                ->name('user-add.store');
            Route::get('/users-view/{user}/access', [SchoolUserController::class, 'access'])
                ->name('users-view.access');
            Route::put('/users-view/{user}/access', [SchoolUserController::class, 'updateAccess'])
                ->name('users-view.access.update');

            Route::post('/designation-add', [SchoolDesignationController::class, 'store'])
                ->middleware('page_access:designation-add')
                ->name('designation-add.store');

            Route::post('/class-add', [SchoolClassController::class, 'store'])
                ->middleware('page_access:class-add')
                ->name('class-add.store');

            Route::post('/student-add', [SchoolStudentController::class, 'store'])
                ->middleware('page_access:student-add')
                ->name('student-add.store');

            Route::get('/students-view/{student}/edit', [SchoolStudentController::class, 'edit'])
                ->middleware('page_access:students-view')
                ->name('students-view.edit');
            Route::put('/students-view/{student}/edit', [SchoolStudentController::class, 'update'])
                ->middleware('page_access:students-view')
                ->name('students-view.update');
            Route::get('/students-view/{student}/card', [SchoolStudentController::class, 'card'])
                ->middleware('page_access:students-view')
                ->name('students-view.card');
        });
    });
});

Route::middleware(['auth', 'student_user'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::get('/id-card', [StudentPortalController::class, 'idCard'])->name('id-card');
});

require __DIR__ . '/menus.php';
