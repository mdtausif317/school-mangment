<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const TYPE_SUPER_ADMIN = 'super_admin';

    public const TYPE_STAFF = 'staff';

    public const TYPE_TEACHER = 'teacher';

    public const TYPE_STUDENT = 'student';

    protected $fillable = [
        'school_id',
        'designation_id',
        'user_type',
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function studentRecord(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === self::TYPE_SUPER_ADMIN;
    }

    public function isSchoolUser(): bool
    {
        return $this->school_id !== null;
    }

    public function isTeacher(): bool
    {
        return $this->user_type === self::TYPE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->user_type === self::TYPE_STUDENT;
    }
}
