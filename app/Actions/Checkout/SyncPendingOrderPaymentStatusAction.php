<?php

namespace App\Actions\Checkout;

use App\Models\Order;
use App\Services\Midtrans\MidtransService;
use App\Support\Enums\PaymentStatus;
use Throwable;

class SyncPendingOrderPaymentStatusAction
{
    public function __construct(
        protected MidtransService $midtransService,
        protected HandleMidtransNotificationAction $handleMidtransNotificationAction,
    ) {}

    public function handle(?Order $order): ?Order
    {
        if (! $order) {
            return null;
        }

        if ($order->payment_status !== PaymentStatus::Pending || ! $this->midtransService->isConfigured()) {
            return $order;
        }

        try {
            $payload = $this->midtransService->fetchTransactionStatus($order->order_number);

            if (! $payload || empty($payload['transaction_status'])) {
                return $order;
            }

            return $this->handleMidtransNotificationAction->handle($payload, verifySignature: false, source: 'status_inquiry');
        } catch (Throwable $throwable) {
            report($throwable);

            return $order;
        }
    }
}
