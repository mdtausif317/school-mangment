<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('super-admin.super-dashboard');
    }

    public function schoolView(): View
    {
        $schools = School::query()->withCount('users')->latest()->get();

        return view('super-admin.school-view', compact('schools'));
    }
}
