<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Admin · '.config('app.name', 'RadeanShoes') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-100 text-stone-900 antialiased">
        <div class="grid min-h-screen lg:grid-cols-[280px_1fr]">
            <aside class="border-r border-stone-800 bg-stone-950 px-6 py-8 text-stone-100">
                <a href="{{ route('admin.dashboard') }}" class="mb-10 block">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-stone-400">Radean</p>
                    <p class="text-3xl font-black tracking-tight text-amber-100">Admin Panel</p>
                </a>

                <nav class="space-y-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-stone-800 text-white' : 'text-stone-300 hover:bg-stone-900' }}">Dashboard</a>
                    <a href="{{ route('admin.categories.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-stone-800 text-white' : 'text-stone-300 hover:bg-stone-900' }}">Kategori</a>
                    <a href="{{ route('admin.products.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-stone-800 text-white' : 'text-stone-300 hover:bg-stone-900' }}">Produk</a>
                    <a href="{{ route('admin.shipping-options.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.shipping-options.*') ? 'bg-stone-800 text-white' : 'text-stone-300 hover:bg-stone-900' }}">Shipping</a>
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-stone-800 text-white' : 'text-stone-300 hover:bg-stone-900' }}">Orders</a>
                    <a href="{{ route('admin.customers.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.customers.*') ? 'bg-stone-800 text-white' : 'text-stone-300 hover:bg-stone-900' }}">Customers</a>
                </nav>

                <div class="mt-10 rounded-3xl border border-stone-800 bg-stone-900 p-4 text-sm text-stone-300">
                    <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p>{{ auth()->user()->email }}</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('home') }}" class="rounded-full border border-stone-700 px-4 py-2 text-xs font-semibold">Storefront</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-amber-200 px-4 py-2 text-xs font-semibold text-stone-900">Logout</button>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
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
        </div>
    </body>
</html>
