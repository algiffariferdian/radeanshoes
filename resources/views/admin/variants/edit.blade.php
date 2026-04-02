<x-layouts.admin :title="'Edit Varian - Admin RadeanShoes'">
    <div class="max-w-4xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
        <h1 class="text-3xl font-black tracking-tight text-stone-950">Edit Varian · {{ $product->name }}</h1>
        <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            @method('PATCH')
            @include('admin.variants._form', ['variant' => $variant])
            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Perbarui Varian</button>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Kembali</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
