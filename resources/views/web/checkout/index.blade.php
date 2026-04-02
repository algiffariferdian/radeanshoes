<x-layouts.store :title="'Checkout · RadeanShoes'">
    <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
        <form method="POST" action="{{ route('checkout.place-order') }}" class="space-y-6">
            @csrf
            <section class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Checkout</p>
                        <h1 class="text-3xl font-black tracking-tight text-stone-950">Alamat Pengiriman</h1>
                    </div>
                    <a href="{{ route('addresses.create') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700">Tambah Alamat</a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($addresses as $address)
                        <label class="flex cursor-pointer items-start gap-4 rounded-[1.5rem] border border-stone-200 p-4">
                            <input type="radio" name="address_id" value="{{ $address->id }}" class="mt-1" @checked(old('address_id', $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id) == $address->id)>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-stone-950">{{ $address->recipient_name }}</p>
                                    @if ($address->is_default)
                                        <span class="rounded-full bg-stone-950 px-3 py-1 text-xs font-semibold text-stone-50">Default</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-stone-600">{{ $address->phone }}</p>
                                <p class="mt-2 text-sm leading-6 text-stone-600">{{ $address->address_line }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 p-5 text-sm text-stone-600">Belum ada alamat. Tambahkan minimal satu alamat untuk melanjutkan checkout.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Shipping</p>
                <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Pilih Opsi Pengiriman</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($shippingOptions as $shippingOption)
                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[1.5rem] border border-stone-200 p-4">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="shipping_option_id" value="{{ $shippingOption->id }}" class="mt-1" @checked(old('shipping_option_id') == $shippingOption->id)>
                                <div>
                                    <p class="font-semibold text-stone-950">{{ $shippingOption->courier_name }} · {{ $shippingOption->service_name }}</p>
                                    <p class="text-sm text-stone-600">Estimasi {{ $shippingOption->etd_text }}</p>
                                </div>
                            </div>
                            <p class="font-bold text-stone-950">Rp{{ number_format((float) $shippingOption->price, 0, ',', '.') }}</p>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <label class="text-sm font-semibold text-stone-900" for="notes">Catatan Order</label>
                <textarea id="notes" name="notes" rows="4" class="mt-2 w-full rounded-[1.25rem] border border-stone-300 px-4 py-3 text-sm" placeholder="Instruksi tambahan untuk admin atau pengiriman">{{ old('notes') }}</textarea>
            </section>

            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-stone-950 px-6 py-3 text-sm font-semibold text-stone-50">Buat Order & Lanjut Bayar</button>
        </form>

        <aside class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Order Summary</p>
            <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Item yang dibayar</h2>
            <div class="mt-6 space-y-4">
                @foreach ($cart->items as $item)
                    <div class="flex items-start justify-between gap-3 border-b border-stone-100 pb-4">
                        <div>
                            <p class="font-semibold text-stone-950">{{ $item->productVariant->product->name }}</p>
                            <p class="text-sm text-stone-600">{{ $item->productVariant->size }} / {{ $item->productVariant->color }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-stone-900">{{ $item->qty }}x</p>
                            <p class="text-sm text-stone-600">Rp{{ number_format((float) $item->unit_price_snapshot, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
</x-layouts.store>
