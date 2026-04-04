@php($banner = $banner ?? null)

<div>
    <label class="text-sm font-semibold text-stone-900" for="sort_order">Urutan tampil</label>
    <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
</div>

<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="image">Gambar banner</label>
    <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" @required(! $banner)>
    <p class="mt-2 text-xs leading-5 text-stone-500">Upload gambar banner ukuran tepat 1920 x 720 piksel dengan ukuran maksimal 12 MB.</p>
    @if ($banner?->image_url)
        <div class="mt-4 overflow-hidden rounded-[1.25rem] border border-stone-200 bg-stone-100">
            <img src="{{ $banner->image_url }}" alt="Banner" class="aspect-[8/3] h-auto w-full object-cover">
        </div>
    @endif
</div>

<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true))>
    Banner aktif
</label>
