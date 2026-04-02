<?php

namespace App\Services\Midtrans;

use App\Models\Order;

class MidtransService
{
    public function createTransaction(Order $order): array
    {
        $payload = $this->buildPayload($order);

        if (! $this->isConfigured()) {
            return [
                'snap_token' => 'sandbox-'.$order->order_number,
                'redirect_url' => route('orders.show', $order->order_number),
                'request_payload' => $payload,
                'raw_response' => [
                    'token' => 'sandbox-'.$order->order_number,
                    'redirect_url' => route('orders.show', $order->order_number),
                    'mocked' => true,
                ],
            ];
        }

        $this->configure();

        $response = \Midtrans\Snap::createTransaction($payload);

        return [
            'snap_token' => $response->token,
            'redirect_url' => $response->redirect_url,
            'request_payload' => $payload,
            'raw_response' => json_decode(json_encode($response), true),
        ];
    }

    public function verifySignature(array $payload): bool
    {
        if (! $this->isConfigured()) {
            return app()->environment('testing');
        }

        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            .($payload['status_code'] ?? '')
            .($payload['gross_amount'] ?? '')
            .config('services.midtrans.server_key'),
        );

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }

    public function clientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    protected function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key')) && filled(config('services.midtrans.client_key'));
    }

    protected function configure(): void
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
    }

    protected function buildPayload(Order $order): array
    {
        $order->loadMissing('items', 'user');

        $items = $order->items->map(function ($item): array {
            return [
                'id' => $item->sku_snapshot,
                'price' => (int) round((float) $item->unit_price),
                'quantity' => $item->qty,
                'name' => $item->product_name_snapshot.' '.$item->variant_color_snapshot.' '.$item->variant_size_snapshot,
            ];
        })->all();

        $items[] = [
            'id' => 'SHIPPING',
            'price' => (int) round((float) $order->shipping_cost),
            'quantity' => 1,
            'name' => 'Biaya Pengiriman '.$order->shipping_courier_name.' '.$order->shipping_service_name,
        ];

        return [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) round((float) $order->total_amount),
            ],
            'customer_details' => [
                'first_name' => $order->shipping_recipient_name,
                'email' => $order->user->email,
                'phone' => $order->shipping_phone,
                'shipping_address' => [
                    'first_name' => $order->shipping_recipient_name,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address_line,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $items,
        ];
    }
}
