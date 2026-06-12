<?php

namespace App\Services;

use App\Models\Designation;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class StudentAccountService
{
    public function createForStudent(Student $student, ?string $password = null): User
    {
        if (! $student->email) {
            throw ValidationException::withMessages([
                'email' => 'Email is required to create a student portal login.',
            ]);
        }

        if ($student->user_id) {
            return $this->syncForStudent($student, $password);
        }

        $this->assertEmailAvailable($student->email, $student->school_id);

        $designation = $this->studentDesignation($student->school);

        $user = User::create([
            'school_id' => $student->school_id,
            'designation_id' => $designation->id,
            'user_type' => User::TYPE_STUDENT,
            'name' => $student->name,
            'email' => $student->email,
            'password' => $password ?? $student->roll_no,
            'is_active' => $student->is_active,
        ]);

        $student->update(['user_id' => $user->id]);

        return $user;
    }

    public function syncForStudent(Student $student, ?string $password = null): User
    {
        $user = $student->user;

        if (! $user) {
            return $this->createForStudent($student, $password);
        }

        if ($student->email && $student->email !== $user->email) {
            $this->assertEmailAvailable($student->email, $student->school_id, $user->id);
        }

        $user->update([
            'name' => $student->name,
            'email' => $student->email ?? $user->email,
            'is_active' => $student->is_active,
            ...(filled($password) ? ['password' => $password] : []),
        ]);

        return $user->fresh();
    }

    public function deactivateForStudent(Student $student): void
    {
        $student->user?->update(['is_active' => false]);
    }

    protected function studentDesignation(School $school): Designation
    {
        $designation = $school->designations()->where('slug', 'student')->first();

        if (! $designation) {
            throw new \RuntimeException('Student designation not found for this school.');
        }

        return $designation;
    }

    protected function assertEmailAvailable(string $email, int $schoolId, ?int $ignoreUserId = null): void
    {
        $query = User::query()->where('email', $email);

        if ($ignoreUserId) {
            $query->where('id', '!=', $ignoreUserId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email is already used by another portal user.',
            ]);
        }
    }
}
