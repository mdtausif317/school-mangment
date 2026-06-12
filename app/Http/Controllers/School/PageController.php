<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    /** Menu slugs that use a controller instead of a plain blade file. */
    protected array $handlers = [
        'users-view' => [UserController::class, 'index'],
        'users' => [UserController::class, 'index'],
        'user-add' => [UserController::class, 'create'],
        'designations' => [DesignationController::class, 'index'],
        'designation-add' => [DesignationController::class, 'create'],
        'classes-view' => [ClassController::class, 'index'],
        'class-add' => [ClassController::class, 'create'],
        'students-view' => [StudentController::class, 'index'],
        'student-add' => [StudentController::class, 'create'],
        'attendance-manage' => [AttendanceController::class, 'index'],
        'fees-collect' => [FeePaymentController::class, 'index'],
        'reports' => [ReportController::class, 'index'],
        'reports-attendance' => [ReportController::class, 'attendance'],
        'reports-fees' => [ReportController::class, 'fees'],
    ];

    public function show(string $slug): View
    {
        if (isset($this->handlers[$slug])) {
            [$controller, $method] = $this->handlers[$slug];

            return app()->call([app($controller), $method]);
        }

        $view = "school.{$slug}";

        if (! view()->exists($view)) {
            abort(404);
        }

        return view($view, [
            'user' => auth()->user(),
        ]);
    }
}
