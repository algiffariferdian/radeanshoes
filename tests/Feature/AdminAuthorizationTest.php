<?php

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer cannot access admin dashboard', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admin can access admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('admin can complete fulfillment flow for processing order', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_status' => OrderStatus::Processing,
        'payment_status' => PaymentStatus::Paid,
        'tracking_number' => null,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.orders.mark-shipped', $order), [
            'tracking_number' => 'RDS123456789',
        ])
        ->assertRedirect();

    $this->actingAs($admin)
        ->post(route('admin.orders.mark-completed', $order))
        ->assertRedirect();

    $order->refresh();

    expect($order->tracking_number)->toBe('RDS123456789')
        ->and($order->order_status)->toBe(OrderStatus::Completed)
        ->and($order->shipped_at)->not->toBeNull()
        ->and($order->completed_at)->not->toBeNull();
});

test('admin must input tracking number when marking order as shipped', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_status' => OrderStatus::Processing,
        'payment_status' => PaymentStatus::Paid,
        'tracking_number' => null,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.orders.show', $order))
        ->post(route('admin.orders.mark-shipped', $order), [])
        ->assertRedirect(route('admin.orders.show', $order))
        ->assertSessionHasErrors('tracking_number');

    $order->refresh();

    expect($order->order_status)->toBe(OrderStatus::Processing)
        ->and($order->tracking_number)->toBeNull();
});

test('admin order detail shows the right actions for shipped and completed orders', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();

    $shippedOrder = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_status' => OrderStatus::Shipped,
        'payment_status' => PaymentStatus::Paid,
        'tracking_number' => 'RDS-SHIP-123',
    ]);

    $completedOrder = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_status' => OrderStatus::Completed,
        'payment_status' => PaymentStatus::Paid,
        'tracking_number' => 'RDS-DONE-456',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.show', $shippedOrder))
        ->assertOk()
        ->assertDontSee('Mark Shipped')
        ->assertSee('Mark Completed')
        ->assertSee('Batalkan Order');

    $this->actingAs($admin)
        ->get(route('admin.orders.show', $completedOrder))
        ->assertOk()
        ->assertDontSee('Mark Shipped')
        ->assertDontSee('Mark Completed')
        ->assertDontSee('Batalkan Order');
});
