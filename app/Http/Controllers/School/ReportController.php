<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $school = auth()->user()->school;
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $stats = [
            'students' => Student::where('school_id', $school->id)->where('is_active', true)->count(),
            'present_today' => StudentAttendance::where('school_id', $school->id)
                ->where('date', $today)
                ->where('status', StudentAttendance::STATUS_PRESENT)
                ->count(),
            'absent_today' => StudentAttendance::where('school_id', $school->id)
                ->where('date', $today)
                ->where('status', StudentAttendance::STATUS_ABSENT)
                ->count(),
            'fee_month' => FeePayment::where('school_id', $school->id)
                ->where('paid_on', '>=', $monthStart)
                ->sum('amount'),
            'fee_total' => FeePayment::where('school_id', $school->id)->sum('amount'),
        ];

        return view('school.reports', compact('school', 'stats'));
    }

    public function attendance(Request $request): View
    {
        $school = auth()->user()->school;
        $classes = SchoolClass::query()
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->toDateString());
        $classId = (int) $request->query('class_id', 0);

        $rows = collect();
        $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];

        if ($classId > 0) {
            $students = Student::query()
                ->where('school_id', $school->id)
                ->where('class_id', $classId)
                ->orderBy('roll_no')
                ->get();

            $records = StudentAttendance::query()
                ->where('school_id', $school->id)
                ->where('class_id', $classId)
                ->whereBetween('date', [$from, $to])
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $studentRecords = $records->get($student->id, collect());
                $counts = [
                    'present' => $studentRecords->where('status', StudentAttendance::STATUS_PRESENT)->count(),
                    'absent' => $studentRecords->where('status', StudentAttendance::STATUS_ABSENT)->count(),
                    'late' => $studentRecords->where('status', StudentAttendance::STATUS_LATE)->count(),
                    'leave' => $studentRecords->where('status', StudentAttendance::STATUS_LEAVE)->count(),
                ];

                foreach ($counts as $key => $value) {
                    $summary[$key] += $value;
                }

                $rows->push([
                    'student' => $student,
                    'counts' => $counts,
                    'total_days' => array_sum($counts),
                ]);
            }
        }

        return view('school.reports-attendance', compact(
            'school',
            'classes',
            'from',
            'to',
            'classId',
            'rows',
            'summary'
        ));
    }

    public function fees(Request $request): View
    {
        $school = auth()->user()->school;

        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->toDateString());

        $payments = FeePayment::query()
            ->where('school_id', $school->id)
            ->whereBetween('paid_on', [$from, $to])
            ->with('student.schoolClass')
            ->orderByDesc('paid_on')
            ->get();

        $total = $payments->sum('amount');
        $byMethod = $payments->groupBy('payment_method')->map->sum('amount');

        return view('school.reports-fees', compact('school', 'from', 'to', 'payments', 'total', 'byMethod'));
    }
}
