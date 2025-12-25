<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OTPVerificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['email_verification', 'phone_verification', 'password_reset']),
            'otp' => fake()->numerify('######'),
            'token' => fake()->uuid(),
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ];
    }
}
