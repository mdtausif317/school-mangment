<?php

namespace App\Services;

use App\Models\PageMenu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class MenuPageService
{
    /** Slugs that map to existing hand-written pages (no auto file). */
    protected array $linkedSlugs = [
        PageMenu::SCOPE_PLATFORM => [
            'schools',
            'school-view',
            'create-school',
            'school-add',
            'menu-add',
            'dashboard',
        ],
        PageMenu::SCOPE_SCHOOL => [
            'dashboard',
            'users-view',
            'user-add',
            'users',
            'designations',
            'designation-add',
        ],
    ];

    /** URL paths already used in routes/web.php — cannot auto-register again. */
    protected array $reservedPaths = [
        'menu-add',
        'schools',
        'menu',
    ];

    public function usesAutoPage(PageMenu $menu): bool
    {
        $linked = $this->linkedSlugs[$menu->scope] ?? [];

        return ! in_array($menu->slug, $linked, true);
    }

    public function syncMenu(PageMenu $menu, ?string $oldSlug = null, ?string $oldScope = null): void
    {
        if (! $this->usesAutoPage($menu)) {
            $this->regenerateRoutes();

            return;
        }

        if ($this->pathIsReserved($menu->slug)) {
            throw new \InvalidArgumentException(
                "Slug \"{$menu->slug}\" is reserved. Please choose a different slug."
            );
        }

        if ($oldSlug && ($oldSlug !== $menu->slug || $oldScope !== $menu->scope)) {
            $this->renameBlade($oldSlug, $oldScope ?? $menu->scope, $menu);
        } else {
            $this->createBladeIfMissing($menu);
        }

        $this->regenerateRoutes();
    }

    public function createBladeIfMissing(PageMenu $menu): void
    {
        $path = $this->bladePath($menu->scope, $menu->slug);

        if (File::exists($path)) {
            return;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->buildStub($menu));
    }

    public function renameBlade(string $oldSlug, string $oldScope, PageMenu $menu): void
    {
        $oldPath = $this->bladePath($oldScope, $oldSlug);
        $newPath = $this->bladePath($menu->scope, $menu->slug);

        if ($oldPath !== $newPath && File::exists($oldPath)) {
            File::ensureDirectoryExists(dirname($newPath));

            if (File::exists($newPath)) {
                File::delete($oldPath);
            } else {
                File::move($oldPath, $newPath);
            }
        }

        $this->createBladeIfMissing($menu);
    }

    public function regenerateRoutes(): void
    {
        $menus = PageMenu::query()
            ->whereNull('school_id')
            ->orderBy('sort_order')
            ->get();

        $lines = [
            '<?php',
            '',
            '/**',
            ' * Auto-generated menu routes. Do not edit manually.',
            ' * Updated when menus are added or edited in Menu Management.',
            ' */',
            '',
            'use App\\Http\\Controllers\\School\\PageController as SchoolPageController;',
            'use App\\Http\\Controllers\\SuperAdmin\\PageController as SuperAdminPageController;',
            'use Illuminate\\Support\\Facades\\Route;',
            '',
        ];

        $platform = $menus->filter(fn (PageMenu $m) => $m->isPlatformMenu() && $this->usesAutoPage($m));
        $school = $menus->filter(fn (PageMenu $m) => $m->isSchoolMenu() && $this->usesAutoPage($m));

        if ($platform->isNotEmpty()) {
            $lines[] = "Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {";
            $lines = array_merge($lines, $this->routeLines($platform, 'super-admin'));
            $lines[] = '});';
            $lines[] = '';
        }

        if ($school->isNotEmpty()) {
            $lines[] = "Route::middleware(['auth', 'school_user'])->prefix('school')->name('school.')->group(function () {";
            $lines = array_merge($lines, $this->routeLines($school, 'school'));
            $lines[] = '});';
        }

        File::put(base_path('routes/menus.php'), implode(PHP_EOL, $lines).PHP_EOL);
    }

    protected function routeLines(Collection $menus, string $area): array
    {
        $controllerClass = $area === 'school'
            ? 'App\\Http\\Controllers\\School\\PageController'
            : 'App\\Http\\Controllers\\SuperAdmin\\PageController';

        $lines = [];

        foreach ($menus as $menu) {
            $slug = $menu->slug;
            $middleware = $area === 'school'
                ? "->middleware('page_access:{$slug}')"
                : '';

            $lines[] = "    Route::get('/{$slug}', [{$controllerClass}::class, 'show'])"
                ."->defaults('slug', '{$slug}')"
                .$middleware
                ."->name('{$slug}');";
        }

        return $lines;
    }

    protected function bladePath(string $scope, string $slug): string
    {
        $folder = $scope === PageMenu::SCOPE_SCHOOL ? 'school' : 'super-admin';

        return resource_path("views/{$folder}/{$slug}.blade.php");
    }

    protected function pathIsReserved(string $slug): bool
    {
        return in_array($slug, $this->reservedPaths, true);
    }

    protected function buildStub(PageMenu $menu): string
    {
        $layout = $menu->isSchoolMenu() ? 'layouts.school' : 'layouts.super-admin';
        $folder = $menu->isSchoolMenu() ? 'school' : 'super-admin';
        $title = str_replace("'", "\\'", $menu->title);
        $file = "{$folder}/{$menu->slug}.blade.php";

        return <<<BLADE
@extends('{$layout}')

@section('title', '{$title}')
@section('page-title', '{$title}')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">{$title}</h5>
        <p class="text-muted mb-0">
            Edit this page in <code>resources/views/{$file}</code>
        </p>
    </div>
</div>
@endsection

BLADE;
    }
}
