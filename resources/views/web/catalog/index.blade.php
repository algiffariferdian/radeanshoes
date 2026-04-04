<x-layouts.store :title="'Katalog Produk - RadeanShoes'">
    <div x-data="{ filtersOpen: false }" class="space-y-6">
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Katalog Produk'],
    ]" />

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <button type="button" class="btn-secondary lg:hidden" @click="filtersOpen = !filtersOpen">
                <x-store.icon name="filter" class="h-4 w-4" />
                Filter & Sort
            </button>
        </div>

        @if ($search || $categoryId || $color || $size || $minPrice || $maxPrice)
            <div class="flex flex-wrap items-center gap-2">
                <span class="meta-copy">Filter aktif:</span>
                @if ($search)
                    <span class="badge-neutral">Cari: {{ $search }}</span>
                @endif
                @if ($categoryId)
                    <span class="badge-neutral">Kategori:
                        {{ optional($categories->firstWhere('id', $categoryId))->name }}</span>
                @endif
                @if ($color)
                    <span class="badge-neutral">Warna: {{ $color }}</span>
                @endif
                @if ($size)
                    <span class="badge-neutral">Ukuran: {{ $size }}</span>
                @endif
                @if ($minPrice)
                    <span class="badge-neutral">Min: Rp{{ number_format($minPrice, 0, ',', '.') }}</span>
                @endif
                @if ($maxPrice)
                    <span class="badge-neutral">Max: Rp{{ number_format($maxPrice, 0, ',', '.') }}</span>
                @endif
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-[var(--accent-primary)]">Reset
                    filter</a>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside x-cloak x-show="filtersOpen || window.innerWidth >= 1024" x-transition
                class="surface-card h-fit p-5">
                <form id="catalog-filters" method="GET" action="{{ route('products.index') }}" class="space-y-5">
                    <div>
                        <label class="field-label" for="q">Cari produk</label>
                        <input id="q" name="q" value="{{ $search }}" class="input-field" placeholder="Nama produk">
                    </div>

                    <div class="space-y-3">
                        <p class="field-label mb-0">Kategori</p>
                        <select id="category" name="category" class="select-field">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <p class="field-label mb-0">Ukuran</p>
                        <select id="size" name="size" class="select-field">
                            <option value="">Semua ukuran</option>
                            @foreach ($sizes as $optionSize)
                                <option value="{{ $optionSize }}" @selected($size === $optionSize)>{{ $optionSize }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <p class="field-label mb-0">Warna</p>
                        <select id="color" name="color" class="select-field">
                            <option value="">Semua warna</option>
                            @foreach ($colors as $optionColor)
                                <option value="{{ $optionColor }}" @selected($color === $optionColor)>{{ $optionColor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div>
                            <label class="field-label" for="min_price">Harga minimum</label>
                            <input id="min_price" name="min_price" value="{{ $minPrice }}" class="input-field"
                                placeholder="250000">
                        </div>
                        <div>
                            <label class="field-label" for="max_price">Harga maksimum</label>
                            <input id="max_price" name="max_price" value="{{ $maxPrice }}" class="input-field"
                                placeholder="1250000">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <button type="submit" class="btn-primary w-full">Terapkan Filter</button>
                        <a href="{{ route('products.index') }}" class="btn-secondary w-full text-center">Reset</a>
                    </div>
                </form>
            </aside>

            <section class="space-y-5">
                <div class="surface-card flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-semibold text-[var(--text-primary)]" for="sort">Urutkan</label>
                        <div class="relative min-w-[210px]">
                            <select id="sort" name="sort" form="catalog-filters" onchange="this.form.submit()"
                                class="select-field pr-10">
                                @foreach ($sortOptions as $key => $label)
                                    <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-store.icon name="sort"
                                class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                        </div>
                    </div>
                </div>

                @if ($products->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($products as $product)
                            <x-store.product-card :product="$product" />
                        @endforeach
                    </div>
                @else
                    <x-store.empty-state icon="search" title="Produk tidak ditemukan"
                        body="Coba ubah kata kunci pencarian atau atur ulang filter untuk melihat hasil lain.">
                        <div class="mt-5">
                            <a href="{{ route('products.index') }}" class="btn-primary">Lihat semua produk</a>
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