<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'school_id',
        'class_id',
        'roll_no',
        'name',
        'photo',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'guardian_name',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function photoUrl(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return asset('storage/'.$this->photo);
    }

    public function barcodeValue(): string
    {
        return sprintf('SCH%d-STU%d-R%s', $this->school_id, $this->id, $this->roll_no);
    }
}
