<?php

namespace App\View\Composers;

use App\Services\AccessMenuService;
use Illuminate\View\View;

class SuperAdminLayoutComposer
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function compose(View $view): void
    {
        $view->with([
            'sidebarMenu' => $this->accessMenu->getSuperAdminSidebarMenu(),
            'accessMenu' => $this->accessMenu,
            'user' => auth()->user(),
        ]);
    }
}
