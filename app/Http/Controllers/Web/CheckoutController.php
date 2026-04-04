<?php

namespace App\Http\Controllers\Web;

use App\Actions\Checkout\CreateOrderAction;
use App\Actions\Checkout\CreateSnapTransactionAction;
use App\Actions\Checkout\SyncPendingOrderPaymentStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Models\Order;
use App\Models\ShippingOption;
use App\Services\Checkout\VoucherService;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request, VoucherService $voucherService): View|RedirectResponse
    {
        $cart = auth()->user()->cart()->firstOrCreate()->load(['items.productVariant.product.images']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->latest()->get();
        $shippingOptions = ShippingOption::query()->where('is_active', true)->orderBy('sort_order')->get();
        $cartSubtotal = $cart->items->sum(fn ($item) => (float) $item->productVariant->effectivePrice() * $item->qty);
        $defaultShipping = $shippingOptions->first();
        $recommendedShipping = $shippingOptions->sortBy('price')->first();
        $selectedShipping = $shippingOptions->firstWhere('id', (int) $request->integer('shipping_option_id'))
            ?? $recommendedShipping
            ?? $defaultShipping;
        $voucherPreview = $voucherService->preview($request->string('voucher_code')->toString(), $cartSubtotal);
        $estimatedTotal = max(
            0,
            $cartSubtotal + (float) ($selectedShipping?->price ?? 0) - (float) $voucherPreview['discount_amount']
        );

        return view('web.checkout.index', compact(
            'cart',
            'addresses',
            'shippingOptions',
            'cartSubtotal',
            'defaultShipping',
            'recommendedShipping',
            'selectedShipping',
            'voucherPreview',
            'estimatedTotal',
        ));
    }

    public function previewVoucher(Request $request, VoucherService $voucherService): \Illuminate\Http\JsonResponse
    {
        $cart = $request->user()->cart()->firstOrCreate()->load(['items.productVariant']);
        $cartSubtotal = $cart->items->sum(fn ($item) => (float) $item->productVariant->effectivePrice() * $item->qty);
        $preview = $voucherService->preview($request->string('voucher_code')->toString(), $cartSubtotal);

        return response()->json([
            'code' => $preview['code'],
            'discount_amount' => (float) $preview['discount_amount'],
            'error' => $preview['error'],
            'is_valid' => (bool) $preview['voucher'],
        ]);
    }

    public function placeOrder(
        PlaceOrderRequest $request,
        CreateOrderAction $createOrderAction,
        CreateSnapTransactionAction $createSnapTransactionAction,
        MidtransService $midtransService,
    ): View {
        $user = $request->user();
        $address = $user->addresses()->findOrFail($request->integer('address_id'));
        $shippingOption = ShippingOption::query()
            ->where('is_active', true)
            ->findOrFail($request->integer('shipping_option_id'));

        $order = $createOrderAction->handle(
            $user,
            $address,
            $shippingOption,
            $request->validated('notes'),
            $request->validated('voucher_code'),
        );
        $order = $createSnapTransactionAction->handle($order);

        return view('web.checkout.payment', [
            'order' => $order,
            'midtransClientKey' => $midtransService->clientKey(),
        ]);
    }

    public function finish(Request $request, SyncPendingOrderPaymentStatusAction $syncPendingOrderPaymentStatusAction): View
    {
        return view('web.checkout.result', [
            'state' => 'finish',
            'order' => $this->resolveOrder($request, $syncPendingOrderPaymentStatusAction),
        ]);
    }

    public function unfinish(Request $request, SyncPendingOrderPaymentStatusAction $syncPendingOrderPaymentStatusAction): View
    {
        return view('web.checkout.result', [
            'state' => 'unfinish',
            'order' => $this->resolveOrder($request, $syncPendingOrderPaymentStatusAction),
        ]);
    }

    public function error(Request $request, SyncPendingOrderPaymentStatusAction $syncPendingOrderPaymentStatusAction): View
    {
        return view('web.checkout.result', [
            'state' => 'error',
            'order' => $this->resolveOrder($request, $syncPendingOrderPaymentStatusAction),
        ]);
    }

    protected function resolveOrder(Request $request, SyncPendingOrderPaymentStatusAction $syncPendingOrderPaymentStatusAction): ?Order
    {
        $orderNumber = trim($request->string('order')->toString());

        if ($orderNumber === '') {
            return null;
        }

        $order = $request->user()
            ->orders()
            ->with(['items.product', 'items.productVariant', 'payment'])
            ->where('order_number', $orderNumber)
            ->first();

        return $syncPendingOrderPaymentStatusAction->handle($order)?->loadMissing(['items.product', 'items.productVariant', 'payment']);
    }
}
