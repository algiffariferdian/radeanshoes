<?php

namespace App\Actions\Checkout;

use App\Models\Order;
use App\Services\Inventory\InventoryService;
use App\Services\Midtrans\MidtransService;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HandleMidtransNotificationAction
{
    public function __construct(
        protected MidtransService $midtransService,
        protected InventoryService $inventoryService,
    ) {}

    public function handle(array $payload, bool $verifySignature = true, string $source = 'webhook'): Order
    {
        $orderNumber = $payload['order_id'] ?? null;

        if (! $orderNumber) {
            throw ValidationException::withMessages([
                'order_id' => 'Payload Midtrans tidak mengandung order_id.',
            ]);
        }

        $order = Order::query()
            ->with(['payment.logs', 'items.productVariant.product'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($verifySignature && ! $this->midtransService->verifySignature($payload)) {
            throw ValidationException::withMessages([
                'signature_key' => 'Signature Midtrans tidak valid.',
            ]);
        }

        $order->payment->logs()->create([
            'source' => $source,
            'payload_json' => $payload,
        ]);

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');

        return DB::transaction(function () use ($order, $payload, $transactionStatus, $fraudStatus): Order {
            $freshOrder = Order::query()
                ->with(['payment', 'items.productVariant.product'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            $freshPayment = $freshOrder->payment()->lockForUpdate()->firstOrFail();

            $freshPayment->fill([
                'transaction_id' => $payload['transaction_id'] ?? $freshPayment->transaction_id,
                'order_id_provider' => $payload['order_id'] ?? $freshPayment->order_id_provider,
                'payment_type' => $payload['payment_type'] ?? $freshPayment->payment_type,
                'transaction_status' => $transactionStatus ?: $freshPayment->transaction_status,
                'fraud_status' => $fraudStatus ?: $freshPayment->fraud_status,
                'gross_amount' => $payload['gross_amount'] ?? $freshPayment->gross_amount,
                'raw_response_json' => $payload,
            ]);

            $mapping = $this->mapStatus($transactionStatus, $fraudStatus);

            if ($mapping['payment_status'] === PaymentStatus::Paid) {
                if ($freshOrder->payment_status !== PaymentStatus::Paid) {
                    $stockReduced = $this->inventoryService->decrementPaidOrderStock($freshOrder);

                    if ($freshOrder->voucher_id) {
                        $freshOrder->voucher()->increment('used_count');
                    }

                    if (! $stockReduced) {
                        $freshOrder->notes = trim(($freshOrder->notes ? $freshOrder->notes."\n" : '').'Stock review required after successful payment.');

                        Log::error('Stock integrity issue during paid Midtrans callback.', [
                            'order_number' => $freshOrder->order_number,
                            'payload' => $payload,
                        ]);
                    }
                }

                if ($freshOrder->order_status === OrderStatus::PendingPayment) {
                    $freshOrder->order_status = OrderStatus::Processing;
                }

                $freshOrder->payment_status = PaymentStatus::Paid;
                $freshOrder->paid_at ??= now();
                $freshPayment->paid_at ??= now();
            }

            if ($mapping['payment_status'] === PaymentStatus::Pending && $freshOrder->payment_status === PaymentStatus::Pending) {
                $freshOrder->order_status = OrderStatus::PendingPayment;
            }

            if (
                in_array($mapping['payment_status'], [PaymentStatus::Expired, PaymentStatus::Cancelled, PaymentStatus::Failed], true)
                && $freshOrder->order_status === OrderStatus::PendingPayment
            ) {
                $freshOrder->payment_status = $mapping['payment_status'];
                $freshOrder->order_status = $mapping['order_status'];

                if ($mapping['payment_status'] === PaymentStatus::Expired) {
                    $freshPayment->expired_at ??= now();
                }

                if ($mapping['order_status'] === OrderStatus::Cancelled) {
                    $freshOrder->cancelled_at ??= now();
                }
            }

            $freshPayment->save();
            $freshOrder->save();

            return $freshOrder->fresh(['payment', 'items.product', 'items.productVariant', 'user']);
        });
    }

    protected function mapStatus(string $transactionStatus, string $fraudStatus): array
    {
        return match ($transactionStatus) {
            'settlement' => [
                'payment_status' => PaymentStatus::Paid,
                'order_status' => OrderStatus::Processing,
            ],
            'capture' => [
                'payment_status' => $fraudStatus === 'challenge' ? PaymentStatus::Pending : PaymentStatus::Paid,
                'order_status' => $fraudStatus === 'challenge' ? OrderStatus::PendingPayment : OrderStatus::Processing,
            ],
            'pending' => [
                'payment_status' => PaymentStatus::Pending,
                'order_status' => OrderStatus::PendingPayment,
            ],
            'expire' => [
                'payment_status' => PaymentStatus::Expired,
                'order_status' => OrderStatus::Expired,
            ],
            'cancel' => [
                'payment_status' => PaymentStatus::Cancelled,
                'order_status' => OrderStatus::Cancelled,
            ],
            default => [
                'payment_status' => PaymentStatus::Failed,
                'order_status' => OrderStatus::Cancelled,
            ],
        };
    }
}
