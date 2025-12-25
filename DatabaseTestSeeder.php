<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\YogaTeacher;
use App\Models\Client;

class DatabaseTestSeeder extends Seeder
{
    public function run()
    {
        // ✅ Check if users already exist, if yes then delete/update
        $teacherUser = User::updateOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'Test Yoga Teacher',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        $clientUser = User::updateOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Test Client',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        $this->command->info('Basic users created/updated successfully!');

        // Create Yoga Teacher (if table exists)
        if (Schema::hasTable('yoga_teachers')) {
            YogaTeacher::updateOrCreate(
                ['user_id' => $teacherUser->id],
                [
                    'bio' => 'Experienced yoga teacher with 5 years of practice',
                    'specializations' => json_encode(['Hatha Yoga', 'Pranayama']),
                    'languages' => json_encode(['Hindi', 'English']),
                    'experience_years' => 5,
                    'hourly_rate' => 500.00,
                    'verification_status' => 'verified',
                ]
            );
            $this->command->info('Yoga Teacher profile created/updated!');
        } else {
            $this->command->warn('yoga_teachers table does not exist yet.');
        }

        // Create Client (if table exists)
        if (Schema::hasTable('clients')) {
            Client::updateOrCreate(
                ['user_id' => $clientUser->id],
                [
                    'health_issues' => 'Back pain, stress',
                    'yoga_goals' => json_encode(['flexibility', 'stress_relief']),
                    'experience_level' => 'beginner',
                ]
            );
            $this->command->info('Client profile created/updated!');
        } else {
            $this->command->warn('clients table does not exist yet.');
        }

        $this->command->info('✅ Test data seeding completed!');
        $this->command->info('Teacher Login: teacher@test.com / password');
        $this->command->info('Client Login: client@test.com / password');
    }
}
