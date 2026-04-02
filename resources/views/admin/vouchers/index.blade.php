<x-layouts.admin :title="'Voucher - Admin RadeanShoes'">
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Marketing</p>
                <h1 class="text-3xl font-black tracking-tight text-stone-950">Voucher</h1>
            </div>
            <a href="{{ route('admin.vouchers.create') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Tambah Voucher</a>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Kode</th>
                            <th class="pb-3">Diskon</th>
                            <th class="pb-3">Periode</th>
                            <th class="pb-3">Penggunaan</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($vouchers as $voucher)
                            <tr>
                                <td class="py-4">
                                    <p class="font-semibold text-stone-900">{{ $voucher->code }}</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $voucher->name }}</p>
                                </td>
                                <td class="py-4 text-stone-600">
                                    @if ($voucher->discount_type === \App\Support\Enums\VoucherDiscountType::Percent)
                                        {{ rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') }}%
                                    @else
                                        Rp{{ number_format((float) $voucher->discount_value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="py-4 text-stone-600">
                                    @if ($voucher->starts_at || $voucher->ends_at)
                                        {{ $voucher->starts_at?->format('d M Y H:i') ?: '-' }}<br>
                                        <span class="text-xs text-stone-500">sampai {{ $voucher->ends_at?->format('d M Y H:i') ?: '-' }}</span>
                                    @else
                                        Tanpa batas waktu
                                    @endif
                                </td>
                                <td class="py-4 text-stone-600">
                                    {{ number_format((int) $voucher->used_count, 0, ',', '.') }}
                                    @if ($voucher->usage_limit)
                                        <span class="text-xs text-stone-500">/ {{ number_format((int) $voucher->usage_limit, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $voucher->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 font-semibold text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-sm text-stone-500">Belum ada voucher.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $vouchers->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
