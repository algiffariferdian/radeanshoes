<x-layouts.store :title="$order->order_number . ' - RadeanShoes'">
    @php
        $orderPlacedAt = $order->placed_at ?? $order->created_at;
        $orderPlacedLabel = $orderPlacedAt ? $orderPlacedAt->translatedFormat('d M Y, H:i') . ' WIB' : '-';
        $totalQuantity = (int) round((float) ($order->total_quantity ?? $order->items->sum('qty')));
        $itemsCount = (int) ($order->items_count ?? $order->items->count());
        $paymentType = str((string) ($order->payment?->payment_type ?? ''))->replace('_', ' ')->headline()->toString();
        $progressIndex = match ($order->order_status->value) {
            'pending_payment', 'expired' => 1,
            'paid', 'processing' => 2,
            'shipped' => 3,
            'completed' => 4,
            'cancelled' => $order->payment_status->value === 'paid' ? 2 : 1,
            default => 1,
        };
        $progressSteps = [
            ['label' => 'Dibuat', 'helper' => $orderPlacedLabel],
            ['label' => 'Dibayar', 'helper' => $order->payment_status->label()],
            ['label' => 'Dikirim', 'helper' => $order->tracking_number ?: ($order->shipping_courier_name ?: 'Menunggu pengiriman')],
            ['label' => 'Selesai', 'helper' => $order->order_status === \App\Support\Enums\OrderStatus::Completed ? 'Pesanan diterima' : ''],
        ];
        $isCancelled = in_array($order->order_status->value, ['cancelled', 'expired'], true);
    @endphp

    <div class="space-y-[var(--space-lg)]">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders.index')],
            ['label' => $order->order_number],
        ]" />

        <section class="rounded-[0.95rem] border border-[var(--border-soft)] bg-white">
            <div
                class="flex flex-col gap-[var(--space-sm)] px-[var(--space-md)] py-[var(--space-md)] lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[var(--text-muted)]">
                        Detail Pesanan
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1
                            class="text-[clamp(1.65rem,2.6vw,2.35rem)] font-semibold tracking-tight text-[var(--text-primary)]">
                            {{ $order->order_number }}
                        </h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--text-secondary)]">
                        <span>Dibuat {{ $orderPlacedLabel }}</span>
                        <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                        <span>{{ number_format($itemsCount, 0, ',', '.') }} produk /
                            {{ number_format($totalQuantity, 0, ',', '.') }} item</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $order->order_status->badgeClasses() }}">
                        {{ $order->order_status->label() }}
                    </span>
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                        {{ $order->payment_status->label() }}
                    </span>
                </div>
            </div>

            <div class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)]">
                <div class="grid gap-[var(--space-sm)] text-center sm:grid-cols-3 sm:justify-items-center">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Total</p>
                        <p class="mt-2 text-lg font-semibold text-[var(--text-primary)]">
                            Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Metode Pembayaran</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                            {{ $paymentType ?: 'Menunggu konfirmasi' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Tujuan</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                            {{ $order->shipping_city }}, {{ $order->shipping_province }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)]">
                <div class="grid gap-[var(--space-sm)] sm:grid-cols-2 lg:grid-cols-4 sm:justify-items-center">
                    @foreach ($progressSteps as $index => $step)
                        @php
                            $isActive = ($index + 1) <= $progressIndex;
                        @endphp
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full border text-xs font-semibold {{ $isActive ? 'border-[#a9cdb8] bg-[#eef7f1] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-muted)]' }}">
                                    {{ $index + 1 }}
                                </span>
                                <p
                                    class="text-sm font-semibold {{ $isActive ? 'text-[var(--text-primary)]' : 'text-[var(--text-muted)]' }}">
                                    {{ $step['label'] }}
                                </p>
                            </div>
                            @if (!empty($step['helper']))
                                <p class="pl-10 text-xs text-[var(--text-secondary)]">
                                    {{ $step['helper'] }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($isCancelled)
                    <div
                        class="mt-4 rounded-[0.8rem] border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                        Status akhir: {{ $order->order_status->label() }}.
                    </div>
                @endif
            </div>
        </section>

        <div class="grid gap-[var(--space-md)] lg:grid-cols-[minmax(0,1fr)_minmax(0,0.62fr)]">
            <section class="space-y-[var(--space-md)]">
                <section class="rounded-[0.9rem] border border-[var(--border-soft)] bg-white">
                    <div class="flex items-center justify-between px-[var(--space-md)] py-[var(--space-sm)]">
                        <h2 class="text-base font-semibold text-[var(--text-primary)]">Produk</h2>
                        <span class="text-xs text-[var(--text-muted)]">{{ number_format($itemsCount, 0, ',', '.') }}
                            produk</span>
                    </div>
                    <div class="border-t border-[var(--border-soft)]">
                        @foreach ($order->items as $item)
                            <article
                                class="border-b border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)] last:border-b-0">
                                <div class="grid gap-4 sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:items-start">
                                    <div
                                        class="h-20 w-20 overflow-hidden rounded-[0.75rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]">
                                        @if ($item->product?->primary_image_url)
                                            <img src="{{ $item->product->primary_image_url }}"
                                                alt="{{ $item->product_name_snapshot }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div
                                                class="flex h-full items-center justify-center text-3xl font-semibold text-[var(--text-muted)]">
                                                !</div>
                                        @endif
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-[var(--text-primary)]">
                                                    {{ $item->product_name_snapshot }}
                                                </p>
                                                <p class="mt-1 text-xs text-[var(--text-secondary)]">
                                                    {{ $item->variant_size_snapshot }} / {{ $item->variant_color_snapshot }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-[var(--text-secondary)]">
                                            <span>{{ $item->qty }} x
                                                Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}</span>
                                            <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                                            <span>Subtotal item</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">
                                            Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                @if ($order->order_status === \App\Support\Enums\OrderStatus::Completed)
                                    @if ($item->product)
                                        <div
                                            class="mt-[var(--space-sm)] border-t border-[var(--border-soft)] pt-[var(--space-sm)]">
                                            <form method="POST"
                                                action="{{ route('products.reviews.store', $item->product) }}"
                                                class="space-y-3">
                                                @csrf
                                                <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @for ($rating = 5; $rating >= 1; $rating--)
                                                        <label class="cursor-pointer"
                                                            for="rating-{{ $item->id }}-{{ $rating }}">
                                                            <input id="rating-{{ $item->id }}-{{ $rating }}"
                                                                type="radio" name="rating" value="{{ $rating }}"
                                                                class="sr-only peer"
                                                                @checked(old('rating', $item->review?->rating) == $rating)>
                                                            <span
                                                                class="inline-flex items-center gap-1 rounded-[0.6rem] border border-[var(--border-soft)] bg-white px-3 py-2 text-xs font-semibold text-[var(--text-primary)] transition peer-checked:border-[var(--accent-primary)] peer-checked:bg-[var(--accent-soft)] peer-checked:text-[var(--accent-primary)]">
                                                                <x-store.icon name="star" class="h-3.5 w-3.5" />
                                                                {{ $rating }}
                                                            </span>
                                                        </label>
                                                    @endfor
                                                </div>
                                                <textarea name="review" rows="3" class="textarea-field text-sm"
                                                    placeholder="Tulis ulasan singkat (opsional)">{{ old('review', $item->review?->review) }}</textarea>
                                                <div class="flex justify-end">
                                                    <button type="submit"
                                                        class="btn-primary rounded-[0.6rem] px-4 py-2 text-sm shadow-none">
                                                        {{ $item->review ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <p class="mt-[var(--space-sm)] text-xs text-[var(--text-muted)]">Produk sudah tidak
                                            tersedia untuk ulasan.</p>
                                    @endif
                                @endif
                            </article>
                        @endforeach
                    </div>
                    @if ($order->order_status !== \App\Support\Enums\OrderStatus::Completed)
                        <div
                            class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)] text-xs text-[var(--text-muted)]">
                            Ulasan tersedia setelah pesanan selesai.
                        </div>
                    @endif
                </section>

                <section class="rounded-[0.9rem] border border-[var(--border-soft)] bg-white">
                    <div class="flex items-center justify-between px-[var(--space-md)] py-[var(--space-sm)]">
                        <h2 class="text-base font-semibold text-[var(--text-primary)]">Pengiriman</h2>
                        <span class="text-xs text-[var(--text-muted)]">{{ $order->shipping_courier_name }}</span>
                    </div>
                    <div
                        class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)] grid gap-[var(--space-sm)] sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Penerima
                            </p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                                {{ $order->shipping_recipient_name }}
                            </p>
                            <p class="mt-1 text-xs text-[var(--text-secondary)]">{{ $order->shipping_phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Alamat</p>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">
                                {{ $order->shipping_address_line }}, {{ $order->shipping_district }},
                                {{ $order->shipping_city }}, {{ $order->shipping_province }}
                                {{ $order->shipping_postal_code }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)] grid gap-[var(--space-sm)] sm:grid-cols-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Layanan</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                                {{ $order->shipping_courier_name }} {{ $order->shipping_service_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Estimasi</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                                {{ $order->shipping_etd_text ?: 'Menunggu konfirmasi' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">No. Resi</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                                {{ $order->tracking_number ?: 'Belum tersedia' }}
                            </p>
                        </div>
                    </div>
                    @if ($order->notes)
                        <div
                            class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)] text-sm text-[var(--text-secondary)]">
                            {{ $order->notes }}
                        </div>
                    @endif
                </section>
            </section>

            <aside class="space-y-[var(--space-md)] lg:sticky lg:top-28 lg:self-start">
                <section class="rounded-[0.9rem] border border-[var(--border-soft)] bg-white">
                    <div class="px-[var(--space-md)] py-[var(--space-sm)]">
                        <h2 class="text-base font-semibold text-[var(--text-primary)]">Ringkasan Pembayaran</h2>
                        <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                            <div class="flex items-center justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold text-[var(--text-primary)]">
                                    Rp{{ number_format((float) $order->subtotal_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Ongkir</span>
                                <span class="font-semibold text-[var(--text-primary)]">
                                    Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}
                                </span>
                            </div>
                            @if ((float) $order->discount_amount > 0)
                                <div class="flex items-center justify-between text-sm text-[var(--text-secondary)]">
                                    <span>Voucher {{ $order->voucher_code }}</span>
                                    <span class="font-semibold text-[var(--discount)]">
                                        -Rp{{ number_format((float) $order->discount_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)]">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[var(--text-primary)]">Total</span>
                            <span class="text-lg font-semibold text-[var(--text-primary)]">
                                Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @if ($order->order_status === \App\Support\Enums\OrderStatus::PendingPayment && $order->payment?->expired_at)
                        <div class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)]">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-[var(--text-secondary)]">Sisa waktu bayar</span>
                                <span class="font-semibold text-[var(--warning)]" data-countdown-expiry
                                    data-expired-at="{{ $order->payment->expired_at->getTimestamp() * 1000 }}">
                                    --
                                </span>
                            </div>
                        </div>
                    @endif
                </section>

                <section class="rounded-[0.9rem] border border-[var(--border-soft)] bg-white">
                    <div class="px-[var(--space-md)] py-[var(--space-sm)]">
                        <h2 class="text-base font-semibold text-[var(--text-primary)]">Pembayaran</h2>
                        <div class="mt-3 space-y-3 text-sm text-[var(--text-secondary)]">
                            <div class="flex items-center justify-between">
                                <span>Metode</span>
                                <span class="font-semibold text-[var(--text-primary)]">
                                    {{ $paymentType ?: 'Belum dipilih' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Status</span>
                                <span class="font-semibold text-[var(--text-primary)]">
                                    {{ $order->payment_status->label() }}
                                </span>
                            </div>
                            @if ($order->payment?->expired_at)
                                <div class="flex items-center justify-between">
                                    <span>Batas bayar</span>
                                    <span class="font-semibold text-[var(--text-primary)]">
                                        {{ $order->payment->expired_at->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 space-y-2">
                            @if ($order->order_status === \App\Support\Enums\OrderStatus::PendingPayment && config('services.midtrans.client_key') && !str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                                <button id="pay-order" type="button" class="btn-primary w-full">Lanjutkan Pembayaran</button>
                            @endif

                            @if ($order->order_status === \App\Support\Enums\OrderStatus::Shipped)
                                <form method="POST" action="{{ route('orders.complete', $order->order_number) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary w-full">Pesanan Diterima</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    @if ($order->order_status === \App\Support\Enums\OrderStatus::PendingPayment && config('services.midtrans.client_key') && !str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            document.getElementById('pay-order')?.addEventListener('click', () => {
                window.snap.pay(@json($order->midtrans_snap_token), {
                    onSuccess: () => window.location.href = @json(route('checkout.finish', ['order' => $order->order_number])),
                    onPending: () => window.location.href = @json(route('checkout.unfinish', ['order' => $order->order_number])),
                    onError: () => window.location.href = @json(route('checkout.error', ['order' => $order->order_number])),
                    onClose: () => window.location.href = @json(route('orders.show', $order->order_number)),
                });
            });

            if (@json(request()->boolean('pay'))) {
                window.addEventListener('load', () => {
                    document.getElementById('pay-order')?.click();
                }, { once: true });
            }
        </script>
    @endif

    <script>
        const liveClock = document.querySelector('[data-live-clock]');
        if (liveClock) {
            const updateClock = () => {
                const now = new Date();
                const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                liveClock.textContent = `Diperbarui ${time}`;
            };
            updateClock();
            setInterval(updateClock, 60000);
        }

        const countdown = document.querySelector('[data-countdown-expiry]');
        if (countdown) {
            const expiry = Number(countdown.dataset.expiredAt);
            const updateCountdown = () => {
                if (!expiry || Number.isNaN(expiry)) {
                    countdown.textContent = '--';
                    return;
                }
                const now = Date.now();
                const diff = Math.max(0, expiry - now);
                const totalSeconds = Math.floor(diff / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                countdown.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            };
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    </script>
</x-layouts.store>
