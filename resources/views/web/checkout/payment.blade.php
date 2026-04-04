<x-layouts.store :title="'Pembayaran - '.$order->order_number">
    <div class="mx-auto max-w-4xl space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders.index')],
            ['label' => 'Pembayaran'],
        ]" />

        <div class="surface-card-strong p-6 sm:p-8">
            <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <div>
                    <p class="heading-eyebrow">Pembayaran</p>
                    <h1 class="heading-page text-[clamp(1.75rem,2.5vw,2.3rem)]">Order berhasil dibuat dan siap dibayar</h1>
                    <p class="mt-3 text-sm leading-7 text-[var(--text-secondary)]">
                        Sistem sudah membuat snapshot order dan menyiapkan transaksi pembayaran. Jika Midtrans sandbox aktif, popup pembayaran akan muncul otomatis.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Nomor order</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->order_number }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Status</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->order_status->label() }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('orders.show', $order->order_number) }}" class="btn-secondary">Lihat Detail Order</a>
                        @if ($midtransClientKey && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                            <button id="pay-now" type="button" class="btn-primary">Bayar Sekarang</button>
                        @endif
                    </div>
                </div>

                <aside class="surface-soft p-5">
                    <p class="text-sm font-semibold text-[var(--text-primary)]">Ringkasan pembayaran</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-sm text-[var(--text-secondary)]">Total</span>
                        <strong class="text-2xl font-bold text-[var(--text-primary)]">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong>
                    </div>
                    <div class="section-divider mt-4 pt-4">
                        <div class="space-y-3 text-sm text-[var(--text-secondary)]">
                            <div class="flex items-center justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->subtotal_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Ongkir</span>
                                <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if ((float) $order->discount_amount > 0)
                                <div class="flex items-center justify-between">
                                    <span>Voucher {{ $order->voucher_code }}</span>
                                    <span class="font-semibold text-emerald-700">-Rp{{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="section-divider mt-4 pt-4">
                            <div class="space-y-3 text-sm text-[var(--text-secondary)]">
                            <div class="flex items-center gap-3">
                                <x-store.icon name="credit-card" class="h-4 w-4 text-[var(--accent-primary)]" />
                                Bayar lewat Midtrans Snap
                            </div>
                            <div class="flex items-center gap-3">
                                <x-store.icon name="shield" class="h-4 w-4 text-[var(--accent-primary)]" />
                                Status akan tersinkron saat pembayaran berhasil
                            </div>
                        </div>
                        </div>
                    </div>
                </aside>
            </div>

            @if (! $midtransClientKey || str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                <div class="mt-6 rounded-[1rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Midtrans sandbox belum bisa diakses atau konfigurasi kunci belum lengkap, jadi transaksi ini berjalan dalam mode mock lokal. Order tetap tersimpan dan bisa diperiksa dari halaman detail order.
                </div>
            @endif
        </div>
    </div>

    @if ($midtransClientKey && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
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
