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

        $schools = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Schools',
            'slug' => 'schools',
            'route_name' => 'super-admin.dashboard',
            'scope' => PageMenu::SCOPE_PLATFORM,
            'icon' => 'fas fa-school',
            'parent_id' => null,
        ]);

        $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'School Add',
            'slug' => 'create-school',
            'route_name' => 'super-admin.schools.create',
            'scope' => PageMenu::SCOPE_PLATFORM,
            'icon' => 'fas fa-plus-circle',
            'parent_id' => $schools->id,
        ]);

        $subscriptions = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Subscriptions',
            'slug' => 'subscriptions',
            'route_name' => 'super-admin.plans.index',
            'scope' => PageMenu::SCOPE_PLATFORM,
            'icon' => 'fas fa-credit-card',
            'parent_id' => null,
        ]);

        $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Packages',
            'slug' => 'plans',
            'route_name' => 'super-admin.plans.index',
            'scope' => PageMenu::SCOPE_PLATFORM,
            'icon' => 'fas fa-box',
            'parent_id' => $subscriptions->id,
        ]);

        $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Payments',
            'slug' => 'payments',
            'route_name' => 'super-admin.payments.index',
            'scope' => PageMenu::SCOPE_PLATFORM,
            'icon' => 'fas fa-money-bill-wave',
            'parent_id' => $subscriptions->id,
        ]);

        $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Menu Management',
            'slug' => 'menu-add',
            'route_name' => 'super-admin.menu.index',
            'scope' => PageMenu::SCOPE_PLATFORM,
            'icon' => 'fas fa-bars',
            'parent_id' => null,
        ]);

        $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'route_name' => 'school.dashboard',
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-home',
            'parent_id' => null,
        ]);
    }

    protected function upsertMenu(AccessMenuService $accessMenu, User $superAdmin, array $menuData): PageMenu
    {
        $existing = PageMenu::query()->where('slug', $menuData['slug'])->first();

        if ($existing) {
            $existing->update([
                'school_id' => null,
                'parent_id' => $menuData['parent_id'],
                'title' => $menuData['title'],
                'route_name' => $menuData['route_name'],
                'scope' => $menuData['scope'],
                'icon' => $menuData['icon'],
            ]);

            return $existing->fresh();
        }

        return $accessMenu->addMenu(null, $superAdmin, $menuData);
    }
}
