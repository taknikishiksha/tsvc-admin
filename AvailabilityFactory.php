<?php

namespace Database\Factories;

use App\Models\YogaTeacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'teacher_id' => YogaTeacher::factory(),
            'day_of_week' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'slot_duration' => 60,
            'service_types' => json_encode(['private', 'group']),
            'locations' => json_encode(['home', 'office']),
            'is_recurring' => true,
            'specific_date' => null,
            'is_available' => true,
            'max_bookings_per_slot' => 5,
            'current_bookings' => 0,
            'buffer_minutes' => 15,
            'priority_level' => fake()->numberBetween(1, 5),
            'dynamic_pricing' => fake()->boolean(),
        ];
    }
}
