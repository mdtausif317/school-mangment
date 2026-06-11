<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $school = auth()->user()->school;

        $classes = SchoolClass::query()
            ->where('school_id', $school->id)
            ->withCount('students')
            ->orderBy('name')
            ->orderBy('section')
            ->get();

        return view('school.classes-view', compact('classes', 'school'));
    }

    public function create(): View
    {
        return view('school.class-add', [
            'school' => auth()->user()->school,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $school = auth()->user()->school;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = SchoolClass::query()
            ->where('school_id', $school->id)
            ->where('name', $validated['name'])
            ->where('section', $validated['section'] ?? null)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'This class and section already exists.');
        }

        SchoolClass::create([
            'school_id' => $school->id,
            'name' => $validated['name'],
            'section' => $validated['section'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('school.classes-view')
            ->with('success', 'Class created successfully.');
    }
}
