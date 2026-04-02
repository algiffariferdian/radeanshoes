<x-layouts.admin :title="'Tambah Kategori · Admin RadeanShoes'">
    <div class="max-w-3xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
        <h1 class="text-3xl font-black tracking-tight text-stone-950">Tambah Kategori</h1>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6">
            @csrf
            @include('admin.categories._form')
            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Simpan Kategori</button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Kembali</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
