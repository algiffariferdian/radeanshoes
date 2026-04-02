<x-layouts.store :title="'Daftar Pesanan - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya'],
        ]" />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="heading-eyebrow">Pesanan saya</p>
                <h1 class="heading-page text-[clamp(1.75rem,2.8vw,2.4rem)]">Pantau status order dengan lebih jelas</h1>
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Setiap pesanan menampilkan status order, status pembayaran, dan ringkasan pengiriman.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn-secondary">Belanja Lagi</a>
        </div>

        @forelse ($orders as $order)
            <article class="surface-card-strong p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge-neutral">{{ $order->order_number }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->order_status->badgeClasses() }}">{{ $order->order_status->label() }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">{{ $order->payment_status->label() }}</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-[var(--text-primary)]">Total Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</h2>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ optional($order->placed_at)->translatedFormat('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>

                    <div class="grid gap-3 text-sm text-[var(--text-secondary)] sm:grid-cols-3 lg:min-w-[420px]">
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Jumlah item</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->items->count() }} produk</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Kurir</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->shipping_courier_name }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Layanan</p>
                            <p class="mt-2 text-base font-semibold text-[var(--text-primary)]">{{ $order->shipping_service_name }}</p>
                        </div>
                    </div>
                </div>

                <div class="section-divider mt-5 pt-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-[var(--text-secondary)]">Pengiriman ke {{ $order->shipping_city }} • {{ $order->shipping_recipient_name }}</p>
                        <a href="{{ route('orders.show', $order->order_number) }}" class="btn-primary px-4 py-2.5">Lihat Detail Order</a>
                    </div>
                </div>
            </article>
        @empty
            <x-store.empty-state
                icon="package"
                title="Belum ada pesanan"
                body="Setelah kamu checkout, status pesanan akan muncul di halaman ini."
            >
                <div class="mt-5">
                    <a href="{{ route('products.index') }}" class="btn-primary">Mulai Belanja</a>
                </div>
            </x-store.empty-state>
        @endforelse

        <div class="pt-2">
            {{ $orders->links() }}
        </div>
    </div>
</x-layouts.store>
