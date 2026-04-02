<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentLog>
 */
class PaymentLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'source' => fake()->randomElement(['create_transaction', 'webhook']),
            'payload_json' => ['status' => 'ok'],
            'created_at' => now(),
        ];
    }
}
