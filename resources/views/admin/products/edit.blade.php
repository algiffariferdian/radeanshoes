<x-layouts.admin :title="'Edit Produk - Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="max-w-4xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-stone-950">Edit Produk</h1>
                    <p class="mt-2 text-sm text-stone-500">Produk dan varian dipisah. Di sini hanya metadata produk dan gambar utama.</p>
                </div>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700">Kelola Varian</a>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="mt-6">
                @csrf
                @method('PATCH')
                @include('admin.products._form', ['product' => $product])
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Perbarui Produk</button>
                    <a href="{{ route('admin.products.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Kembali</a>
                </div>
            </form>
        </div>

        <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black tracking-tight text-stone-950">Gambar Utama</h2>
                <p class="mt-1 text-sm text-stone-500">Gambar pertama yang tampil di katalog dan detail produk.</p>

                @if ($product->images->first())
                    <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-stone-200">
                        <div class="aspect-[4/3] bg-stone-100">
                            @if ($product->images->first()->image_url)
                                <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                            <div>
                                <p class="font-semibold text-stone-900">Cover produk</p>
                                <p class="mt-1 text-xs text-stone-500">{{ basename($product->images->first()->image_path) }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $product->images->first()]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-semibold text-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-5 py-8 text-sm text-stone-600">
                        Produk ini belum punya gambar utama.
                    </div>
                @endif
            </div>

            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-stone-950">Ringkasan Varian</h2>
                        <p class="mt-1 text-sm text-stone-500">Harga, diskon, SKU otomatis, stok, dan gambar detail semuanya dikelola dari varian.</p>
                    </div>
                    <a href="{{ route('admin.products.variants.create', $product) }}" class="rounded-full bg-stone-950 px-4 py-2 text-sm font-semibold text-stone-50">Tambah Varian</a>
                </div>

                @if ($product->variants->isNotEmpty())
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach ($product->variants as $variant)
                            <article class="rounded-[1.4rem] border border-stone-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-base font-bold text-stone-950">EU {{ $variant->size }} · {{ $variant->color }}</h3>
                                        <p class="mt-1 text-xs text-stone-500">{{ $variant->sku }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $variant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-2xl bg-stone-50 px-3 py-3">
                                        <p class="text-xs uppercase tracking-[0.16em] text-stone-500">Harga</p>
                                        <p class="mt-2 text-sm font-semibold text-stone-900">Rp{{ number_format((float) $variant->price_override, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-stone-50 px-3 py-3">
                                        <p class="text-xs uppercase tracking-[0.16em] text-stone-500">Diskon</p>
                                        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $variant->discount_percentage }}%</p>
                                    </div>
                                    <div class="rounded-2xl bg-stone-50 px-3 py-3">
                                        <p class="text-xs uppercase tracking-[0.16em] text-stone-500">Gambar</p>
                                        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $variant->images->count() }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-5 py-8 text-sm text-stone-600">
                        Produk ini belum punya varian. Tambahkan varian agar harga dan stok bisa dijual.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
