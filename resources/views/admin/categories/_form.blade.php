@php($category = $category ?? null)

<div>
    <label class="text-sm font-semibold text-stone-900" for="name">Nama Kategori</label>
    <input id="name" name="name" value="{{ old('name', $category?->name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
</div>

<div class="mt-4 rounded-[1.25rem] border border-stone-200 bg-stone-50 px-4 py-3">
    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Slug</p>
    <p class="mt-2 text-sm font-semibold text-stone-900">{{ $category?->slug ?: 'Otomatis dibuat saat kategori disimpan' }}</p>
</div>

<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))>
    Aktif
</label>
