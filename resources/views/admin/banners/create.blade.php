<x-layouts.admin :title="'Tambah Banner - Admin RadeanShoes'">
    <div class="mx-auto max-w-4xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Marketing</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">Tambah Banner</h1>
        </div>

        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.banners._form')

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.banners.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Batal</a>
                <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Simpan Banner</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
