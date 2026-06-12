<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\IdCardService;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function __construct(
        protected IdCardService $idCards
    ) {}

    public function dashboard(): View
    {
        $user = auth()->user();
        $student = $user->studentRecord()->with('schoolClass')->firstOrFail();

        return view('student.dashboard', compact('user', 'student'));
    }

    public function profile(): View
    {
        $user = auth()->user();
        $student = $user->studentRecord()->with(['schoolClass', 'school'])->firstOrFail();

        return view('student.profile', compact('user', 'student'));
    }

    public function idCard(): View
    {
        $student = auth()->user()->studentRecord()
            ->with(['schoolClass', 'school.idCardSettings'])
            ->firstOrFail();

        $school = $student->school;
        $settings = $this->idCards->settingsFor($school);

        return view('school.id-cards.print', $this->idCards->buildCardRenderData($student, $school, $settings));
    }
}
