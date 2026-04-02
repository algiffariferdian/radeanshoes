<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderTrackingRequest;
use App\Models\Order;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim($request->string('status')->toString());

        $orders = Order::query()
            ->with(['user', 'payment'])
            ->when($status !== '', fn ($query) => $query->where('order_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product.images', 'items.productVariant', 'payment.logs']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(UpdateOrderTrackingRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return back()->with('status', 'Detail order berhasil diperbarui.');
    }

    public function markProcessing(Order $order): RedirectResponse
    {
        if ($order->order_status !== OrderStatus::Paid) {
            return back()->withErrors(['order' => 'Hanya order yang sudah dibayar yang dapat diproses.']);
        }

        $order->update(['order_status' => OrderStatus::Processing]);

        return back()->with('status', 'Order dipindahkan ke status processing.');
    }

    public function markShipped(Order $order): RedirectResponse
    {
        if (! in_array($order->order_status, [OrderStatus::Paid, OrderStatus::Processing], true)) {
            return back()->withErrors(['order' => 'Order belum siap dikirim.']);
        }

        if (! $order->tracking_number) {
            return back()->withErrors(['tracking_number' => 'Isi nomor resi terlebih dahulu sebelum mengirim pesanan.']);
        }

        $order->update([
            'order_status' => OrderStatus::Shipped,
            'shipped_at' => now(),
        ]);

        return back()->with('status', 'Order dipindahkan ke status shipped.');
    }

    public function markCompleted(Order $order): RedirectResponse
    {
        if ($order->order_status !== OrderStatus::Shipped) {
            return back()->withErrors(['order' => 'Hanya order yang sudah dikirim yang dapat diselesaikan.']);
        }

        $order->update([
            'order_status' => OrderStatus::Completed,
            'completed_at' => now(),
        ]);

        return back()->with('status', 'Order dipindahkan ke status completed.');
    }

    public function markCancelled(Order $order): RedirectResponse
    {
        if ($order->order_status !== OrderStatus::PendingPayment) {
            return back()->withErrors(['order' => 'Pada MVP ini pembatalan admin hanya diizinkan untuk order yang belum dibayar.']);
        }

        $order->update([
            'order_status' => OrderStatus::Cancelled,
            'payment_status' => PaymentStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        return back()->with('status', 'Order berhasil dibatalkan.');
    }
}
