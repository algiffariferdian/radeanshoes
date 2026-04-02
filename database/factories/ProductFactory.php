<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Radean '.Str::title(fake()->unique()->words(2, true));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'sku_prefix' => 'RDS-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraphs(3, true),
            'base_price' => fake()->numberBetween(350000, 1250000),
            'weight_gram' => fake()->numberBetween(500, 1600),
            'is_active' => true,
        ];
    }
}
