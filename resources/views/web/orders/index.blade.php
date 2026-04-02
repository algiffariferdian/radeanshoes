<x-layouts.store :title="'Riwayat Pesanan · RadeanShoes'">
    <div class="space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Orders</p>
            <h1 class="text-3xl font-black tracking-tight text-stone-950">Riwayat Pesanan</h1>
        </div>

        @forelse ($orders as $order)
            <article class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-stone-500">{{ $order->order_number }}</p>
                        <h2 class="mt-1 text-xl font-black tracking-tight text-stone-950">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</h2>
                        <p class="mt-2 text-sm text-stone-600">{{ optional($order->placed_at)->format('d M Y H:i') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $order->order_status->label() }}</span>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $order->payment_status->label() }}</span>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-sm text-stone-600">{{ $order->items->count() }} item · {{ $order->shipping_courier_name }} {{ $order->shipping_service_name }}</p>
                    <a href="{{ route('orders.show', $order->order_number) }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700">Detail Order</a>
                </div>
            </article>
        @empty
            <div class="rounded-[1.75rem] border border-dashed border-stone-300 bg-white p-8 text-sm text-stone-600">Belum ada pesanan. Mulai belanja dari katalog untuk membuat order pertama Anda.</div>
        @endforelse

        {{ $orders->links() }}
    </div>
</x-layouts.store>
