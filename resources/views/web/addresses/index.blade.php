<x-layouts.store :title="'Alamat · RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Address Book</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Alamat Pengiriman</h1>
            </div>
            <a href="{{ route('addresses.create') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Alamat</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            @forelse ($addresses as $address)
                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-stone-950">{{ $address->recipient_name }}</p>
                            <p class="text-sm text-stone-600">{{ $address->phone }}</p>
                        </div>
                        @if ($address->is_default)
                            <span class="rounded-full bg-stone-950 px-3 py-1 text-xs font-semibold text-stone-50">Default</span>
                        @endif
                    </div>
                    <p class="mt-4 text-sm leading-6 text-stone-600">{{ $address->address_line }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('addresses.edit', $address) }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700">Edit</a>
                        <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700">Hapus</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-stone-300 bg-white p-8 text-sm text-stone-600">Belum ada alamat tersimpan.</div>
            @endforelse
        </div>
    </div>
</x-layouts.store>
