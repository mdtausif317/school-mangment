<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\IdCardService;
use App\Services\StudentPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
  public function __construct(
    protected StudentPhotoService $photos,
    protected IdCardService $idCards
  ) {}

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

    $classes = $this->activeClasses($school->id);

    return view('school.student-add', compact('classes', 'school'));
  }

  public function store(Request $request): RedirectResponse
  {
    $school = auth()->user()->school;
    $validated = $this->validateStudent($request, $school->id);

    $photoPath = null;
    if ($request->hasFile('photo')) {
      $photoPath = $this->photos->store($request->file('photo'), $school->id);
    }

    Student::create([
      'school_id' => $school->id,
      'class_id' => $validated['class_id'],
      'roll_no' => $validated['roll_no'],
      'name' => $validated['name'],
      'photo' => $photoPath,
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

  public function edit(Student $student): View
  {
    $this->ensureSameSchool($student);

    $school = auth()->user()->school;
    $classes = $this->activeClasses($school->id);

    return view('school.student-edit', compact('student', 'classes', 'school'));
  }

  public function update(Request $request, Student $student): RedirectResponse
  {
    $this->ensureSameSchool($student);

    $school = auth()->user()->school;
    $validated = $this->validateStudent($request, $school->id, $student->id);

    $photoPath = $student->photo;
    if ($request->hasFile('photo')) {
      $this->photos->delete($student->photo);
      $photoPath = $this->photos->store($request->file('photo'), $school->id);
    }

    $student->update([
      'class_id' => $validated['class_id'],
      'roll_no' => $validated['roll_no'],
      'name' => $validated['name'],
      'photo' => $photoPath,
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
      ->with('success', 'Student updated successfully.');
  }

  public function card(Student $student): View
  {
    $this->ensureSameSchool($student);

    $student->load(['schoolClass', 'school.idCardSettings']);
    $school = $student->school;
    $settings = $this->idCards->settingsFor($school);

    return view('school.id-cards.print', [
      'student' => $student,
      'school' => $school,
      'settings' => $settings,
      'barcodeValue' => $student->barcodeValue(),
      'cardView' => $this->idCards->cardViewName($settings),
    ]);
  }

  protected function validateStudent(Request $request, int $schoolId, ?int $studentId = null): array
  {
    $rollRule = Rule::unique('students', 'roll_no')
      ->where('school_id', $schoolId);

    if ($studentId) {
      $rollRule->ignore($studentId);
    }

    return $request->validate([
      'class_id' => [
        'required',
        'integer',
        Rule::exists('school_classes', 'id')->where('school_id', $schoolId),
      ],
      'roll_no' => ['required', 'string', 'max:50', $rollRule],
      'name' => ['required', 'string', 'max:255'],
      'photo' => [
        'nullable',
        'image',
        'mimes:jpeg,jpg,png,webp',
        'max:2048',
        'dimensions:min_width=150,min_height=150',
      ],
      'email' => ['nullable', 'email', 'max:255'],
      'phone' => ['nullable', 'string', 'max:50'],
      'gender' => ['nullable', 'in:male,female,other'],
      'date_of_birth' => ['nullable', 'date'],
      'guardian_name' => ['nullable', 'string', 'max:255'],
      'address' => ['nullable', 'string'],
      'is_active' => ['nullable', 'boolean'],
    ]);
  }

  protected function activeClasses(int $schoolId)
  {
    return SchoolClass::query()
      ->where('school_id', $schoolId)
      ->where('is_active', true)
      ->orderBy('name')
      ->get();
  }

  protected function ensureSameSchool(Student $student): void
  {
    if ($student->school_id !== auth()->user()->school_id) {
      abort(403);
    }
  }
}
