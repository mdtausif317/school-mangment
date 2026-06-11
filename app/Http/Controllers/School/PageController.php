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
    ];

    public function show(string $slug): View
    {
        if (isset($this->handlers[$slug])) {
            [$controller, $method] = $this->handlers[$slug];

            return app($controller)->{$method}();
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
