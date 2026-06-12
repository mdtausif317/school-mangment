<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\IdCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function __construct(
        protected IdCardService $idCards
    ) {}

    protected function studentProfile(): Student
    {
        $student = auth()->user()->linkedStudentProfile();

        if (! $student) {
            abort(403, 'No student record found for your school. Ensure your login email matches the student profile email.');
        }

        return $student;
    }

    public function dashboard(): View|RedirectResponse
    {
        $user = auth()->user();
        $student = $this->studentProfile()->load('schoolClass');

        return view('student.dashboard', compact('user', 'student'));
    }

    public function profile(): View
    {
        $user = auth()->user();
        $student = $this->studentProfile()->load(['schoolClass', 'school']);

        return view('student.profile', compact('user', 'student'));
    }

    public function idCard(): View
    {
        $student = $this->studentProfile()
            ->load(['schoolClass', 'school.idCardSettings']);

        $school = $student->school;
        $settings = $this->idCards->settingsFor($school);

        return view('school.id-cards.print', $this->idCards->buildCardRenderData($student, $school, $settings));
    }
}
