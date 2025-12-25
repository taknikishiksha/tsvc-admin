<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'shobhitsingh@taknikishiksha.org.in'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Win@12345'),
                'role' => 'superadmin',
                'is_active' => 1,
                'email_verified_at' => now(),
            ]
        );
    }
}