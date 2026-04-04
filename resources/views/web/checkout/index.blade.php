<x-layouts.store :title="'Checkout - RadeanShoes'">
    @php
        $addressOptions = $addresses->map(fn($address) => [
            'id' => $address->id,
            'name' => $address->recipient_name,
            'phone' => $address->phone,
            'label' => $address->label,
            'is_default' => (bool) $address->is_default,
            'address' => $address->address_line,
            'district' => $address->district,
            'city' => $address->city,
            'province' => $address->province,
            'postal_code' => $address->postal_code,
        ])->values();
        $shippingOptionsPayload = $shippingOptions->map(fn($option) => [
            'id' => $option->id,
            'courier' => $option->courier_name,
            'service' => $option->service_name,
            'price' => (float) $option->price,
            'etd' => $option->etd_text,
        ])->values();
        $selectedAddressId = old('address_id', $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id);
        $selectedShippingId = old('shipping_option_id', request('shipping_option_id', $selectedShipping?->id));
    @endphp

    <div x-data="checkoutPage({
        addresses: @js($addressOptions),
        shippingOptions: @js($shippingOptionsPayload),
        selectedAddressId: @js($selectedAddressId),
        selectedShippingId: @js($selectedShippingId),
        cartSubtotal: @js((float) $cartSubtotal),
        voucherCode: @js($voucherPreview['code']),
        voucherDiscount: @js((float) $voucherPreview['discount_amount']),
        voucherError: @js($voucherPreview['error']),
        voucherPreviewUrl: @js(route('checkout.voucher-preview')),
    })" class="space-y-6">
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Keranjang', 'url' => route('cart.index')],
        ['label' => 'Checkout'],
    ]" />

        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold text-[var(--text-primary)]">Checkout Belanja</h1>
                <p class="max-w-2xl text-sm text-[var(--text-secondary)]">Pastikan alamat dan kurir sudah tepat sebelum
                    membayar.</p>
            </div>
            <a href="{{ route('addresses.create') }}"
                class="btn-secondary rounded-[0.6rem] px-4 py-2 text-sm shadow-none">Tambah Alamat</a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <form method="POST" action="{{ route('checkout.place-order') }}"
                class="rounded-[0.75rem] border border-[var(--border-soft)] bg-white">
                @csrf
                <input type="hidden" name="address_id" x-model="selectedAddressId">
                <input type="hidden" name="shipping_option_id" x-model="selectedShippingId">

                <section class="p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-[var(--text-primary)]">Alamat Pengiriman</h2>
                        <button type="button" class="btn-secondary rounded-[0.6rem] px-3 py-1.5 text-xs shadow-none"
                            @click="$dispatch('open-modal', 'address-picker')">
                            Pilih alamat
                        </button>
                    </div>
                    <div class="mt-4 rounded-[0.6rem] border border-[var(--border-soft)] bg-white px-3 py-3">
                        <template x-if="selectedAddress">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-[var(--text-primary)]"
                                        x-text="selectedAddress.name"></p>
                                    <template x-if="selectedAddress.is_default">
                                        <span
                                            class="rounded-[0.4rem] bg-[#e8f5ee] px-2 py-0.5 text-[10px] font-semibold text-[var(--accent-primary)]">Utama</span>
                                    </template>
                                </div>
                                <p class="mt-1 text-xs text-[var(--text-secondary)]" x-text="selectedAddress.phone"></p>
                                <p class="mt-2 text-xs leading-5 text-[var(--text-secondary)]"
                                    x-text="`${selectedAddress.address}, ${selectedAddress.district}, ${selectedAddress.city}, ${selectedAddress.province} ${selectedAddress.postal_code}`">
                                </p>
                            </div>
                        </template>
                        <template x-if="!selectedAddress">
                            <p class="text-sm text-[var(--text-secondary)]">Belum ada alamat dipilih.</p>
                        </template>
                    </div>
                </section>

                <section class="border-t border-[var(--border-soft)] p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-[var(--text-primary)]">Kurir Pengiriman</h2>
                        <button type="button" class="btn-secondary rounded-[0.6rem] px-3 py-1.5 text-xs shadow-none"
                            @click="$dispatch('open-modal', 'shipping-picker')">
                            Pilih kurir
                        </button>
                    </div>
                    <div class="mt-4 rounded-[0.6rem] border border-[var(--border-soft)] bg-white px-3 py-3">
                        <template x-if="selectedShipping">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]"
                                        x-text="`${selectedShipping.courier} ${selectedShipping.service}`"></p>
                                    <p class="mt-1 text-xs text-[var(--text-secondary)]"
                                        x-text="`Estimasi ${selectedShipping.etd}`"></p>
                                </div>
                                <p class="text-sm font-semibold text-[var(--text-primary)]"
                                    x-text="'Rp' + formatCurrency(shippingPrice)"></p>
                            </div>
                        </template>
                        <template x-if="!selectedShipping">
                            <p class="text-sm text-[var(--text-secondary)]">Belum ada kurir dipilih.</p>
                        </template>
                    </div>
                </section>

                <section class="border-t border-[var(--border-soft)] p-4 sm:p-5">
                    <div class="grid gap-4 lg:grid-cols-[1fr_1fr]">
                        <div>
                            <label class="text-sm font-semibold text-[var(--text-primary)]" for="notes">Catatan</label>
                            <textarea id="notes" name="notes" rows="2" class="textarea-field min-h-[2.75rem]"
                                placeholder="Contoh: titip di resepsionis.">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-[var(--text-primary)]"
                                for="voucher_code">Voucher</label>
                            <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                                <input id="voucher_code" name="voucher_code" x-model="voucherCode"
                                    @input.debounce.350ms="previewVoucher()" class="input-field"
                                    placeholder="Contoh: HEMAT20">
                                <button type="submit" formaction="{{ route('checkout.index') }}" formmethod="GET"
                                    class="btn-secondary shrink-0 rounded-[0.6rem] px-4 py-2 text-sm shadow-none">
                                    Terapkan
                                </button>
                            </div>
                            <template x-if="voucherDiscount > 0">
                                <div
                                    class="mt-3 rounded-[0.6rem] border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                                    Voucher aktif. Potongan Rp<span x-text="formatCurrency(voucherDiscount)"></span>.
                                </div>
                            </template>
                            <template x-if="voucherDiscount === 0 && voucherError">
                                <div class="mt-3 rounded-[0.6rem] border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800"
                                    x-text="voucherError"></div>
                            </template>
                        </div>
                    </div>
                </section>

                <div class="border-t border-[var(--border-soft)] p-4 sm:p-5">
                    <button type="submit" class="btn-primary w-full rounded-[0.6rem] py-3 text-sm shadow-none"
                        @disabled($addresses->isEmpty() || $shippingOptions->isEmpty())>
                        Bayar Sekarang
                    </button>
                </div>
            </form>

            <aside class="space-y-4 xl:sticky xl:top-28 xl:self-start">
                <div class="rounded-[0.75rem] border border-[var(--border-soft)] bg-white p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-[var(--text-primary)]">Ringkasan Pesanan</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ $cart->items->sum('qty') }} item</p>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach ($cart->items as $item)
                            <div class="flex gap-3">
                                <div
                                    class="h-14 w-14 overflow-hidden rounded-[0.6rem] border border-[var(--border-soft)] bg-[#f6f8f7]">
                                    @if ($item->productVariant->product->primary_image_url)
                                        <img src="{{ $item->productVariant->product->primary_image_url }}"
                                            alt="{{ $item->productVariant->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div
                                            class="flex h-full items-center justify-center text-xl font-semibold text-[var(--text-muted)]">
                                            !</div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-[var(--text-primary)]">
                                        {{ $item->productVariant->product->name }}</p>
                                    <p class="mt-1 text-xs text-[var(--text-secondary)]">{{ $item->productVariant->size }} /
                                        {{ $item->productVariant->color }}</p>
                                    <div
                                        class="mt-2 flex items-center justify-between text-xs text-[var(--text-secondary)]">
                                        <span>{{ $item->qty }}x</span>
                                        <span
                                            class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $item->productVariant->effectivePrice(), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 border-t border-[var(--border-soft)] pt-4 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span
                                class="font-semibold text-[var(--text-primary)]">Rp{{ number_format($cartSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span>Ongkir</span>
                            <span class="font-semibold text-[var(--text-primary)]"
                                x-text="'Rp' + formatCurrency(shippingPrice)"></span>
                        </div>
                        <template x-if="voucherDiscount > 0">
                            <div class="mt-3 flex items-center justify-between">
                                <span>Diskon voucher</span>
                                <span class="font-semibold text-emerald-700"
                                    x-text="'-Rp' + formatCurrency(voucherDiscount)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 border-t border-[var(--border-soft)] pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[var(--text-primary)]">Total pembayaran</span>
                            <span class="text-lg font-semibold text-[var(--text-primary)]"
                                x-text="'Rp' + formatCurrency(totalAmount)">Rp{{ number_format($estimatedTotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-[var(--text-muted)]">Total akhir mengikuti pilihan kurir.</p>
                    </div>
                </div>
            </aside>
        </div>

        <x-modal name="address-picker" maxWidth="lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[var(--text-primary)]">Pilih Alamat</h3>
                    <button type="button" class="text-xs text-[var(--text-muted)]"
                        @click="$dispatch('close-modal', 'address-picker')">Tutup</button>
                </div>
                <div class="mt-4 grid gap-3">
                    <template x-for="address in addresses" :key="address.id">
                        <label
                            class="flex cursor-pointer gap-3 rounded-[0.6rem] border border-[var(--border-soft)] p-3 transition hover:border-[#c7d2cb]">
                            <input type="radio"
                                class="mt-1 h-4 w-4 border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]"
                                name="address_picker" x-model="selectedAddressId" :value="address.id">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-[var(--text-primary)]" x-text="address.name">
                                    </p>
                                    <template x-if="address.is_default">
                                        <span
                                            class="rounded-[0.4rem] bg-[#e8f5ee] px-2 py-0.5 text-[10px] font-semibold text-[var(--accent-primary)]">Utama</span>
                                    </template>
                                </div>
                                <p class="mt-1 text-xs text-[var(--text-secondary)]" x-text="address.phone"></p>
                                <p class="mt-2 text-xs leading-5 text-[var(--text-secondary)]"
                                    x-text="`${address.address}, ${address.district}, ${address.city}, ${address.province} ${address.postal_code}`">
                                </p>
                            </div>
                        </label>
                    </template>
                    <template x-if="addresses.length === 0">
                        <div
                            class="rounded-[0.6rem] border border-dashed border-[var(--border-soft)] bg-[#f8faf9] px-4 py-3 text-sm text-[var(--text-secondary)]">
                            Belum ada alamat tersimpan.
                        </div>
                    </template>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="button" class="btn-primary rounded-[0.6rem] px-4 py-2 text-sm shadow-none"
                        @click="$dispatch('close-modal', 'address-picker')">Simpan</button>
                </div>
            </div>
        </x-modal>

        <x-modal name="shipping-picker" maxWidth="lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[var(--text-primary)]">Pilih Kurir</h3>
                    <button type="button" class="text-xs text-[var(--text-muted)]"
                        @click="$dispatch('close-modal', 'shipping-picker')">Tutup</button>
                </div>
                <div class="mt-4 grid gap-3">
                    <template x-for="option in shippingOptions" :key="option.id">
                        <label
                            class="flex cursor-pointer items-center justify-between gap-3 rounded-[0.6rem] border border-[var(--border-soft)] p-3 transition hover:border-[#c7d2cb]">
                            <div class="flex items-start gap-3">
                                <input type="radio"
                                    class="mt-1 h-4 w-4 border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]"
                                    name="shipping_picker" x-model="selectedShippingId" :value="option.id">
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]"
                                        x-text="`${option.courier} ${option.service}`"></p>
                                    <p class="mt-1 text-xs text-[var(--text-secondary)]"
                                        x-text="`Estimasi ${option.etd}`"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-[var(--text-primary)]"
                                    x-text="'Rp' + formatCurrency(option.price)"></p>
                                <template x-if="option.id === @js($recommendedShipping?->id)">
                                    <span
                                        class="mt-1 inline-flex rounded-[0.4rem] bg-[#e8f5ee] px-2 py-0.5 text-[10px] font-semibold text-[var(--accent-primary)]">Rekomendasi</span>
                                </template>
                            </div>
                        </label>
                    </template>
                    <template x-if="shippingOptions.length === 0">
                        <div
                            class="rounded-[0.6rem] border border-dashed border-[var(--border-soft)] bg-[#f8faf9] px-4 py-3 text-sm text-[var(--text-secondary)]">
                            Kurir belum tersedia.
                        </div>
                    </template>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="button" class="btn-primary rounded-[0.6rem] px-4 py-2 text-sm shadow-none"
                        @click="$dispatch('close-modal', 'shipping-picker')">Simpan</button>
                </div>
            </div>
        </x-modal>
    </div>
</x-layouts.store>