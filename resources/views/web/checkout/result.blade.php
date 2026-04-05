<x-layouts.store :title="'Hasil Pembayaran - RadeanShoes'">
    @php
        $copy = match ($state) {
            'finish' => ['title' => 'Pembayaran berhasil', 'body' => 'Pembayaran sudah diterima. Pesanan kamu segera diproses dan akan muncul di status pesanan.'],
            'unfinish' => ['title' => 'Pembayaran belum diselesaikan', 'body' => 'Order tetap tersimpan. Kamu bisa melanjutkan pembayaran dari detail order selama transaksi belum kedaluwarsa.'],
            default => ['title' => 'Terjadi kendala saat pembayaran', 'body' => 'Silakan cek kembali status order. Jika pembayaran belum berhasil, kamu masih bisa mencoba lagi dari detail order.'],
        };
    @endphp

    <div class="mx-auto max-w-3xl space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders.index')],
            ['label' => 'Hasil Pembayaran'],
        ]" />

        <div class="surface-card-strong p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full {{ $state === 'finish' ? 'bg-emerald-50 text-emerald-700' : ($state === 'unfinish' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                    <x-store.icon :name="$state === 'finish' ? 'shield' : ($state === 'unfinish' ? 'wallet' : 'package')" class="h-6 w-6" />
                </div>
                <div>
                    <p class="heading-eyebrow">Hasil pembayaran</p>
                    <h1 class="heading-page text-[clamp(1.7rem,2.5vw,2.2rem)]">{{ $copy['title'] }}</h1>
                    <p class="mt-3 text-sm leading-7 text-[var(--text-secondary)]">{{ $copy['body'] }}</p>
                </div>
            </div>

            @if ($order)
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="surface-soft p-4">
                        <p class="meta-copy">Nomor order</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $order->order_number }}</p>
                    </div>
                    <div class="surface-soft p-4">
                        <p class="meta-copy">Status order</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $order->order_status->label() }}</p>
                    </div>
                    <div class="surface-soft p-4">
                        <p class="meta-copy">Status payment</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $order->payment_status->label() }}</p>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ $order ? route('orders.show', $order->order_number) : route('orders.index') }}" class="btn-primary">Lihat Order</a>
                <a href="{{ route('products.index') }}" class="btn-secondary">Kembali ke Katalog</a>
            </div>
        </div>
    </div>
</x-layouts.store>
