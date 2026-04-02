@php($banner = $banner ?? null)

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="title">Judul banner</label>
        <input id="title" name="title" value="{{ old('title', $banner?->title) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="button_label">Label tombol</label>
        <input id="button_label" name="button_label" value="{{ old('button_label', $banner?->button_label) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Belanja Sekarang">
    </div>
</div>

<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="subtitle">Subjudul</label>
    <textarea id="subtitle" name="subtitle" rows="3" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm" placeholder="Tambahkan copy singkat untuk banner">{{ old('subtitle', $banner?->subtitle) }}</textarea>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="link_url">Link tujuan</label>
        <input id="link_url" name="link_url" value="{{ old('link_url', $banner?->link_url) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="https:// atau /products">
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="sort_order">Urutan tampil</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
</div>

<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="image">Gambar banner</label>
    <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" @required(! $banner)>
    <p class="mt-2 text-xs leading-5 text-stone-500">Gunakan gambar landscape agar slider tampil rapi di desktop dan mobile.</p>
    @if ($banner?->image_url)
        <div class="mt-4 overflow-hidden rounded-[1.25rem] border border-stone-200 bg-stone-100">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="h-48 w-full object-cover">
        </div>
    @endif
</div>

<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true))>
    Banner aktif
</label>
