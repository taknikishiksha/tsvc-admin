<?php

namespace Database\Factories;

use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherProfileFactory extends Factory
{
    protected $model = TeacherProfile::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'bio' => $this->faker->paragraph(3),
            'specialization' => json_encode(['Hatha Yoga', 'Meditation']),
            'experience_years' => $this->faker->numberBetween(1, 20),
            'hourly_rate' => $this->faker->numberBetween(300, 2000),
            'languages' => json_encode(['Hindi', 'English']),
            'certifications' => json_encode(['YCB Level 1', 'RYT 200']),
            'teaching_style' => $this->faker->sentence(),
            'availability_slots' => json_encode([]),
            'completion_percentage' => $this->faker->numberBetween(0, 100),
            'visibility_score' => $this->faker->numberBetween(0, 100),
            'verification_status' => 'not_verified',
        ];
    }

    public function verified()
    {
        return $this->state([
            'verification_status' => 'fully_verified',
            'verified_at' => now(),
        ]);
    }

    public function withHighVisibility()
    {
        return $this->state([
            'visibility_score' => 90,
            'completion_percentage' => 100,
        ]);
    }
}
?>