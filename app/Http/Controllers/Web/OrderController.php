<?php

namespace App\Http\Controllers\Web;

use App\Actions\Checkout\SyncPendingOrderPaymentStatusAction;
use App\Http\Controllers\Controller;
use App\Support\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View|JsonResponse
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
        $activeSearch = str($request->string('q')->toString())->squish()->toString();
        $validStatuses = $statusOptions->pluck('value')->all();
        $validSorts = $sortOptions->pluck('value')->all();
        if ($activeStatus !== '' && ! in_array($activeStatus, $validStatuses, true)) {
            $activeStatus = '';
        }
        if (! in_array($activeSort, $validSorts, true)) {
            $activeSort = 'latest';
        }

        $summaryQuery = auth()->user()->orders();
        $statusCounts = (clone $summaryQuery)
            ->selectRaw('order_status, COUNT(*) as aggregate')
            ->groupBy('order_status')
            ->pluck('aggregate', 'order_status');

        $totalOrdersCount = (int) $statusCounts->sum();

        $statusOptions = $statusOptions
            ->map(fn (array $status) => [
                ...$status,
                'count' => (int) ($statusCounts[$status['value']] ?? 0),
            ]);

        $ordersQuery = auth()->user()
            ->orders()
            ->with([
                'items.product.images',
                'payment',
            ])
            ->withCount('items')
            ->withSum('items as total_quantity', 'qty');

        if ($activeStatus !== '') {
            $ordersQuery->where('order_status', $activeStatus);
        }

        if ($activeSearch !== '') {
            $ordersQuery->where(function ($query) use ($activeSearch): void {
                $query
                    ->where('order_number', 'like', "%{$activeSearch}%")
                    ->orWhere('shipping_recipient_name', 'like', "%{$activeSearch}%")
                    ->orWhere('shipping_city', 'like', "%{$activeSearch}%")
                    ->orWhereHas('items', function ($itemQuery) use ($activeSearch): void {
                        $itemQuery
                            ->where('product_name_snapshot', 'like', "%{$activeSearch}%")
                            ->orWhere('sku_snapshot', 'like', "%{$activeSearch}%");
                    });
            });
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
            ->paginate(5)
            ->appends($request->only('status', 'sort', 'q'));

        $activeStatusLabel = $activeStatus !== ''
            ? $statusOptions->pluck('label', 'value')->get($activeStatus)
            : null;

        $viewData = compact(
            'orders',
            'statusOptions',
            'sortOptions',
            'activeStatus',
            'activeSort',
            'activeSearch',
            'activeStatusLabel',
            'totalOrdersCount',
        );

        if ($request->boolean('ajax') || $request->ajax()) {
            return response()->json([
                'listing' => view('web.orders.partials.listing', $viewData)->render(),
            ]);
        }

        return view('web.orders.index', $viewData);
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

    public function complete(string $orderNumber): RedirectResponse
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->order_status !== OrderStatus::Shipped) {
            return back()->withErrors([
                'order' => 'Pesanan hanya bisa diselesaikan saat statusnya sedang dikirim.',
            ]);
        }

        $order->update([
            'order_status' => OrderStatus::Completed,
            'completed_at' => now(),
        ]);

        return back()->with([
            'status_title' => 'Pesanan Selesai',
            'status' => 'Pesanan berhasil dikonfirmasi selesai.',
        ]);
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
