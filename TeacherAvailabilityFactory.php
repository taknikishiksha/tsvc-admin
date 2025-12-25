<?php

namespace Database\Factories;

use App\Models\TeacherAvailability;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherAvailabilityFactory extends Factory
{
    protected $model = TeacherAvailability::class;

    public function definition()
    {
        return [
            'teacher_id' => User::factory(),
            'day_of_week' => $this->faker->randomElement([
                'monday', 'tuesday', 'wednesday', 'thursday', 
                'friday', 'saturday', 'sunday'
            ]),
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
            'is_available' => true,
            'session_type' => $this->faker->randomElement(['home', 'online', 'corporate']),
            'max_sessions' => $this->faker->numberBetween(1, 5),
        ];
    }

    public function available()
    {
        return $this->state([
            'is_available' => true,
        ]);
    }

    public function unavailable()
    {
        return $this->state([
            'is_available' => false,
        ]);
    }

    public function homeSession()
    {
        return $this->state([
            'session_type' => 'home',
        ]);
    }

    public function onlineSession()
    {
        return $this->state([
            'session_type' => 'online',
        ]);
    }
}
?>