@php($address = $address ?? null)
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="recipient_name">Nama Penerima</label>
        <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $address?->recipient_name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="phone">Telepon</label>
        <input id="phone" name="phone" value="{{ old('phone', $address?->phone) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="label">Label</label>
    <input id="label" name="label" value="{{ old('label', $address?->label) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" placeholder="Rumah / Kantor">
</div>
<div class="mt-4">
    <label class="text-sm font-semibold text-stone-900" for="address_line">Alamat Lengkap</label>
    <textarea id="address_line" name="address_line" rows="4" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm" required>{{ old('address_line', $address?->address_line) }}</textarea>
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="district">Kecamatan / Distrik</label>
        <input id="district" name="district" value="{{ old('district', $address?->district) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="city">Kota</label>
        <input id="city" name="city" value="{{ old('city', $address?->city) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-stone-900" for="province">Provinsi</label>
        <input id="province" name="province" value="{{ old('province', $address?->province) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
    <div>
        <label class="text-sm font-semibold text-stone-900" for="postal_code">Kode Pos</label>
        <input id="postal_code" name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
    </div>
</div>
<label class="mt-4 flex items-center gap-3 rounded-[1.25rem] bg-stone-50 px-4 py-3 text-sm text-stone-700">
    <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default))>
    Jadikan alamat default
</label>
