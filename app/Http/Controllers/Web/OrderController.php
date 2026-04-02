<?php

namespace App\Http\Controllers\Web;

use App\Actions\Checkout\SyncPendingOrderPaymentStatusAction;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('web.orders.index', compact('orders'));
    }

    public function show(string $orderNumber, SyncPendingOrderPaymentStatusAction $syncPendingOrderPaymentStatusAction): View
    {
        $order = auth()->user()
            ->orders()
            ->with(['items.product.images', 'items.productVariant', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $order = $syncPendingOrderPaymentStatusAction->handle($order)?->loadMissing(['items.product.images', 'items.productVariant', 'payment']) ?? $order;

        return view('web.orders.show', compact('order'));
    }
}
