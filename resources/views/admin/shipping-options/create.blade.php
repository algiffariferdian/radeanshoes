<x-layouts.admin :title="'Tambah Shipping Option · Admin RadeanShoes'">
    <div class="max-w-3xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
        <h1 class="text-3xl font-black tracking-tight text-stone-950">Tambah Opsi Pengiriman</h1>
        <form method="POST" action="{{ route('admin.shipping-options.store') }}" class="mt-6">
            @csrf
            @include('admin.shipping-options._form')
            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Simpan Opsi</button>
                <a href="{{ route('admin.shipping-options.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Kembali</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
