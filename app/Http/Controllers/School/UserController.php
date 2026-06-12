<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\UniqueSchoolUserEmail;
use App\Services\AccessMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function index(): View
    {
        $school = auth()->user()->school;

        $users = User::query()
            ->where('school_id', $school->id)
            ->with('designation')
            ->orderBy('name')
            ->get();

        return view('school.users-view', compact('users', 'school'));
    }

    public function create(): View
    {
        $school = auth()->user()->school;
        $designations = $school->designations()->orderBy('name')->get();

        return view('school.user-add', compact('designations', 'school'));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = auth()->user()->school;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', UniqueSchoolUserEmail::for($school->id)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'designation_id' => [
                'required',
                'integer',
                Rule::exists('designations', 'id')->where('school_id', $school->id),
            ],
            'user_type' => ['required', 'in:staff,teacher,student'],
        ]);

        $designation = $school->designations()->findOrFail($validated['designation_id']);

        User::create([
            'school_id' => $school->id,
            'designation_id' => $designation->id,
            'user_type' => $validated['user_type'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('school.users-view')
            ->with('success', 'User created. Access comes from their designation automatically.');
    }

    public function access(User $user): View
    {
        $this->ensureSameSchool($user);

        $school = auth()->user()->school;
        $menus = $this->accessMenu->getSchoolMenuTree($school->id);
        $selected = $this->accessMenu->getUserEffectiveMenuIds($user);

        return view('school.user-access', compact('user', 'menus', 'selected', 'school'));
    }

    public function updateAccess(Request $request, User $user): RedirectResponse
    {
        $this->ensureSameSchool($user);

        $validated = $request->validate([
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:pages_menu_list,id'],
        ]);

        $this->accessMenu->syncUserMenuAccess(
            $user->school_id,
            $user->id,
            $validated['menu_ids'] ?? []
        );

        return redirect()
            ->route('school.users-view')
            ->with('success', "Access updated for {$user->name}.");
    }

    protected function ensureSameSchool(User $user): void
    {
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403);
        }
    }
}
