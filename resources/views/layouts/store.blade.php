<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'RadeanShoes') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[var(--bg-main)] text-[var(--text-primary)] antialiased">
    @php
        $categoryDropdownItems = $storefrontNavCategories->map(fn($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'url' => route('products.index', ['category' => $category->id]),
            'productsCount' => (int) $category->products_count,
            'variantsCount' => (int) ($category->variants_count ?? 0),
            'productsLabel' => $category->products_count > 0
                ? $category->products_count . ' produk • ' . ((int) ($category->variants_count ?? 0)) . ' varian'
                : 'Belum ada produk',
            'previewImages' => collect($category->getRelation('previewProducts') ?? [])
                ->flatMap(fn($product) => collect($product->getAttribute('category_preview_image_urls') ?? [])
                    ->map(fn($imageUrl, $index) => [
                        'id' => $product->id . '-' . $index,
                        'productId' => $product->id,
                        'productName' => $product->name,
                        'productUrl' => route('products.show', $product),
                        'imageUrl' => $imageUrl,
                    ]))
                ->values(),
            'previewProducts' => collect($category->getRelation('previewProducts') ?? [])
                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('products.show', $product),
                    'imageUrl' => $product->getAttribute('category_preview_image_url'),
                    'imageUrls' => $product->getAttribute('category_preview_image_urls') ?? [],
                ])
                ->values(),
        ])->values();
    @endphp

    <div x-data="storefrontHeader(@js($categoryDropdownItems))" @keydown.escape.window="closeAllMenus()"
        @click.window="if (categoryMenu && $refs.categoryMenuRoot && ! $refs.categoryMenuRoot.contains($event.target)) closeCategoryMenu()"
        class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-[var(--border-soft)] bg-white">
            <div class="relative mx-auto w-full max-w-[1280px] px-2 py-2.5 sm:px-3 lg:px-3 lg:py-3">
                <div class="flex items-center gap-3 lg:gap-5">
                    <button type="button" class="icon-button lg:hidden" @click="toggleMobileMenu()"
                        aria-label="Buka menu">
                        <x-store.icon name="menu" class="h-5 w-5" />
                    </button>

                    <a href="{{ route('home') }}" class="block shrink-0">
                        <img src="{{ asset('logo.png') }}" alt="RadeanShoes" class="h-8 w-auto sm:h-9 lg:h-10">
                    </a>

                    @if ($categoryDropdownItems->isNotEmpty())
                        <div x-ref="categoryMenuRoot" class="hidden lg:block">
                            <button type="button" class="btn-secondary h-11 gap-2 px-4 py-2.5" @click="toggleCategoryMenu()"
                                :aria-expanded="categoryMenu.toString()" aria-controls="store-category-menu"
                                x-bind:class="categoryMenu ? 'border-[var(--accent-primary)] bg-white text-[var(--accent-primary)]' : ''">
                                <span>Kategori</span>
                                <x-store.icon name="chevron-down" class="h-4 w-4 transition duration-150"
                                    x-bind:class="categoryMenu ? 'rotate-180' : ''" />
                            </button>

                            <div x-cloak x-show="categoryMenu" x-transition.opacity.duration.150ms id="store-category-menu"
                                class="absolute inset-x-0 top-[calc(100%+0.45rem)] z-50 hidden lg:block">
                                <div
                                    class="overflow-hidden rounded-[1rem] border border-[var(--border-soft)] bg-white shadow-[0_10px_24px_rgba(15,23,42,0.08)]">
                                    <div class="grid min-h-[23rem] grid-cols-[320px_minmax(0,1fr)]">
                                        <div
                                            class="border-r border-[var(--border-soft)] bg-[var(--surface-soft)] px-3 py-3">
                                            <p
                                                class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-[var(--text-muted)]">
                                                Belanja</p>
                                            <div class="max-h-[26rem] space-y-1 overflow-y-auto pr-1">
                                                <template x-for="category in categories" :key="category.id">
                                                    <a x-bind:href="category.url"
                                                        @mouseenter="activateCategory(category.id)"
                                                        @focus="activateCategory(category.id)"
                                                        class="flex items-center justify-between gap-3 rounded-[0.8rem] border border-transparent px-3 py-3 transition duration-150"
                                                        x-bind:class="activeCategory && Number(activeCategory.id) === Number(category.id) ? 'border-[var(--border-soft)] bg-white text-[var(--accent-primary)]' : 'text-[var(--text-primary)] hover:border-[var(--border-soft)] hover:bg-white'">
                                                        <div class="min-w-0">
                                                            <p class="truncate text-sm font-semibold"
                                                                x-text="category.name"></p>
                                                            <p class="mt-1 text-xs text-[var(--text-secondary)]"
                                                                x-text="category.productsLabel"></p>
                                                        </div>
                                                        <x-store.icon name="chevron-right" class="h-4 w-4 shrink-0"
                                                            x-bind:class="activeCategory && Number(activeCategory.id) === Number(category.id) ? 'text-[var(--accent-primary)]' : 'text-[var(--text-muted)]'" />
                                                    </a>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="flex min-h-full flex-col justify-between px-7 py-6">
                                            <div x-show="activeCategory" x-transition.opacity>
                                                <p
                                                    class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[var(--text-muted)]">
                                                    Kategori</p>
                                                <h2 class="mt-3 text-[1.75rem] font-bold tracking-tight text-[var(--text-primary)]"
                                                    x-text="activeCategory ? activeCategory.name : ''"></h2>
                                                <p class="mt-3 max-w-xl text-sm leading-7 text-[var(--text-secondary)]">
                                                    Temukan koleksi terbaik pada kategori ini.
                                                </p>
                                                <div class="mt-5">
                                                    <div x-show="activeCategoryPreviewImages.length > 0" class="space-y-3">
                                                        <div class="flex items-center gap-3">
                                                            <button type="button"
                                                                x-show="activeCategoryPreviewImages.length > 5"
                                                                @click.prevent.stop="prevCategoryPreview()"
                                                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[var(--border-soft)] bg-white text-[var(--text-primary)]"
                                                                aria-label="Gambar sebelumnya">
                                                                <x-store.icon name="chevron-left" class="h-4 w-4" />
                                                            </button>

                                                            <div class="grid min-w-0 flex-1 grid-cols-5 gap-3">
                                                                <template x-for="image in visibleCategoryPreviewImages"
                                                                    :key="image.id">
                                                                    <a x-bind:href="image.productUrl"
                                                                        class="group block overflow-hidden rounded-[0.75rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]">
                                                                        <img x-bind:src="image.imageUrl"
                                                                            x-bind:alt="image.productName"
                                                                            class="aspect-square h-full w-full object-cover transition duration-150 group-hover:scale-[1.02]">
                                                                    </a>
                                                                </template>
                                                            </div>

                                                            <button type="button"
                                                                x-show="activeCategoryPreviewImages.length > 5"
                                                                @click.prevent.stop="nextCategoryPreview()"
                                                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[var(--border-soft)] bg-white text-[var(--text-primary)]"
                                                                aria-label="Gambar berikutnya">
                                                                <x-store.icon name="chevron-right" class="h-4 w-4" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <p x-show="activeCategoryPreviewImages.length === 0"
                                                        class="text-sm text-[var(--text-secondary)]">
                                                        Preview produk belum tersedia untuk kategori ini.
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-4 border-t border-[var(--border-soft)] pt-5">
                                                <div class="mt-1 flex flex-wrap gap-3">
                                                    <a x-bind:href="activeCategory ? activeCategory.url : '{{ route('products.index') }}'"
                                                        class="btn-primary px-5 py-3">Lihat kategori</a>
                                                    <a href="{{ route('products.index') }}"
                                                        class="btn-secondary px-5 py-3">Lihat semua katalog</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('products.index') }}" method="GET" class="hidden min-w-0 flex-1 lg:block"
                        x-data="{ q: @js(request('q')) ?? '' }">
                        <div
                            class="flex items-center gap-3 rounded-[0.95rem] border border-[var(--border-strong)] bg-[var(--surface-soft)] px-4 py-2.5">
                            <x-store.icon name="search" class="h-5 w-5 text-[var(--text-muted)]" />
                            <input type="text" name="q" x-model="q" value="{{ request('q') }}"
                                placeholder="Cari sepatu favoritmu"
                                class="w-full border-0 bg-transparent p-0 text-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:ring-0">
                            <button type="submit" class="btn-primary px-4 py-2 disabled:cursor-not-allowed disabled:opacity-60"
                                x-bind:disabled="!q || !q.trim()">
                                Cari
                            </button>
                        </div>
                    </form>

                    <div class="ml-auto flex items-center gap-2 lg:gap-3">
                        @auth
                            <a href="{{ route('cart.index') }}" class="icon-button relative" aria-label="Keranjang">
                                <x-store.icon name="cart" class="h-5 w-5" />
                                @if ($storefrontCartCount > 0)
                                    <span
                                        class="absolute -right-1 -top-1 min-w-[1.15rem] rounded-full bg-[var(--accent-primary)] px-1 text-center text-[10px] font-semibold text-white">{{ $storefrontCartCount }}</span>
                                @endif
                            </a>
                            <div class="relative hidden md:block" @click.outside="closeProfileMenu()">
                                <button type="button" class="icon-button" @click="toggleProfileMenu()"
                                    aria-label="Menu akun">
                                    <x-store.icon name="user" class="h-5 w-5" />
                                </button>

                                <div x-cloak x-show="profileMenu" x-transition.opacity.scale.origin.top.right
                                    class="absolute right-0 top-[calc(100%+0.75rem)] z-50 w-60 rounded-[1rem] border border-[var(--border-soft)] bg-white p-2 shadow-[0_18px_40px_rgba(16,24,20,0.12)]">
                                    <div class="rounded-[0.9rem] bg-[var(--surface-soft)] px-3 py-3">
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">
                                            {{ auth()->user()->name }}
                                        </p>
                                        <p class="mt-1 text-xs text-[var(--text-secondary)]">{{ auth()->user()->email }}</p>
                                    </div>
                                    <div class="mt-2 space-y-1">
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="block rounded-[0.85rem] px-3 py-2.5 text-sm text-[var(--text-primary)] transition hover:bg-[var(--surface-soft)]">Dashboard
                                                Admin</a>
                                        @endif
                                        <a href="{{ route('account.profile.edit') }}"
                                            class="block rounded-[0.85rem] px-3 py-2.5 text-sm text-[var(--text-primary)] transition hover:bg-[var(--surface-soft)]">Edit
                                            Profil</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full rounded-[0.85rem] px-3 py-2.5 text-left text-sm text-[var(--error)] transition hover:bg-[#fff3f2]">Logout</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('cart.index') }}" class="icon-button relative" aria-label="Keranjang">
                                <x-store.icon name="cart" class="h-5 w-5" />
                            </a>
                            <a href="{{ route('login') }}" class="btn-secondary px-4 py-2.5">Login</a>
                            <a href="{{ route('register') }}" class="btn-primary px-4 py-2.5">Daftar</a>
                        @endauth
                    </div>
                </div>

                <form action="{{ route('products.index') }}" method="GET" class="mt-2.5 lg:hidden">
                    <div
                        class="flex items-center gap-3 rounded-[0.95rem] border border-[var(--border-strong)] bg-[var(--surface-soft)] px-4 py-2.5">
                        <x-store.icon name="search" class="h-5 w-5 text-[var(--text-muted)]" />
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari sepatu favoritmu"
                            class="w-full border-0 bg-transparent p-0 text-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:ring-0">
                    </div>
                </form>
            </div>

            <div x-cloak x-show="mobileMenu" x-transition
                class="border-t border-[var(--border-soft)] bg-white px-4 py-4 lg:hidden">
                <div class="page-shell-tight space-y-2 px-0">
                    <a href="{{ route('home') }}"
                        class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Beranda</a>
                    <a href="{{ route('products.index') }}"
                        class="nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}">Katalog</a>
                    <a href="{{ route('orders.index') }}"
                        class="nav-link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}">Pesanan</a>
                    <a href="{{ route('addresses.index') }}"
                        class="nav-link {{ request()->routeIs('addresses.*') ? 'is-active' : '' }}">Alamat</a>
                    @auth
                        <a href="{{ route('account.profile.edit') }}"
                            class="nav-link {{ request()->routeIs('account.profile.*') ? 'is-active' : '' }}">Akun Saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link w-full text-left">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                        <a href="{{ route('register') }}" class="nav-link">Daftar</a>
                    @endauth
                </div>
            </div>
        </header>

        @if ($storefrontBanners->isNotEmpty() && request()->routeIs('home'))
            @php
                $bannerPayload = $storefrontBanners->map(fn($banner) => [
                    'id' => $banner->id,
                    'link_url' => $banner->link_url,
                    'image_url' => $banner->image_url,
                ])->values();
            @endphp

            <section class="bg-white">
                <div class="page-shell py-4 lg:py-5">
                    <div x-data="bannerCarousel(@js($bannerPayload))" @mouseenter="stopAutoplay()"
                        @mouseleave="startAutoplay()" @touchstart.passive="onTouchStart($event)"
                        @touchend.passive="onTouchEnd($event)" class="relative overflow-hidden">
                        <div class="flex transition-transform duration-500 ease-out"
                            x-bind:style="{ transform: translateX }">
                            <template x-for="banner in items" :key="banner.id">
                                <div class="min-w-full">
                                    <template x-if="banner.link_url">
                                        <a x-bind:href="banner.link_url" class="block aspect-[8/3] overflow-hidden">
                                            <img x-bind:src="banner.image_url" alt="" class="h-full w-full object-cover">
                                        </a>
                                    </template>
                                    <template x-if="!banner.link_url">
                                        <div class="aspect-[8/3] overflow-hidden">
                                            <img x-bind:src="banner.image_url" alt="" class="h-full w-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        @if ($storefrontBanners->count() > 1)
                            <div
                                class="pointer-events-none absolute inset-x-0 top-1/2 z-10 flex -translate-y-1/2 items-center justify-between px-3">
                                <button type="button" @click.prevent="prev()"
                                    class="pointer-events-auto flex h-9 w-9 items-center justify-center rounded-full border border-white/70 bg-white/92 text-[var(--text-primary)] shadow-sm"
                                    aria-label="Banner sebelumnya">
                                    <x-store.icon name="chevron-left" class="h-4 w-4" />
                                </button>
                                <button type="button" @click.prevent="next()"
                                    class="pointer-events-auto flex h-9 w-9 items-center justify-center rounded-full border border-white/70 bg-white/92 text-[var(--text-primary)] shadow-sm"
                                    aria-label="Banner berikutnya">
                                    <x-store.icon name="chevron-right" class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="absolute inset-x-0 bottom-4 z-10 flex justify-center gap-2">
                                <template x-for="(banner, index) in items" :key="'dot-' + banner.id">
                                    <button type="button" @click.prevent="goTo(index)" class="h-2 rounded-full transition-all"
                                        x-bind:class="activeIndex === index ? 'w-6 bg-white' : 'w-2 bg-white/55'"
                                        x-bind:aria-label="'Pilih banner ' + (index + 1)"></button>
                                </template>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <main class="page-shell py-6 pb-24 lg:py-8 lg:pb-10">
            @if (session('status'))
                <div class="mb-6 rounded-[1rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-[1rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="border-t border-[var(--border-soft)] bg-white">
            <div class="page-shell grid gap-8 py-5 lg:grid-cols-[1.2fr_1fr_1fr_1fr]">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-bold text-[var(--text-primary)]">RadeanShoes</p>
                        <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">
                            Toko sepatu online dengan pengalaman belanja yang ringan, rapi, dan mudah dipakai untuk
                            kebutuhan harian, olahraga, dan gaya kasual.
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
<div class="flex items-center gap-3 text-sm text-[var(--text-secondary)]">
                            <x-store.icon name="wallet" class="h-5 w-5 text-[var(--accent-primary)]" />
                            Promo khusus untuk pengguna
                        </div>
                        <div class="flex items-center gap-3 text-sm text-[var(--text-secondary)]">
                            <x-store.icon name="truck" class="h-5 w-5 text-[var(--accent-primary)]" />
                            Pengiriman produk setiap hari
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Belanja</h2>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('products.index') }}" class="footer-link block">Katalog Produk</a>
                        <a href="{{ route('cart.index') }}" class="footer-link block">Keranjang</a>
                        <a href="{{ route('checkout.index') }}" class="footer-link block">Checkout</a>
                        <a href="{{ route('orders.index') }}" class="footer-link block">Status Pesanan</a>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Akun</h2>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('account.profile.edit') }}" class="footer-link block">Profil Saya</a>
                        <a href="{{ route('addresses.index') }}" class="footer-link block">Alamat Tersimpan</a>
                        <a href="{{ route('orders.index') }}" class="footer-link block">Riwayat Pesanan</a>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Bantuan dan Panduan</h2>
                    <div class="mt-4 space-y-3">
                        <button type="button" class="footer-link block text-left"
                            @click="$dispatch('open-modal', 'radean-care-modal')">Radean Care</button>
                        <button type="button" class="footer-link block text-left"
                            @click="$dispatch('open-modal', 'terms-modal')">Syarat dan Ketentuan</button>
                        <button type="button" class="footer-link block text-left"
                            @click="$dispatch('open-modal', 'privacy-modal')">Kebijakan Privasi</button>
                    </div>
                    <div class="mt-6">
                        <img src="{{ asset('logo.png') }}" alt="RadeanShoes" class="h-10 w-auto">
                    </div>
                </div>
            </div>
            <div class="border-t border-[var(--border-soft)]">
                <div class="page-shell py-4 text-center text-sm text-[var(--text-secondary)]">
                    &copy; 2026, PT. Rafky Dean Textile. All Rights Reserved.
                </div>
            </div>
        </footer>

        <x-modal name="radean-care-modal" maxWidth="lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Radean Care</h2>
                <p class="mt-3 text-sm leading-6 text-[var(--text-secondary)]">
                    Radean Care membantu pelanggan untuk pertanyaan seputar pesanan, ukuran, pengiriman, dan kendala
                    setelah pembelian.
                    Tim kami merespons pada jam operasional toko dan mengutamakan solusi yang cepat serta jelas.
                </p>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="btn-secondary px-4 py-2.5"
                        @click="$dispatch('close-modal', 'radean-care-modal')">Tutup</button>
                </div>
            </div>
        </x-modal>

        <x-modal name="terms-modal" maxWidth="lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Syarat dan Ketentuan</h2>
                <p class="mt-3 text-sm leading-6 text-[var(--text-secondary)]">
                    Seluruh transaksi di RadeanShoes mengikuti ketersediaan stok, verifikasi pembayaran, dan alamat
                    pengiriman yang valid.
                    Pesanan yang sudah dibayar akan diproses sesuai antrian, dan perubahan data pesanan hanya dapat
                    dilakukan sebelum status pengemasan dimulai.
                </p>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="btn-secondary px-4 py-2.5"
                        @click="$dispatch('close-modal', 'terms-modal')">Tutup</button>
                </div>
            </div>
        </x-modal>

        <x-modal name="privacy-modal" maxWidth="lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Kebijakan Privasi</h2>
                <p class="mt-3 text-sm leading-6 text-[var(--text-secondary)]">
                    Data pelanggan digunakan untuk kebutuhan akun, pemrosesan pesanan, pengiriman, dan komunikasi
                    layanan.
                    RadeanShoes tidak membagikan data pribadi pelanggan ke pihak lain di luar kebutuhan operasional
                    pembayaran dan logistik.
                </p>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="btn-secondary px-4 py-2.5"
                        @click="$dispatch('close-modal', 'privacy-modal')">Tutup</button>
                </div>
            </div>
        </x-modal>

        <nav
            class="fixed inset-x-0 bottom-0 z-40 border-t border-[var(--border-soft)] bg-white/95 px-4 py-2 shadow-[0_-4px_18px_rgba(16,24,20,0.06)] lg:hidden">
            <div class="mx-auto flex max-w-xl items-center justify-between gap-1">
                <a href="{{ route('home') }}"
                    class="mobile-nav-item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                    <x-store.icon name="home" class="h-5 w-5" />
                    <span>Beranda</span>
                </a>
                <a href="{{ route('products.index') }}"
                    class="mobile-nav-item {{ request()->routeIs('products.*') ? 'is-active' : '' }}">
                    <x-store.icon name="grid" class="h-5 w-5" />
                    <span>Katalog</span>
                </a>
                <a href="{{ route('cart.index') }}"
                    class="mobile-nav-item {{ request()->routeIs('cart.*') ? 'is-active' : '' }}">
                    <div class="relative">
                        <x-store.icon name="cart" class="h-5 w-5" />
                        @if ($storefrontCartCount > 0)
                            <span
                            class="absolute -right-2 -top-2 min-w-[1rem] rounded-full bg-[var(--accent-primary)] px-1 text-center text-[10px] font-semibold text-white">{{ $storefrontCartCount }}</span>
                        @endif
                    </div>
                    <span>Keranjang</span>
                </a>
                <a href="{{ route('account.profile.edit') }}"
                    class="mobile-nav-item {{ request()->routeIs('account.profile.*') ? 'is-active' : '' }}">
                    <x-store.icon name="user" class="h-5 w-5" />
                    <span>Akun</span>
                </a>
            </div>
        </nav>
    </div>
</body>

</html>
