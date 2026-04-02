<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Models\User;
use App\Services\Midtrans\MidtransService;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('checkout creates order snapshots payment and clears cart', function () {
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create(['is_default' => true]);
    $shippingOption = ShippingOption::factory()->create(['price' => 25000]);
    $product = Product::factory()->create(['base_price' => 500000]);
    $variant = ProductVariant::factory()->for($product)->create([
        'price_override' => null,
        'stock_qty' => 10,
    ]);

    $cart = Cart::factory()->for($customer)->create();
    CartItem::factory()
        ->for($cart)
        ->for($variant, 'productVariant')
        ->create([
            'qty' => 2,
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

    $response = $this->actingAs($customer)
        ->post(route('checkout.place-order'), [
            'address_id' => $address->id,
            'shipping_option_id' => $shippingOption->id,
            'notes' => 'Mohon kirim sore hari',
        ]);

    $response->assertOk()->assertSee('unit-test-token');

    $order = Order::query()->with('items', 'payment')->first();

    expect($order)->not->toBeNull()
        ->and($order->order_status)->toBe(OrderStatus::PendingPayment)
        ->and($order->payment_status)->toBe(PaymentStatus::Pending)
        ->and((float) $order->subtotal_amount)->toBe(1000000.0)
        ->and((float) $order->shipping_cost)->toBe(25000.0)
        ->and((float) $order->total_amount)->toBe(1025000.0)
        ->and($order->shipping_city)->toBe($address->city)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->product_name_snapshot)->toBe($product->name)
        ->and($order->payment)->not->toBeNull()
        ->and($customer->fresh()->cart->items()->count())->toBe(0);
});

test('checkout finish syncs pending order status from midtrans inquiry when webhook is unavailable', function () {
    config()->set('services.midtrans.server_key', 'test-server-key');
    config()->set('services.midtrans.client_key', 'test-client-key');

    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 5,
    ]);

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_number' => 'RDS-20260402-90001',
        'subtotal_amount' => 500000,
        'shipping_cost' => 25000,
        'total_amount' => 525000,
        'order_status' => OrderStatus::PendingPayment,
        'payment_status' => PaymentStatus::Pending,
    ]);

    OrderItem::factory()
        ->for($order)
        ->for($product)
        ->for($variant, 'productVariant')
        ->create([
            'product_name_snapshot' => $product->name,
            'variant_size_snapshot' => $variant->size,
            'variant_color_snapshot' => $variant->color,
            'sku_snapshot' => $variant->sku,
            'unit_price' => 250000,
            'qty' => 1,
            'line_total' => 250000,
        ]);

    Payment::factory()->for($order)->create([
        'gross_amount' => 525000,
    ]);

    $this->mock(MidtransService::class, function (MockInterface $mock) use ($order): void {
        $mock->shouldReceive('isConfigured')->andReturn(true);
        $mock->shouldReceive('fetchTransactionStatus')->once()->with($order->order_number)->andReturn([
            'transaction_id' => 'txn-sync-001',
            'order_id' => $order->order_number,
            'status_code' => '200',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'gross_amount' => '525000.00',
        ]);
    });

    $this->actingAs($customer)
        ->get(route('checkout.finish', ['order' => $order->order_number]))
        ->assertOk();

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->fresh()->order_status)->toBe(OrderStatus::Processing)
        ->and($variant->fresh()->stock_qty)->toBe(4);
});
