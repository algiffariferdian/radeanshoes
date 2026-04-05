@php
    $formatCurrency = fn ($amount) => 'Rp' . number_format((float) $amount, 0, ',', '.');

    $orderToneMap = [
        'pending_payment' => [
            'classes' => 'border-[#ead9a5] bg-[#fff9ec] text-[#8b6a1f]',
            'note' => 'Pesanan dibuat dan menunggu pembayaran.',
        ],
        'paid' => [
            'classes' => 'border-[#cfe1d6] bg-[#eef7f1] text-[#2a744a]',
            'note' => 'Pembayaran diterima. Menunggu pesanan dikirim.',
        ],
        'processing' => [
            'classes' => 'border-[#d8e3dc] bg-[#f3f7f5] text-[#335247]',
            'note' => 'Pesanan sedang disiapkan sebelum pengiriman.',
        ],
        'shipped' => [
            'classes' => 'border-[#d7e0db] bg-[#f4f7f6] text-[#29453a]',
            'note' => 'Pesanan sedang dalam perjalanan ke alamat tujuan.',
        ],
        'completed' => [
            'classes' => 'border-[#cadecf] bg-[#edf7f0] text-[#1f6a3f]',
            'note' => 'Pesanan selesai dan tersimpan di riwayat.',
        ],
        'cancelled' => [
            'classes' => 'border-[#ead5d1] bg-[#faf4f2] text-[#995047]',
            'note' => 'Pesanan dibatalkan dan tidak dilanjutkan.',
        ],
        'expired' => [
            'classes' => 'border-[#dde1de] bg-[#f4f5f4] text-[#66736c]',
            'note' => 'Batas waktu pembayaran pesanan sudah berakhir.',
        ],
    ];

    $paymentToneMap = [
        'pending' => 'border-[#ead9a5] bg-[#fff9ec] text-[#8b6a1f]',
        'paid' => 'border-[#cfe1d6] bg-[#eef7f1] text-[#2a744a]',
        'failed' => 'border-[#ead5d1] bg-[#faf4f2] text-[#995047]',
        'expired' => 'border-[#dde1de] bg-[#f4f5f4] text-[#66736c]',
        'cancelled' => 'border-[#ead5d1] bg-[#faf4f2] text-[#995047]',
    ];

    $progressSteps = ['Dibuat', 'Dibayar', 'Dikirim', 'Selesai'];
@endphp

@if ($activeStatus !== '' || $activeSearch !== '')
    <section class="overflow-hidden rounded-[1rem] border border-[var(--border-soft)] bg-white shadow-[0_1px_2px_rgba(16,24,20,0.04)]">
        <div class="flex flex-col gap-3 px-4 py-3 text-sm text-[var(--text-secondary)] sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex flex-wrap items-center gap-2">
                <span>{{ number_format($orders->total(), 0, ',', '.') }} pesanan ditemukan</span>

                @if ($activeStatusLabel)
                    <span class="inline-flex items-center rounded-[0.65rem] bg-[#f7f9f8] px-2.5 py-1 text-xs font-medium text-[var(--text-primary)] ring-1 ring-[var(--border-soft)]">
                        Status: {{ $activeStatusLabel }}
                    </span>
                @endif

                @if ($activeSearch !== '')
                    <span class="inline-flex items-center rounded-[0.65rem] bg-[#f7f9f8] px-2.5 py-1 text-xs font-medium text-[var(--text-primary)] ring-1 ring-[var(--border-soft)]">
                        Cari: {{ $activeSearch }}
                    </span>
                @endif
            </div>

            <button type="button"
                class="text-left text-sm font-semibold text-[var(--accent-primary)]"
                @click="resetFilters()">
                Reset filter
            </button>
        </div>
    </section>
@endif

