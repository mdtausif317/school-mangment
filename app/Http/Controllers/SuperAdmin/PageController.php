<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    /** Menu slugs that use a controller instead of a plain blade file. */
    protected array $handlers = [
        'school-view' => [DashboardController::class, 'schoolView'],
        'schools' => [DashboardController::class, 'schoolView'],
    ];

    public function show(string $slug): View
    {
        if (isset($this->handlers[$slug])) {
            [$controller, $method] = $this->handlers[$slug];

            return app($controller)->{$method}();
        }

        $view = "super-admin.{$slug}";

        if (! view()->exists($view)) {
            abort(404);
        }

        return view($view);
    }
}
