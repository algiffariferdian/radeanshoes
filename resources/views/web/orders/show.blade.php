<x-layouts.store :title="$order->order_number.' · RadeanShoes'">
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
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
                        <p class="font-semibold text-stone-900">Penerima</p>
                        <p>{{ $order->shipping_recipient_name }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-stone-900">Alamat</p>
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
                <h2 class="text-2xl font-black tracking-tight text-stone-950">Ringkasan</h2>
                <div class="mt-6 space-y-3 text-sm text-stone-600">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold text-stone-900">Rp{{ number_format((float) $order->subtotal_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Ongkir</span>
                        <span class="font-semibold text-stone-900">Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-stone-100 pt-3 text-base">
                        <span class="font-semibold text-stone-900">Total</span>
                        <span class="font-black text-stone-950">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black tracking-tight text-stone-950">Fulfillment</h2>
                <div class="mt-4 space-y-2 text-sm text-stone-600">
                    <p><span class="font-semibold text-stone-900">Kurir:</span> {{ $order->shipping_courier_name }} {{ $order->shipping_service_name }}</p>
                    <p><span class="font-semibold text-stone-900">Estimasi:</span> {{ $order->shipping_etd_text }}</p>
                    <p><span class="font-semibold text-stone-900">Resi:</span> {{ $order->tracking_number ?: 'Belum diinput admin' }}</p>
                </div>

                @if ($order->order_status === \App\Support\Enums\OrderStatus::PendingPayment && config('services.midtrans.client_key') && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                    <button id="pay-order" type="button" class="mt-6 w-full rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Lanjutkan Pembayaran</button>
                @endif
            </div>
        </aside>
    </div>

    @if ($order->order_status === \App\Support\Enums\OrderStatus::PendingPayment && config('services.midtrans.client_key') && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            document.getElementById('pay-order')?.addEventListener('click', () => {
                window.snap.pay(@json($order->midtrans_snap_token), {
                    onSuccess: () => window.location.href = @json(route('checkout.finish', ['order' => $order->order_number])),
                    onPending: () => window.location.href = @json(route('checkout.unfinish', ['order' => $order->order_number])),
                    onError: () => window.location.href = @json(route('checkout.error', ['order' => $order->order_number])),
                });
            });
        </script>
    @endif
</x-layouts.store>
