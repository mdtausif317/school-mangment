<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeePaymentController extends Controller
{
    public function index(): View
    {
        $school = auth()->user()->school;

        $payments = FeePayment::query()
            ->where('school_id', $school->id)
            ->with('student.schoolClass')
            ->orderByDesc('paid_on')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $students = Student::query()
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'roll_no']);

        $totalThisMonth = FeePayment::query()
            ->where('school_id', $school->id)
            ->whereMonth('paid_on', now()->month)
            ->whereYear('paid_on', now()->year)
            ->sum('amount');

        return view('school.fees-collect', compact('school', 'payments', 'students', 'totalThisMonth'));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = auth()->user()->school;

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')->where('school_id', $school->id),
            ],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'paid_on' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(array_keys(FeePayment::paymentMethods()))],
            'reference' => ['nullable', 'string', 'max:100'],
            'fee_for' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        FeePayment::create([
            'school_id' => $school->id,
            'student_id' => $validated['student_id'],
            'amount' => $validated['amount'],
            'paid_on' => $validated['paid_on'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? null,
            'fee_for' => $validated['fee_for'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('school.fees-collect')
            ->with('success', 'Fee payment recorded successfully.');
    }
}
