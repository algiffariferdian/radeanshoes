@php($variant = $variant ?? null)
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="size">Ukuran</label>
        <input id="size" name="size" value="{{ old('size', $variant?->size) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="color">Warna</label>
        <input id="color" name="color" value="{{ old('color', $variant?->color) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-3">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="sku">SKU</label>
        <input id="sku" name="sku" value="{{ old('sku', $variant?->sku) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="price_override">Price Override</label>
        <input id="price_override" type="number" step="0.01" name="price_override" value="{{ old('price_override', $variant?->price_override) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="stock_qty">Stock Qty</label>
        <input id="stock_qty" type="number" name="stock_qty" value="{{ old('stock_qty', $variant?->stock_qty) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $variant?->is_active ?? true))>
    Varian aktif
</label>
