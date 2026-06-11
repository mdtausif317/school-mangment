<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SchoolService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function __construct(
        protected SchoolService $schoolService
    ) {}

    public function create(): View
    {
        return view('super-admin.create-school');
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
            'portal_enabled' => ['nullable', 'boolean'],
        ]);

        $school = $this->schoolService->createSchool(
            collect($validated)->only(['name', 'slug', 'email', 'phone', 'address'])->all(),
            [
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $validated['admin_password'],
            ],
            $request->boolean('portal_enabled')
        );

        return redirect()
            ->route('super-admin.dashboard')
            ->with('success', "School \"{$school->name}\" created successfully.");
    }

    public function access(School $school): View
    {
        return view('super-admin.school-access', compact('school'));
    }

    public function updateAccess(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'portal_enabled' => ['nullable', 'boolean'],
        ]);

        $this->schoolService->setPortalAccess($school, $request->boolean('portal_enabled'));

        return redirect()
            ->route('super-admin.schools.access', $school)
            ->with('success', "Portal access updated for \"{$school->name}\".");
    }
}
