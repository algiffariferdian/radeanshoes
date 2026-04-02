<x-layouts.admin :title="'Shipping Options · Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Shipping</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Shipping Options</h1>
            </div>
            <a href="{{ route('admin.shipping-options.create') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Opsi</a>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Kurir</th>
                            <th class="pb-3">Layanan</th>
                            <th class="pb-3">Estimasi</th>
                            <th class="pb-3">Harga</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($shippingOptions as $option)
                            <tr>
                                <td class="py-4 font-semibold text-stone-900">{{ $option->courier_name }}</td>
                                <td class="py-4 text-stone-600">{{ $option->service_name }}</td>
                                <td class="py-4 text-stone-600">{{ $option->etd_text }}</td>
                                <td class="py-4 text-stone-600">Rp{{ number_format((float) $option->price, 0, ',', '.') }}</td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.shipping-options.edit', $option) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.shipping-options.destroy', $option) }}">
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
            <div class="mt-6">{{ $shippingOptions->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
