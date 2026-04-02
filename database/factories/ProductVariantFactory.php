<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size' => (string) fake()->randomElement([38, 39, 40, 41, 42, 43, 44]),
            'color' => fake()->randomElement(['Black', 'White', 'Navy', 'Olive', 'Sand']),
            'sku' => 'SKU-'.Str::upper(fake()->unique()->bothify('??###??')),
            'price_override' => fake()->boolean(35) ? fake()->numberBetween(300000, 1300000) : null,
            'stock_qty' => fake()->numberBetween(1, 25),
            'is_active' => true,
        ];
    }
}
