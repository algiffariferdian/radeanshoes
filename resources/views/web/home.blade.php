<x-layouts.store :title="'RadeanShoes · Home'">
    <section class="grid gap-8 rounded-[2rem] border border-stone-200 bg-white/80 p-8 shadow-sm lg:grid-cols-[1.25fr_0.75fr]">
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.35em] text-stone-500">MVP Storefront</p>
            <h1 class="max-w-3xl text-4xl font-black leading-tight tracking-tight text-stone-950 sm:text-5xl">Sepatu harian dengan checkout cepat, stok per varian, dan alur order yang bisa ditelusuri.</h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-stone-600">RadeanShoes dibangun sebagai toko online single-brand dengan pengalaman belanja sederhana: pilih ukuran dan warna, checkout via Midtrans sandbox, lalu pantau status pesanan langsung dari akun.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('products.index') }}" class="rounded-full bg-stone-950 px-6 py-3 text-sm font-semibold text-stone-50">Lihat Katalog</a>
                @guest
                    <a href="{{ route('register') }}" class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-stone-700">Buat Akun</a>
                @else
                    <a href="{{ route('orders.index') }}" class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-stone-700">Riwayat Pesanan</a>
                @endguest
            </div>
        </div>

        <div class="grid gap-4 rounded-[1.75rem] bg-stone-950 p-6 text-stone-100">
            <div class="rounded-[1.5rem] bg-stone-900 p-5">
                <p class="text-sm font-semibold text-amber-200">Domain MVP</p>
                <p class="mt-2 text-sm leading-6 text-stone-300">Produk tunggal dengan varian ukuran dan warna. Stok hidup di level varian. Ongkir dikelola manual oleh admin.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-[1.5rem] border border-stone-800 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-stone-500">Kategori</p>
                    <p class="mt-2 text-3xl font-black">{{ $categories->count() }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-stone-800 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-stone-500">Produk Aktif</p>
                    <p class="mt-2 text-3xl font-black">{{ $featuredProducts->count() }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-stone-800 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-stone-500">Pembayaran</p>
                    <p class="mt-2 text-lg font-bold text-amber-100">Midtrans Sandbox</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-10">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Pilihan Utama</p>
                <h2 class="text-2xl font-black tracking-tight text-stone-950">Produk unggulan minggu ini</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-stone-700">Lihat semua</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($featuredProducts as $product)
                <article class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div class="aspect-[4/3] bg-gradient-to-br from-amber-100 via-stone-100 to-teal-100">
                            @if ($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="space-y-3 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-stone-500">{{ $product->category->name }}</p>
                                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600">{{ $product->variants->count() }} varian</span>
                            </div>
                            <h3 class="text-xl font-black tracking-tight text-stone-950">{{ $product->name }}</h3>
                            <p class="line-clamp-2 text-sm leading-6 text-stone-600">{{ $product->description }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-bold text-stone-950">Rp{{ number_format((float) $product->base_price, 0, ',', '.') }}</p>
                                <span class="text-sm font-semibold text-stone-600">Detail</span>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-stone-300 bg-white p-6 text-sm text-stone-600">Belum ada produk aktif untuk ditampilkan.</div>
            @endforelse
        </div>
    </section>
</x-layouts.store>
