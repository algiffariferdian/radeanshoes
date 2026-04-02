<?php

namespace Database\Factories;

use App\Models\ShippingOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingOption>
 */
class ShippingOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'courier_name' => fake()->randomElement(['JNE', 'SiCepat', 'AnterAja']),
            'service_name' => fake()->randomElement(['Regular', 'Express', 'Next Day']),
            'etd_text' => fake()->randomElement(['1-2 hari', '2-3 hari', '3-5 hari']),
            'price' => fake()->numberBetween(18000, 55000),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 5),
        ];
    }
}
