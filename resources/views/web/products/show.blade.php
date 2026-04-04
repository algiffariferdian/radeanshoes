<x-layouts.store :title="$product->name . ' - RadeanShoes'">
    @php
        $variantPayload = $product->variants->map(fn($variant) => [
            'id' => $variant->id,
            'size' => $variant->size,
            'color' => $variant->color,
            'stock_qty' => $variant->stock_qty,
            'original_price' => (float) $variant->originalPrice(),
            'effective_price' => (float) $variant->effectivePrice(),
            'discount_percentage' => (int) $variant->discount_percentage,
            'images' => $variant->image_urls,
        ])->values();
    @endphp

    <div x-data="productConfigurator({ variants: @js($variantPayload), coverImage: @js($product->cover_image_url), qty: 1 })"
        class="space-y-6">
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Katalog', 'url' => route('products.index')],
        ['label' => $product->category->name, 'url' => route('products.index', ['category' => $product->category_id])],
        ['label' => $product->name],
    ]" />

        <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-2 lg:max-w-[525px] w-full mx-auto">
                <div class="overflow-hidden rounded-[0.75rem] border border-[var(--border-soft)] bg-white">
                    <div class="aspect-[4/3] max-h-[1080px] bg-[#f6f8f7]">
                        <template x-if="currentImage">
                            <img x-bind:src="currentImage" alt="{{ $product->name }}"
                                class="block h-full w-full object-fill">
                        </template>
                        <template x-if="!currentImage">
                            <div
                                class="flex h-full items-center justify-center text-5xl font-semibold text-[var(--text-muted)]">
                                !</div>
                        </template>
                    </div>
                </div>

                <div x-show="galleryItems.length > 0" class="grid grid-cols-6 gap-2 lg:grid-cols-7">
                    <template x-for="item in galleryItems" :key="item.id">
                        <button type="button" class="overflow-hidden rounded-[0.6rem] border bg-[#f6f8f7]"
                            x-bind:class="Number(selectedVariantId) === Number(item.id) ? 'border-[var(--accent-primary)]' : 'border-[var(--border-soft)] hover:border-[#c7d2cb]'"
                            @click="selectGalleryItem(item)">
                            <img x-bind:src="item.image"
                                x-bind:alt="item.color && item.size ? (item.color + ' ' + item.size) : 'Preview produk'"
                                class="aspect-square h-full w-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            <div id="purchase" class="space-y-5">
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                        {{ $product->category->name }}
                    </p>
                    <h1 class="text-2xl font-semibold text-[var(--text-primary)]">{{ $product->name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--text-secondary)]">
                        <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count"
                            size="h-3.5 w-3.5" text-class="text-sm text-[var(--text-secondary)]" />
                        <span class="inline-flex items-center gap-1">
                            <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                            {{ number_format($product->sold_count, 0, ',', '.') }} terjual
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                            {{ number_format($product->total_stock, 0, ',', '.') }} stok
                        </span>
                    </div>
                </div>

                <div class="space-y-2 border-y border-[var(--border-soft)] py-4">
                    <div class="flex flex-wrap items-end gap-3">
                        <p class="text-3xl font-semibold text-[var(--text-primary)]"
                            x-text="'Rp' + new Intl.NumberFormat('id-ID').format(currentPrice)"></p>
                        <template x-if="comparePrice">
                            <p class="text-sm text-[var(--text-muted)] line-through"
                                x-text="'Rp' + new Intl.NumberFormat('id-ID').format(comparePrice)"></p>
                        </template>
                        <template x-if="discountPercentage">
                            <span class="badge-discount" x-text="'Hemat ' + discountPercentage + '%'"></span>
                        </template>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)]">Harga mengikuti varian ukuran dan warna.</p>
                </div>

                <div class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Pilih Warna</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="colorOption in colors" :key="colorOption.name">
                                    <button type="button"
                                        class="rounded-[0.6rem] border px-3 py-2 text-sm font-semibold transition"
                                        x-bind:class="selectedColor === colorOption.name ? 'border-[var(--accent-primary)] bg-[var(--accent-soft)] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-primary)] hover:border-[#c7d2cb]'"
                                        @click="selectColor(colorOption.name)">
                                        <span x-text="colorOption.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Pilih Ukuran</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="sizeOption in sizesForSelectedColor()" :key="sizeOption.name">
                                    <button type="button"
                                        class="rounded-[0.6rem] border px-3 py-2 text-sm font-semibold transition"
                                        x-bind:class="selectedSize === sizeOption.name ? 'border-[var(--accent-primary)] bg-[var(--accent-soft)] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-primary)] hover:border-[#c7d2cb]'"
                                        @click="selectSize(sizeOption.name)">
                                        <span x-text="'EU ' + sizeOption.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-4 rounded-[0.65rem] border border-[var(--border-soft)] bg-white px-3 py-3">
                        <div>
                            <p class="text-xs text-[var(--text-muted)]">Stok tersedia</p>
                            <p class="text-sm font-semibold text-[var(--text-primary)]"
                                x-text="availableStock > 0 ? 'Tersisa ' + availableStock : 'Varian ini habis'"></p>
                        </div>
                        <div
                            class="ml-auto flex items-center rounded-[0.6rem] border border-[var(--border-soft)] bg-white">
                            <button type="button"
                                class="flex h-10 w-10 items-center justify-center text-lg text-[var(--text-secondary)]"
                                @click="decrementQty()">-</button>
                            <input type="number" x-model="qty" min="1"
                                class="w-14 border-0 text-center text-sm font-semibold text-[var(--text-primary)] focus:ring-0">
                            <button type="button"
                                class="flex h-10 w-10 items-center justify-center text-lg text-[var(--text-secondary)]"
                                @click="incrementQty()">+</button>
                        </div>
                    </div>
                </div>

                @auth
                    <form method="POST" action="{{ route('cart.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="product_variant_id" x-bind:value="selectedVariantId">
                        <input type="hidden" name="qty" x-bind:value="qty">
                        <input type="hidden" name="buy_now" x-ref="buyNowField" value="0">

                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="submit" class="btn-secondary w-full rounded-[0.6rem] py-2.5 text-sm shadow-none"
                                x-bind:disabled="!selectedVariantId || availableStock < 1"
                                @click="$refs.buyNowField.value = 0">
                                <x-store.icon name="cart" class="h-4 w-4" />
                                Masukkan ke Keranjang
                            </button>
                            <button type="submit" class="btn-primary w-full rounded-[0.6rem] py-2.5 text-sm shadow-none"
                                x-bind:disabled="!selectedVariantId || availableStock < 1"
                                @click="$refs.buyNowField.value = 1">
                                Beli Sekarang
                            </button>
                        </div>
                    </form>
                @else
                    <div
                        class="rounded-[0.65rem] border border-[var(--border-soft)] bg-white p-4 text-sm text-[var(--text-secondary)]">
                        <p class="font-semibold text-[var(--text-primary)]">Login untuk membeli</p>
                        <p class="mt-1">Masuk terlebih dahulu agar bisa memilih varian dan checkout.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('login') }}"
                                class="btn-primary rounded-[0.6rem] py-2 text-sm shadow-none">Login</a>
                            <a href="{{ route('register') }}"
                                class="btn-secondary rounded-[0.6rem] py-2 text-sm shadow-none">Daftar</a>
                        </div>
                    </div>
                @endauth
            </div>
        </section>

        <section class="space-y-8 border-t border-[var(--border-soft)] pt-8">
            <section>
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Deskripsi Produk</h2>
                <p class="mt-3 text-sm leading-7 text-[var(--text-secondary)]">{{ $product->description }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Spesifikasi</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[0.65rem] border border-[var(--border-soft)] bg-white p-3">
                        <p class="text-xs text-[var(--text-muted)]">Kategori</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $product->category->name }}
                        </p>
                    </div>
                    <div class="rounded-[0.65rem] border border-[var(--border-soft)] bg-white p-3">
                        <p class="text-xs text-[var(--text-muted)]">SKU prefix</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                            {{ $product->sku_prefix ?: 'RDS' }}
                        </p>
                    </div>
                    <div class="rounded-[0.65rem] border border-[var(--border-soft)] bg-white p-3">
                        <p class="text-xs text-[var(--text-muted)]">Berat</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                            {{ number_format($product->weight_gram, 0, ',', '.') }} gram
                        </p>
                    </div>
                    <div class="rounded-[0.65rem] border border-[var(--border-soft)] bg-white p-3">
                        <p class="text-xs text-[var(--text-muted)]">Pilihan ukuran</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">
                            {{ implode(', ', collect($product->available_sizes)->map(fn($size) => 'EU ' . $size)->all()) }}
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex flex-wrap items-center gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Ulasan
                            pembeli</p>
                        <h2 class="text-lg font-semibold text-[var(--text-primary)]">Rating dan ulasan</h2>
                    </div>
                    <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count" />
                </div>

                @auth
                    <div
                        class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-[0.65rem] border border-dashed border-[var(--border-soft)] bg-[#f8faf9] px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Beri ulasan dari pesananmu</p>
                        </div>
                        <a href="{{ route('orders.index') }}"
                            class="btn-primary rounded-[0.6rem] px-4 py-2 text-sm shadow-none">Lihat pesanan</a>
                    </div>
                @else
                    <div
                        class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-[0.65rem] border border-dashed border-[var(--border-soft)] bg-[#f8faf9] px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Login untuk memberi ulasan</p>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">Masuk terlebih dahulu untuk melihat pesanan
                                dan memberi ulasan produk.</p>
                        </div>
                        <a href="{{ route('login') }}"
                            class="btn-primary rounded-[0.6rem] px-4 py-2 text-sm shadow-none">Login</a>
                    </div>
                @endauth

                @if ($reviewHighlights->isNotEmpty())
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($reviewHighlights as $review)
                            <article class="rounded-[0.65rem] border border-[var(--border-soft)] bg-white p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">
                                            {{ $review->user?->name ?? 'Pembeli terverifikasi' }}
                                        </p>
                                        <p class="text-xs text-[var(--text-muted)]">
                                            {{ $review->created_at?->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                    <x-store.rating-stars :rating="$review->rating" textClass="text-xs text-stone-500" />
                                </div>
                                <p class="mt-3 text-sm leading-6 text-[var(--text-secondary)]">
                                    {{ $review->review ?: 'Pembeli memberi rating tanpa komentar tambahan.' }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-4">
                        <x-store.empty-state icon="star" title="Belum ada ulasan"
                            body="Ulasan pembeli akan tampil di sini setelah ada transaksi yang memberi rating." />
                    </div>
                @endif
            </section>

            <section class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-lg font-semibold text-[var(--text-primary)]">Produk terlaris</h2>
                    <a href="{{ route('products.index', ['sort' => 'terlaris']) }}"
                        class="text-sm font-semibold text-[var(--accent-primary)]">Lihat produk lainnya</a>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                    @forelse ($relatedProducts as $relatedProduct)
                        <x-store.product-card :product="$relatedProduct" full-link compact />
                    @empty
                        <div class="sm:col-span-2 lg:col-span-4 xl:col-span-4">
                            <x-store.empty-state icon="package" title="Produk terlaris belum tersedia"
                                body="Produk akan tampil di sini setelah mulai terjual." />
                        </div>
                    @endforelse
                </div>
            </section>
        </section>
    </div>

    <div
        class="fixed inset-x-0 bottom-20 z-30 border-t border-[var(--border-soft)] bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(16,24,20,0.08)] lg:hidden">
        <div class="mx-auto flex max-w-xl items-center justify-between gap-3">
            <div>
                <p class="text-xs text-[var(--text-muted)]">Mulai dari</p>
                <p class="text-lg font-semibold text-[var(--text-primary)]">
                    Rp{{ number_format((float) $product->lowest_display_price, 0, ',', '.') }}</p>
            </div>
            <a href="#purchase" class="btn-primary flex-1 rounded-[0.6rem] shadow-none">Pilih Produk</a>
        </div>
    </div>
</x-layouts.store>