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

test('admin can complete fulfillment flow for paid order', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create();

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'order_status' => OrderStatus::Paid,
        'payment_status' => PaymentStatus::Paid,
        'tracking_number' => null,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update', $order), [
            'tracking_number' => 'RDS123456789',
        ])
        ->assertRedirect();

    $this->actingAs($admin)
        ->post(route('admin.orders.mark-processing', $order))
        ->assertRedirect();

    $this->actingAs($admin)
        ->post(route('admin.orders.mark-shipped', $order))
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
