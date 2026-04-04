<x-layouts.store :title="'RadeanShoes - Toko Sepatu Online'">
    <section id="produk-terlaris">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="heading-eyebrow">Produk terlaris</p>
                <h2 class="heading-section">Pilihan yang paling banyak dicari</h2>

            </div>
            <a href="{{ route('products.index', ['sort' => 'terlaris']) }}" class="btn-ghost px-0 py-0 text-sm">Lihat
                semua</a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
            @forelse ($bestSellerProducts as $product)
                <x-store.product-card :product="$product" full-link compact />
            @empty
                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-4">
                    <x-store.empty-state icon="package" title="Produk terlaris belum tersedia"
                        body="Produk akan tampil di sini setelah mulai terjual." />
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-10">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="heading-eyebrow">Produk baru</p>
                <h2 class="heading-section">Koleksi terbaru yang baru masuk</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'terbaru']) }}" class="btn-ghost px-0 py-0 text-sm">Lihat
                semua</a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
            @forelse ($newArrivalProducts as $product)
                <x-store.product-card :product="$product" full-link compact />
            @empty
                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-4">
                    <x-store.empty-state icon="sparkles" title="Belum ada produk baru"
                        body="Produk terbaru akan muncul di sini." />
                </div>
            @endforelse
        </div>
    </section>

    <section id="promo-diskon" class="mt-10">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="heading-eyebrow">Promo diskon</p>
                <h2 class="heading-section">Harga spesial yang sedang aktif</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'harga_terendah']) }}"
                class="btn-ghost px-0 py-0 text-sm">Lihat semua</a>
        </div>

        @if ($promoProducts->isNotEmpty())
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                @foreach ($promoProducts as $product)
                    <x-store.product-card :product="$product" full-link compact />
                @endforeach
            </div>
        @else
            <x-store.empty-state icon="tag" title="Belum ada promo aktif"
                body="Produk promo akan muncul di sini saat ada harga spesial." />
        @endif
    </section>
</x-layouts.store>
