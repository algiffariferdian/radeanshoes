<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'RDS-'.now()->format('Ymd').'-'.fake()->unique()->numberBetween(10000, 99999),
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'shipping_recipient_name' => fake()->name(),
            'shipping_phone' => fake()->phoneNumber(),
            'shipping_address_line' => fake()->streetAddress(),
            'shipping_district' => fake()->citySuffix(),
            'shipping_city' => fake()->city(),
            'shipping_province' => fake()->state(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_courier_name' => fake()->randomElement(['JNE', 'SiCepat']),
            'shipping_service_name' => fake()->randomElement(['Regular', 'Express']),
            'shipping_etd_text' => fake()->randomElement(['1-2 hari', '2-3 hari']),
            'shipping_cost' => 25000,
            'subtotal_amount' => 550000,
            'total_amount' => 575000,
            'order_status' => OrderStatus::PendingPayment,
            'payment_status' => PaymentStatus::Pending,
            'placed_at' => now(),
        ];
    }
}
