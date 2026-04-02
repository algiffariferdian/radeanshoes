@php($address = $address ?? null)

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="field-label" for="recipient_name">Nama Penerima</label>
        <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $address?->recipient_name) }}" class="input-field" required>
    </div>
    <div>
        <label class="field-label" for="phone">Telepon</label>
        <input id="phone" name="phone" value="{{ old('phone', $address?->phone) }}" class="input-field" required>
    </div>
</div>

<div class="mt-4">
    <label class="field-label" for="label">Label alamat</label>
    <input id="label" name="label" value="{{ old('label', $address?->label) }}" class="input-field" placeholder="Rumah, kantor, apartemen">
</div>

<div class="mt-4">
    <label class="field-label" for="address_line">Alamat lengkap</label>
    <textarea id="address_line" name="address_line" rows="4" class="textarea-field" required>{{ old('address_line', $address?->address_line) }}</textarea>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="field-label" for="district">Kecamatan / Distrik</label>
        <input id="district" name="district" value="{{ old('district', $address?->district) }}" class="input-field">
    </div>
    <div>
        <label class="field-label" for="city">Kota</label>
        <input id="city" name="city" value="{{ old('city', $address?->city) }}" class="input-field" required>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="field-label" for="province">Provinsi</label>
        <input id="province" name="province" value="{{ old('province', $address?->province) }}" class="input-field" required>
    </div>
    <div>
        <label class="field-label" for="postal_code">Kode Pos</label>
        <input id="postal_code" name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}" class="input-field" required>
    </div>
</div>

<label class="mt-4 flex items-center gap-3 rounded-[0.95rem] border border-[var(--border-soft)] bg-[var(--surface-soft)] px-4 py-3 text-sm text-[var(--text-secondary)]">
    <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default)) class="h-4 w-4 rounded border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]">
    Jadikan alamat utama untuk checkout berikutnya
</label>
