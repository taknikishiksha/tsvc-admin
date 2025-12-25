<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Client;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'health_issues' => $this->faker->sentence(),
            'yoga_goals' => json_encode(['flexibility', 'stress_relief']),
            'experience_level' => 'beginner',
        ];
    }
}