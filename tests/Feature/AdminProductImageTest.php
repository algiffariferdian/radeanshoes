<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can create product with automatic slug sku prefix and single cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'category_id' => $category->id,
        'name' => 'Runner Pro',
        'description' => 'Sepatu lari ringan.',
        'weight_gram' => 850,
        'is_active' => 1,
        'cover_image' => UploadedFile::fake()->image('runner-cover.jpg'),
    ]);

    $product = Product::query()->with('images')->firstOrFail();

    $response->assertRedirect(route('admin.products.edit', $product));

    expect($product->slug)->toBe('runner-pro')
        ->and($product->sku_prefix)->toStartWith('RDS-')
        ->and($product->images)->toHaveCount(1)
        ->and($product->images->first()->is_primary)->toBeTrue()
        ->and($product->images->first()->product_variant_id)->toBeNull();

    Storage::disk('public')->assertExists($product->images->first()->image_path);
});

test('admin can replace product cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();

    Storage::disk('public')->put('products/existing-cover.jpg', 'one');

    $existingImage = $product->images()->create([
        'image_path' => 'products/existing-cover.jpg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $this->actingAs($admin)->patch(route('admin.products.update', $product), [
        'category_id' => $category->id,
        'name' => $product->name,
        'description' => $product->description,
        'weight_gram' => $product->weight_gram,
        'is_active' => 1,
        'cover_image' => UploadedFile::fake()->image('runner-cover-new.webp'),
    ])->assertRedirect(route('admin.products.edit', $product));

    $product->refresh();
    $product->load('images');

    expect($product->images)->toHaveCount(1)
        ->and($product->images->first()->id)->not->toBe($existingImage->id)
        ->and($product->images->first()->is_primary)->toBeTrue();

    Storage::disk('public')->assertMissing('products/existing-cover.jpg');
    Storage::disk('public')->assertExists($product->images->first()->image_path);
});

test('admin can create variant with automatic sku discount and multiple images', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create([
        'name' => 'Runner Pro',
        'slug' => 'runner-pro',
        'sku_prefix' => 'RDS-RUNNER',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.products.variants.store', $product), [
        'size' => 42,
        'color' => 'Hitam',
        'price' => 899000,
        'discount_percentage' => 15,
        'stock_qty' => 7,
        'is_active' => 1,
        'images' => [
            UploadedFile::fake()->image('variant-1.jpg'),
            UploadedFile::fake()->image('variant-2.jpg'),
        ],
    ]);

    $variant = $product->variants()->with('images')->firstOrFail();

    $response->assertRedirect(route('admin.products.variants.index', $product));

    expect($variant->sku)->toStartWith('RDS-RUNNER-HIT-42')
        ->and((float) $variant->price_override)->toBe(899000.0)
        ->and($variant->discount_percentage)->toBe(15)
        ->and($variant->images)->toHaveCount(2)
        ->and($variant->images->pluck('product_variant_id')->unique()->all())->toBe([$variant->id]);

    foreach ($variant->images as $image) {
        Storage::disk('public')->assertExists($image->image_path);
    }
});

test('deleting a variant image removes the file', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create();
    $variant = $product->variants()->create([
        'size' => '41',
        'color' => 'Navy',
        'sku' => 'RDS-NAV-41',
        'price_override' => 750000,
        'discount_percentage' => 10,
        'stock_qty' => 8,
        'is_active' => true,
    ]);

    Storage::disk('public')->put('products/variant-delete.jpg', 'one');

    $image = $variant->images()->create([
        'product_id' => $product->id,
        'image_path' => 'products/variant-delete.jpg',
        'sort_order' => 0,
        'is_primary' => false,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.products.variants.images.destroy', [$product, $variant, $image]))
        ->assertRedirect();

    Storage::disk('public')->assertMissing('products/variant-delete.jpg');
    expect($variant->images()->count())->toBe(0);
});
