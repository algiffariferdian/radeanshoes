<x-layouts.admin :title="'Edit Produk · Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="max-w-4xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <h1 class="text-3xl font-black tracking-tight text-stone-950">Edit Produk</h1>
            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="mt-6">
                @csrf
                @method('PATCH')
                @include('admin.products._form', ['product' => $product])
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Perbarui Produk</button>
                    <a href="{{ route('admin.products.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Kembali</a>
                </div>
            </form>
        </div>

        @if ($product->images->count())
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-2xl font-black tracking-tight text-stone-950">Gambar Produk</h2>
                    <a href="{{ route('admin.products.variants.index', $product) }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700">Kelola Varian</a>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($product->images as $image)
                        <div class="overflow-hidden rounded-[1.5rem] border border-stone-200">
                            <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-cover">
                            <div class="flex items-center justify-between px-4 py-3 text-sm">
                                <span class="font-semibold text-stone-700">{{ $image->is_primary ? 'Primary' : 'Gallery' }}</span>
                                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
