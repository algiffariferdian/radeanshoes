<?php

namespace App\Http\Controllers\Web;

use App\Actions\Checkout\SyncPendingOrderPaymentStatusAction;
use App\Http\Controllers\Controller;
use App\Support\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $statusOptions = collect(OrderStatus::cases())
            ->map(fn (OrderStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ]);

        $sortOptions = collect([
            ['value' => 'latest', 'label' => 'Terbaru'],
            ['value' => 'oldest', 'label' => 'Terlama'],
            ['value' => 'total_high', 'label' => 'Total Tertinggi'],
            ['value' => 'total_low', 'label' => 'Total Terendah'],
        ]);

        $activeStatus = $request->string('status')->toString();
        $activeSort = $request->string('sort')->toString() ?: 'latest';
        $validStatuses = $statusOptions->pluck('value')->all();
        $validSorts = $sortOptions->pluck('value')->all();
        if ($activeStatus !== '' && ! in_array($activeStatus, $validStatuses, true)) {
            $activeStatus = '';
        }
        if (! in_array($activeSort, $validSorts, true)) {
            $activeSort = 'latest';
        }

        $ordersQuery = auth()->user()
            ->orders()
            ->with(['items.product', 'payment']);

        if ($activeStatus !== '') {
            $ordersQuery->where('order_status', $activeStatus);
        }

        switch ($activeSort) {
            case 'oldest':
                $ordersQuery->orderBy('placed_at')->orderBy('created_at');
                break;
            case 'total_high':
                $ordersQuery->orderByDesc('total_amount')->orderByDesc('placed_at');
                break;
            case 'total_low':
                $ordersQuery->orderBy('total_amount')->orderByDesc('placed_at');
                break;
            case 'latest':
            default:
                $ordersQuery->orderByDesc('placed_at')->orderByDesc('created_at');
                break;
        }

        $orders = $ordersQuery
            ->paginate(10)
            ->appends($request->only('status', 'sort'));

        return view('web.orders.index', compact(
            'orders',
            'statusOptions',
            'sortOptions',
            'activeStatus',
            'activeSort',
        ));
    }

    public function show(string $orderNumber, SyncPendingOrderPaymentStatusAction $syncPendingOrderPaymentStatusAction): View
    {
        $order = auth()->user()
            ->orders()
            ->with(['items.product.images', 'items.productVariant', 'items.review', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $order = $syncPendingOrderPaymentStatusAction->handle($order)?->loadMissing(['items.product.images', 'items.productVariant', 'items.review', 'payment']) ?? $order;

        return view('web.orders.show', compact('order'));
    }

    public function destroy(string $orderNumber): RedirectResponse
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->order_status !== OrderStatus::Completed) {
            return redirect()
                ->route('orders.index')
                ->withErrors(['order' => 'Hanya pesanan yang sudah selesai yang bisa dihapus.']);
        }

        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with([
                'status_title' => 'Pesanan Dihapus',
                'status' => 'Pesanan selesai berhasil dihapus dari riwayat.',
            ]);
    }
}
