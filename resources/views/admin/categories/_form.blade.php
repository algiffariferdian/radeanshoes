@php($category = $category ?? null)
<div>
    <label class="text-sm font-semibold text-stone-900" for="name">Nama Kategori</label>
    <input id="name" name="name" value="{{ old('name', $category?->name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
</div>
<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="slug">Slug</label>
    <input id="slug" name="slug" value="{{ old('slug', $category?->slug) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Kosongkan untuk generate otomatis">
</div>
<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))>
    Aktif
</label>
