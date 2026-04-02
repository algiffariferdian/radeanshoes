@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-stone-500">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <x-store.icon name="chevron-right" class="h-4 w-4 text-stone-300" />
        @endif

        @if (! empty($item['url']) && $index !== count($items) - 1)
            <a href="{{ $item['url'] }}" class="transition hover:text-[var(--accent-primary)]">{{ $item['label'] }}</a>
        @else
            <span class="font-medium text-stone-700">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
