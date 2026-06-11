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
            ['title' => 'Schools', 'slug' => 'schools', 'icon' => 'fas fa-school'],
            ['title' => 'School Add', 'slug' => 'create-school', 'icon' => 'fas fa-plus-circle'],
            ['title' => 'Menu Management', 'slug' => 'menu-add', 'icon' => 'fas fa-bars'],
            ['title' => 'Dashboard', 'slug' => 'dashboard', 'icon' => 'fas fa-home'],
        ];

        foreach ($menus as $menuData) {
            $existing = PageMenu::query()->where('slug', $menuData['slug'])->first();

            if ($existing) {
                $existing->update([
                    'school_id' => null,
                    'title' => $menuData['title'],
                ]);

                continue;
            }

            $accessMenu->addMenu(null, $superAdmin, $menuData);
        }
    }
}
