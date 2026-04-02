<x-layouts.store :title="'Edit Alamat - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Alamat', 'url' => route('addresses.index')],
            ['label' => 'Edit Alamat'],
        ]" />

        <div class="mx-auto max-w-4xl surface-card-strong p-6">
            <div class="mb-5">
                <p class="heading-eyebrow">Edit alamat</p>
                <h1 class="heading-page text-[clamp(1.7rem,2.5vw,2.25rem)]">Perbarui alamat pengiriman</h1>
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Pastikan detail penerima, kota, dan kode pos sudah benar sebelum disimpan.</p>
            </div>

            <form method="POST" action="{{ route('addresses.update', $address) }}">
                @csrf
                @method('PATCH')
                @include('web.addresses._form', ['address' => $address])
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary">Perbarui Alamat</button>
                    <a href="{{ route('addresses.index') }}" class="btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.store>
