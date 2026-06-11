<?php

namespace App\Services;

use App\Models\PageAuth;
use App\Models\PageButton;
use App\Models\PageButtonAuth;
use App\Models\PageMenu;
use App\Models\School;
use App\Models\SchoolMenuAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AccessMenuService
{
    public function __construct(
        protected MenuPageService $menuPages
    ) {}

    public function getParentMenus(?int $schoolId = null): Collection
    {
        return $this->menuQuery($schoolId)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function getAllGlobalMenus(): Collection
    {
        return $this->menuQuery(null)->orderBy('title')->get();
    }

    public function getAllMenusWithDisplay(?int $schoolId = null): Collection
    {
        $menus = $this->menuQuery($schoolId)
            ->with('buttons')
            ->orderBy('sort_order')
            ->get();

        return $this->buildMenuTreeWithDisplay($menus);
    }

    public function buildMenuTreeWithDisplay(Collection $menus, ?int $parentId = null): Collection
    {
        return $menus
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (PageMenu $menu) use ($menus) {
                $menu->setRelation('children', $this->buildMenuTreeWithDisplay($menus, $menu->id));

                return $menu;
            });
    }

    public function getMenuButtons(int $menuId): Collection
    {
        return PageButton::query()
            ->where('menu_id', $menuId)
            ->orderBy('id')
            ->get();
    }

    public function getSuperAdminSidebarMenu(): Collection
    {
        $menus = PageMenu::query()
            ->whereNull('school_id')
            ->where('scope', PageMenu::SCOPE_PLATFORM)
            ->where('display', false)
            ->orderBy('sort_order')
            ->get();

        return $this->buildMenuTreeWithDisplay($menus);
    }

    public function resolveMenuUrl(PageMenu $menu): string
    {
        if ($menu->route_name && Route::has($menu->route_name)) {
            return route($menu->route_name);
        }

        return '#';
    }

    public function isMenuActive(PageMenu $menu): bool
    {
        if (! $menu->route_name) {
            return false;
        }

        return request()->routeIs($menu->route_name)
            || request()->routeIs($menu->route_name.'.*');
    }

    /** @deprecated Use resolveMenuUrl() */
    public function resolveSuperAdminMenuUrl(PageMenu $menu): string
    {
        return $this->resolveMenuUrl($menu);
    }

    /** @deprecated Use isMenuActive() */
    public function isSuperAdminMenuActive(PageMenu $menu): bool
    {
        return $this->isMenuActive($menu);
    }

    public function getSidebarMenu(User $user): Collection
    {
        $menuIds = $this->getAuthorizedMenuIds($user);

        $menus = PageMenu::query()
            ->whereNull('school_id')
            ->where('scope', PageMenu::SCOPE_SCHOOL)
            ->where('display', false)
            ->whereIn('id', $menuIds)
            ->orderBy('sort_order')
            ->get();

        return $this->buildMenuTreeWithDisplay($menus);
    }

    public function userHasPageAccess(User $user, string $slug): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $menu = PageMenu::query()
            ->whereNull('school_id')
            ->where('scope', PageMenu::SCOPE_SCHOOL)
            ->where('slug', $slug)
            ->first();

        if (! $menu) {
            return false;
        }

        return $this->getAuthorizedMenuIds($user)->contains($menu->id);
    }

    public function userHasButtonAccess(User $user, string $buttonLink): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $button = PageButton::query()
            ->where('button_link', $buttonLink)
            ->whereHas('menu', fn ($q) => $q->whereNull('school_id'))
            ->first();

        if (! $button) {
            return false;
        }

        return PageButtonAuth::query()
            ->where('button_id', $button->id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->designation_id) {
                    $query->orWhere('designation_id', $user->designation_id);
                }
            })
            ->exists();
    }

    public function getMenuRouteOptions(): array
    {
        $excluded = ['login', 'logout', 'super-admin.login'];

        $options = [
            PageMenu::SCOPE_PLATFORM => [],
            PageMenu::SCOPE_SCHOOL => [],
        ];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            if (! $name || in_array($name, $excluded, true)) {
                continue;
            }

            if (! in_array('GET', $route->methods(), true) || $route->parameterNames()) {
                continue;
            }

            if (str_starts_with($name, 'super-admin.')) {
                $options[PageMenu::SCOPE_PLATFORM][$name] = $name;
            } elseif (str_starts_with($name, 'school.')) {
                $options[PageMenu::SCOPE_SCHOOL][$name] = $name;
            }
        }

        ksort($options[PageMenu::SCOPE_PLATFORM]);
        ksort($options[PageMenu::SCOPE_SCHOOL]);

        return $options;
    }

    public function suggestRouteName(string $slug, string $scope): ?string
    {
        $normalized = Str::slug($slug);
        $prefix = $scope === PageMenu::SCOPE_SCHOOL ? 'school.' : 'super-admin.';

        $candidates = array_unique([
            $prefix.$normalized,
            $prefix.str_replace('-', '.', $normalized),
        ]);

        $aliases = [
            PageMenu::SCOPE_PLATFORM => [
                'schools' => 'super-admin.school-view',
                'school-view' => 'super-admin.school-view',
                'create-school' => 'super-admin.schools.create',
                'school-add' => 'super-admin.schools.create',
                'menu-add' => 'super-admin.menu.index',
                'dashboard' => 'super-admin.dashboard',
                'super-dashboard' => 'super-admin.dashboard',
            ],
            PageMenu::SCOPE_SCHOOL => [
                'dashboard' => 'school.dashboard',
            ],
        ];

        if (isset($aliases[$scope][$normalized])) {
            array_unshift($candidates, $aliases[$scope][$normalized]);
        }

        foreach ($candidates as $name) {
            if (! Route::has($name)) {
                continue;
            }

            $route = Route::getRoutes()->getByName($name);

            if ($route && empty($route->parameterNames())) {
                return $name;
            }
        }

        return $prefix.$normalized;
    }

    public function resolveMenuDefaults(array $data, ?PageMenu $existing = null): array
    {
        if (! empty($data['page_folder'])) {
            $data['scope'] = $data['page_folder'] === 'school'
                ? PageMenu::SCOPE_SCHOOL
                : PageMenu::SCOPE_PLATFORM;
        }

        if (! empty($data['parent_id'])) {
            $parent = PageMenu::query()->find($data['parent_id']);

            if ($parent) {
                $data['scope'] = $parent->scope;
            }
        } elseif (empty($data['scope'])) {
            if ($existing) {
                $data['scope'] = $existing->scope;
            } else {
                $data['scope'] = PageMenu::SCOPE_PLATFORM;
            }
        }

        if (! empty($data['slug']) && ! $this->isGroupMenuSlug((string) $data['slug'])) {
            $data['route_name'] = $this->suggestRouteName($data['slug'], $data['scope']);
        } elseif ($this->isGroupMenuSlug((string) ($data['slug'] ?? ''))) {
            $data['route_name'] = null;
        }

        return $data;
    }

    public function addMenu(?int $schoolId, User $creator, array $data): PageMenu
    {
        $data = $this->resolveMenuDefaults($data);
        $slug = $this->normalizeMenuSlug($data['slug'] ?? '', $data['title']);

        $this->assertSlugAvailable($schoolId, $slug);

        $parentId = ! empty($data['parent_id']) ? $data['parent_id'] : null;

        $sortOrder = $this->menuQuery($schoolId)
            ->where('parent_id', $parentId)
            ->max('sort_order') + 1;

        $menu = PageMenu::create([
            'school_id' => $schoolId,
            'parent_id' => $parentId,
            'title' => $data['title'],
            'slug' => $slug,
            'route_name' => $data['route_name'] ?? null,
            'scope' => $data['scope'] ?? PageMenu::SCOPE_PLATFORM,
            'icon' => $data['icon'] ?? 'fas fa-circle',
            'sort_order' => $sortOrder,
            'display' => (bool) ($data['display_in_menu'] ?? false),
        ]);

        if ($schoolId !== null) {
            $this->addUserPageAccess($schoolId, $menu->id, $creator->id);
        }

        $this->menuPages->syncMenu($menu);

        return $menu;
    }

    public function updateMenuDisplay(int $menuId, bool $hidden): void
    {
        PageMenu::query()->where('id', $menuId)->update(['display' => $hidden]);
    }

    public function updateMenu(int $menuId, array $data): PageMenu
    {
        $menu = PageMenu::query()->whereNull('school_id')->findOrFail($menuId);
        $data = $this->resolveMenuDefaults($data, $menu);
        $slug = $this->normalizeMenuSlug($data['slug'] ?? '', $data['title']);

        $this->assertSlugAvailable(null, $slug, $menuId);

        $parentId = ! empty($data['parent_id']) ? (int) $data['parent_id'] : null;

        if ($parentId === $menuId) {
            throw new \InvalidArgumentException('A menu cannot be its own parent.');
        }

        if ($parentId && $this->isMenuDescendantOf($menuId, $parentId)) {
            throw new \InvalidArgumentException('Cannot move a menu under its own child.');
        }

        $oldSlug = $menu->slug;
        $oldScope = $menu->scope;

        $menu->update([
            'parent_id' => $parentId,
            'title' => $data['title'],
            'slug' => $slug,
            'route_name' => $data['route_name'] ?? null,
            'scope' => $data['scope'] ?? $menu->scope,
            'icon' => $data['icon'] ?? 'fas fa-circle',
            'display' => (bool) ($data['display_in_menu'] ?? false),
        ]);

        $menu = $menu->fresh();
        $this->menuPages->syncMenu($menu, $oldSlug, $oldScope);

        return $menu;
    }

    public function reorderMenus(?int $schoolId, array $items): void
    {
        foreach ($items as $item) {
            $query = PageMenu::query()->where('id', (int) $item['id']);

            if ($schoolId === null) {
                $query->whereNull('school_id');
            } else {
                $query->where('school_id', $schoolId);
            }

            $query->update([
                'parent_id' => $item['parent_id'] ?? null,
                'sort_order' => (int) $item['sort_order'],
            ]);
        }
    }

    protected function isMenuDescendantOf(int $ancestorId, int $possibleDescendantId): bool
    {
        $current = PageMenu::query()->find($possibleDescendantId);

        while ($current) {
            if ((int) $current->parent_id === $ancestorId) {
                return true;
            }

            $current = $current->parent_id
                ? PageMenu::query()->find($current->parent_id)
                : null;
        }

        return false;
    }

    public function addButton(int $menuId, User $creator, array $data): PageButton
    {
        $button = PageButton::create([
            'menu_id' => $menuId,
            'button_title' => $data['button_title'],
            'button_link' => Str::slug($data['button_link']),
            'status' => (bool) ($data['button_status'] ?? false),
        ]);

        PageButtonAuth::create([
            'button_id' => $button->id,
            'user_id' => $creator->id,
        ]);

        return $button;
    }

    public function deleteButton(int $buttonId): void
    {
        PageButtonAuth::query()->where('button_id', $buttonId)->delete();
        PageButton::query()->where('id', $buttonId)->delete();
    }

    public function addUserPageAccess(int $schoolId, int $menuId, int $userId): void
    {
        PageAuth::firstOrCreate([
            'school_id' => $schoolId,
            'menu_id' => $menuId,
            'user_id' => $userId,
        ]);
    }

    public function addDesignationPageAccess(int $schoolId, int $menuId, int $designationId): void
    {
        PageAuth::firstOrCreate([
            'school_id' => $schoolId,
            'menu_id' => $menuId,
            'designation_id' => $designationId,
        ]);
    }

    public function getAllSchoolMenus(): Collection
    {
        return PageMenu::query()
            ->whereNull('school_id')
            ->where('scope', PageMenu::SCOPE_SCHOOL)
            ->orderBy('sort_order')
            ->get();
    }

    public function getSchoolMenuTree(?int $schoolId = null): Collection
    {
        $menus = $schoolId === null
            ? $this->getAllSchoolMenus()
            : $this->getSchoolAssignableMenus($schoolId);

        return $this->buildMenuTreeWithDisplay($menus);
    }

    public function getSchoolAssignableMenus(?int $schoolId = null): Collection
    {
        $menus = $this->getAllSchoolMenus();

        if ($schoolId === null) {
            return $menus;
        }

        $enabledIds = $this->getSchoolEnabledMenuIds($schoolId);

        return $menus->whereIn('id', $enabledIds)->values();
    }

    public function getSchoolEnabledMenuIds(int $schoolId): array
    {
        $ids = SchoolMenuAccess::query()
            ->where('school_id', $schoolId)
            ->pluck('menu_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($ids !== []) {
            return $ids;
        }

        $school = School::query()->find($schoolId);
        if ($school?->portal_enabled) {
            return $this->getAllSchoolMenus()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return [];
    }

    public function syncSchoolEnabledMenus(int $schoolId, array $menuIds): void
    {
        $menuIds = array_unique(array_map('intval', $menuIds));

        SchoolMenuAccess::query()->where('school_id', $schoolId)->delete();

        foreach ($menuIds as $menuId) {
            if ($menuId > 0) {
                SchoolMenuAccess::create([
                    'school_id' => $schoolId,
                    'menu_id' => $menuId,
                ]);
            }
        }

        PageAuth::query()
            ->where('school_id', $schoolId)
            ->whereNotIn('menu_id', $menuIds)
            ->delete();
    }

    public function getSchoolDesignationMenuAccess(int $schoolId): array
    {
        return PageAuth::query()
            ->where('school_id', $schoolId)
            ->whereNotNull('designation_id')
            ->get()
            ->groupBy('menu_id')
            ->map(fn ($rows) => $rows->pluck('designation_id')->map(fn ($id) => (int) $id)->all())
            ->all();
    }

    public function syncDesignationMenuAccess(int $schoolId, int $designationId, array $menuIds): void
    {
        PageAuth::query()
            ->where('school_id', $schoolId)
            ->where('designation_id', $designationId)
            ->delete();

        foreach (array_unique(array_map('intval', $menuIds)) as $menuId) {
            if ($menuId > 0) {
                PageAuth::create([
                    'school_id' => $schoolId,
                    'menu_id' => $menuId,
                    'designation_id' => $designationId,
                ]);
            }
        }
    }

    public function syncUserMenuAccess(int $schoolId, int $userId, array $menuIds): void
    {
        PageAuth::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $userId)
            ->delete();

        foreach (array_unique(array_map('intval', $menuIds)) as $menuId) {
            if ($menuId > 0) {
                PageAuth::create([
                    'school_id' => $schoolId,
                    'menu_id' => $menuId,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    public function getDesignationMenuIds(int $schoolId, int $designationId): array
    {
        return PageAuth::query()
            ->where('school_id', $schoolId)
            ->where('designation_id', $designationId)
            ->pluck('menu_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function getUserMenuIds(int $schoolId, int $userId): array
    {
        return PageAuth::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $userId)
            ->pluck('menu_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function grantAdminAllSchoolMenus(int $schoolId, int $adminDesignationId): void
    {
        $menuIds = $this->getSchoolEnabledMenuIds($schoolId);
        $this->syncDesignationMenuAccess($schoolId, $adminDesignationId, $menuIds);
    }

    public function syncSchoolDesignationAccess(int $schoolId, array $menuAccess): void
    {
        PageAuth::query()
            ->where('school_id', $schoolId)
            ->whereNotNull('designation_id')
            ->delete();

        foreach ($menuAccess as $menuId => $designationIds) {
            if (empty($designationIds)) {
                continue;
            }

            foreach (array_unique($designationIds) as $designationId) {
                PageAuth::create([
                    'school_id' => $schoolId,
                    'menu_id' => (int) $menuId,
                    'designation_id' => (int) $designationId,
                ]);
            }
        }
    }

    public function grantAccessByDesignationSlugs(School $school, array $designationsBySlug, array $menuAccessBySlug): void
    {
        foreach ($menuAccessBySlug as $menuId => $slugList) {
            if (empty($slugList)) {
                continue;
            }

            foreach (array_unique($slugList) as $slug) {
                if (! isset($designationsBySlug[$slug])) {
                    continue;
                }

                $this->addDesignationPageAccess(
                    $school->id,
                    (int) $menuId,
                    $designationsBySlug[$slug]->id
                );
            }
        }
    }

    public function defaultDesignationLabels(): array
    {
        return [
            'admin' => 'Admin',
            'principal' => 'Principal',
            'teacher' => 'Teacher',
            'student' => 'Student',
        ];
    }

    public function getUserEffectiveMenuIds(User $user): array
    {
        return $this->getAuthorizedMenuIds($user)->all();
    }

    protected function getAuthorizedMenuIds(User $user): Collection
    {
        return PageAuth::query()
            ->where('school_id', $user->school_id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->designation_id) {
                    $query->orWhere('designation_id', $user->designation_id);
                }
            })
            ->pluck('menu_id')
            ->unique();
    }

    protected function menuQuery(?int $schoolId): Builder
    {
        return PageMenu::query()->when(
            $schoolId === null,
            fn ($q) => $q->whereNull('school_id'),
            fn ($q) => $q->where('school_id', $schoolId)
        );
    }

    protected function normalizeMenuSlug(string $slug, string $title): string
    {
        $trimmed = trim($slug);

        if ($trimmed === '#') {
            return '#';
        }

        return Str::slug($trimmed !== '' ? $trimmed : $title);
    }

    protected function isGroupMenuSlug(string $slug): bool
    {
        return trim($slug) === '#';
    }

    protected function assertSlugAvailable(?int $schoolId, string $slug, ?int $ignoreMenuId = null): void
    {
        if ($this->isGroupMenuSlug($slug)) {
            return;
        }

        $query = $this->menuQuery($schoolId)->where('slug', $slug);

        if ($ignoreMenuId) {
            $query->where('id', '!=', $ignoreMenuId);
        }

        if ($query->exists()) {
            throw new \InvalidArgumentException('A menu with this slug already exists.');
        }
    }
}
