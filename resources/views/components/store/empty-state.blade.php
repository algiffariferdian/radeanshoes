@props([
    'icon' => 'package',
    'title' => 'Belum ada data',
    'body' => 'Konten akan muncul di sini setelah data tersedia.',
])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-stone-100 text-stone-500">
        <x-store.icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="mt-4 text-base font-semibold text-stone-900">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">{{ $body }}</p>
    {{ $slot }}
</div>
