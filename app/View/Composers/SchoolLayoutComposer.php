<?php

namespace App\View\Composers;

use App\Services\AccessMenuService;
use Illuminate\View\View;

class SchoolLayoutComposer
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function compose(View $view): void
    {
        $view->with([
            'accessMenu' => $this->accessMenu,
            'user' => auth()->user(),
            'sidebarMenu' => auth()->check() && auth()->user()->isSchoolUser()
                ? $this->accessMenu->getSidebarMenu(auth()->user())
                : collect(),
        ]);
    }
}
