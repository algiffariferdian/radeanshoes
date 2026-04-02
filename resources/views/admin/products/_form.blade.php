@php($product = $product ?? null)
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="category_id">Kategori</label>
        <select id="category_id" name="category_id" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="name">Nama Produk</label>
        <input id="name" name="name" value="{{ old('name', $product?->name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-3">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="slug">Slug</label>
        <input id="slug" name="slug" value="{{ old('slug', $product?->slug) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Auto generate">
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="sku_prefix">SKU Prefix</label>
        <input id="sku_prefix" name="sku_prefix" value="{{ old('sku_prefix', $product?->sku_prefix) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="weight_gram">Berat (gram)</label>
        <input id="weight_gram" type="number" name="weight_gram" value="{{ old('weight_gram', $product?->weight_gram) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm" required>{{ old('description', $product?->description) }}</textarea>
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="base_price">Base Price</label>
        <input id="base_price" type="number" step="0.01" name="base_price" value="{{ old('base_price', $product?->base_price) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="images">Upload Gambar</label>
        <input id="images" type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
</div>
@if ($product?->images?->count())
    <div class="mt-4">
        <label class="text-sm font-semibold text-stone-900" for="primary_image_id">Gambar Utama</label>
        <select id="primary_image_id" name="primary_image_id" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
            <option value="">Pertahankan primary saat ini</option>
            @foreach ($product->images as $image)
                <option value="{{ $image->id }}" @selected(old('primary_image_id', $product->images->firstWhere('is_primary', true)?->id) == $image->id)>
                    Gambar #{{ $image->id }}{{ $image->is_primary ? ' (current)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
@endif
<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))>
    Produk aktif
</label>
