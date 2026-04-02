<?php

namespace App\Services\Pricing;

use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use Illuminate\Support\Collection;

class OrderPricingService
{
    public function unitPrice(ProductVariant $variant): string
    {
        return $variant->effectivePrice();
    }

    /**
     * @param  Collection<int, CartItem>  $cartItems
     * @return array{
     *     items: array<int, array{
     *         product_id: int,
     *         product_variant_id: int,
     *         product_name_snapshot: string,
     *         variant_size_snapshot: string,
     *         variant_color_snapshot: string,
     *         sku_snapshot: string,
     *         unit_price: string,
     *         qty: int,
     *         line_total: string
     *     }>,
     *     subtotal: string,
     *     shipping_cost: string,
     *     total: string
     * }
     */
    public function calculate(Collection $cartItems, ShippingOption $shippingOption): array
    {
        $items = $cartItems->map(function (CartItem $cartItem): array {
            $variant = $cartItem->productVariant;
            $product = $variant->product;
            $unitPrice = (float) $this->unitPrice($variant);
            $lineTotal = $unitPrice * $cartItem->qty;

            return [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'product_name_snapshot' => $product->name,
                'variant_size_snapshot' => $variant->size,
                'variant_color_snapshot' => $variant->color,
                'sku_snapshot' => $variant->sku,
                'unit_price' => number_format($unitPrice, 2, '.', ''),
                'qty' => (int) $cartItem->qty,
                'line_total' => number_format($lineTotal, 2, '.', ''),
            ];
        })->values();

        $subtotal = $items->sum(fn (array $item) => (float) $item['line_total']);
        $shippingCost = (float) $shippingOption->price;

        return [
            'items' => $items->all(),
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'shipping_cost' => number_format($shippingCost, 2, '.', ''),
            'total' => number_format($subtotal + $shippingCost, 2, '.', ''),
        ];
    }
}
