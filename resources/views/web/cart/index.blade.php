<x-layouts.store :title="'Keranjang Belanja - RadeanShoes'">
    @php
        $itemCount = $cart->items->sum('qty');
        $uniqueCount = $cart->items->count();
        $cartItemsPayload = $cart->items->map(fn($item) => [
            'id' => $item->id,
            'qty' => (int) $item->qty,
            'unitPrice' => (float) $item->unit_price_snapshot,
            'maxQty' => max(1, (int) $item->productVariant->stock_qty),
            'updateUrl' => route('cart.update', $item),
        ])->values();
    @endphp

    <div x-data="cartPage(@js($cartItemsPayload))" class="space-y-6">
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Keranjang'],
    ]" />

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold text-[var(--text-primary)]">Keranjang Belanja</h1>
                <p class="max-w-2xl text-sm text-[var(--text-secondary)]">Cek kembali produk yang kamu pilih sebelum
                    lanjut ke pembayaran.</p>
            </div>
            <div
                class="inline-flex items-center gap-2 rounded-[0.7rem] border border-[var(--border-soft)] bg-white px-3 py-2 text-sm text-[var(--text-secondary)]">
                <span class="font-semibold text-[var(--text-primary)]" x-text="totalQty">{{ $itemCount }}</span> item
                <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                <span x-text="Object.keys(items).length">{{ $uniqueCount }}</span> produk
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <section class="space-y-4">
                @forelse ($cart->items as $item)
                @php($variant = $item->productVariant)
                @php($product = $variant->product)
                <article class="border-b border-[var(--border-soft)] pb-4" x-data="{ itemId: {{ $item->id }} }">
                    <div class="grid gap-4 sm:grid-cols-[108px_minmax(0,1fr)]">
                        <div class="overflow-hidden rounded-[0.7rem] border border-[var(--border-soft)] bg-[#f6f8f7]">
                            @if ($variant->primary_image_url ?? $product->primary_image_url)
                                <img src="{{ $variant->primary_image_url ?? $product->primary_image_url }}"
                                    alt="{{ $product->name }}" class="aspect-square h-full w-full object-cover">
                            @else
                                <div
                                    class="flex aspect-square items-center justify-center text-3xl font-semibold text-[var(--text-muted)]">
                                    !</div>
                            @endif
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="space-y-1">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="text-sm font-semibold leading-6 text-[var(--text-primary)] hover:text-[var(--accent-primary)]">
                                        {{ $product->name }}
                                    </a>
                                    <div class="flex flex-wrap gap-2 text-xs text-[var(--text-secondary)]">
                                        <span class="rounded-[0.4rem] bg-[#f2f4f3] px-2 py-1">EU
                                            {{ $variant->size }}</span>
                                        <span
                                            class="rounded-[0.4rem] bg-[#f2f4f3] px-2 py-1">{{ $variant->color }}</span>
                                        <span
                                            class="rounded-[0.4rem] bg-[#e8f5ee] px-2 py-1 text-[var(--accent-primary)]">Stok
                                            {{ $variant->stock_qty }}</span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-base font-semibold text-[var(--text-primary)]">
                                        Rp{{ number_format((float) $item->unit_price_snapshot, 0, ',', '.') }}</p>
                                    @if ($variant->hasDiscount())
                                        <p class="mt-1 text-xs text-[var(--text-muted)] line-through">
                                            Rp{{ number_format((float) $variant->originalPrice(), 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex items-center rounded-[0.6rem] border border-[var(--border-soft)] bg-white">
                                            <button type="button"
                                                class="flex h-9 w-9 items-center justify-center text-lg text-[var(--text-secondary)]"
                                                @click="changeQty(itemId, -1)">-</button>
                                            <input name="qty" x-model.number="items[itemId].qty" min="1"
                                                max="{{ $variant->stock_qty }}"
                                                @input.debounce.300ms="setQty(itemId, $event.target.value)"
                                                class="w-12 border-0 text-center text-sm font-semibold text-[var(--text-primary)] focus:ring-0">
                                            <button type="button"
                                                class="flex h-9 w-9 items-center justify-center text-lg text-[var(--text-secondary)]"
                                                @click="changeQty(itemId, 1)">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 text-sm">
                                    <span class="text-[var(--text-secondary)]">Subtotal</span>
                                    <span class="font-semibold text-[var(--text-primary)]"
                                        x-text="'Rp' + formatCurrency((items[itemId]?.qty ?? 0) * (items[itemId]?.unitPrice ?? 0))">
                                        Rp{{ number_format((float) $item->unit_price_snapshot * $item->qty, 0, ',', '.') }}
                                    </span>
                                    <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs font-semibold text-[var(--error)]">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                @empty
                <x-store.empty-state icon="cart" title="Keranjang masih kosong"
                    body="Tambahkan produk dari katalog untuk mulai belanja dan melanjutkan checkout.">
                    <div class="mt-5">
                        <a href="{{ route('products.index') }}" class="btn-primary rounded-[0.6rem] shadow-none">Belanja
                            Produk</a>
                    </div>
                </x-store.empty-state>
                @endforelse
            </section>

            <aside class="space-y-4 xl:sticky xl:top-28 xl:self-start">
                <div class="rounded-[0.75rem] border border-[var(--border-soft)] bg-white p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-[var(--text-primary)]">Ringkasan</p>
                        <p class="text-xs text-[var(--text-muted)]" x-text="totalQty + ' item'">{{ $itemCount }} item
                        </p>
                    </div>
                    <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[var(--text-primary)]"
                                x-text="'Rp' + formatCurrency(subtotalAmount)">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Estimasi ongkir</span>
                            <span class="text-[var(--text-muted)]">Pilih di checkout</span>
                        </div>
                    </div>
                    <div class="mt-4 border-t border-[var(--border-soft)] pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[var(--text-primary)]">Total sementara</span>
                            <span class="text-lg font-semibold text-[var(--text-primary)]"
                                x-text="'Rp' + formatCurrency(subtotalAmount)">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-[var(--text-muted)]">Total final dihitung saat memilih kurir.</p>
                    </div>
                    <a href="{{ $cart->items->isEmpty() ? route('products.index') : route('checkout.index') }}"
                        class="btn-primary mt-5 w-full rounded-[0.6rem] py-2.5 text-sm shadow-none">
                        {{ $cart->items->isEmpty() ? 'Belanja Produk' : 'Lanjut ke Checkout' }}
                    </a>
                    <a href="{{ route('products.index') }}"
                        class="btn-secondary mt-3 w-full rounded-[0.6rem] py-2.5 text-sm shadow-none">Tambah Produk
                        Lain</a>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.store>