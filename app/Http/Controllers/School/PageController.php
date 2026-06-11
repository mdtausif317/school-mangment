<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $view = "school.{$slug}";

        if (! view()->exists($view)) {
            abort(404);
        }

        return view($view, [
            'user' => auth()->user(),
        ]);
    }
}
