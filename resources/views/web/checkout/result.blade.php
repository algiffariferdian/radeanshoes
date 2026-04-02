<x-layouts.store :title="'Checkout Result · RadeanShoes'">
    @php
        $copy = match ($state) {
            'finish' => ['title' => 'Pembayaran selesai diproses', 'body' => 'Status final akan diselaraskan oleh webhook Midtrans. Anda bisa memantau perubahan status dari detail order.'],
            'unfinish' => ['title' => 'Pembayaran belum diselesaikan', 'body' => 'Order tetap tercatat dalam status menunggu pembayaran. Anda dapat melanjutkan dari detail order selama transaksi belum expired.'],
            default => ['title' => 'Terjadi kendala saat pembayaran', 'body' => 'Cek kembali status order Anda. Jika belum dibayar, Anda bisa mencoba ulang dari halaman detail order.'],
        };
    @endphp

    <div class="mx-auto max-w-3xl rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">Payment Result</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">{{ $copy['title'] }}</h1>
        <p class="mt-4 text-base leading-7 text-stone-600">{{ $copy['body'] }}</p>

        @if ($order)
            <div class="mt-6 rounded-[1.5rem] bg-stone-50 p-5 text-sm text-stone-700">
                <div class="flex items-center justify-between">
                    <span>Order</span>
                    <strong class="text-stone-950">{{ $order->order_number }}</strong>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span>Status order</span>
                    <strong class="text-stone-950">{{ $order->order_status->label() }}</strong>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span>Status payment</span>
                    <strong class="text-stone-950">{{ $order->payment_status->label() }}</strong>
                </div>
            </div>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ $order ? route('orders.show', $order->order_number) : route('orders.index') }}" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Lihat Order</a>
            <a href="{{ route('products.index') }}" class="rounded-full border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">Kembali ke Katalog</a>
        </div>
    </div>
</x-layouts.store>
