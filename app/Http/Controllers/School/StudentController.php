<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $school = auth()->user()->school;

        $students = Student::query()
            ->where('school_id', $school->id)
            ->with('schoolClass')
            ->orderBy('roll_no')
            ->get();

        return view('school.students-view', compact('students', 'school'));
    }

    public function create(): View
    {
        $school = auth()->user()->school;

        $classes = SchoolClass::query()
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('school.student-add', compact('classes', 'school'));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = auth()->user()->school;

        $validated = $request->validate([
            'class_id' => [
                'required',
                'integer',
                Rule::exists('school_classes', 'id')->where('school_id', $school->id),
            ],
            'roll_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'roll_no')->where('school_id', $school->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Student::create([
            'school_id' => $school->id,
            'class_id' => $validated['class_id'],
            'roll_no' => $validated['roll_no'],
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('school.students-view')
            ->with('success', 'Student added successfully.');
    }
}
