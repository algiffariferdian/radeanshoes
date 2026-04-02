<x-layouts.store :title="'Keranjang · RadeanShoes'">
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <section class="space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Cart</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Keranjang Belanja</h1>
            </div>

            @forelse ($cart->items as $item)
                @php($variant = $item->productVariant)
                @php($product = $variant->product)
                <article class="grid gap-4 rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-[140px_1fr]">
                    <div class="overflow-hidden rounded-[1.25rem] bg-gradient-to-br from-amber-100 via-stone-100 to-teal-100">
                        @if ($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-black tracking-tight text-stone-950">{{ $product->name }}</p>
                                <p class="text-sm text-stone-600">Ukuran {{ $variant->size }} · {{ $variant->color }}</p>
                            </div>
                            <p class="text-lg font-bold text-stone-950">Rp{{ number_format((float) $item->unit_price_snapshot, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-3">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="qty" min="1" max="{{ $variant->stock_qty }}" value="{{ $item->qty }}" class="w-24 rounded-full border border-stone-300 px-4 py-2 text-sm">
                                <button type="submit" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700">Update</button>
                            </form>
                            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-stone-300 bg-white p-8 text-sm text-stone-600">Keranjang masih kosong. Tambahkan produk dari katalog terlebih dahulu.</div>
            @endforelse
        </section>

        <aside class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Summary</p>
            <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Ringkasan Keranjang</h2>
            <div class="mt-6 space-y-4 text-sm text-stone-600">
                <div class="flex items-center justify-between">
                    <span>Total item</span>
                    <span class="font-semibold text-stone-900">{{ $cart->items->sum('qty') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Subtotal</span>
                    <span class="font-semibold text-stone-900">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            <a href="{{ $cart->items->isEmpty() ? route('products.index') : route('checkout.index') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">
                {{ $cart->items->isEmpty() ? 'Belanja Produk' : 'Lanjut Checkout' }}
            </a>
        </aside>
    </div>
</x-layouts.store>
