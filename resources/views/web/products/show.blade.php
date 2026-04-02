<x-layouts.store :title="$product->name.' · RadeanShoes'">
    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-4">
            <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="aspect-[4/3] bg-gradient-to-br from-amber-100 via-stone-100 to-teal-100">
                    @if ($product->primary_image_url)
                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @endif
                </div>
            </div>
            @if ($product->images->count() > 1)
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($product->images as $image)
                        <div class="overflow-hidden rounded-[1.25rem] border border-stone-200 bg-white">
                            <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $product->name }}" class="aspect-square h-full w-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
            <p class="text-sm font-semibold text-stone-500">{{ $product->category->name }}</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight text-stone-950">{{ $product->name }}</h1>
            <p class="mt-4 text-2xl font-bold text-stone-950">Rp{{ number_format((float) $product->base_price, 0, ',', '.') }}</p>
            <p class="mt-5 text-base leading-7 text-stone-600">{{ $product->description }}</p>

            <div class="mt-8 rounded-[1.5rem] bg-stone-50 p-5">
                <div class="grid gap-3 text-sm text-stone-600 sm:grid-cols-2">
                    <div>
                        <p class="font-semibold text-stone-900">SKU Prefix</p>
                        <p>{{ $product->sku_prefix ?: 'Tidak ada' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-stone-900">Berat</p>
                        <p>{{ number_format($product->weight_gram) }} gram</p>
                    </div>
                </div>
            </div>

            @auth
                <form method="POST" action="{{ route('cart.store') }}" class="mt-8 space-y-4 rounded-[1.75rem] border border-stone-200 bg-stone-50 p-5">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-stone-900" for="product_variant_id">Pilih varian</label>
                        <select id="product_variant_id" name="product_variant_id" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
                            <option value="">Pilih ukuran dan warna</option>
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->id }}">
                                    {{ $variant->size }} / {{ $variant->color }} · stok {{ $variant->stock_qty }} · Rp{{ number_format((float) ($variant->price_override ?? $product->base_price), 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-stone-900" for="qty">Jumlah</label>
                        <input id="qty" type="number" name="qty" min="1" value="1" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
                    </div>
                    <button type="submit" class="w-full rounded-full bg-stone-950 px-6 py-3 text-sm font-semibold text-stone-50">Tambah ke Keranjang</button>
                </form>
            @else
                <div class="mt-8 rounded-[1.75rem] border border-stone-200 bg-stone-50 p-5 text-sm text-stone-700">
                    Login terlebih dahulu untuk menambahkan produk ke keranjang dan melanjutkan checkout.
                </div>
            @endauth
        </section>
    </div>
</x-layouts.store>
