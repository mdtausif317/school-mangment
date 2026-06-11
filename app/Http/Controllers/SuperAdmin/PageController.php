<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $view = "super-admin.{$slug}";

        if (! view()->exists($view)) {
            abort(404);
        }

        return view($view);
    }
}
