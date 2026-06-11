<?php

namespace App\Services;

use App\Models\Designation;
use App\Models\School;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolService
{
    public function __construct(
        protected AccessMenuService $accessMenu,
        protected SubscriptionService $subscriptions
    ) {}

    public function createSchool(array $data, array $adminData, bool $portalEnabled = false, ?int $planId = null): School
    {
        return DB::transaction(function () use ($data, $adminData, $portalEnabled, $planId) {
            $school = School::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['slug'] ?? $data['name']),
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => true,
                'portal_enabled' => $portalEnabled,
            ]);

            $designations = $this->createDefaultDesignations($school);

            User::create([
                'school_id' => $school->id,
                'designation_id' => $designations['admin']->id,
                'user_type' => User::TYPE_STAFF,
                'name' => $adminData['name'],
                'email' => $adminData['email'],
                'password' => $adminData['password'],
                'is_active' => true,
            ]);

            if ($portalEnabled) {
                $this->accessMenu->grantAdminAllSchoolMenus(
                    $school->id,
                    $designations['admin']->id
                );

                if ($planId) {
                    $plan = SubscriptionPlan::query()->find($planId);
                    if ($plan) {
                        $this->subscriptions->activatePlan($school, $plan);
                    }
                }
            }

            return $school->load('designations');
        });
    }

    public function setPortalAccess(School $school, bool $enabled): void
    {
        $school->update(['portal_enabled' => $enabled]);

        if (! $enabled) {
            return;
        }

        $this->syncAdminMenuAccess($school);
    }

    public function syncSchoolMenuAccess(School $school, array $menuIds): void
    {
        $menuIds = array_unique(array_map('intval', $menuIds));
        $portalEnabled = $menuIds !== [];

        $school->update(['portal_enabled' => $portalEnabled]);
        $this->accessMenu->syncSchoolEnabledMenus($school->id, $menuIds);

        if ($portalEnabled) {
            $this->syncAdminMenuAccess($school);
        }
    }

    protected function syncAdminMenuAccess(School $school): void
    {
        $adminDesignation = $school->designations()->where('slug', 'admin')->first();

        if ($adminDesignation) {
            $this->accessMenu->grantAdminAllSchoolMenus($school->id, $adminDesignation->id);
        }
    }

    protected function createDefaultDesignations(School $school): array
    {
        $defaults = [
            'admin' => ['name' => 'Admin', 'slug' => 'admin'],
            'principal' => ['name' => 'Principal', 'slug' => 'principal'],
            'teacher' => ['name' => 'Teacher', 'slug' => 'teacher'],
            'student' => ['name' => 'Student', 'slug' => 'student'],
        ];

        $result = [];
        foreach ($defaults as $key => $item) {
            $result[$key] = Designation::create([
                'school_id' => $school->id,
                'name' => $item['name'],
                'slug' => $item['slug'],
            ]);
        }

        return $result;
    }
}
