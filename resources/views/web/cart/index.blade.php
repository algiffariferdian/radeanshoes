<x-layouts.store :title="'Keranjang Belanja - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Keranjang'],
        ]" />

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <section class="space-y-4">
                <div class="surface-card p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="heading-eyebrow">Keranjang belanja</p>
                            <h1 class="heading-page text-[clamp(1.75rem,2.8vw,2.4rem)]">Ringkas, jelas, dan siap checkout</h1>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Perbarui jumlah item, cek varian yang dipilih, lalu lanjut ke pembayaran.</p>
                        </div>
                        <div class="surface-soft px-4 py-3 text-sm text-[var(--text-secondary)]">
                            <span class="font-semibold text-[var(--text-primary)]">{{ $cart->items->sum('qty') }}</span> item aktif di keranjang
                        </div>
                    </div>
                </div>

                @forelse ($cart->items as $item)
                    @php($variant = $item->productVariant)
                    @php($product = $variant->product)
                    <article class="surface-card-strong p-4 sm:p-5">
                        <div class="grid gap-4 sm:grid-cols-[132px_minmax(0,1fr)]">
                            <div class="relative overflow-hidden rounded-[1rem] bg-[var(--surface-soft)]">
                                <div class="absolute left-3 top-3">
                                    <input type="checkbox" checked disabled class="h-4 w-4 rounded border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]">
                                </div>
                                @if ($variant->primary_image_url ?? $product->primary_image_url)
                                    <img src="{{ $variant->primary_image_url ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="aspect-[4/4.3] h-full w-full object-cover">
                                @else
                                    <div class="flex aspect-[4/4.3] items-center justify-center text-4xl font-semibold text-[var(--text-muted)]">!</div>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-2">
                                        <a href="{{ route('products.show', $product) }}" class="text-lg font-semibold leading-snug text-[var(--text-primary)] hover:text-[var(--accent-primary)]">
                                            {{ $product->name }}
                                        </a>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="badge-neutral">Ukuran EU {{ $variant->size }}</span>
                                            <span class="badge-neutral">{{ $variant->color }}</span>
                                            <span class="badge-accent">Tersisa {{ $variant->stock_qty }}</span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="price-current">Rp{{ number_format((float) $item->unit_price_snapshot, 0, ',', '.') }}</p>
                                        @if ($variant->hasDiscount())
                                            <p class="price-strike mt-1">Rp{{ number_format((float) $variant->originalPrice(), 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div x-data="quantityStepper({{ $item->qty }}, 1, {{ max(1, $variant->stock_qty) }})" class="flex items-center gap-3">
                                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-3">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex items-center rounded-[0.9rem] border border-[var(--border-strong)] bg-white">
                                                <button type="button" class="flex h-11 w-11 items-center justify-center text-lg text-[var(--text-secondary)]" @click="decrease()">-</button>
                                                <input name="qty" x-model="value" min="1" max="{{ $variant->stock_qty }}" class="w-16 border-0 text-center text-sm font-semibold text-[var(--text-primary)] focus:ring-0">
                                                <button type="button" class="flex h-11 w-11 items-center justify-center text-lg text-[var(--text-secondary)]" @click="increase()">+</button>
                                            </div>
                                            <button type="submit" class="btn-secondary px-4 py-2.5">Update</button>
                                        </form>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <p class="text-sm text-[var(--text-secondary)]">Subtotal item: <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $item->unit_price_snapshot * $item->qty, 0, ',', '.') }}</span></p>
                                        <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger px-4 py-2.5">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <x-store.empty-state
                        icon="cart"
                        title="Keranjang masih kosong"
                        body="Tambahkan produk dari katalog untuk mulai belanja dan melanjutkan checkout."
                    >
                        <div class="mt-5">
                            <a href="{{ route('products.index') }}" class="btn-primary">Belanja Produk</a>
                        </div>
                    </x-store.empty-state>
                @endforelse
            </section>

            <aside class="space-y-4 xl:sticky xl:top-28 xl:self-start">
                <div class="surface-card p-5">
                    <p class="heading-eyebrow">Ringkasan belanja</p>
                    <h2 class="mt-2 text-xl font-bold text-[var(--text-primary)]">Checkout summary</h2>

                    <div class="mt-5 space-y-3 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center justify-between">
                            <span>Jumlah item</span>
                            <span class="font-semibold text-[var(--text-primary)]">{{ $cart->items->sum('qty') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Estimasi ongkir</span>
                            <span class="text-[var(--text-muted)]">Pilih kurir saat checkout</span>
                        </div>
                    </div>

                    <div class="section-divider mt-5 pt-5">
                        <div class="surface-soft p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">Gunakan Voucher</p>
                                    <p class="mt-1 text-xs leading-5 text-[var(--text-secondary)]">Masukkan kode voucher di halaman checkout untuk melihat potongan yang berlaku.</p>
                                </div>
                                <span class="badge-discount">Aktif di checkout</span>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider mt-5 pt-5">
                        <p class="text-sm font-semibold text-[var(--text-primary)]">Catatan UX</p>
                        <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">
                            Pada MVP ini, checkout memproses seluruh item yang ada di keranjang. Checkbox ditampilkan sebagai penanda item aktif.
                        </p>
                    </div>

                    <a href="{{ $cart->items->isEmpty() ? route('products.index') : route('checkout.index') }}" class="btn-primary mt-6 w-full">
                        {{ $cart->items->isEmpty() ? 'Belanja Produk' : 'Lanjut ke Checkout' }}
                    </a>
                    <a href="{{ route('products.index') }}" class="btn-secondary mt-3 w-full">Tambah Produk Lain</a>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.store>
