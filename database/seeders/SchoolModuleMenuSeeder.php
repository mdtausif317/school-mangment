<?php

namespace Database\Seeders;

use App\Models\PageMenu;
use App\Models\School;
use App\Models\SchoolMenuAccess;
use App\Models\User;
use App\Services\AccessMenuService;
use App\Services\MenuPageService;
use Illuminate\Database\Seeder;

class SchoolModuleMenuSeeder extends Seeder
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
        $maxOrder = (int) PageMenu::query()->whereNull('school_id')->max('sort_order');

        $students = PageMenu::query()->where('slug', 'students-view')->first();
        $parentStudents = $students?->parent_id
            ? PageMenu::query()->find($students->parent_id)
            : null;

        $reportsParent = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Reports',
            'slug' => '#',
            'route_name' => null,
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-chart-bar',
            'parent_id' => null,
            'sort_order' => ++$maxOrder,
        ]);

        $attendance = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Attendance',
            'slug' => 'attendance-manage',
            'route_name' => 'school.attendance-manage',
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-calendar-check',
            'parent_id' => null,
            'sort_order' => ++$maxOrder,
        ]);

        $fees = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Fee Collection',
            'slug' => 'fees-collect',
            'route_name' => 'school.fees-collect',
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-rupee-sign',
            'parent_id' => null,
            'sort_order' => ++$maxOrder,
        ]);

        $reportsHub = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Overview',
            'slug' => 'reports',
            'route_name' => 'school.reports',
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-tachometer-alt',
            'parent_id' => $reportsParent->id,
            'sort_order' => ++$maxOrder,
        ]);

        $reportsAttendance = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Attendance Report',
            'slug' => 'reports-attendance',
            'route_name' => 'school.reports-attendance',
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-user-check',
            'parent_id' => $reportsParent->id,
            'sort_order' => ++$maxOrder,
        ]);

        $reportsFees = $this->upsertMenu($accessMenu, $superAdmin, [
            'title' => 'Fee Revenue',
            'slug' => 'reports-fees',
            'route_name' => 'school.reports-fees',
            'scope' => PageMenu::SCOPE_SCHOOL,
            'icon' => 'fas fa-file-invoice-dollar',
            'parent_id' => $reportsParent->id,
            'sort_order' => ++$maxOrder,
        ]);

        $newMenuIds = collect([
            $attendance->id,
            $fees->id,
            $reportsParent->id,
            $reportsHub->id,
            $reportsAttendance->id,
            $reportsFees->id,
        ])->unique()->values()->all();

        $this->enableMenusForExistingSchools($newMenuIds);

        app(MenuPageService::class)->regenerateRoutes();
    }

    protected function enableMenusForExistingSchools(array $menuIds): void
    {
        School::query()
            ->where('portal_enabled', true)
            ->each(function (School $school) use ($menuIds) {
                $existing = SchoolMenuAccess::query()
                    ->where('school_id', $school->id)
                    ->pluck('menu_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                if ($existing === []) {
                    return;
                }

                foreach ($menuIds as $menuId) {
                    if (! in_array($menuId, $existing, true)) {
                        SchoolMenuAccess::create([
                            'school_id' => $school->id,
                            'menu_id' => $menuId,
                        ]);
                    }
                }

                $adminDesignation = $school->designations()->where('slug', 'admin')->first();
                if ($adminDesignation) {
                    app(AccessMenuService::class)->grantAdminAllSchoolMenus(
                        $school->id,
                        $adminDesignation->id
                    );
                }
            });
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
                'sort_order' => $menuData['sort_order'] ?? $existing->sort_order,
            ]);

            return $existing->fresh();
        }

        return $accessMenu->addMenu(null, $superAdmin, $menuData);
    }
}
