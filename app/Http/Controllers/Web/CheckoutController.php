<?php

namespace App\Http\Controllers\Web;

use App\Actions\Checkout\CreateOrderAction;
use App\Actions\Checkout\CreateSnapTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Models\Order;
use App\Models\ShippingOption;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = auth()->user()->cart()->firstOrCreate()->load(['items.productVariant.product.images']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->latest()->get();
        $shippingOptions = ShippingOption::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('web.checkout.index', compact('cart', 'addresses', 'shippingOptions'));
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

        $order = $createOrderAction->handle($user, $address, $shippingOption, $request->validated('notes'));
        $order = $createSnapTransactionAction->handle($order);

        return view('web.checkout.payment', [
            'order' => $order,
            'midtransClientKey' => $midtransService->clientKey(),
        ]);
    }

    public function finish(Request $request): View
    {
        return view('web.checkout.result', [
            'state' => 'finish',
            'order' => $this->resolveOrder($request),
        ]);
    }

    public function unfinish(Request $request): View
    {
        return view('web.checkout.result', [
            'state' => 'unfinish',
            'order' => $this->resolveOrder($request),
        ]);
    }

    public function error(Request $request): View
    {
        return view('web.checkout.result', [
            'state' => 'error',
            'order' => $this->resolveOrder($request),
        ]);
    }

    protected function resolveOrder(Request $request): ?Order
    {
        $orderNumber = trim($request->string('order')->toString());

        if ($orderNumber === '') {
            return null;
        }

        return $request->user()
            ->orders()
            ->with(['items.product', 'items.productVariant', 'payment'])
            ->where('order_number', $orderNumber)
            ->first();
    }
}
