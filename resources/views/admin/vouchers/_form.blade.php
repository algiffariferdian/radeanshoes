@php($voucher = $voucher ?? null)

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="code">Kode voucher</label>
        <input id="code" name="code" value="{{ old('code', $voucher?->code) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm uppercase" required>
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="name">Nama voucher</label>
        <input id="name" name="name" value="{{ old('name', $voucher?->name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="discount_value">Potongan diskon (%)</label>
        <input id="discount_value" type="number" step="0.01" min="0" max="100" name="discount_value" value="{{ old('discount_value', $voucher?->discount_value) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="max_discount">Maksimal diskon</label>
        <input id="max_discount" type="number" step="0.01" min="0" name="max_discount" value="{{ old('max_discount', $voucher?->max_discount) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Opsional">
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-3">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="min_subtotal">Minimal belanja</label>
        <input id="min_subtotal" type="number" step="0.01" min="0" name="min_subtotal" value="{{ old('min_subtotal', $voucher?->min_subtotal) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Opsional">
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="usage_limit">Batas penggunaan</label>
        <input id="usage_limit" type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $voucher?->usage_limit) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Opsional">
    </div>

    <div class="rounded-[1.25rem] border border-stone-200 bg-stone-50 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Dipakai</p>
        <p class="mt-2 text-sm font-semibold text-stone-900">{{ number_format((int) ($voucher?->used_count ?? 0), 0, ',', '.') }} kali</p>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="starts_at">Mulai aktif</label>
        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $voucher?->starts_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>

    <div>
        <label class="text-sm font-semibold text-stone-900" for="ends_at">Berakhir</label>
        <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $voucher?->ends_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
</div>

<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $voucher?->is_active ?? true))>
    Voucher aktif
</label>
