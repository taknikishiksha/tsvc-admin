<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\YogaTeacher;

class YogaTeacherFactory extends Factory
{
    protected $model = YogaTeacher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => $this->faker->paragraph(),
            'specializations' => json_encode(['Hatha Yoga', 'Pranayama']),
            'languages' => json_encode(['Hindi', 'English']),
            'experience_years' => $this->faker->numberBetween(1, 10),
            'hourly_rate' => $this->faker->numberBetween(300, 1000),
            'verification_status' => 'verified',
            'rating' => $this->faker->randomFloat(1, 3, 5),
        ];
    }
}