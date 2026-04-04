<x-layouts.store :title="$order->order_number.' - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders.index')],
            ['label' => $order->order_number],
        ]" />

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <section class="space-y-5">
                <div class="surface-card-strong p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="heading-eyebrow">Status order</p>
                            <h1 class="heading-page text-[clamp(1.7rem,2.5vw,2.35rem)]">{{ $order->order_number }}</h1>
                            <p class="mt-2 text-sm text-[var(--text-secondary)]">Dibuat pada {{ optional($order->placed_at)->translatedFormat('d M Y, H:i') }} WIB</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->order_status->badgeClasses() }}">{{ $order->order_status->label() }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">{{ $order->payment_status->label() }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Total pembayaran</p>
                            <p class="mt-2 text-xl font-bold text-[var(--text-primary)]">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Kurir</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->shipping_courier_name }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Resi</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->tracking_number ?: 'Belum diinput admin' }}</p>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-6">
                    <h2 class="heading-section">Alamat Pengiriman</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Penerima</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $order->shipping_recipient_name }}</p>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $order->shipping_phone }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Alamat lengkap</p>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">{{ $order->shipping_address_line }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-6">
                    <h2 class="heading-section">Item Pesanan</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($order->items as $item)
                            <article class="flex gap-4 rounded-[1rem] border border-[var(--border-soft)] p-4">
                                <div class="h-20 w-20 overflow-hidden rounded-[0.9rem] bg-[var(--surface-soft)]">
                                    @if ($item->product?->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name_snapshot }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-4xl font-semibold text-[var(--text-muted)]">!</div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $item->product_name_snapshot }}</p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $item->variant_size_snapshot }} / {{ $item->variant_color_snapshot }}</p>
                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm">
                                        <span class="text-[var(--text-secondary)]">{{ $item->qty }} x Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}</span>
                                        <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                                    </div>

                                    @if ($order->order_status === \App\Support\Enums\OrderStatus::Completed)
                                        @if ($item->product)
                                            <div class="mt-4 border-t border-[var(--border-soft)] pt-4">
                                                <form method="POST" action="{{ route('products.reviews.store', $item->product) }}" class="space-y-3">
                                                    @csrf
                                                    <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        @for ($rating = 5; $rating >= 1; $rating--)
                                                            <label class="cursor-pointer" for="rating-{{ $item->id }}-{{ $rating }}">
                                                                <input
                                                                    id="rating-{{ $item->id }}-{{ $rating }}"
                                                                    type="radio"
                                                                    name="rating"
                                                                    value="{{ $rating }}"
                                                                    class="sr-only peer"
                                                                    @checked(old('rating', $item->review?->rating) == $rating)
                                                                >
                                                                <span class="inline-flex items-center gap-1 rounded-[0.6rem] border border-[var(--border-soft)] bg-white px-3 py-2 text-xs font-semibold text-[var(--text-primary)] transition peer-checked:border-[var(--accent-primary)] peer-checked:bg-[var(--accent-soft)] peer-checked:text-[var(--accent-primary)]">
                                                                    <x-store.icon name="star" class="h-3.5 w-3.5" />
                                                                    {{ $rating }}
                                                                </span>
                                                            </label>
                                                        @endfor
                                                    </div>
                                                    <textarea
                                                        name="review"
                                                        rows="3"
                                                        class="textarea-field text-sm"
                                                        placeholder="Tulis ulasan singkat (opsional)"
                                                    >{{ old('review', $item->review?->review) }}</textarea>
                                                    <div class="flex justify-end">
                                                        <button type="submit" class="btn-primary rounded-[0.6rem] px-4 py-2 text-sm shadow-none">
                                                            {{ $item->review ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            <p class="mt-4 text-sm text-[var(--text-muted)]">Produk sudah tidak tersedia untuk ulasan.</p>
                                        @endif
                                    @else
                                        <p class="mt-4 text-sm text-[var(--text-muted)]">Ulasan tersedia setelah pesanan selesai.</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <aside class="space-y-4 xl:sticky xl:top-28 xl:self-start">
                <div class="surface-card p-5">
                    <p class="heading-eyebrow">Ringkasan pembayaran</p>
                    <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->subtotal_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Ongkir</span>
                            <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="section-divider mt-4 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[var(--text-primary)]">Total</span>
                            <span class="text-xl font-bold text-[var(--text-primary)]">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ((float) $order->discount_amount > 0)
                            <div class="mt-3 flex items-center justify-between text-sm text-[var(--text-secondary)]">
                                <span>Voucher {{ $order->voucher_code }}</span>
                                <span class="font-semibold text-emerald-700">-Rp{{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="surface-card p-5">
                    <p class="heading-eyebrow">Status pengiriman</p>
                    <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--accent-soft)] text-[var(--accent-primary)]">
                                <x-store.icon name="package" class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="font-semibold text-[var(--text-primary)]">Diproses toko</p>
                                <p>Status pesanan akan bergerak otomatis setelah pembayaran berhasil.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--surface-soft)] text-[var(--text-secondary)]">
                                <x-store.icon name="truck" class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="font-semibold text-[var(--text-primary)]">Kurir {{ $order->shipping_courier_name }}</p>
                                <p>Estimasi {{ $order->shipping_etd_text }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($order->order_status === \App\Support\Enums\OrderStatus::PendingPayment && config('services.midtrans.client_key') && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                        <button id="pay-order" type="button" class="btn-primary mt-5 w-full">Lanjutkan Pembayaran</button>
                    @endif
                </div>
            </aside>
        </div>
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
