<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Services\AccessMenuService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $sidebarMenu = $this->accessMenu->getSidebarMenu($user);

        return view('school.dashboard', compact('sidebarMenu', 'user'));
    }
}
