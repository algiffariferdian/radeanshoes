<x-layouts.admin :title="'Kategori · Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Catalog</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Kategori</h1>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Kategori</a>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">Slug</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="py-4 font-semibold text-stone-900">{{ $category->name }}</td>
                                <td class="py-4 text-stone-600">{{ $category->slug }}</td>
                                <td class="py-4">
                                    <span class="rounded-full {{ $category->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }} px-3 py-1 text-xs font-semibold">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 font-semibold text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $categories->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
