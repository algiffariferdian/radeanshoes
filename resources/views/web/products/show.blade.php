<x-layouts.store :title="$product->name.' - RadeanShoes'">
    @php
        $variantPayload = $product->variants->map(fn ($variant) => [
            'id' => $variant->id,
            'size' => $variant->size,
            'color' => $variant->color,
            'stock_qty' => $variant->stock_qty,
            'original_price' => (float) $variant->originalPrice(),
            'effective_price' => (float) $variant->effectivePrice(),
            'discount_percentage' => (int) $variant->discount_percentage,
            'images' => $variant->image_urls,
        ])->values();

        $wishlistPayload = [
            'id' => $product->id,
            'name' => $product->name,
            'url' => route('products.show', $product),
            'image' => $product->primary_image_url,
            'price' => number_format((float) $product->lowest_display_price, 0, ',', '.'),
        ];
    @endphp

    <div x-data="productConfigurator({ variants: @js($variantPayload), coverImage: @js($product->primary_image_url), qty: 1 })" class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Katalog', 'url' => route('products.index')],
            ['label' => $product->category->name, 'url' => route('products.index', ['category' => $product->category_id])],
            ['label' => $product->name],
        ]" />

        <div class="grid gap-6 xl:grid-cols-[1.02fr_0.98fr]">
            <section class="space-y-4">
                <div class="surface-card-strong overflow-hidden">
                    <div class="product-media aspect-[4/4.3] sm:aspect-[4/3]">
                        <template x-if="currentImage">
                            <img x-bind:src="currentImage" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!currentImage">
                            <div class="flex h-full items-center justify-center text-5xl font-semibold text-[var(--text-muted)]">!</div>
                        </template>
                    </div>
                </div>

                <div x-show="galleryImages.length > 0" class="grid grid-cols-4 gap-3 sm:grid-cols-5">
                    <template x-for="image in galleryImages" :key="image">
                        <button
                            type="button"
                            class="overflow-hidden rounded-[0.9rem] border bg-white"
                            x-bind:class="currentImage === image ? 'border-[var(--accent-primary)]' : 'border-[var(--border-soft)]'"
                            @click="selectGalleryImage(image)"
                        >
                            <img x-bind:src="image" alt="{{ $product->name }}" class="aspect-square h-full w-full object-cover">
                        </button>
                    </template>
                </div>

                <div class="surface-card grid gap-4 p-5 sm:grid-cols-3">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.8rem] bg-[var(--accent-soft)] text-[var(--accent-primary)]">
                            <x-store.icon name="shield" class="h-4 w-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Tampilan terpercaya</p>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">Harga, stok, dan varian dibuat jelas sebelum checkout.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.8rem] bg-[var(--accent-soft)] text-[var(--accent-primary)]">
                            <x-store.icon name="truck" class="h-4 w-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Estimasi kurir jelas</p>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">Pilih layanan pengiriman saat checkout dengan ongkir transparan.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.8rem] bg-[var(--accent-soft)] text-[var(--accent-primary)]">
                            <x-store.icon name="wallet" class="h-4 w-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Pembayaran familiar</p>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">Gunakan metode pembayaran favorit lewat Midtrans Snap.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="purchase" class="space-y-4">
                <div class="surface-card-strong p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge-accent">{{ $product->category->name }}</span>
                                <template x-if="discountPercentage > 0">
                                    <span class="badge-discount" x-text="'Diskon ' + discountPercentage + '%'"></span>
                                </template>
                            </div>
                            <h1 class="heading-page text-[clamp(1.8rem,2.8vw,2.45rem)]">{{ $product->name }}</h1>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--text-secondary)]">
                                <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count" />
                                <span class="inline-flex items-center gap-1">
                                    <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                                    {{ number_format($product->sold_count, 0, ',', '.') }} terjual
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                                    {{ number_format($product->total_stock, 0, ',', '.') }} stok tersedia
                                </span>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="icon-button"
                            x-bind:class="$store.wishlist.has({{ $product->id }}) ? 'wishlist-active' : ''"
                            @click.prevent="$store.wishlist.toggle(@js($wishlistPayload))"
                            aria-label="Tambah ke wishlist"
                        >
                            <x-store.icon name="heart" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-6 rounded-[1rem] bg-[var(--surface-soft)] p-5">
                        <div class="flex flex-wrap items-end gap-3">
                            <p class="text-3xl font-extrabold text-[var(--text-primary)]" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(currentPrice)"></p>
                            <template x-if="comparePrice">
                                <p class="price-strike" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(comparePrice)"></p>
                            </template>
                            <template x-if="discountPercentage">
                                <span class="badge-discount" x-text="'Hemat ' + discountPercentage + '%'"></span>
                            </template>
                        </div>
                        <p class="mt-2 text-sm text-[var(--text-secondary)]">Harga mengikuti varian ukuran dan warna yang dipilih.</p>
                    </div>

                    <div class="mt-6 space-y-5">
                        <div>
                            <p class="field-label">Pilih Warna</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="colorOption in colors" :key="colorOption.name">
                                    <button
                                        type="button"
                                        class="rounded-[0.8rem] border px-4 py-2 text-sm font-semibold transition"
                                        x-bind:class="selectedColor === colorOption.name ? 'border-[var(--accent-primary)] bg-[var(--accent-soft)] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-primary)]'"
                                        @click="selectColor(colorOption.name)"
                                    >
                                        <span x-text="colorOption.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <p class="field-label">Pilih Ukuran</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="sizeOption in sizesForSelectedColor()" :key="sizeOption.name">
                                    <button
                                        type="button"
                                        class="rounded-[0.8rem] border px-4 py-2 text-sm font-semibold transition"
                                        x-bind:class="selectedSize === sizeOption.name ? 'border-[var(--accent-primary)] bg-[var(--accent-soft)] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-primary)]'"
                                        @click="selectSize(sizeOption.name)"
                                    >
                                        <span x-text="'EU ' + sizeOption.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_180px]">
                            <div class="surface-soft p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">Stok produk</p>
                                        <p class="mt-1 text-sm text-[var(--text-secondary)]" x-text="availableStock > 0 ? 'Tersisa ' + availableStock : 'Varian ini sedang habis'"></p>
                                    </div>
                                    <span class="badge-accent" x-show="selectedVariantId">Varian siap beli</span>
                                </div>
                            </div>

                            <div>
                                <p class="field-label">Jumlah</p>
                                <div class="flex items-center rounded-[0.9rem] border border-[var(--border-strong)] bg-white">
                                    <button type="button" class="flex h-12 w-12 items-center justify-center text-lg text-[var(--text-secondary)]" @click="decrementQty()">-</button>
                                    <input type="number" x-model="qty" min="1" class="w-full border-0 text-center text-sm font-semibold text-[var(--text-primary)] focus:ring-0">
                                    <button type="button" class="flex h-12 w-12 items-center justify-center text-lg text-[var(--text-secondary)]" @click="incrementQty()">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                        <form method="POST" action="{{ route('cart.store') }}" class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="product_variant_id" x-bind:value="selectedVariantId">
                            <input type="hidden" name="qty" x-bind:value="qty">
                            <input type="hidden" name="buy_now" x-ref="buyNowField" value="0">

                            <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                                <button type="submit" class="btn-secondary w-full" x-bind:disabled="!selectedVariantId || availableStock < 1" @click="$refs.buyNowField.value = 0">
                                    <x-store.icon name="cart" class="h-4 w-4" />
                                    Masukkan ke Keranjang
                                </button>
                                <button type="submit" class="btn-primary w-full" x-bind:disabled="!selectedVariantId || availableStock < 1" @click="$refs.buyNowField.value = 1">
                                    Beli Sekarang
                                </button>
                                <button
                                    type="button"
                                    class="icon-button"
                                    x-bind:class="$store.wishlist.has({{ $product->id }}) ? 'wishlist-active' : ''"
                                    @click.prevent="$store.wishlist.toggle(@js($wishlistPayload))"
                                >
                                    <x-store.icon name="heart" class="h-5 w-5" />
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="mt-6 surface-soft p-4">
                            <p class="text-sm text-[var(--text-secondary)]">Login terlebih dahulu untuk memilih varian, menambahkan ke keranjang, atau langsung checkout.</p>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="btn-primary">Login</a>
                                <a href="{{ route('register') }}" class="btn-secondary">Daftar</a>
                            </div>
                        </div>
                    @endauth
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.85rem] bg-[#fff3eb] text-[var(--discount)]">
                            <x-store.icon name="tag" class="h-4 w-4" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Voucher toko</p>
                            <p class="text-sm text-[var(--text-secondary)]">Gunakan kode voucher saat checkout untuk mendapatkan potongan belanja yang sedang aktif.</p>
                            <span class="badge-discount">Bisa dipakai saat checkout</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
            <section class="space-y-6">
                <div class="surface-card p-6">
                    <h2 class="heading-section">Deskripsi Produk</h2>
                    <p class="mt-4 text-sm leading-7 text-[var(--text-secondary)]">{{ $product->description }}</p>
                </div>

                <div class="surface-card p-6">
                    <h2 class="heading-section">Spesifikasi</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Kategori</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $product->category->name }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">SKU prefix</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $product->sku_prefix ?: 'RDS' }}</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Berat</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ number_format($product->weight_gram, 0, ',', '.') }} gram</p>
                        </div>
                        <div class="surface-soft p-4">
                            <p class="meta-copy">Pilihan ukuran</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ implode(', ', collect($product->available_sizes)->map(fn ($size) => 'EU '.$size)->all()) }}</p>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="heading-eyebrow">Ulasan pembeli</p>
                            <h2 class="heading-section">Rating dan ulasan pembeli</h2>
                        </div>
                        <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count" />
                    </div>

                    @auth
                        <div class="mt-5 rounded-[1rem] border border-[var(--border-soft)] bg-[var(--surface-soft)] p-4">
                            @if ($canReview)
                                <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">Beri rating produk ini</p>
                                        <p class="mt-1 text-sm text-[var(--text-secondary)]">Hanya pembeli yang sudah transaksi bisa memberi ulasan. Kamu bisa mengubah ulasan yang sudah pernah dikirim.</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @for ($rating = 5; $rating >= 1; $rating--)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $rating }}" class="sr-only peer" @checked(old('rating', $existingReview?->rating) == $rating)>
                                                <span class="inline-flex items-center gap-2 rounded-[0.8rem] border border-[var(--border-soft)] bg-white px-4 py-2 text-sm font-semibold text-[var(--text-primary)] transition peer-checked:border-[var(--accent-primary)] peer-checked:bg-[var(--accent-soft)] peer-checked:text-[var(--accent-primary)]">
                                                    <x-store.icon name="star" class="h-4 w-4" />
                                                    {{ $rating }}
                                                </span>
                                            </label>
                                        @endfor
                                    </div>
                                    <div>
                                        <label class="field-label" for="review">Ulasan</label>
                                        <textarea id="review" name="review" rows="4" class="textarea-field" placeholder="Ceritakan pengalaman memakai produk ini">{{ old('review', $existingReview?->review) }}</textarea>
                                    </div>
                                    <button type="submit" class="btn-primary">Kirim Ulasan</button>
                                </form>
                            @else
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">Belum bisa memberi ulasan</p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">Ulasan hanya tersedia untuk akun yang sudah membeli produk ini.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-5 rounded-[1rem] border border-[var(--border-soft)] bg-[var(--surface-soft)] p-4">
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Login untuk memberi ulasan</p>
                            <p class="mt-1 text-sm text-[var(--text-secondary)]">Setelah membeli produk ini, kamu bisa memberi rating bintang 1 sampai 5.</p>
                        </div>
                    @endauth

                    @if ($reviewHighlights->isNotEmpty())
                        <div class="mt-5 grid gap-4 lg:grid-cols-3">
                            @foreach ($reviewHighlights as $review)
                                <article class="surface-soft p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $review->user?->name ?? 'Pembeli terverifikasi' }}</p>
                                            <p class="text-xs text-[var(--text-muted)]">{{ $review->created_at?->translatedFormat('d M Y') }}</p>
                                        </div>
                                        <x-store.rating-stars :rating="$review->rating" textClass="text-xs text-stone-500" />
                                    </div>
                                    <p class="mt-4 text-sm leading-6 text-[var(--text-secondary)]">{{ $review->review ?: 'Pembeli memberi rating tanpa komentar tambahan.' }}</p>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-5">
                            <x-store.empty-state
                                icon="star"
                                title="Belum ada ulasan"
                                body="Ulasan pembeli akan tampil di sini setelah ada transaksi yang memberi rating."
                            />
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="heading-eyebrow">Produk serupa</p>
                            <h2 class="heading-section">Rekomendasi lain yang masih satu kategori</h2>
                        </div>
                        <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="btn-ghost px-0 py-0 text-sm">Lihat kategori</a>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @forelse ($relatedProducts as $relatedProduct)
                            <x-store.product-card :product="$relatedProduct" />
                        @empty
                            <x-store.empty-state
                                class="md:col-span-2 xl:col-span-4"
                                icon="package"
                                title="Belum ada produk serupa"
                                body="Tambahkan lebih banyak produk dalam kategori ini untuk menampilkan rekomendasi."
                            />
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <div class="surface-card p-5">
                    <p class="heading-eyebrow">Informasi toko</p>
                    <h2 class="mt-2 text-lg font-bold text-[var(--text-primary)]">{{ $storeProfile['name'] }}</h2>
                    <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center gap-2">
                            <x-store.icon name="map-pin" class="h-4 w-4 text-[var(--accent-primary)]" />
                            {{ $storeProfile['location'] }}
                        </div>
                        <div class="flex items-center gap-2">
                            <x-store.icon name="package" class="h-4 w-4 text-[var(--accent-primary)]" />
                            {{ number_format($storeProfile['product_count'], 0, ',', '.') }} produk aktif
                        </div>
                        <div class="flex items-center gap-2">
                            Waktu respons {{ $storeProfile['response_time'] }}
                        </div>
                    </div>
                    <div class="mt-4 section-divider pt-4">
                        <x-store.rating-stars :rating="$storeProfile['rating']" :reviews="1284" />
                    </div>
                </div>

                <div class="surface-card p-5">
                    <p class="heading-eyebrow">Metode pembayaran</p>
                    <div class="mt-4 grid gap-3">
                        <div class="surface-soft flex items-center gap-3 p-4">
                            <x-store.icon name="credit-card" class="h-4 w-4 text-[var(--accent-primary)]" />
                            <div>
                                <p class="text-sm font-semibold text-[var(--text-primary)]">Kartu & virtual account</p>
                                <p class="text-xs text-[var(--text-secondary)]">Mudah dipakai lewat Midtrans Snap.</p>
                            </div>
                        </div>
                        <div class="surface-soft flex items-center gap-3 p-4">
                            <x-store.icon name="wallet" class="h-4 w-4 text-[var(--accent-primary)]" />
                            <div>
                                <p class="text-sm font-semibold text-[var(--text-primary)]">E-wallet & QRIS</p>
                                <p class="text-xs text-[var(--text-secondary)]">Pilihan kanal mengikuti yang tersedia di Midtrans.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <div class="fixed inset-x-0 bottom-20 z-30 border-t border-[var(--border-soft)] bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(16,24,20,0.08)] lg:hidden">
        <div class="mx-auto flex max-w-xl items-center justify-between gap-3">
            <div>
                <p class="text-xs text-[var(--text-muted)]">Mulai dari</p>
                <p class="text-lg font-bold text-[var(--text-primary)]">Rp{{ number_format((float) $product->lowest_display_price, 0, ',', '.') }}</p>
            </div>
            <a href="#purchase" class="btn-primary flex-1">Pilih Produk</a>
        </div>
    </div>
</x-layouts.store>
