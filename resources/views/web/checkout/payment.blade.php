<x-layouts.store :title="'Pembayaran · '.$order->order_number">
    <div class="mx-auto max-w-3xl rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Snap Transaction</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">Order {{ $order->order_number }} berhasil dibuat</h1>
        <p class="mt-4 text-base leading-7 text-stone-600">Sistem sudah membuat snapshot order dan menyiapkan transaksi pembayaran. Popup Midtrans akan dibuka otomatis jika client key sandbox sudah tersedia.</p>

        <div class="mt-6 rounded-[1.5rem] bg-stone-50 p-5 text-sm text-stone-700">
            <div class="flex items-center justify-between">
                <span>Total pembayaran</span>
                <strong class="text-stone-950">Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <span>Status</span>
                <strong class="text-stone-950">{{ $order->order_status->label() }}</strong>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('orders.show', $order->order_number) }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Lihat Detail Order</a>
            @if ($midtransClientKey && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
                <button id="pay-now" type="button" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Bayar Sekarang</button>
            @endif
        </div>

        @if (! $midtransClientKey || str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
            <p class="mt-6 rounded-[1.25rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">MIDTRANS_CLIENT_KEY atau server key belum dikonfigurasi, jadi transaksi ini dibuat dalam mode mock lokal. Order tetap tersimpan dan bisa diperiksa dari halaman detail order.</p>
        @endif
    </div>

    @if ($midtransClientKey && ! str_starts_with($order->midtrans_snap_token ?? '', 'sandbox-'))
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const pay = () => {
                    window.snap.pay(@json($order->midtrans_snap_token), {
                        onSuccess: () => window.location.href = @json(route('checkout.finish', ['order' => $order->order_number])),
                        onPending: () => window.location.href = @json(route('checkout.unfinish', ['order' => $order->order_number])),
                        onError: () => window.location.href = @json(route('checkout.error', ['order' => $order->order_number])),
                        onClose: () => window.location.href = @json(route('orders.show', $order->order_number)),
                    });
                };

                document.getElementById('pay-now')?.addEventListener('click', pay);
                pay();
            });
        </script>
    @endif
</x-layouts.store>
