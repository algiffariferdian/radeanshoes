<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Models\User;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public storefront pages render successfully', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    ProductVariant::factory()->for($product)->create([
        'price_override' => 450000,
        'stock_qty' => 10,
    ]);

    $product->images()->create([
        'image_path' => 'products/demo-image.svg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $this->get(route('home'))->assertOk();
    $this->get(route('products.index'))->assertOk();
    $this->get(route('products.show', $product))->assertOk();
});

test('authenticated storefront pages render successfully', function () {
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create(['is_default' => true]);
    $shippingOption = ShippingOption::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 10,
    ]);

    $cart = Cart::factory()->for($customer)->create();
    CartItem::factory()->for($cart)->for($variant, 'productVariant')->create();

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'shipping_courier_name' => $shippingOption->courier_name,
        'shipping_service_name' => $shippingOption->service_name,
        'shipping_etd_text' => $shippingOption->etd_text,
        'order_status' => OrderStatus::Processing,
        'payment_status' => PaymentStatus::Paid,
    ]);

    OrderItem::factory()->for($order)->for($product)->for($variant, 'productVariant')->create();
    Payment::factory()->for($order)->create();

    $this->actingAs($customer)->get(route('cart.index'))->assertOk();
    $this->actingAs($customer)->get(route('checkout.index'))->assertOk();
    $this->actingAs($customer)->get(route('orders.index'))->assertOk();
    $this->actingAs($customer)->get(route('orders.show', $order->order_number))->assertOk();
    $this->actingAs($customer)->get(route('addresses.index'))->assertOk();
    $this->actingAs($customer)->get(route('account.profile.edit'))->assertOk();
});
