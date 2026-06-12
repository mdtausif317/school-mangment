<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $school = auth()->user()->school;
        $classes = $this->activeClasses($school->id);

        $classId = (int) $request->query('class_id', 0);
        $date = $request->query('date', now()->toDateString());

        $students = collect();
        $attendance = [];

        if ($classId > 0) {
            $students = Student::query()
                ->where('school_id', $school->id)
                ->where('class_id', $classId)
                ->where('is_active', true)
                ->orderBy('roll_no')
                ->get();

            $attendance = StudentAttendance::query()
                ->where('school_id', $school->id)
                ->where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->pluck('status', 'student_id')
                ->all();
        }

        return view('school.attendance-manage', compact(
            'school',
            'classes',
            'classId',
            'date',
            'students',
            'attendance'
        ));
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
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', Rule::in(array_keys(StudentAttendance::statusOptions()))],
        ]);

        $studentIds = Student::query()
            ->where('school_id', $school->id)
            ->where('class_id', $validated['class_id'])
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $allowed = array_flip($studentIds);
        $markedBy = auth()->id();

        DB::transaction(function () use ($validated, $school, $allowed, $markedBy) {
            foreach ($validated['attendance'] as $studentId => $status) {
                if (! isset($allowed[(int) $studentId])) {
                    continue;
                }

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => (int) $studentId,
                        'date' => $validated['date'],
                    ],
                    [
                        'school_id' => $school->id,
                        'class_id' => $validated['class_id'],
                        'status' => $status,
                        'marked_by' => $markedBy,
                    ]
                );
            }
        });

        return redirect()
            ->route('school.attendance-manage', [
                'class_id' => $validated['class_id'],
                'date' => $validated['date'],
            ])
            ->with('success', 'Attendance saved successfully.');
    }

    protected function activeClasses(int $schoolId)
    {
        return SchoolClass::query()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
