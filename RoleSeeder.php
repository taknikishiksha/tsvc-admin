<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'superadmin',
            'admin',
            'hr',
            'finance',
            'training',
            'exam',
            'usermgmt',
            'service',
            'client',
            'teacher',
            'student',
            'partner',
            'consultant',
            'volunteer',
            'intern',
            'donor',
            'corporate',
            'affiliate',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->command->info('Roles seeded: ' . count($roles));
    }
}