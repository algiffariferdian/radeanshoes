<x-layouts.store :title="'Pesanan Saya - RadeanShoes'">
    <div x-data="ordersPage({
            baseUrl: @js(route('orders.index')),
            initialStatus: @js($activeStatus),
            initialSort: @js($activeSort),
            initialSearch: @js($activeSearch),
            defaultSort: 'latest',
        })" class="space-y-5 lg:space-y-6">
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Pesanan Saya'],
    ]" />

        <div class="space-y-2">
            <h1 class="text-2xl font-semibold text-[var(--text-primary)]">Pesanan Saya</h1>
            <p class="max-w-2xl text-sm text-[var(--text-secondary)]">Ringkasan tiap pesanan menampilkan item utama,
                pembayaran, pengiriman, dan progres pesanan.</p>
        </div>

        <section
            class="overflow-hidden rounded-[1rem] border border-[var(--border-soft)] bg-white shadow-[0_1px_2px_rgba(16,24,20,0.04)]">
            <div class="space-y-4 px-4 py-4 sm:px-6 sm:py-5 items-center object-center justify-center">
                <nav class="-mb-1 flex items-center object-center justify-center gap-2 overflow-x-auto pb-1">
                    <a x-bind:href="buildUrl({ status: '' })" @click.prevent="setStatus('')"
                        class="inline-flex shrink-0 items-center gap-2 rounded-[0.75rem] border px-3 py-2 text-sm font-medium transition"
                        :class="status === '' ? 'border-[#a9cdb8] bg-[#eef7f1] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-secondary)] hover:border-[#bfd0c6] hover:text-[var(--text-primary)]'">
                        <span>Semua</span>
                        <span
                            class="rounded-[0.5rem] bg-black/5 px-2 py-0.5 text-xs text-inherit">{{ number_format($totalOrdersCount, 0, ',', '.') }}</span>
                    </a>

                    @foreach ($statusOptions as $status)
                        <a x-bind:href="buildUrl({ status: @js($status['value']) })"
                            @click.prevent="setStatus(@js($status['value']))"
                            class="inline-flex shrink-0 items-center gap-2 rounded-[0.75rem] border px-3 py-2 text-sm font-medium transition"
                            :class="status === @js($status['value']) ? 'border-[#a9cdb8] bg-[#eef7f1] text-[var(--accent-primary)]' : 'border-[var(--border-soft)] bg-white text-[var(--text-secondary)] hover:border-[#bfd0c6] hover:text-[var(--text-primary)]'">
                            <span>{{ $status['label'] }}</span>
                            <span
                                class="rounded-[0.5rem] bg-black/5 px-2 py-0.5 text-xs text-inherit">{{ number_format($status['count'], 0, ',', '.') }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="grid gap-3 lg:grid-cols-[minmax(0,1.618fr)_250px]">
                    <div class="rounded-[0.9rem] border border-[var(--border-soft)] bg-[var(--surface-soft)] px-4 py-3">
                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                            Cari Pesanan</p>
                        <div class="flex items-center gap-3">
                            <x-store.icon name="search" class="h-4 w-4 shrink-0 text-[var(--text-muted)]" />
                            <input type="text" x-model="search" @input="onSearchInput()"
                                @keydown.enter.prevent="submitFilters()" placeholder="Cari nomor pesanan atau produk"
                                class="w-full border-0 bg-transparent p-0 text-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:ring-0">
                            <button type="button" x-show="search !== ''" x-cloak @click="clearSearch()"
                                class="shrink-0 text-xs font-semibold text-[var(--text-secondary)] transition hover:text-[var(--text-primary)]">
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="rounded-[0.9rem] border border-[var(--border-soft)] bg-white px-4 py-3">
                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                            Urutkan</p>
                        <select x-model="sort" @change="onSortChange()"
                            class="w-full border-0 bg-transparent p-0 text-sm font-medium text-[var(--text-primary)] focus:ring-0">
                            @foreach ($sortOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <div x-ref="listing" class="space-y-5 transition-opacity duration-150"
            :class="isLoading ? 'opacity-60' : 'opacity-100'">
            @include('web.orders.partials.listing')
        </div>
    </div>
</x-layouts.store>