<section class="overflow-hidden rounded-[1rem] border border-[var(--border-soft)] bg-white shadow-[0_1px_2px_rgba(16,24,20,0.04)]">
    <div class="flex items-center justify-between border-b border-[var(--border-soft)] px-4 py-4 sm:px-6">
        <div>
            <h2 class="text-lg font-semibold tracking-[-0.02em] text-[var(--text-primary)]">Daftar Pesanan</h2>
        </div>
        <p class="hidden text-sm text-[var(--text-secondary)] sm:block">
            {{ number_format($orders->total(), 0, ',', '.') }} data
        </p>
    </div>

    @forelse ($orders as $order)
        @php
            $orderTone = $orderToneMap[$order->order_status->value] ?? [
                'classes' => 'border-[#dde1de] bg-[#f4f5f4] text-[#66736c]',
                'note' => 'Status pesanan sedang diperbarui.',
            ];
            $paymentTone = $paymentToneMap[$order->payment_status->value] ?? 'border-[#dde1de] bg-[#f4f5f4] text-[#66736c]';
            $activeProgress = match ($order->order_status->value) {
                'pending_payment', 'expired' => 1,
                'paid', 'processing' => 2,
                'shipped' => 3,
                'completed' => 4,
                'cancelled' => $order->payment_status->value === 'paid' ? 2 : 1,
                default => 1,
            };
            $allItems = $order->items;
            $hasScrollableItems = $allItems->count() > 4;
            $itemPages = $allItems->chunk(4)->values();
            $totalQuantity = (int) round((float) ($order->total_quantity ?? $order->items->sum('qty')));
            $paymentType = str((string) ($order->payment?->payment_type ?? ''))->replace('_', ' ')->headline()->toString();
        @endphp

        <article class="px-4 py-5 sm:px-6 sm:py-6 {{ $loop->last ? '' : 'border-b border-[var(--border-soft)]' }}">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('orders.show', $order->order_number) }}"
                                class="text-[1.02rem] font-semibold tracking-[-0.02em] text-[var(--text-primary)] transition hover:text-[var(--accent-primary)]">
                                {{ $order->order_number }}
                            </a>

                            <span class="inline-flex items-center rounded-[0.65rem] border px-2.5 py-1 text-xs font-semibold {{ $orderTone['classes'] }}">
                                {{ $order->order_status->label() }}
                            </span>

                            <span class="inline-flex items-center rounded-[0.65rem] border px-2.5 py-1 text-xs font-semibold {{ $paymentTone }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-[var(--text-secondary)]">
                            <span>{{ optional($order->placed_at ?? $order->created_at)->translatedFormat('d M Y, H:i') }} WIB</span>
                            <span>{{ number_format((int) $order->items_count, 0, ',', '.') }} produk</span>
                            <span>{{ number_format($totalQuantity, 0, ',', '.') }} item</span>
                            @if ($paymentType !== '')
                                <span>{{ $paymentType }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-left lg:text-right">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Total Pembayaran</p>
                        <p class="mt-1 text-[1.2rem] font-semibold leading-none tracking-[-0.03em] text-[var(--text-primary)]">
                            {{ $formatCurrency($order->total_amount) }}
                        </p>
                        @if ((float) $order->discount_amount > 0)
                            <p class="mt-2 text-xs font-medium text-[#2f8f5b]">
                                Hemat {{ $formatCurrency($order->discount_amount) }}
                                @if ($order->voucher_code)
                                    dengan {{ $order->voucher_code }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="hidden rounded-[0.85rem] border border-[var(--border-soft)] bg-[#fbfcfb] px-4 py-3 md:block">
                    <div class="flex items-center object-center justify-center gap-2">
                        @foreach ($progressSteps as $index => $step)
                            @php
                                $isReached = $index < $activeProgress;
                                $isConnectorReached = $index < ($activeProgress - 1);
                            @endphp

                            <div class="flex min-w-0 flex-1 items-center gap-2">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-[11px] font-semibold {{ $isReached ? 'border-[#a9cdb8] bg-[#eef7f1] text-[var(--accent-primary)]' : 'border-[#dfe5e1] bg-white text-[var(--text-muted)]' }}">
                                    {{ $index + 1 }}
                                </span>
                                <span class="truncate text-xs font-medium {{ $isReached ? 'text-[var(--text-primary)]' : 'text-[var(--text-muted)]' }}">
                                    {{ $step }}
                                </span>
                                @if (! $loop->last)
                                    <span class="hidden h-px flex-1 rounded-full md:block {{ $isConnectorReached ? 'bg-[#bfd4c6]' : 'bg-[#e4e8e5]' }}"></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[0.85rem] border border-[var(--border-soft)] bg-[#fbfcfb] px-4 py-3 text-sm text-[var(--text-secondary)] md:hidden">
                    {{ $orderTone['note'] }}
                </div>

                <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_18.5rem]">
                    <div class="min-w-0 rounded-[0.9rem] border border-[var(--border-soft)] bg-[#fbfcfb] p-2 sm:p-4"
                        x-data="{ move(delta) { this.$refs.rail?.scrollBy({ left: this.$refs.rail.clientWidth * delta, behavior: 'smooth' }); } }">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Produk Dipesan</p>
                                <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                    {{ number_format((int) $order->items_count, 0, ',', '.') }} produk / {{ number_format($totalQuantity, 0, ',', '.') }} item
                                </p>
                            </div>

                            @if ($hasScrollableItems)
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-[0.65rem] border border-[var(--border-soft)] bg-white text-[var(--text-secondary)] transition hover:text-[var(--text-primary)]"
                                        @click="move(-1)">
                                        <x-store.icon name="chevron-left" class="h-4 w-4" />
                                    </button>
                                    <button type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-[0.65rem] border border-[var(--border-soft)] bg-white text-[var(--text-secondary)] transition hover:text-[var(--text-primary)]"
                                        @click="move(1)">
                                        <x-store.icon name="chevron-right" class="h-4 w-4" />
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($hasScrollableItems)
                            <div x-ref="rail" class="flex overflow-x-auto scroll-smooth pb-1 snap-x snap-mandatory [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                @foreach ($itemPages as $page)
                                    <div class="min-w-full shrink-0 snap-start pr-0">
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            @foreach ($page as $item)
                                                <div class="rounded-[0.8rem] border border-[var(--border-soft)] bg-white p-3">
                                                    <div class="flex gap-3">
                                                        <div class="h-[60px] w-[60px] shrink-0 overflow-hidden rounded-[0.7rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]">
                                                            @if ($item->product?->primary_image_url)
                                                                <img src="{{ $item->product->primary_image_url }}"
                                                                    alt="{{ $item->product_name_snapshot ?? $item->product?->name }}"
                                                                    class="h-full w-full object-cover">
                                                            @else
                                                                <div class="flex h-full items-center justify-center text-[11px] font-semibold tracking-[0.08em] text-[var(--text-muted)]">IMG</div>
                                                            @endif
                                                        </div>

                                                        <div class="min-w-0 flex-1">
                                                            @if ($item->product)
                                                                <a href="{{ route('products.show', $item->product) }}"
                                                                    class="line-clamp-2 text-sm font-semibold leading-5 text-[var(--text-primary)] transition hover:text-[var(--accent-primary)]">
                                                                    {{ $item->product_name_snapshot ?? $item->product->name }}
                                                                </a>
                                                            @else
                                                                <p class="line-clamp-2 text-sm font-semibold leading-5 text-[var(--text-primary)]">
                                                                    {{ $item->product_name_snapshot }}
                                                                </p>
                                                            @endif

                                                            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                                                {{ $item->variant_color_snapshot ?: 'Warna standar' }}
                                                                @if ($item->variant_size_snapshot)
                                                                    / {{ $item->variant_size_snapshot }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 flex items-center justify-between gap-3 border-t border-[var(--border-soft)] pt-3 text-sm">
                                                        <span class="text-[var(--text-secondary)]">{{ number_format((int) $item->qty, 0, ',', '.') }} x {{ $formatCurrency($item->unit_price) }}</span>
                                                        <span class="font-semibold text-[var(--text-primary)]">{{ $formatCurrency($item->line_total) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="grid gap-3 {{ $allItems->count() > 1 ? 'sm:grid-cols-2' : '' }}">
                                @foreach ($allItems as $item)
                                    <div class="rounded-[0.8rem] border border-[var(--border-soft)] bg-white p-3">
                                        <div class="flex gap-3">
                                            <div class="h-[60px] w-[60px] shrink-0 overflow-hidden rounded-[0.7rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]">
                                                @if ($item->product?->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}"
                                                        alt="{{ $item->product_name_snapshot ?? $item->product?->name }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full items-center justify-center text-[11px] font-semibold tracking-[0.08em] text-[var(--text-muted)]">IMG</div>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                @if ($item->product)
                                                    <a href="{{ route('products.show', $item->product) }}"
                                                        class="line-clamp-2 text-sm font-semibold leading-5 text-[var(--text-primary)] transition hover:text-[var(--accent-primary)]">
                                                        {{ $item->product_name_snapshot ?? $item->product->name }}
                                                    </a>
                                                @else
                                                    <p class="line-clamp-2 text-sm font-semibold leading-5 text-[var(--text-primary)]">
                                                        {{ $item->product_name_snapshot }}
                                                    </p>
                                                @endif

                                                <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                                    {{ $item->variant_color_snapshot ?: 'Warna standar' }}
                                                    @if ($item->variant_size_snapshot)
                                                        / {{ $item->variant_size_snapshot }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-[var(--border-soft)] pt-3 text-sm">
                                            <span class="text-[var(--text-secondary)]">{{ number_format((int) $item->qty, 0, ',', '.') }} x {{ $formatCurrency($item->unit_price) }}</span>
                                            <span class="text-right font-semibold text-[var(--text-primary)]">{{ $formatCurrency($item->line_total) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($order->notes)
                            <div class="mt-3 rounded-[0.8rem] border border-[var(--border-soft)] bg-white px-4 py-3 text-sm text-[var(--text-secondary)]">
                                {{ $order->notes }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-[0.9rem] border border-[var(--border-soft)] bg-[#fbfcfb] p-4">
                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Pengiriman</p>
                                    <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                                        {{ $order->shipping_courier_name }} {{ $order->shipping_service_name }}
                                    </p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                        Estimasi {{ $order->shipping_etd_text }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Tujuan</p>
                                    <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $order->shipping_city }}</p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $order->shipping_recipient_name }}</p>
                                </div>

                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Pembayaran</p>
                                    <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                                        {{ $order->payment_status->label() }}
                                    </p>
                                    @if ($order->order_status->value === 'pending_payment' && $order->payment?->expired_at)
                                        <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                            Batas bayar {{ $order->payment->expired_at->translatedFormat('d M Y, H:i') }} WIB
                                        </p>
                                    @elseif ($paymentType !== '')
                                        <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $paymentType }}</p>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Resi</p>
                                    <p class="mt-2 break-all text-sm font-semibold text-[var(--text-primary)]">
                                        {{ $order->tracking_number ?: 'Belum tersedia' }}
                                    </p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $orderTone['note'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 border-t border-[var(--border-soft)] pt-4">
                            @if ($order->order_status->value === 'pending_payment')
                                <a href="{{ route('orders.show', ['orderNumber' => $order->order_number, 'pay' => 1]) }}"
                                    class="btn-primary rounded-[0.7rem] px-4 py-2.5 text-sm shadow-none">
                                    Bayar Sekarang
                                </a>
                            @endif

                            @if ($order->order_status->value === 'shipped')
                                <form method="POST" action="{{ route('orders.complete', $order->order_number) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary rounded-[0.7rem] px-4 py-2.5 text-sm shadow-none">
                                        Pesanan Diterima
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('orders.show', $order->order_number) }}"
                                class="btn-secondary rounded-[0.7rem] px-4 py-2.5 text-sm shadow-none">
                                Lihat Detail
                            </a>

                            @if ($order->order_status->value === 'completed')
                                <form method="POST" action="{{ route('orders.destroy', $order->order_number) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-danger rounded-[0.7rem] px-4 py-2.5 text-sm shadow-none">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="px-4 py-14 text-center sm:px-6">
            <div class="mx-auto max-w-[32rem]">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[var(--text-muted)]">Belum Ada Pesanan</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="btn-primary rounded-[0.75rem] px-5 py-3 text-sm shadow-none">
                        Mulai Belanja
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</section>

<div class="pt-1" data-orders-pagination>
    {{ $orders->links() }}
</div>
