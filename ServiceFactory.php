<?php

namespace Database\Factories;

use App\Models\YogaTeacher;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'teacher_id' => YogaTeacher::factory(),
            'service_type' => fake()->randomElement(['personal', 'group', 'corporate', 'online']),
            'package_type' => fake()->randomElement(['single', 'weekly', 'monthly', 'custom']),
            'total_sessions' => 12,
            'sessions_completed' => 0,
            'sessions_remaining' => 12,
            'start_date' => fake()->dateTimeBetween('now', '+7 days'),
            'end_date' => fake()->dateTimeBetween('+30 days', '+90 days'),
            'scheduled_days' => json_encode(['monday', 'wednesday', 'friday']),
            'preferred_time' => '09:00:00',
            'session_duration' => 60,
            'service_address' => fake()->address(),
            'landmark' => fake()->streetName(),
            'location_type' => fake()->randomElement(['home', 'studio', 'online', 'office']),
            'package_amount' => fake()->randomFloat(2, 5000, 50000),
            'per_session_rate' => fake()->randomFloat(2, 500, 2000),
            'platform_fee' => fake()->randomFloat(2, 100, 500),
            'tds_amount' => fake()->randomFloat(2, 50, 200),
            'final_amount' => fake()->randomFloat(2, 4500, 49000),
            'status' => 'confirmed', // ✅ Fixed
            'payment_status' => 'paid',
            'has_emergency_substitute' => fake()->boolean(),
            'allows_rescheduling' => true,
            'reschedule_count' => 0,
            'special_instructions' => fake()->sentence(),
            'auto_renew' => fake()->boolean(),
            'next_billing_date' => fake()->dateTimeBetween('+60 days', '+90 days'),
        ];
    }
}
