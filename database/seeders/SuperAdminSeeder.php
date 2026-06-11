<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'superadmin@school.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'user_type' => User::TYPE_SUPER_ADMIN,
                'school_id' => null,
                'designation_id' => null,
                'is_active' => true,
            ]
        );
    }
}
