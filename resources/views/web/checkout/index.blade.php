<x-layouts.store :title="'Checkout - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Keranjang', 'url' => route('cart.index')],
            ['label' => 'Checkout'],
        ]" />

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
            <form method="POST" action="{{ route('checkout.place-order') }}" class="space-y-5">
                @csrf

                <section class="surface-card-strong p-5 sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="heading-eyebrow">Checkout</p>
                            <h1 class="heading-page text-[clamp(1.75rem,2.8vw,2.4rem)]">Alamat pengiriman</h1>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Pilih alamat yang akan digunakan untuk pengiriman pesananmu.</p>
                        </div>
                        <a href="{{ route('addresses.create') }}" class="btn-secondary px-4 py-2.5">Tambah Alamat</a>
                    </div>

                    <div class="mt-5 grid gap-4">
                        @forelse ($addresses as $address)
                            <label class="surface-soft flex cursor-pointer gap-4 p-4">
                                <input
                                    type="radio"
                                    name="address_id"
                                    value="{{ $address->id }}"
                                    class="mt-1 h-4 w-4 border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]"
                                    @checked(old('address_id', $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id) == $address->id)
                                >
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $address->recipient_name }}</p>
                                        @if ($address->is_default)
                                            <span class="badge-accent">Utama</span>
                                        @endif
                                        @if ($address->label)
                                            <span class="badge-neutral">{{ $address->label }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $address->phone }}</p>
                                    <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">{{ $address->address_line }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                </div>
                            </label>
                        @empty
                            <x-store.empty-state
                                icon="map-pin"
                                title="Belum ada alamat pengiriman"
                                body="Tambahkan minimal satu alamat agar checkout bisa diproses."
                            >
                                <div class="mt-5">
                                    <a href="{{ route('addresses.create') }}" class="btn-primary">Tambah Alamat</a>
                                </div>
                            </x-store.empty-state>
                        @endforelse
                    </div>
                </section>

                <section class="surface-card p-5 sm:p-6">
                    <p class="heading-eyebrow">Pilihan kurir</p>
                    <h2 class="heading-section">Pilih layanan pengiriman</h2>
                    <div class="mt-5 grid gap-4">
                        @foreach ($shippingOptions as $shippingOption)
                            <label class="surface-soft flex cursor-pointer items-center justify-between gap-4 p-4">
                                <div class="flex items-start gap-4">
                                    <input
                                        type="radio"
                                        name="shipping_option_id"
                                        value="{{ $shippingOption->id }}"
                                        class="mt-1 h-4 w-4 border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]"
                                        @checked(old('shipping_option_id', request('shipping_option_id', $selectedShipping?->id ?? $defaultShipping?->id)) == $shippingOption->id)
                                    >
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $shippingOption->courier_name }} - {{ $shippingOption->service_name }}</p>
                                        <p class="mt-1 text-sm text-[var(--text-secondary)]">Estimasi {{ $shippingOption->etd_text }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-[var(--text-primary)]">Rp{{ number_format((float) $shippingOption->price, 0, ',', '.') }}</p>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="surface-card p-5 sm:p-6">
                    <p class="heading-eyebrow">Metode pembayaran</p>
                    <h2 class="heading-section">Pilih metode pembayaran</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="surface-soft p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.85rem] bg-[var(--accent-soft)] text-[var(--accent-primary)]">
                                    <x-store.icon name="credit-card" class="h-4 w-4" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">Midtrans Snap</p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">Kartu, virtual account, e-wallet, dan QRIS dalam satu alur pembayaran.</p>
                                </div>
                            </div>
                        </div>
                        <div class="surface-soft p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.85rem] bg-[var(--accent-soft)] text-[var(--accent-primary)]">
                                    <x-store.icon name="wallet" class="h-4 w-4" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">Kanal pembayaran fleksibel</p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">Daftar metode yang tampil mengikuti channel yang tersedia di Midtrans sandbox.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="surface-card p-5 sm:p-6">
                    <div class="grid gap-5 lg:grid-cols-[1fr_1fr]">
                        <div>
                            <label class="field-label" for="notes">Catatan untuk penjual</label>
                            <textarea id="notes" name="notes" rows="5" class="textarea-field" placeholder="Contoh: titip di resepsionis, kirim sore hari, atau catatan ukuran.">{{ old('notes') }}</textarea>
                        </div>
                        <div class="surface-soft p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-[0.85rem] bg-[#fff3eb] text-[var(--discount)]">
                                    <x-store.icon name="tag" class="h-4 w-4" />
                                </div>
                                <div class="w-full">
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">Gunakan Voucher</p>
                                    <p class="mt-1 text-sm leading-6 text-[var(--text-secondary)]">Masukkan kode voucher untuk cek potongan sebelum order dibuat.</p>
                                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                                        <input
                                            id="voucher_code"
                                            name="voucher_code"
                                            value="{{ old('voucher_code', $voucherPreview['code']) }}"
                                            class="input-field"
                                            placeholder="Contoh: HEMAT20"
                                        >
                                        <button
                                            type="submit"
                                            formaction="{{ route('checkout.index') }}"
                                            formmethod="GET"
                                            class="btn-secondary shrink-0"
                                        >
                                            Gunakan Voucher
                                        </button>
                                    </div>
                                    @if ($voucherPreview['voucher'])
                                        <div class="mt-3 rounded-[0.9rem] border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                                            Voucher <strong>{{ $voucherPreview['voucher']->code }}</strong> aktif. Potongan Rp{{ number_format((float) $voucherPreview['discount_amount'], 0, ',', '.') }}.
                                        </div>
                                    @elseif ($voucherPreview['code'] && $voucherPreview['error'])
                                        <div class="mt-3 rounded-[0.9rem] border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                                            {{ $voucherPreview['error'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <button type="submit" class="btn-primary w-full py-4 text-base" @disabled($addresses->isEmpty() || $shippingOptions->isEmpty())>
                    Bayar Sekarang
                </button>
            </form>

            <aside class="space-y-4 xl:sticky xl:top-28 xl:self-start">
                <div class="surface-card p-5">
                    <p class="heading-eyebrow">Ringkasan barang</p>
                    <h2 class="mt-2 text-xl font-bold text-[var(--text-primary)]">Checkout summary</h2>

                    <div class="mt-5 space-y-4">
                        @foreach ($cart->items as $item)
                            <div class="flex gap-3">
                                <div class="h-16 w-16 overflow-hidden rounded-[0.9rem] bg-[var(--surface-soft)]">
                                    @if ($item->productVariant->product->primary_image_url)
                                        <img src="{{ $item->productVariant->product->primary_image_url }}" alt="{{ $item->productVariant->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-2xl font-semibold text-[var(--text-muted)]">!</div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-[var(--text-primary)]">{{ $item->productVariant->product->name }}</p>
                                    <p class="mt-1 text-xs text-[var(--text-secondary)]">{{ $item->productVariant->size }} / {{ $item->productVariant->color }}</p>
                                    <div class="mt-2 flex items-center justify-between text-sm">
                                        <span class="text-[var(--text-secondary)]">{{ $item->qty }}x</span>
                                        <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $item->productVariant->effectivePrice(), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="section-divider mt-5 pt-5 text-sm text-[var(--text-secondary)]">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format($cartSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span>Estimasi ongkir</span>
                            <span class="font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) ($selectedShipping?->price ?? 0), 0, ',', '.') }}</span>
                        </div>
                        @if ((float) $voucherPreview['discount_amount'] > 0)
                            <div class="mt-3 flex items-center justify-between">
                                <span>Diskon voucher</span>
                                <span class="font-semibold text-emerald-700">-Rp{{ number_format((float) $voucherPreview['discount_amount'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="section-divider mt-5 pt-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[var(--text-primary)]">Total pembayaran</span>
                            <span class="text-xl font-extrabold text-[var(--text-primary)]">Rp{{ number_format($estimatedTotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-2 text-xs leading-5 text-[var(--text-muted)]">Total akhir akan mengikuti layanan kurir yang kamu pilih sebelum order dibuat.</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.store>
