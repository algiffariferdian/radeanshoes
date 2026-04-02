<?php

namespace App\Actions\Checkout;

use App\Models\Order;
use App\Services\Midtrans\MidtransService;

class CreateSnapTransactionAction
{
    public function __construct(
        protected MidtransService $midtransService,
    ) {
    }

    public function handle(Order $order): Order
    {
        $order->loadMissing('items', 'payment', 'user');

        $transaction = $this->midtransService->createTransaction($order);

        $order->update([
            'midtrans_snap_token' => $transaction['snap_token'],
            'midtrans_redirect_url' => $transaction['redirect_url'],
        ]);

        $order->payment()->update([
            'order_id_provider' => $order->order_number,
            'raw_response_json' => $transaction['raw_response'],
        ]);

        $order->payment->logs()->create([
            'source' => 'create_transaction',
            'payload_json' => [
                'request' => $transaction['request_payload'],
                'response' => $transaction['raw_response'],
            ],
        ]);

        return $order->fresh(['items.product', 'items.productVariant', 'payment', 'user']);
    }
}
