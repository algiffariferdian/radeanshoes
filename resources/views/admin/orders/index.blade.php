<x-layouts.admin :title="'Orders · Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Fulfillment</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Orders</h1>
            </div>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-3">
                <input name="status" value="{{ $status }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm" placeholder="pending_payment / paid / shipped">
                <button type="submit" class="rounded-full bg-stone-950 px-4 py-2 text-sm font-semibold text-stone-50">Filter</button>
            </form>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Order</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Order Status</th>
                            <th class="pb-3">Payment</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="py-4 font-semibold text-stone-900">{{ $order->order_number }}</td>
                                <td class="py-4 text-stone-600">{{ $order->user->name }}</td>
                                <td class="py-4 text-stone-600">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                <td class="py-4"><span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $order->order_status->label() }}</span></td>
                                <td class="py-4"><span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $order->payment_status->label() }}</span></td>
                                <td class="py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
