<x-layouts.store :title="'Pembayaran - ' . $order->order_number">
    <div class="page-shell space-y-6">
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Pesanan Saya', 'url' => route('orders.index')],
        ['label' => 'Pembayaran'],
    ]" />

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <h1 class="heading-page text-[clamp(1.75rem,2.5vw,2.35rem)]">Selesaikan pembayaran pesananmu</h1>
                <p class="body-copy max-w-2xl">
                    Detail pesanan sudah dikunci silhkaan lanjutkan pembayaran.
                </p>
            </div>
            <a href="{{ route('orders.show', $order->order_number) }}"
                class="btn-secondary rounded-[0.8rem] px-4 py-2.5 text-sm shadow-none">Lihat Detail Order</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="surface-card-strong space-y-5 p-5 sm:p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="surface-soft p-4">
                        <p class="meta-copy">Nomor order</p>
                        <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->order_number }}
                        </p>
                    </div>
                    <div class="surface-soft p-4">
                        <p class="meta-copy">Status</p>
                        <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">
                            {{ $order->order_status->label() }}</p>
                    </div>
                </div>

                <div class="section-divider pt-5">
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Pengiriman</h2>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="meta-copy">Penerima</p>
                            <p class="mt-1 text-sm font-semibold text-[var(--text-primary)]">
                                {{ $order->shipping_recipient_name }}</p>
                            <p class="mt-1 text-xs text-[var(--text-secondary)]">{{ $order->shipping_phone }}</p>
                        </div>
                        <div>
                            <p class="meta-copy">Kurir</p>
                            <p class="mt-1 text-sm font-semibold text-[var(--text-primary)]">
                                {{ $order->shipping_courier_name }} {{ $order->shipping_service_name }}
                            </p>
                            <p class="mt-1 text-xs text-[var(--text-secondary)]">Estimasi
                                {{ $order->shipping_etd_text }}</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs leading-5 text-[var(--text-secondary)]">
                        {{ $order->shipping_address_line }}, {{ $order->shipping_district }},
                        {{ $order->shipping_city }},
                        {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                    </p>
                </div>
            </section>

            <aside class="surface-card-strong space-y-5 p-5 sm:p-6">
                <div>
                    <p class="text-sm font-semibold text-[var(--text-primary)]">Ringkasan pembayaran</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-sm text-[var(--text-secondary)]">Total</span>
                        <strong
                            class="text-2xl font-bold text-[var(--text-primary)]">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="section-divider pt-4 text-sm text-[var(--text-secondary)]">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span
                            class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->subtotal_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span>Ongkir</span>
                        <span
                            class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if ((float) $order->discount_amount > 0)
                        <div class="mt-3 flex items-center justify-between">
                            <span>Voucher {{ $order->voucher_code }}</span>
                            <span
                                class="font-semibold text-emerald-700">-Rp{{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col gap-3">
                    @if ($midtransClientKey && !str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                        <button id="pay-now" type="button"
                            class="btn-primary w-full rounded-[0.85rem] py-3 text-sm shadow-none">Bayar Sekarang</button>
                        <p class="text-xs text-[var(--text-muted)]">Popup pembayaran akan terbuka otomatis, pastikan popup
                            browser tidak diblokir.</p>
                    @else
                        <p class="text-xs text-[var(--text-muted)]">Popup pembayaran tidak tersedia di mode mock lokal.</p>
                    @endif
                </div>
            </aside>
        </div>

        @if (!$midtransClientKey || str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
            <div class="rounded-[1rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Midtrans sandbox belum bisa diakses atau konfigurasi kunci belum lengkap, jadi transaksi ini berjalan dalam
                mode mock lokal. Order tetap tersimpan dan bisa diperiksa dari halaman detail order.
            </div>
        @endif
    </div>

    @if ($midtransClientKey && !str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const pay = () => {
                    window.snap.pay(@json($order->midtrans_snap_token), {
                        onSuccess: () => window.location.href = @json(route('checkout.finish', ['order' => $order->order_number])),
                        onPending: () => window.location.href = @json(route('checkout.unfinish', ['order' => $order->order_number])),
                        onError: () => window.location.href = @json(route('checkout.error', ['order' => $order->order_number])),
                        onClose: () => window.location.href = @json(route('orders.show', $order->order_number)),
                    });
                };

                document.getElementById('pay-now')?.addEventListener('click', pay);
                pay();
            });
        </script>
    @endif
</x-layouts.store>