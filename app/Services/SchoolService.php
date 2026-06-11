<?php

namespace App\Services;

use App\Models\Designation;
use App\Models\PageMenu;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolService
{
    public function __construct(
        protected AccessMenuService $accessMenu
    ) {}

    public function createSchool(array $data, array $adminData, array $menuAccess = []): School
    {
        return DB::transaction(function () use ($data, $adminData, $menuAccess) {
            $school = School::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['slug'] ?? $data['name']),
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => true,
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

            if (! empty($menuAccess)) {
                $this->accessMenu->grantAccessByDesignationSlugs($school, $designations, $menuAccess);
            } else {
                $this->grantDefaultMenuAccess($school, $designations);
            }

            return $school->load('designations');
        });
    }

    public function updateSchoolAccess(School $school, array $menuAccess): void
    {
        $this->accessMenu->syncSchoolDesignationAccess($school->id, $menuAccess);
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

    protected function grantDefaultMenuAccess(School $school, array $designations): void
    {
        PageMenu::query()
            ->whereNull('school_id')
            ->where('scope', PageMenu::SCOPE_SCHOOL)
            ->where('slug', 'dashboard')
            ->each(function (PageMenu $menu) use ($school, $designations) {
                $this->accessMenu->addDesignationPageAccess(
                    $school->id,
                    $menu->id,
                    $designations['admin']->id
                );
            });
    }
}
