<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\YogaTeacher;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'client_id' => Client::factory(),
            'teacher_id' => YogaTeacher::factory(),
            'payment_id' => fake()->uuid(),
            'order_id' => fake()->uuid(),
            'payment_method' => 'online',
            'gateway' => fake()->randomElement(['razorpay', 'stripe']),
            'amount' => fake()->randomFloat(2, 500, 5000),
            'platform_fee' => fake()->randomFloat(2, 50, 500),
            'teacher_share' => fake()->randomFloat(2, 400, 4500),
            'coordinator_share' => fake()->randomFloat(2, 0, 200),
            'tds_deducted' => fake()->randomFloat(2, 10, 100),
            'net_teacher_share' => fake()->randomFloat(2, 350, 4400),
            'status' => 'captured',  // ✅ Fixed
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'payout_processed_at' => null,
            'payout_status' => 'pending',
            'payout_id' => null,
            'payout_response' => null,
            'invoice_number' => fake()->unique()->numerify('INV-#####'),
            'invoice_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'invoice_details' => null,
            'refund_amount' => 0.00,
            'refund_id' => null,
            'refund_reason' => null,
            'refunded_at' => null,
            'gateway_request' => null,
            'gateway_response' => null,
            'gateway_error' => null,
        ];
    }
}
