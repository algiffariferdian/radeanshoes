<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected when opening cart page', function () {
    $this->get(route('cart.index'))
        ->assertRedirect(route('login'));
});

test('authenticated customer can add active product variant to cart', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($customer)
        ->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'qty' => 2,
        ])
        ->assertSessionHasNoErrors();

    $cart = $customer->refresh()->cart;

    expect($cart)->not->toBeNull()
        ->and($cart->items()->count())->toBe(1)
        ->and($cart->items()->first()->qty)->toBe(2);
});

test('customer cannot add quantity beyond available stock', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($customer)
        ->from(route('products.show', $product))
        ->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'qty' => 2,
        ])
        ->assertSessionHasErrors('qty');
});
