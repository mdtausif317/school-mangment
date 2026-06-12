<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Rules\UniqueSchoolUserEmail;
use App\Services\IdCardService;
use App\Services\StudentAccountService;
use App\Services\StudentPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
  public function __construct(
    protected StudentPhotoService $photos,
    protected IdCardService $idCards,
    protected StudentAccountService $accounts
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

    $createLogin = $request->boolean('create_portal_login', true);

    $student = DB::transaction(function () use ($request, $school, $validated, $photoPath, $createLogin) {
      $student = Student::create([
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

      if ($createLogin) {
        $this->accounts->createForStudent(
          $student,
          $request->input('portal_password') ?: null
        );
      }

      return $student;
    });

    $message = 'Student added successfully.';
    if ($createLogin && $student->email) {
      $passwordHint = $request->filled('portal_password')
        ? 'the password you set'
        : "roll number ({$student->roll_no})";
      $message .= " Portal login created — email: {$student->email}, password: {$passwordHint}.";
    }

    return redirect()
      ->route('school.students-view')
      ->with('success', $message);
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

    DB::transaction(function () use ($request, $student, $validated, $photoPath) {
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

      if ($request->boolean('create_portal_login')) {
        $this->accounts->syncForStudent(
          $student->fresh(),
          $request->input('portal_password') ?: null
        );
      } elseif ($student->user) {
        $this->accounts->deactivateForStudent($student->fresh());
      }
    });

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

    return view('school.id-cards.print', $this->idCards->buildCardRenderData($student, $school, $settings));
  }

  protected function validateStudent(Request $request, int $schoolId, ?int $studentId = null): array
  {
    $rollRule = Rule::unique('students', 'roll_no')
      ->where('school_id', $schoolId);

    if ($studentId) {
      $rollRule->ignore($studentId);
    }

    $existingUserId = $studentId
      ? Student::query()->whereKey($studentId)->value('user_id')
      : null;

    $emailRules = ['nullable', 'email', 'max:255'];

    if ($request->boolean('create_portal_login')) {
      $emailRules[] = 'required';
      $emailRules[] = UniqueSchoolUserEmail::for($schoolId, $existingUserId);
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
      'email' => $emailRules,
      'phone' => ['nullable', 'string', 'max:50'],
      'gender' => ['nullable', 'in:male,female,other'],
      'date_of_birth' => ['nullable', 'date'],
      'guardian_name' => ['nullable', 'string', 'max:255'],
      'address' => ['nullable', 'string'],
      'is_active' => ['nullable', 'boolean'],
      'create_portal_login' => ['nullable', 'boolean'],
      'portal_password' => ['nullable', 'string', 'min:6', 'max:255'],
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
