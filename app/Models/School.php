<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'is_active',
        'portal_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'portal_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(PageMenu::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function enabledMenus(): HasMany
    {
        return $this->hasMany(SchoolMenuAccess::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(SchoolSubscription::class)
            ->where('status', SchoolSubscription::STATUS_ACTIVE)
            ->where('expires_at', '>', now())
            ->latest('expires_at');
    }
}
