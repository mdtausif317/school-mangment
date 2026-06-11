<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\AccessMenuService;
use App\Services\SchoolService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function __construct(
        protected SchoolService $schoolService,
        protected AccessMenuService $accessMenu
    ) {}

    public function create(): View
    {
        return view('super-admin.create-school', [
            'menus' => $this->accessMenu->getSchoolAssignableMenus(),
            'designationLabels' => $this->accessMenu->defaultDesignationLabels(),
            'useDesignationSlugs' => true,
            'currentAccess' => $this->defaultCreateAccess(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:schools,slug'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
            'menu_access' => ['nullable', 'array'],
            'menu_access.*' => ['nullable', 'array'],
            'menu_access.*.*' => ['string', 'in:admin,principal,teacher,student'],
        ]);

        $school = $this->schoolService->createSchool(
            collect($validated)->only(['name', 'slug', 'email', 'phone', 'address'])->all(),
            [
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $validated['admin_password'],
            ],
            $validated['menu_access'] ?? []
        );

        return redirect()
            ->route('super-admin.dashboard')
            ->with('success', "School \"{$school->name}\" created with admin account and menu access.");
    }

    public function access(School $school): View
    {
        $school->load('designations');

        return view('super-admin.school-access', [
            'school' => $school,
            'menus' => $this->accessMenu->getSchoolAssignableMenus(),
            'designations' => $school->designations->sortBy('name'),
            'currentAccess' => $this->accessMenu->getSchoolDesignationMenuAccess($school->id),
            'useDesignationSlugs' => false,
        ]);
    }

    public function updateAccess(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'menu_access' => ['nullable', 'array'],
            'menu_access.*' => ['nullable', 'array'],
            'menu_access.*.*' => ['integer', 'exists:designations,id'],
        ]);

        $designationIds = $school->designations()->pluck('id')->all();
        $menuAccess = [];

        foreach ($validated['menu_access'] ?? [] as $menuId => $ids) {
            $menuAccess[$menuId] = array_values(array_intersect(
                array_map('intval', $ids ?? []),
                $designationIds
            ));
        }

        $this->schoolService->updateSchoolAccess($school, $menuAccess);

        return redirect()
            ->route('super-admin.schools.access', $school)
            ->with('success', "Menu access updated for \"{$school->name}\".");
    }

    protected function defaultCreateAccess(): array
    {
        $access = [];

        $this->accessMenu->getSchoolAssignableMenus()
            ->where('slug', 'dashboard')
            ->each(function ($menu) use (&$access) {
                $access[$menu->id] = ['admin'];
            });

        return $access;
    }
}
