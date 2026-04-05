<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RadeanShoes') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[var(--bg-main)] text-[var(--text-primary)] antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-[var(--border-soft)] bg-white">
            <div class="page-shell flex items-center justify-between py-4">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="RadeanShoes" class="h-9 w-auto">
                </a>
                <span class="text-xs text-[var(--text-secondary)]">Akses akun pelanggan</span>
            </div>
        </header>

        <main class="flex-1">
            <div class="page-shell py-[var(--space-lg)]">
                <div class="mx-auto w-full max-w-[560px]">
                    <div
                        class="rounded-[0.95rem] border border-[var(--border-soft)] bg-white px-[var(--space-md)] py-[var(--space-md)]">
                        {{ $slot }}
                    </div>
                    <p class="mt-6 text-center text-xs text-[var(--text-muted)]">© 2026, PT. Rafky Dean Textile. All Rights Reserved.</p>
                </div>
            </div>
        </main>
    </div>

</body>

</html>
