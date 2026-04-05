<x-layouts.store :title="'Daftar Pesanan - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya'],
        ]" />

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="heading-page text-[clamp(1.75rem,2.8vw,2.4rem)]">Pesanan Saya</h1>
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">
                    Riwayat pesanan tersaji ringkas agar status dan total mudah dipantau.
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="btn-secondary rounded-[0.8rem] px-4 py-2.5 text-sm shadow-none">Belanja Lagi</a>
        </div>

        <div class="flex flex-col gap-4 rounded-[0.9rem] border border-[var(--border-soft)] bg-white p-4 sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('orders.index', array_filter(['sort' => $activeSort])) }}"
                        class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $activeStatus === '' ? 'bg-[var(--accent-soft)] text-[var(--accent-primary)]' : 'bg-[#f2f4f3] text-[var(--text-secondary)] hover:bg-[#e8eeeb]' }}">
                        Semua
                    </a>
                    @foreach ($statusOptions as $status)
                        <a href="{{ route('orders.index', array_filter(['status' => $status['value'], 'sort' => $activeSort])) }}"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $activeStatus === $status['value'] ? 'bg-[var(--accent-soft)] text-[var(--accent-primary)]' : 'bg-[#f2f4f3] text-[var(--text-secondary)] hover:bg-[#e8eeeb]' }}">
                            {{ $status['label'] }}
                        </a>
                    @endforeach
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $activeStatus }}">
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--text-muted)]">Urutkan</label>
                    <select name="sort" class="select-field h-10 min-w-[180px] rounded-[0.7rem] px-3 py-2 text-sm" onchange="this.form.submit()">
                        @foreach ($sortOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($activeSort === $option['value'])>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @forelse ($orders as $order)
            <article class="rounded-[0.95rem] border border-[var(--border-soft)] bg-white p-4 sm:p-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge-neutral">{{ $order->order_number }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->order_status->badgeClasses() }}">{{ $order->order_status->label() }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">{{ $order->payment_status->label() }}</span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)]">{{ optional($order->placed_at ?? $order->created_at)->translatedFormat('d M Y, H:i') }} WIB</p>
                </div>

                <div class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_240px]">
                    <div class="space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--text-muted)]">Produk</p>
                        <div class="space-y-3">
                            @foreach ($order->items->take(2) as $item)
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 overflow-hidden rounded-[0.7rem] border border-[var(--border-soft)] bg-[#f6f8f7]">
                                        @if ($item->product?->primary_image_url)
                                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-xs font-semibold text-[var(--text-muted)]">IMG</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-[var(--text-primary)]">{{ $item->product_name_snapshot ?? $item->product?->name }}</p>
                                        <p class="mt-1 text-xs text-[var(--text-secondary)]">
                                            {{ $item->variant_color_snapshot }} / {{ $item->variant_size_snapshot }} - {{ $item->qty }}x
                                        </p>
                                    </div>
                                    <p class="ml-auto text-xs font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        @if ($order->items->count() > 2)
                            <p class="text-xs text-[var(--text-muted)]">+ {{ $order->items->count() - 2 }} produk lainnya</p>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--text-muted)]">Pengiriman</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $order->shipping_city }}</p>
                            <p class="mt-1 text-xs text-[var(--text-secondary)]">
                                {{ $order->shipping_courier_name }} {{ $order->shipping_service_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--text-muted)]">Total</p>
                            <p class="mt-2 text-lg font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2" x-data="{ confirmDelete: false }">
                            <a href="{{ route('orders.show', $order->order_number) }}" class="btn-secondary rounded-[0.7rem] px-4 py-2 text-sm shadow-none">Detail</a>
                            @if ($order->order_status === \App\Support\Enums\OrderStatus::Completed)
                                <button type="button" class="btn-danger rounded-[0.7rem] px-4 py-2 text-sm shadow-none" x-show="!confirmDelete"
                                    @click="confirmDelete = true">Hapus</button>
                                <div class="flex items-center gap-2" x-show="confirmDelete" x-cloak>
                                    <button type="button" class="btn-secondary rounded-[0.7rem] px-4 py-2 text-sm shadow-none"
                                        @click="confirmDelete = false">Batal</button>
                                    <form method="POST" action="{{ route('orders.destroy', $order->order_number) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger rounded-[0.7rem] px-4 py-2 text-sm shadow-none">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <x-store.empty-state icon="package" title="Belum ada pesanan"
                body="Setelah kamu checkout, status pesanan akan muncul di halaman ini.">
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
