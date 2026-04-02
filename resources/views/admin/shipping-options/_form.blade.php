@php($shippingOption = $shippingOption ?? null)
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="courier_name">Nama Kurir</label>
        <input id="courier_name" name="courier_name" value="{{ old('courier_name', $shippingOption?->courier_name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="service_name">Nama Layanan</label>
        <input id="service_name" name="service_name" value="{{ old('service_name', $shippingOption?->service_name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="etd_text">Estimasi</label>
        <input id="etd_text" name="etd_text" value="{{ old('etd_text', $shippingOption?->etd_text) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="price">Ongkir</label>
        <input id="price" type="number" step="0.01" name="price" value="{{ old('price', $shippingOption?->price) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="sort_order">Sort Order</label>
    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $shippingOption?->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
</div>
<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $shippingOption?->is_active ?? true))>
    Opsi pengiriman aktif
</label>
