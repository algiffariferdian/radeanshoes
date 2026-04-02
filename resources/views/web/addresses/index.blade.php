<x-layouts.store :title="'Alamat Pengiriman - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Akun Saya', 'url' => route('account.profile.edit')],
            ['label' => 'Alamat'],
        ]" />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="heading-eyebrow">Alamat pengiriman</p>
                <h1 class="heading-page text-[clamp(1.75rem,2.8vw,2.4rem)]">Kelola alamat untuk checkout yang lebih cepat</h1>
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Atur alamat utama, simpan beberapa tujuan pengiriman, dan perbarui detail kapan saja.</p>
            </div>
            <a href="{{ route('addresses.create') }}" class="btn-primary">Tambah Alamat</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($addresses as $address)
                <article class="surface-card-strong p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-base font-semibold text-[var(--text-primary)]">{{ $address->recipient_name }}</p>
                                @if ($address->is_default)
                                    <span class="badge-accent">Utama</span>
                                @endif
                                @if ($address->label)
                                    <span class="badge-neutral">{{ $address->label }}</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-[var(--text-secondary)]">{{ $address->phone }}</p>
                        </div>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-[var(--text-secondary)]">{{ $address->address_line }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>

                    <div class="section-divider mt-5 pt-5">
                        <div class="flex gap-3">
                            <a href="{{ route('addresses.edit', $address) }}" class="btn-secondary flex-1 text-center">Edit</a>
                            <form method="POST" action="{{ route('addresses.destroy', $address) }}" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <x-store.empty-state
                    class="md:col-span-2 xl:col-span-3"
                    icon="map-pin"
                    title="Belum ada alamat tersimpan"
                    body="Tambahkan alamat pertama untuk mempercepat checkout dan pengiriman."
                >
                    <div class="mt-5">
                        <a href="{{ route('addresses.create') }}" class="btn-primary">Tambah Alamat</a>
                    </div>
                </x-store.empty-state>
            @endforelse
        </div>
    </div>
</x-layouts.store>
