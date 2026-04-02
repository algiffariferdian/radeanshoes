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

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="weight_gram">Berat (gram)</label>
        <input id="weight_gram" type="number" name="weight_gram" min="1" value="{{ old('weight_gram', $product?->weight_gram) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="cover_image">Gambar Utama Produk</label>
        <input id="cover_image" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" @required(! $product)>
        <p class="mt-2 text-xs leading-5 text-stone-500">Produk hanya punya 1 gambar utama. Gambar varian diatur terpisah di menu varian.</p>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div class="rounded-[1.25rem] border border-stone-200 bg-stone-50 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Slug</p>
        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $product?->slug ?: 'Otomatis dibuat saat produk disimpan' }}</p>
    </div>

    <div class="rounded-[1.25rem] border border-stone-200 bg-stone-50 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">SKU Prefix</p>
        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $product?->sku_prefix ?: 'Otomatis dibuat saat produk disimpan' }}</p>
    </div>
</div>

<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm" required>{{ old('description', $product?->description) }}</textarea>
</div>

<div class="mt-4 rounded-[1.25rem] border border-dashed border-stone-300 bg-stone-50 px-4 py-4 text-sm text-stone-600">
    Harga, diskon, stok, ukuran, warna, dan galeri detail diatur dari menu varian agar pengelolaan produk lebih rapi.
</div>

<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))>
    Produk aktif
</label>
