<x-layouts.admin :title="'Banner - Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Marketing</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Banner</h1>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Banner</a>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Banner</th>
                            <th class="pb-3">Urutan</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($banners as $banner)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-20 w-36 overflow-hidden rounded-[1rem] bg-stone-100">
                                            @if ($banner->image_url)
                                                <img src="{{ $banner->image_url }}" alt="Banner" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0 text-sm text-stone-500">
                                            Banner gambar
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-stone-600">{{ $banner->sort_order }}</td>
                                <td class="py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $banner->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 font-semibold text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-sm text-stone-500">Belum ada banner.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $banners->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
