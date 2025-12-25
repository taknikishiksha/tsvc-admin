<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\YogaTeacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'transactionable_type' => YogaTeacher::class,
            'transactionable_id' => YogaTeacher::factory(),
            'service_id' => null,
            'transaction_type' => fake()->randomElement(['points_earned', 'points_redeemed', 'referral_bonus', 'signup_bonus']),
            'points' => fake()->numberBetween(10, 500),
            'cash_value' => fake()->randomFloat(2, 10, 500),
            'currency' => 'INR',
            'loyalty_tier' => 'basic',
            'multiplier' => 1.00,
            'reference_type' => 'session_completion',
            'reference_id' => fake()->uuid(),
            'description' => fake()->sentence(),
            'validity_days' => 365,
            'expires_at' => fake()->dateTimeBetween('now', '+1 year'),
            'is_expired' => false,
            'status' => 'active',
        ];
    }
}
