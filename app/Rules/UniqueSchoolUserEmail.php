<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UniqueSchoolUserEmail implements ValidationRule
{
    public function __construct(
        protected int $schoolId,
        protected ?int $ignoreUserId = null
    ) {}

    public static function for(int $schoolId, ?int $ignoreUserId = null): self
    {
        return new self($schoolId, $ignoreUserId);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $rule = Rule::unique('users', 'email')
            ->where('school_id', $this->schoolId);

        if ($this->ignoreUserId) {
            $rule->ignore($this->ignoreUserId);
        }

        $validator = validator(
            [$attribute => $value],
            [$attribute => [$rule]]
        );

        if ($validator->fails()) {
            $fail('This email is already used by another user in this school.');
        }
    }
}
