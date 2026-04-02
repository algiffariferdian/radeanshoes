<x-layouts.store :title="'RadeanShoes · Katalog'">
    <div class="grid gap-8 lg:grid-cols-[300px_1fr]">
        <aside class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Filter</p>
            <h1 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Katalog Produk</h1>

            <form method="GET" action="{{ route('products.index') }}" class="mt-6 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-stone-700" for="q">Cari</label>
                    <input id="q" name="q" value="{{ $search }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Nama atau deskripsi produk">
                </div>
                <div>
                    <label class="text-sm font-semibold text-stone-700" for="category">Kategori</label>
                    <select id="category" name="category" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <div>
                        <label class="text-sm font-semibold text-stone-700" for="color">Warna</label>
                        <select id="color" name="color" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
                            <option value="">Semua warna</option>
                            @foreach ($colors as $optionColor)
                                <option value="{{ $optionColor }}" @selected($color === $optionColor)>{{ $optionColor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-stone-700" for="size">Ukuran</label>
                        <select id="size" name="size" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
                            <option value="">Semua ukuran</option>
                            @foreach ($sizes as $optionSize)
                                <option value="{{ $optionSize }}" @selected($size === $optionSize)>{{ $optionSize }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <div>
                        <label class="text-sm font-semibold text-stone-700" for="min_price">Harga minimum</label>
                        <input id="min_price" name="min_price" value="{{ $minPrice }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="350000">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-stone-700" for="max_price">Harga maksimum</label>
                        <input id="max_price" name="max_price" value="{{ $maxPrice }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="1000000">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Terapkan</button>
                    <a href="{{ route('products.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Reset</a>
                </div>
            </form>
        </aside>

        <section>
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Produk</p>
                <h2 class="text-2xl font-black tracking-tight text-stone-950">{{ $products->total() }} item ditemukan</h2>
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($products as $product)
                    <article class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="aspect-[4/3] bg-gradient-to-br from-amber-100 via-stone-100 to-teal-100">
                                @if ($product->primary_image_url)
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="space-y-3 p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-stone-500">{{ $product->category->name }}</p>
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600">{{ $product->variants->count() }} varian</span>
                                </div>
                                <h3 class="text-xl font-black tracking-tight text-stone-950">{{ $product->name }}</h3>
                                <p class="line-clamp-2 text-sm leading-6 text-stone-600">{{ $product->description }}</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-bold text-stone-950">Rp{{ number_format((float) $product->base_price, 0, ',', '.') }}</p>
                                    <span class="text-sm font-semibold text-stone-600">Pilih varian</span>
                                </div>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="rounded-[1.75rem] border border-dashed border-stone-300 bg-white p-8 text-sm text-stone-600 lg:col-span-3">
                        Tidak ada produk yang cocok dengan filter saat ini.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </section>
    </div>
</x-layouts.store>
