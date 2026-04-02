<x-layouts.admin :title="'Varian Produk - Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Inventory</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">{{ $product->name }} · Varian</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.products.edit', $product) }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Produk</a>
                <a href="{{ route('admin.products.variants.create', $product) }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Varian</a>
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Ukuran</th>
                            <th class="pb-3">Warna</th>
                            <th class="pb-3">SKU</th>
                            <th class="pb-3">Harga</th>
                            <th class="pb-3">Diskon</th>
                            <th class="pb-3">Stok</th>
                            <th class="pb-3">Gambar</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($variants as $variant)
                            <tr>
                                <td class="py-4 font-semibold text-stone-900">EU {{ $variant->size }}</td>
                                <td class="py-4 text-stone-600">{{ $variant->color }}</td>
                                <td class="py-4 text-stone-600">{{ $variant->sku }}</td>
                                <td class="py-4 text-stone-600">Rp{{ number_format((float) $variant->price_override, 0, ',', '.') }}</td>
                                <td class="py-4 text-stone-600">{{ $variant->discount_percentage }}%</td>
                                <td class="py-4 text-stone-600">{{ $variant->stock_qty }}</td>
                                <td class="py-4 text-stone-600">{{ $variant->images_count }}</td>
                                <td class="py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $variant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 font-semibold text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $variants->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
