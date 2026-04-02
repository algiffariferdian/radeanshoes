<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'RadeanShoes') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f5efe6] text-stone-900 antialiased">
        <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-r from-amber-200/70 via-orange-100/30 to-teal-100/70 blur-3xl"></div>

        <header class="sticky top-0 z-30 border-b border-stone-200/70 bg-[#f5efe6]/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-stone-900 text-sm font-black uppercase tracking-[0.3em] text-amber-100">RS</span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-stone-500">Radean</p>
                        <p class="text-lg font-black tracking-tight">Shoes</p>
                    </div>
                </a>

                <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-stone-950' : 'text-stone-600' }}">Beranda</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'text-stone-950' : 'text-stone-600' }}">Katalog</a>
                    @auth
                        <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'text-stone-950' : 'text-stone-600' }}">Keranjang</a>
                        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'text-stone-950' : 'text-stone-600' }}">Pesanan</a>
                    @endauth
                </nav>

                <div class="flex items-center gap-3 text-sm">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Admin</a>
                        @endif
                        <a href="{{ route('account.profile.edit') }}" class="hidden rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700 sm:inline-flex">Akun</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-stone-900 px-4 py-2 font-semibold text-stone-50">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700">Login</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-stone-900 px-4 py-2 font-semibold text-stone-50">Daftar</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="border-t border-stone-200 bg-white/50">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-8 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>RadeanShoes MVP storefront built on Laravel, Tailwind, and Midtrans sandbox.</p>
                <p>Operasional: katalog, checkout, order tracking, dan admin fulfillment.</p>
            </div>
        </footer>
    </body>
</html>
