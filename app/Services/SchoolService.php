<?php

namespace App\Services;

use App\Models\Designation;
use App\Models\School;
use App\Models\SchoolIdCardSetting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolService
{
    public function __construct(
        protected AccessMenuService $accessMenu,
        protected SubscriptionService $subscriptions,
        protected IdCardService $idCards
    ) {}

    public function createSchool(
        array $data,
        array $adminData,
        bool $portalEnabled = false,
        ?int $planId = null,
        ?array $idCardInput = null,
        ?UploadedFile $schoolLogo = null
    ): School {
        return DB::transaction(function () use ($data, $adminData, $portalEnabled, $planId, $idCardInput, $schoolLogo) {
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

            $this->idCards->saveForSchool($school, $idCardInput ?? [
                'id_card_template' => SchoolIdCardSetting::TEMPLATE_CLASSIC,
                'id_card_primary_color' => '#0a5f47',
                'id_card_secondary_color' => '#0d7a5c',
                'id_card_header_title' => 'Student Identity Card',
                'id_card_footer_text' => null,
                'id_card_show_photo' => true,
                'id_card_show_roll_no' => true,
                'id_card_show_class' => true,
                'id_card_show_guardian' => true,
                'id_card_show_phone' => false,
                'id_card_show_barcode' => true,
            ]);

            if ($schoolLogo) {
                $this->idCards->storeSchoolLogo($school, $schoolLogo);
            }

            return $school->load(['designations', 'idCardSettings']);
        });
    }

    public function updateSchool(School $school, array $data): School
    {
        $wasPortalEnabled = $school->portal_enabled;
        $portalEnabled = (bool) ($data['portal_enabled'] ?? false);

        $school->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['slug'] ?? $data['name']),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'portal_enabled' => $portalEnabled,
        ]);

        if ($portalEnabled && ! $wasPortalEnabled) {
            $this->syncAdminMenuAccess($school);
        }

        return $school->fresh();
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
