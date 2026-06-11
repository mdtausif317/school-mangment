<?php

namespace Database\Seeders;

use App\Models\PageMenu;
use App\Models\User;
use App\Services\AccessMenuService;
use Illuminate\Database\Seeder;

class GlobalMenuSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::query()
            ->where('user_type', User::TYPE_SUPER_ADMIN)
            ->first();

        if (! $superAdmin) {
            return;
        }

        $accessMenu = app(AccessMenuService::class);

        $menus = [
            ['title' => 'Dashboard', 'slug' => 'dashboard', 'icon' => 'fas fa-home'],
        ];

        foreach ($menus as $menuData) {
            $existing = PageMenu::query()->where('slug', $menuData['slug'])->first();

            if ($existing) {
                if ($existing->school_id !== null) {
                    $existing->update(['school_id' => null]);
                }

                continue;
            }

            $accessMenu->addMenu(null, $superAdmin, $menuData);
        }
    }
}
