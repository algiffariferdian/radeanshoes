<x-layouts.admin :title="$order->order_number.' · Admin RadeanShoes'">
    @php
        $showMarkProcessing = $order->order_status === \App\Support\Enums\OrderStatus::Paid;
        $showMarkShipped = $order->order_status === \App\Support\Enums\OrderStatus::Processing;
        $showMarkCompleted = $order->order_status === \App\Support\Enums\OrderStatus::Shipped;
        $showCancel = ! in_array($order->order_status, [
            \App\Support\Enums\OrderStatus::Completed,
            \App\Support\Enums\OrderStatus::Cancelled,
            \App\Support\Enums\OrderStatus::Expired,
        ], true);
        $hasActions = $showMarkProcessing || $showMarkShipped || $showMarkCompleted || $showCancel;
    @endphp

    <div class="grid gap-8 xl:grid-cols-[1fr_380px]">
        <section class="space-y-6">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Order Detail</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">{{ $order->order_number }}</h1>
                    </div>
                    <div class="flex gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->order_status->badgeClasses() }}">{{ $order->order_status->label() }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">{{ $order->payment_status->label() }}</span>
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

                <div class="mt-6 rounded-[1.25rem] bg-stone-50 p-4 text-sm text-stone-600">
                    <p class="font-semibold text-stone-900">Nomor Resi</p>
                    <p class="mt-1 font-mono text-sm text-stone-700">{{ $order->tracking_number ?: 'Belum diinput' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="text-sm font-semibold text-stone-900" for="notes">Catatan</label>
                        <textarea id="notes" name="notes" rows="4" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                    <button type="submit" class="w-full rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Simpan Catatan</button>
                </form>

                @if ($hasActions)
                    <div class="mt-6 grid gap-3">
                        @if ($showMarkProcessing)
                            <form method="POST" action="{{ route('admin.orders.mark-processing', $order) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Mark Processing</button>
                            </form>
                        @endif

                        @if ($showMarkShipped)
                            <button
                                id="open-ship-modal"
                                type="button"
                                class="w-full rounded-full bg-amber-200 px-5 py-3 text-sm font-semibold text-stone-900"
                            >
                                Mark Shipped
                            </button>
                        @endif

                        @if ($showMarkCompleted)
                            <form method="POST" action="{{ route('admin.orders.mark-completed', $order) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-full bg-emerald-200 px-5 py-3 text-sm font-semibold text-emerald-900">Mark Completed</button>
                            </form>
                        @endif

                        @if ($showCancel)
                            <form method="POST" action="{{ route('admin.orders.mark-cancelled', $order) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-full bg-rose-100 px-5 py-3 text-sm font-semibold text-rose-700">Batalkan Order</button>
                            </form>
                        @endif
                    </div>
                @else
                    <p class="mt-6 rounded-[1.25rem] border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
                        Tidak ada aksi lanjutan untuk order dengan status {{ $order->order_status->label() }}.
                    </p>
                @endif
            </div>

            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black tracking-tight text-stone-950">Payment Log</h2>
                <div class="mt-4 space-y-3 text-sm text-stone-600">
                    @if ($order->payment)
                        @forelse ($order->payment->logs as $log)
                            <div class="rounded-[1.25rem] bg-stone-50 p-3">
                                <p class="font-semibold text-stone-900">{{ $log->source }}</p>
                                <p>{{ optional($log->created_at)->format('d M Y H:i:s') }}</p>
                            </div>
                        @empty
                            <p>Belum ada payment log.</p>
                        @endforelse
                    @else
                        <p>Belum ada data pembayaran.</p>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    @if ($showMarkShipped)
        <div id="ship-modal" class="fixed inset-0 z-50 hidden bg-stone-950/60 px-4 py-10">
            <div class="mx-auto max-w-md rounded-[1.75rem] bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Shipment</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Input Nomor Resi</h2>
                    </div>
                    <button
                        type="button"
                        data-close-ship-modal
                        class="rounded-full border border-stone-200 px-3 py-2 text-xs font-semibold text-stone-600"
                    >
                        Tutup
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.orders.mark-shipped', $order) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-stone-900" for="ship-tracking-number">Nomor Resi</label>
                        <input
                            id="ship-tracking-number"
                            name="tracking_number"
                            value="{{ old('tracking_number', $order->tracking_number) }}"
                            class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm {{ $errors->has('tracking_number') ? 'border-rose-400' : 'border-stone-300' }}"
                            placeholder="Contoh: JNE1234567890"
                        >
                        @error('tracking_number')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            data-close-ship-modal
                            class="w-full rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="w-full rounded-full bg-amber-200 px-5 py-3 text-sm font-semibold text-stone-900"
                        >
                            Simpan dan Shipped
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('ship-modal');
                const openButton = document.getElementById('open-ship-modal');
                const trackingInput = document.getElementById('ship-tracking-number');

                if (!modal || !openButton) {
                    return;
                }

                const openModal = () => {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                    trackingInput?.focus();
                };

                const closeModal = () => {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                };

                openButton.addEventListener('click', openModal);

                modal.querySelectorAll('[data-close-ship-modal]').forEach((button) => {
                    button.addEventListener('click', closeModal);
                });

                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });

                if (@json($errors->has('tracking_number'))) {
                    openModal();
                }
            });
        </script>
    @endif
</x-layouts.admin>
