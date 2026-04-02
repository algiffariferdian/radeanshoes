<?php

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('midtrans webhook marks order as paid and decrements stock only once', function () {
    config()->set('services.midtrans.server_key', 'test-server-key');
    config()->set('services.midtrans.client_key', 'test-client-key');

    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 10,
    ]);

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_number' => 'RDS-20260402-12345',
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
            'qty' => 2,
            'line_total' => 500000,
        ]);

    Payment::factory()->for($order)->create([
        'gross_amount' => 525000,
    ]);

    $payload = [
        'transaction_id' => 'txn-001',
        'order_id' => $order->order_number,
        'status_code' => '200',
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'gross_amount' => '525000.00',
    ];

    $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'test-server-key');

    $this->postJson(route('payments.midtrans.notification'), $payload)
        ->assertOk()
        ->assertJson(['ok' => true]);

    $order->refresh();
    $variant->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->order_status)->toBe(OrderStatus::Paid)
        ->and($variant->stock_qty)->toBe(8);

    $this->postJson(route('payments.midtrans.notification'), $payload)
        ->assertOk();

    expect($variant->fresh()->stock_qty)->toBe(8);
});
