<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@radeanshoes.test'],
            [
                'name' => 'Radean Admin',
                'phone' => '081234567890',
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        ShippingOption::factory()->count(3)->create();

        Category::factory()
            ->count(3)
            ->create()
            ->each(function (Category $category): void {
                Product::factory()
                    ->count(3)
                    ->for($category)
                    ->create()
                    ->each(function (Product $product): void {
                        $product->images()->create([
                            'image_path' => 'products/'.Str::slug($product->name).'.jpg',
                            'sort_order' => 0,
                            'is_primary' => true,
                        ]);

                        collect([
                            ['size' => '39', 'color' => 'Black'],
                            ['size' => '40', 'color' => 'White'],
                            ['size' => '41', 'color' => 'Navy'],
                        ])->each(function (array $variantData, int $index) use ($product): void {
                            ProductVariant::factory()->for($product)->create([
                                'size' => $variantData['size'],
                                'color' => $variantData['color'],
                                'sku' => ($product->sku_prefix ?: 'RDS').'-'.$variantData['size'].'-'.$index,
                                'stock_qty' => fake()->numberBetween(4, 18),
                            ]);
                        });
                    });
            });

        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@radeanshoes.test',
            'role' => UserRole::Customer,
            'email_verified_at' => now(),
        ]);
    }
}
