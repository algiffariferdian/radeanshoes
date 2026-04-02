<x-layouts.store :title="'Tambah Alamat - RadeanShoes'">
    <div class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Alamat', 'url' => route('addresses.index')],
            ['label' => 'Tambah Alamat'],
        ]" />

        <div class="mx-auto max-w-4xl surface-card-strong p-6">
            <div class="mb-5">
                <p class="heading-eyebrow">Tambah alamat</p>
                <h1 class="heading-page text-[clamp(1.7rem,2.5vw,2.25rem)]">Simpan alamat baru</h1>
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Gunakan alamat ini untuk mempercepat checkout dan pengiriman berikutnya.</p>
            </div>

            <form method="POST" action="{{ route('addresses.store') }}">
                @csrf
                @include('web.addresses._form')
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary">Simpan Alamat</button>
                    <a href="{{ route('addresses.index') }}" class="btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.store>
