<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Services\AccessMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignationController extends Controller
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function index(): View
    {
        $school = auth()->user()->school;
        $designations = $school->designations()->withCount('users')->orderBy('name')->get();

        return view('school.designations', compact('designations', 'school'));
    }

    public function create(): View
    {
        $school = auth()->user()->school;
        $menus = $this->accessMenu->getSchoolAssignableMenus();

        return view('school.designation-create', compact('menus', 'school'));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = auth()->user()->school;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:pages_menu_list,id'],
        ]);

        $designation = Designation::create([
            'school_id' => $school->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        $this->accessMenu->syncDesignationMenuAccess(
            $school->id,
            $designation->id,
            $validated['menu_ids'] ?? []
        );

        return redirect()
            ->route('school.designations.index')
            ->with('success', "Designation \"{$designation->name}\" created with page access.");
    }
}
