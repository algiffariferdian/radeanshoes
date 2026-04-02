<x-layouts.admin :title="'Produk - Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Catalog</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Produk</h1>
            </div>
            <a href="{{ route('admin.products.create') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Produk</a>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Produk</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">SKU Prefix</th>
                            <th class="pb-3">Varian</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($products as $product)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-16 overflow-hidden rounded-[1rem] bg-stone-100">
                                            @if ($product->primary_image_url)
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-stone-900">{{ $product->name }}</p>
                                            <p class="text-xs text-stone-500">{{ $product->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-stone-600">{{ $product->category->name }}</td>
                                <td class="py-4 text-stone-600">{{ $product->sku_prefix }}</td>
                                <td class="py-4 text-stone-600">
                                    {{ $product->variants_count }} varian
                                    @if ($product->variants_count > 0)
                                        <div class="mt-1 text-xs text-stone-500">Mulai Rp{{ number_format((float) $product->lowest_display_price, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.products.variants.index', $product) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Varian</a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
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
            <div class="mt-6">{{ $products->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
