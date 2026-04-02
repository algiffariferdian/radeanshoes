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
        <div x-data="{ mobileMenu: false }" class="min-h-screen">
            <header class="sticky top-0 z-40 border-b border-[var(--border-soft)] bg-white/95 backdrop-blur-sm">
                <div class="border-b border-[var(--border-soft)] bg-[var(--surface-soft)]">
                    <div class="page-shell flex items-center justify-between gap-4 py-1.5 text-xs text-[var(--text-secondary)]">
                        <div class="flex items-center gap-4">
                            <span class="hidden sm:inline-flex items-center gap-2">
                                <x-store.icon name="shield" class="h-4 w-4 text-[var(--accent-primary)]" />
                                Belanja aman dan stok per varian lebih jelas
                            </span>
                            <span class="sm:hidden">Belanja sepatu jadi lebih ringkas</span>
                        </div>
                        <div class="hidden items-center gap-4 sm:flex">
                            <a href="{{ route('orders.index') }}" class="hover:text-[var(--accent-primary)]">Lacak pesanan</a>
                            <a href="{{ route('addresses.index') }}" class="hover:text-[var(--accent-primary)]">Alamat saya</a>
                        </div>
                    </div>
                </div>

                <div class="page-shell py-2.5 lg:py-3">
                    <div class="flex items-center gap-3 lg:gap-5">
                        <button type="button" class="icon-button lg:hidden" @click="mobileMenu = !mobileMenu" aria-label="Buka menu">
                            <x-store.icon name="menu" class="h-5 w-5" />
                        </button>

                        <a href="{{ route('home') }}" class="block shrink-0">
                            <img src="{{ asset('logo.png') }}" alt="RadeanShoes" class="h-8 w-auto sm:h-9 lg:h-10">
                        </a>

                        <form action="{{ route('products.index') }}" method="GET" class="hidden flex-1 lg:block">
                            <div class="flex items-center gap-3 rounded-[0.95rem] border border-[var(--border-strong)] bg-[var(--surface-soft)] px-4 py-2.5">
                                <x-store.icon name="search" class="h-5 w-5 text-[var(--text-muted)]" />
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Cari sepatu favoritmu"
                                    class="w-full border-0 bg-transparent p-0 text-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:ring-0"
                                >
                                <button type="submit" class="btn-primary px-4 py-2">Cari</button>
                            </div>
                        </form>

                        <div class="ml-auto flex items-center gap-2 lg:gap-3">
                            @auth
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary hidden px-4 py-2.5 lg:inline-flex">Admin</a>
                                @endif

                                <a href="{{ route('account.profile.edit') }}#wishlist" class="icon-button relative hidden sm:inline-flex" aria-label="Wishlist">
                                    <x-store.icon name="heart" class="h-5 w-5" />
                                    <span
                                        x-cloak
                                        class="absolute -right-1 -top-1 hidden min-w-[1.15rem] rounded-full bg-[var(--accent-primary)] px-1 text-center text-[10px] font-semibold text-white"
                                        x-data
                                        x-show="$store.wishlist.count() > 0"
                                        x-text="$store.wishlist.count()"
                                    ></span>
                                </a>
                                <a href="{{ route('cart.index') }}" class="icon-button relative" aria-label="Keranjang">
                                    <x-store.icon name="cart" class="h-5 w-5" />
                                    @if ($storefrontCartCount > 0)
                                        <span class="absolute -right-1 -top-1 min-w-[1.15rem] rounded-full bg-[var(--accent-primary)] px-1 text-center text-[10px] font-semibold text-white">{{ $storefrontCartCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('account.profile.edit') }}" class="hidden items-center gap-2 rounded-[0.9rem] border border-[var(--border-soft)] px-4 py-2.5 text-sm font-semibold text-[var(--text-primary)] md:inline-flex">
                                    <x-store.icon name="user" class="h-4 w-4 text-[var(--text-secondary)]" />
                                    Akun
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                                    @csrf
                                    <button type="submit" class="btn-secondary px-4 py-2.5">Keluar</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn-secondary px-4 py-2.5">Login</a>
                                <a href="{{ route('register') }}" class="btn-primary px-4 py-2.5">Daftar</a>
                            @endauth
                        </div>
                    </div>

                    <form action="{{ route('products.index') }}" method="GET" class="mt-2.5 lg:hidden">
                        <div class="flex items-center gap-3 rounded-[0.95rem] border border-[var(--border-strong)] bg-[var(--surface-soft)] px-4 py-2.5">
                            <x-store.icon name="search" class="h-5 w-5 text-[var(--text-muted)]" />
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Cari sepatu favoritmu"
                                class="w-full border-0 bg-transparent p-0 text-sm text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:ring-0"
                            >
                        </div>
                    </form>
                </div>

                <div
                    x-cloak
                    x-show="mobileMenu"
                    x-transition
                    class="border-t border-[var(--border-soft)] bg-white px-4 py-4 lg:hidden"
                >
                    <div class="page-shell space-y-2 px-0">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Beranda</a>
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}">Katalog</a>
                        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}">Pesanan</a>
                        <a href="{{ route('addresses.index') }}" class="nav-link {{ request()->routeIs('addresses.*') ? 'is-active' : '' }}">Alamat</a>
                        @auth
                            <a href="{{ route('account.profile.edit') }}" class="nav-link {{ request()->routeIs('account.profile.*') ? 'is-active' : '' }}">Akun Saya</a>
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

            @if ($storefrontBanners->isNotEmpty() && request()->routeIs('home', 'products.index', 'products.show'))
                @php
                    $bannerPayload = $storefrontBanners->map(fn ($banner) => [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'subtitle' => $banner->subtitle,
                        'button_label' => $banner->button_label,
                        'link_url' => $banner->link_url,
                        'image_url' => $banner->image_url,
                    ])->values();
                @endphp

                <section class="border-b border-[var(--border-soft)] bg-white">
                    <div class="page-shell py-4 lg:py-5">
                        <div
                            x-data="bannerCarousel(@js($bannerPayload))"
                            @mouseenter="stopAutoplay()"
                            @mouseleave="startAutoplay()"
                            @touchstart.passive="onTouchStart($event)"
                            @touchend.passive="onTouchEnd($event)"
                            class="relative overflow-hidden rounded-[1.35rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]"
                        >
                            <template x-for="(banner, index) in items" :key="banner.id">
                                <div x-show="activeIndex === index" x-transition.opacity.duration.300ms class="relative min-h-[180px] sm:min-h-[220px]">
                                    <template x-if="banner.image_url">
                                        <img x-bind:src="banner.image_url" x-bind:alt="banner.title" class="absolute inset-0 h-full w-full object-cover">
                                    </template>
                                    <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(15,23,42,0.82)_0%,rgba(15,23,42,0.55)_42%,rgba(15,23,42,0.15)_100%)]"></div>
                                    <div class="relative flex min-h-[180px] items-end sm:min-h-[220px]">
                                        <div class="max-w-xl p-5 sm:p-8">
                                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/70">Promo pilihan</p>
                                            <h2 class="mt-3 text-2xl font-bold tracking-tight text-white sm:text-3xl" x-text="banner.title"></h2>
                                            <p class="mt-3 max-w-lg text-sm leading-6 text-white/82" x-text="banner.subtitle"></p>
                                            <a
                                                x-show="banner.link_url && banner.button_label"
                                                x-bind:href="banner.link_url"
                                                class="mt-5 inline-flex rounded-[0.9rem] bg-white px-4 py-2.5 text-sm font-semibold text-[var(--text-primary)]"
                                                x-text="banner.button_label"
                                            ></a>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            @if ($storefrontBanners->count() > 1)
                                <div class="pointer-events-none absolute inset-x-0 top-1/2 z-10 flex -translate-y-1/2 items-center justify-between px-3">
                                    <button type="button" @click="prev()" class="pointer-events-auto flex h-10 w-10 items-center justify-center rounded-full border border-white/30 bg-white/85 text-[var(--text-primary)] shadow-sm" aria-label="Banner sebelumnya">
                                        <x-store.icon name="chevron-left" class="h-4 w-4" />
                                    </button>
                                    <button type="button" @click="next()" class="pointer-events-auto flex h-10 w-10 items-center justify-center rounded-full border border-white/30 bg-white/85 text-[var(--text-primary)] shadow-sm" aria-label="Banner berikutnya">
                                        <x-store.icon name="chevron-right" class="h-4 w-4" />
                                    </button>
                                </div>

                                <div class="absolute bottom-4 left-5 z-10 flex gap-2">
                                    <template x-for="(banner, index) in items" :key="'dot-' + banner.id">
                                        <button type="button" @click="goTo(index)" class="h-2.5 rounded-full transition-all" x-bind:class="activeIndex === index ? 'w-8 bg-white' : 'w-2.5 bg-white/50'" x-bind:aria-label="'Pilih banner ' + (index + 1)"></button>
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
                <div class="page-shell grid gap-8 py-10 lg:grid-cols-[1.2fr_1fr_1fr_1fr]">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-bold text-[var(--text-primary)]">RadeanShoes</p>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">
                                Toko sepatu online dengan pengalaman belanja yang ringan, rapi, dan mudah dipakai untuk kebutuhan harian, olahraga, dan gaya kasual.
                            </p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                            <div class="flex items-center gap-3 text-sm text-[var(--text-secondary)]">
                                <x-store.icon name="shield" class="h-5 w-5 text-[var(--accent-primary)]" />
                                Pembayaran aman
                            </div>
                            <div class="flex items-center gap-3 text-sm text-[var(--text-secondary)]">
                                <x-store.icon name="truck" class="h-5 w-5 text-[var(--accent-primary)]" />
                                Pengiriman terjadwal
                            </div>
                            <div class="flex items-center gap-3 text-sm text-[var(--text-secondary)]">
                                <x-store.icon name="package" class="h-5 w-5 text-[var(--accent-primary)]" />
                                Stok jelas per varian
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
                            <a href="{{ route('account.profile.edit') }}#wishlist" class="footer-link block">Wishlist</a>
                            <a href="{{ route('orders.index') }}" class="footer-link block">Riwayat Pesanan</a>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-[var(--text-primary)]">Informasi</h2>
                        <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                            <p>Operasional Senin - Sabtu, 09.00 - 20.00 WIB</p>
                            <p>Email: support@radeanshoes.test</p>
                            <p>Gudang: Jakarta Selatan</p>
                            <p>Pembayaran menggunakan Midtrans Sandbox untuk tahap MVP.</p>
                        </div>
                    </div>
                </div>
            </footer>

            <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-[var(--border-soft)] bg-white/95 px-4 py-2 shadow-[0_-4px_18px_rgba(16,24,20,0.06)] lg:hidden">
                <div class="mx-auto flex max-w-xl items-center justify-between gap-1">
                    <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                        <x-store.icon name="home" class="h-5 w-5" />
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="mobile-nav-item {{ request()->routeIs('products.*') ? 'is-active' : '' }}">
                        <x-store.icon name="grid" class="h-5 w-5" />
                        <span>Katalog</span>
                    </a>
                    <a href="{{ route('account.profile.edit') }}#wishlist" class="mobile-nav-item">
                        <div class="relative">
                            <x-store.icon name="heart" class="h-5 w-5" />
                            <span
                                x-cloak
                                class="absolute -right-2 -top-2 hidden min-w-[1rem] rounded-full bg-[var(--accent-primary)] px-1 text-center text-[10px] font-semibold text-white"
                                x-data
                                x-show="$store.wishlist.count() > 0"
                                x-text="$store.wishlist.count()"
                            ></span>
                        </div>
                        <span>Wishlist</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="mobile-nav-item {{ request()->routeIs('cart.*') ? 'is-active' : '' }}">
                        <div class="relative">
                            <x-store.icon name="cart" class="h-5 w-5" />
                            @if ($storefrontCartCount > 0)
                                <span class="absolute -right-2 -top-2 min-w-[1rem] rounded-full bg-[var(--accent-primary)] px-1 text-center text-[10px] font-semibold text-white">{{ $storefrontCartCount }}</span>
                            @endif
                        </div>
                        <span>Keranjang</span>
                    </a>
                    <a href="{{ route('account.profile.edit') }}" class="mobile-nav-item {{ request()->routeIs('account.profile.*') ? 'is-active' : '' }}">
                        <x-store.icon name="user" class="h-5 w-5" />
                        <span>Akun</span>
                    </a>
                </div>
            </nav>
        </div>
    </body>
</html>
