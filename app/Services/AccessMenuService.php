<?php

namespace App\Services;

use App\Models\PageAuth;
use App\Models\PageButton;
use App\Models\PageButtonAuth;
use App\Models\PageMenu;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AccessMenuService
{
    public function getParentMenus(?int $schoolId = null): Collection
    {
        return $this->menuQuery($schoolId)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
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

    public function getSidebarMenu(User $user): Collection
    {
        $menuIds = $this->getAuthorizedMenuIds($user);

        $menus = $this->menuQuery(null)
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

        $menu = $this->menuQuery(null)->where('slug', $slug)->first();

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

    public function addMenu(?int $schoolId, User $creator, array $data): PageMenu
    {
        $slug = Str::slug($data['slug'] ?? $data['title']);

        if ($this->menuQuery($schoolId)->where('slug', $slug)->exists()) {
            throw new \InvalidArgumentException('A menu with this slug already exists.');
        }

        $parentId = ! empty($data['parent_id']) ? $data['parent_id'] : null;

        $sortOrder = $this->menuQuery($schoolId)
            ->where('parent_id', $parentId)
            ->max('sort_order') + 1;

        $menu = PageMenu::create([
            'school_id' => $schoolId,
            'parent_id' => $parentId,
            'title' => $data['title'],
            'slug' => $slug,
            'icon' => $data['icon'] ?? 'fas fa-circle',
            'sort_order' => $sortOrder,
            'display' => (bool) ($data['display_in_menu'] ?? false),
        ]);

        if ($schoolId !== null) {
            $this->addUserPageAccess($schoolId, $menu->id, $creator->id);
        }

        return $menu;
    }

    public function updateMenuDisplay(int $menuId, bool $hidden): void
    {
        PageMenu::query()->where('id', $menuId)->update(['display' => $hidden]);
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
}
