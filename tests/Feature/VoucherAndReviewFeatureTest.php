<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Models\User;
use App\Models\Voucher;
use App\Services\Midtrans\MidtransService;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use App\Support\Enums\VoucherDiscountType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('checkout applies voucher and stores discount snapshot', function () {
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create(['is_default' => true]);
    $shippingOption = ShippingOption::factory()->create(['price' => 25000]);
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'price_override' => 500000,
        'discount_percentage' => 0,
        'stock_qty' => 10,
    ]);

    Voucher::query()->create([
        'code' => 'HEMAT10',
        'name' => 'Diskon 10%',
        'discount_type' => VoucherDiscountType::Percent,
        'discount_value' => 10,
        'min_subtotal' => 300000,
        'max_discount' => 75000,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $cart = Cart::factory()->for($customer)->create();
    CartItem::factory()->for($cart)->for($variant, 'productVariant')->create([
        'qty' => 1,
        'unit_price_snapshot' => 500000,
    ]);

    $this->mock(MidtransService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('createTransaction')->once()->andReturn([
            'snap_token' => 'unit-test-token',
            'redirect_url' => 'https://example.test/pay',
            'request_payload' => ['foo' => 'bar'],
            'raw_response' => ['token' => 'unit-test-token'],
        ]);
        $mock->shouldReceive('clientKey')->andReturn('unit-test-client-key');
    });

    $this->actingAs($customer)
        ->post(route('checkout.place-order'), [
            'address_id' => $address->id,
            'shipping_option_id' => $shippingOption->id,
            'voucher_code' => 'HEMAT10',
        ])
        ->assertOk();

    $order = Order::query()->with('payment', 'voucher')->first();

    expect($order)->not->toBeNull()
        ->and($order->voucher_code)->toBe('HEMAT10')
        ->and((float) $order->discount_amount)->toBe(50000.0)
        ->and((float) $order->total_amount)->toBe(475000.0)
        ->and((float) $order->payment->gross_amount)->toBe(475000.0)
        ->and($order->voucher)->not->toBeNull();
});

test('verified buyer can submit product review', function () {
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 8,
    ]);

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_status' => OrderStatus::Completed,
        'payment_status' => PaymentStatus::Paid,
    ]);

    $orderItem = OrderItem::factory()
        ->for($order)
        ->for($product)
        ->for($variant, 'productVariant')
        ->create();

    Payment::factory()->for($order)->create();

    $this->actingAs($customer)
        ->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'review' => 'Nyaman dipakai seharian.',
        ])
        ->assertRedirect();

    $review = ProductReview::query()->first();

    expect($review)->not->toBeNull()
        ->and($review->order_item_id)->toBe($orderItem->id)
        ->and($review->rating)->toBe(5);
});

test('user without purchase cannot submit product review', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($customer)
        ->from(route('products.show', $product))
        ->post(route('products.reviews.store', $product), [
            'rating' => 4,
            'review' => 'Ingin kasih rating.',
        ])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasErrors('rating');

    expect(ProductReview::query()->count())->toBe(0);
});
