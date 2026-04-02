@php($variant = $variant ?? null)

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="size">Ukuran</label>
        <input id="size" type="number" min="1" name="size" value="{{ old('size', $variant?->size) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
        <p class="mt-2 text-xs text-stone-500">Ukuran wajib angka, misalnya 39, 40, atau 41.</p>
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="color">Warna</label>
        <select id="color" name="color" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
            <option value="">Pilih warna</option>
            @foreach ($colorOptions as $colorOption)
                <option value="{{ $colorOption }}" @selected(old('color', $variant?->color) === $colorOption)>{{ $colorOption }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-4">
    <div class="sm:col-span-2">
        <label class="text-sm font-semibold text-stone-900" for="sku_preview">SKU</label>
        <input id="sku_preview" value="{{ $variant?->sku ?: 'Otomatis dibuat saat varian disimpan' }}" class="mt-2 w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600" readonly>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="price">Harga</label>
        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $variant?->price_override) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="discount_percentage">Diskon (%)</label>
        <input id="discount_percentage" type="number" min="0" max="100" name="discount_percentage" value="{{ old('discount_percentage', $variant?->discount_percentage ?? 0) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="stock_qty">Stok</label>
        <input id="stock_qty" type="number" min="0" name="stock_qty" value="{{ old('stock_qty', $variant?->stock_qty) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="images">Gambar Varian</label>
        <input id="images" type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
        <p class="mt-2 text-xs text-stone-500">Boleh lebih dari satu gambar. Gambar ini akan tampil setelah user memilih varian.</p>
    </div>
</div>

@if ($variant?->images?->isNotEmpty())
    <div class="mt-5">
        <h2 class="text-sm font-semibold text-stone-900">Galeri Varian</h2>
        <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($variant->images as $image)
                <div class="overflow-hidden rounded-[1.4rem] border border-stone-200 bg-white">
                    <div class="aspect-[4/3] bg-stone-100">
                        @if ($image->image_url)
                            <img src="{{ $image->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                        <p class="truncate text-stone-500">{{ basename($image->image_path) }}</p>
                        <form method="POST" action="{{ route('admin.products.variants.images.destroy', [$product, $variant, $image]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-semibold text-rose-700">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $variant?->is_active ?? true))>
    Varian aktif
</label>
