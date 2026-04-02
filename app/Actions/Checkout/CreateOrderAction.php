<?php

namespace App\Actions\Checkout;

use App\Models\Address;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ShippingOption;
use App\Models\User;
use App\Services\Inventory\InventoryService;
use App\Services\Pricing\OrderPricingService;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    public function __construct(
        protected OrderPricingService $orderPricingService,
        protected InventoryService $inventoryService,
    ) {
    }

    public function handle(User $user, Address $address, ShippingOption $shippingOption, ?string $notes = null): Order
    {
        if ($address->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'address_id' => 'Alamat pengiriman tidak valid.',
            ]);
        }

        if (! $shippingOption->is_active) {
            throw ValidationException::withMessages([
                'shipping_option_id' => 'Opsi pengiriman sudah tidak aktif.',
            ]);
        }

        return DB::transaction(function () use ($user, $address, $shippingOption, $notes): Order {
            $cart = $user->cart()->firstOrCreate()->load(['items.productVariant.product']);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang masih kosong.',
                ]);
            }

            foreach ($cart->items as $item) {
                $this->inventoryService->ensureVariantCanBePurchased($item->productVariant, $item->qty);
            }

            $pricing = $this->orderPricingService->calculate($cart->items, $shippingOption);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'address_id' => $address->id,
                'shipping_recipient_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_address_line' => $address->address_line,
                'shipping_district' => $address->district,
                'shipping_city' => $address->city,
                'shipping_province' => $address->province,
                'shipping_postal_code' => $address->postal_code,
                'shipping_courier_name' => $shippingOption->courier_name,
                'shipping_service_name' => $shippingOption->service_name,
                'shipping_etd_text' => $shippingOption->etd_text,
                'shipping_cost' => $pricing['shipping_cost'],
                'subtotal_amount' => $pricing['subtotal'],
                'total_amount' => $pricing['total'],
                'order_status' => OrderStatus::PendingPayment,
                'payment_status' => PaymentStatus::Pending,
                'notes' => $notes,
                'placed_at' => now(),
            ]);

            $order->items()->createMany($pricing['items']);

            Payment::create([
                'order_id' => $order->id,
                'provider' => 'midtrans',
                'provider_mode' => config('services.midtrans.is_production') ? 'production' : 'sandbox',
                'gross_amount' => $pricing['total'],
            ]);

            $cart->items()->delete();

            return $order->load('items.product', 'items.productVariant', 'payment', 'user');
        });
    }

    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'RDS-'.now()->format('Ymd').'-'.random_int(10000, 99999);
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
