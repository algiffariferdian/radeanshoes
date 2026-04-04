<x-layouts.store :title="'Katalog Produk - RadeanShoes'">
    @php
        $hasFilters = $search || $categoryId || $color || $size || $minPrice || $maxPrice;
        $totalProducts = $products->total();
        $from = $products->firstItem() ?? 0;
        $to = $products->lastItem() ?? 0;
    @endphp

    <div x-data="{ filtersOpen: false }" class="space-y-6">
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Katalog Produk'],
        ]" />

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold text-[var(--text-primary)]">KATALOG PRODUK</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="btn-secondary rounded-[0.6rem] px-4 py-2 text-sm lg:hidden" @click="filtersOpen = !filtersOpen">
                    <x-store.icon name="filter" class="h-4 w-4" />
                    Filter
                </button>
                <div class="relative min-w-[220px]">
                    <label class="sr-only" for="sort">Urutkan</label>
                    <select id="sort" name="sort" form="catalog-filters" onchange="this.form.submit()"
                        class="select-field rounded-[0.65rem] pr-10 text-sm">
                        @foreach ($sortOptions as $key => $label)
                            <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-store.icon name="sort"
                        class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                </div>
            </div>
        </div>

        @if ($hasFilters)
            <div
                class="flex flex-wrap items-center gap-2 rounded-[0.7rem] border border-[var(--border-soft)] bg-white px-3 py-2 text-xs text-[var(--text-secondary)] shadow-[0_1px_2px_rgba(16,24,20,0.04)]">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Filter aktif</span>
                @if ($search)
                    <span class="inline-flex rounded-[0.5rem] bg-[#f2f4f3] px-2 py-1">Cari: {{ $search }}</span>
                @endif
                @if ($categoryId)
                    <span class="inline-flex rounded-[0.5rem] bg-[#f2f4f3] px-2 py-1">Kategori:
                        {{ optional($categories->firstWhere('id', $categoryId))->name }}</span>
                @endif
                @if ($color)
                    <span class="inline-flex rounded-[0.5rem] bg-[#f2f4f3] px-2 py-1">Warna: {{ $color }}</span>
                @endif
                @if ($size)
                    <span class="inline-flex rounded-[0.5rem] bg-[#f2f4f3] px-2 py-1">Ukuran: {{ $size }}</span>
                @endif
                @if ($minPrice)
                    <span class="inline-flex rounded-[0.5rem] bg-[#f2f4f3] px-2 py-1">Min:
                        Rp{{ number_format($minPrice, 0, ',', '.') }}</span>
                @endif
                @if ($maxPrice)
                    <span class="inline-flex rounded-[0.5rem] bg-[#f2f4f3] px-2 py-1">Max:
                        Rp{{ number_format($maxPrice, 0, ',', '.') }}</span>
                @endif
                <a href="{{ route('products.index') }}" class="ml-auto text-xs font-semibold text-[var(--accent-primary)]">Reset semua</a>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            <aside x-cloak x-show="filtersOpen || window.innerWidth >= 1024" x-transition.opacity
                class="h-fit rounded-[0.75rem] border border-[var(--border-soft)] bg-white p-4 shadow-[0_1px_2px_rgba(16,24,20,0.05)]">
                <form id="catalog-filters" method="GET" action="{{ route('products.index') }}" class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-[var(--text-primary)]" for="q">Cari produk</label>
                        <input id="q" name="q" value="{{ $search }}" class="input-field rounded-[0.65rem] text-sm"
                            placeholder="Nama produk">
                    </div>

                    <div class="space-y-3 border-t border-[var(--border-soft)] pt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Kategori</p>
                        <select id="category" name="category" class="select-field rounded-[0.65rem] text-sm">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3 border-t border-[var(--border-soft)] pt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Ukuran</p>
                        <select id="size" name="size" class="select-field rounded-[0.65rem] text-sm">
                            <option value="">Semua ukuran</option>
                            @foreach ($sizes as $optionSize)
                                <option value="{{ $optionSize }}" @selected($size === $optionSize)>{{ $optionSize }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3 border-t border-[var(--border-soft)] pt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Warna</p>
                        <select id="color" name="color" class="select-field rounded-[0.65rem] text-sm">
                            <option value="">Semua warna</option>
                            @foreach ($colors as $optionColor)
                                <option value="{{ $optionColor }}" @selected($color === $optionColor)>{{ $optionColor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3 border-t border-[var(--border-soft)] pt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Rentang Harga (Rp)</p>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                            <div>
                                <label class="text-xs font-medium text-[var(--text-secondary)]" for="min_price">Minimum</label>
                                <input id="min_price" name="min_price" value="{{ $minPrice }}"
                                    class="input-field rounded-[0.65rem] text-sm" placeholder="250000">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-[var(--text-secondary)]" for="max_price">Maksimum</label>
                                <input id="max_price" name="max_price" value="{{ $maxPrice }}"
                                    class="input-field rounded-[0.65rem] text-sm" placeholder="1250000">
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-2 pt-2">
                        <button type="submit" class="btn-primary w-full rounded-[0.6rem] py-2 text-sm shadow-none">Terapkan</button>
                        <a href="{{ route('products.index') }}"
                            class="btn-secondary w-full rounded-[0.6rem] py-2 text-center text-sm shadow-none">Reset</a>
                    </div>
                </form>
            </aside>

            <section class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-[var(--text-secondary)]">
                        @if ($totalProducts > 0)
                            Menampilkan {{ $from }}-{{ $to }} dari {{ $totalProducts }} produk
                        @else
                            Belum ada produk untuk ditampilkan
                        @endif
                    </p>
                </div>

                @if ($products->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($products as $product)
                            @php
                                $productUrl = route('products.show', $product);
                                $soldLabel = number_format($product->sold_count, 0, ',', '.') . ' terjual';
                                $sizeLimit = 3;
                            @endphp
                            <article
                                class="group flex h-full flex-col overflow-hidden rounded-[0.75rem] border border-[var(--border-soft)] bg-white shadow-[0_1px_2px_rgba(16,24,20,0.05)] transition hover:border-[#c9d2cd]">
                                <a href="{{ $productUrl }}" class="block">
                                    <div class="relative aspect-square bg-[#f6f8f7]">
                                        @if ($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-4xl font-semibold text-[var(--text-muted)]">!</div>
                                        @endif
                                        @if ($product->discount_percentage > 0)
                                            <span
                                                class="absolute left-3 top-3 inline-flex items-center rounded-[0.4rem] bg-[#fff0ed] px-2 py-1 text-[10px] font-semibold text-[var(--discount)]">
                                                -{{ $product->discount_percentage }}%
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-2 p-3">
                                        <p class="text-xs font-medium text-[var(--text-muted)]">{{ $product->category->name }}</p>
                                        <h3
                                            class="line-clamp-2 text-sm font-semibold leading-5 text-[var(--text-primary)] group-hover:text-[var(--accent-primary)]">
                                            {{ $product->name }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-2 text-xs text-[var(--text-secondary)]">
                                            @if ($product->review_count > 0)
                                                <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count" size="h-3.5 w-3.5"
                                                    text-class="text-xs text-[var(--text-secondary)]" />
                                            @else
                                                <span class="text-[var(--text-muted)]">Belum ada rating</span>
                                            @endif
                                            <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                                            <span>{{ $soldLabel }}</span>
                                        </div>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-base font-semibold text-[var(--text-primary)]">Rp{{ number_format((float) $product->lowest_display_price, 0, ',', '.') }}</span>
                                            @if ($product->compare_at_price)
                                                <span class="text-xs text-[var(--text-muted)] line-through">Rp{{ number_format((float) $product->compare_at_price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach (collect($product->available_sizes)->take($sizeLimit) as $size)
                                                <span
                                                    class="inline-flex rounded-[0.4rem] bg-[#f2f4f3] px-2 py-0.5 text-[10px] font-semibold text-[var(--text-secondary)]">EU
                                                    {{ $size }}</span>
                                            @endforeach
                                            @if (count($product->available_sizes) > $sizeLimit)
                                                <span
                                                    class="inline-flex rounded-[0.4rem] bg-[#f2f4f3] px-2 py-0.5 text-[10px] font-semibold text-[var(--text-secondary)]">+{{ count($product->available_sizes) - $sizeLimit }}
                                                    ukuran</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                                <div class="mt-auto px-3 pb-3">
                                    <a href="{{ $productUrl }}#purchase"
                                        class="btn-primary w-full rounded-[0.6rem] py-2 text-sm shadow-none">Pilih Produk</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-store.empty-state icon="search" title="Produk tidak ditemukan"
                        body="Coba ubah kata kunci pencarian atau atur ulang filter untuk melihat hasil lain.">
                        <div class="mt-5">
                            <a href="{{ route('products.index') }}" class="btn-primary rounded-[0.6rem] shadow-none">Lihat semua produk</a>
                        </div>
                    </x-store.empty-state>
                @endif

                <div class="pt-2">
                    {{ $products->links() }}
                </div>
            </section>
        </div>
    </div>
</x-layouts.store>
