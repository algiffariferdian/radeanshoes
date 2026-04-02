<x-layouts.admin :title="'Admin Dashboard · RadeanShoes'">
    <div class="space-y-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Operations</p>
            <h1 class="text-3xl font-black tracking-tight text-stone-950">Dashboard</h1>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $label => $value)
                <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-stone-500">{{ str($label)->replace('_', ' ')->headline() }}</p>
                    <p class="mt-3 text-4xl font-black tracking-tight text-stone-950">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-black tracking-tight text-stone-950">Order Terbaru</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-stone-700">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Order</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td class="py-3 font-semibold text-stone-900">{{ $order->order_number }}</td>
                                <td class="py-3 text-stone-600">{{ $order->user->name }}</td>
                                <td class="py-3 text-stone-600">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $order->order_status->label() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
