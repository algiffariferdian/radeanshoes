<x-layouts.admin :title="$order->order_number.' · Admin RadeanShoes'">
    <div class="grid gap-8 xl:grid-cols-[1fr_380px]">
        <section class="space-y-6">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Order Detail</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">{{ $order->order_number }}</h1>
                    </div>
                    <div class="flex gap-2">
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $order->order_status->label() }}</span>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $order->payment_status->label() }}</span>
                    </div>
                </div>
                <div class="mt-6 grid gap-4 text-sm text-stone-600 sm:grid-cols-2">
                    <div>
                        <p class="font-semibold text-stone-900">Customer</p>
                        <p>{{ $order->user->name }}</p>
                        <p>{{ $order->user->email }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-stone-900">Alamat Kirim</p>
                        <p>{{ $order->shipping_recipient_name }} · {{ $order->shipping_phone }}</p>
                        <p>{{ $order->shipping_address_line }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black tracking-tight text-stone-950">Item Pesanan</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                            <div>
                                <p class="font-semibold text-stone-950">{{ $item->product_name_snapshot }}</p>
                                <p class="text-sm text-stone-600">{{ $item->variant_size_snapshot }} / {{ $item->variant_color_snapshot }} · SKU {{ $item->sku_snapshot }}</p>
                            </div>
                            <p class="text-sm font-semibold text-stone-700">{{ $item->qty }}x · Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black tracking-tight text-stone-950">Fulfillment Actions</h2>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="text-sm font-semibold text-stone-900" for="tracking_number">Nomor Resi</label>
                        <input id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-stone-900" for="notes">Catatan</label>
                        <textarea id="notes" name="notes" rows="4" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                    <button type="submit" class="w-full rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Simpan Detail Order</button>
                </form>

                <div class="mt-6 grid gap-3">
                    <form method="POST" action="{{ route('admin.orders.mark-processing', $order) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Mark Processing</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.mark-shipped', $order) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-full bg-amber-200 px-5 py-3 text-sm font-semibold text-stone-900">Mark Shipped</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.mark-completed', $order) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-full bg-emerald-200 px-5 py-3 text-sm font-semibold text-emerald-900">Mark Completed</button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.mark-cancelled', $order) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-full bg-rose-100 px-5 py-3 text-sm font-semibold text-rose-700">Batalkan Order</button>
                    </form>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black tracking-tight text-stone-950">Payment Log</h2>
                <div class="mt-4 space-y-3 text-sm text-stone-600">
                    @forelse ($order->payment->logs as $log)
                        <div class="rounded-[1.25rem] bg-stone-50 p-3">
                            <p class="font-semibold text-stone-900">{{ $log->source }}</p>
                            <p>{{ optional($log->created_at)->format('d M Y H:i:s') }}</p>
                        </div>
                    @empty
                        <p>Belum ada payment log.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</x-layouts.admin>
