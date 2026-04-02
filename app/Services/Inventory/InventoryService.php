<?php

namespace App\Services\Inventory;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function ensureVariantCanBePurchased(ProductVariant $variant, int $qty): void
    {
        if (! $variant->is_active || ! $variant->product->is_active) {
            throw ValidationException::withMessages([
                'variant' => 'Varian produk ini sudah tidak aktif.',
            ]);
        }

        if ($variant->stock_qty < $qty) {
            throw ValidationException::withMessages([
                'qty' => 'Stok varian tidak mencukupi untuk jumlah yang dipilih.',
            ]);
        }
    }

    public function decrementPaidOrderStock(Order $order): bool
    {
        return DB::transaction(function () use ($order): bool {
            $orderItems = $order->items()->with('productVariant.product')->get();
            $variants = ProductVariant::query()
                ->whereIn('id', $orderItems->pluck('product_variant_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($orderItems as $item) {
                $variant = $variants->get($item->product_variant_id);

                if (! $variant || ! $variant->is_active || $variant->stock_qty < $item->qty) {
                    return false;
                }
            }

            foreach ($orderItems as $item) {
                $variants->get($item->product_variant_id)?->decrement('stock_qty', $item->qty);
            }

            return true;
        });
    }
}
