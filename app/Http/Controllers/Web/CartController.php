<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Services\Inventory\InventoryService;
use App\Services\Pricing\OrderPricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected OrderPricingService $orderPricingService,
    ) {
    }

    public function index(): View
    {
        $cart = $this->cartForCurrentUser()->load(['items.productVariant.product.images']);
        $subtotal = $cart->items->sum(fn (CartItem $item) => (float) $item->unit_price_snapshot * $item->qty);

        return view('web.cart.index', [
            'cart' => $cart,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        $cart = $this->cartForCurrentUser();
        $variant = ProductVariant::query()->with('product')->findOrFail($request->integer('product_variant_id'));
        $existingItem = $cart->items()->where('product_variant_id', $variant->id)->first();
        $newQty = ($existingItem?->qty ?? 0) + $request->integer('qty');

        $this->inventoryService->ensureVariantCanBePurchased($variant, $newQty);

        $cart->items()->updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'qty' => $newQty,
                'unit_price_snapshot' => $this->orderPricingService->unitPrice($variant),
            ],
        );

        return back()->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): RedirectResponse
    {
        $item = $this->ownedCartItem($item)->load('productVariant.product');

        $this->inventoryService->ensureVariantCanBePurchased($item->productVariant, $request->integer('qty'));

        $item->update([
            'qty' => $request->integer('qty'),
            'unit_price_snapshot' => $this->orderPricingService->unitPrice($item->productVariant),
        ]);

        return back()->with('status', 'Keranjang berhasil diperbarui.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->ownedCartItem($item)->delete();

        return back()->with('status', 'Item dihapus dari keranjang.');
    }

    protected function cartForCurrentUser(): Cart
    {
        return auth()->user()->cart()->firstOrCreate();
    }

    protected function ownedCartItem(CartItem $item): CartItem
    {
        abort_unless($item->cart->user_id === auth()->id(), 404);

        return $item;
    }
}
