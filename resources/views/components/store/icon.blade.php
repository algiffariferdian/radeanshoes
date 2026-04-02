@props([
    'name',
    'class' => 'h-5 w-5',
])

@php
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.75 12 3l9 7.75"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 9.75V20h13.5V9.75"/>',
        'grid' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z"/>',
        'heart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25s-7.5-4.68-7.5-10.2A4.55 4.55 0 0 1 9 5.55c1.18 0 2.3.46 3 1.29.7-.83 1.82-1.29 3-1.29a4.55 4.55 0 0 1 4.5 4.5c0 5.52-7.5 10.2-7.5 10.2Z"/>',
        'cart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h1.5l1.8 8.1a1.5 1.5 0 0 0 1.47 1.17h7.98a1.5 1.5 0 0 0 1.47-1.17l1.05-4.95H7.2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 19.5a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM17.25 19.5a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/>',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0 1 15 0"/>',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/><circle cx="10.5" cy="10.5" r="6.75"/>',
        'chevron-left' => '<path stroke-linecap="round" stroke-linejoin="round" d="m15 6-6 6 6 6"/>',
        'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/>',
        'chevron-down' => '<path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>',
        'star' => '<path stroke-linecap="round" stroke-linejoin="round" d="m12 3.75 2.4 4.87 5.38.78-3.89 3.79.92 5.36L12 16.07l-4.81 2.53.92-5.36-3.89-3.79 5.38-.78L12 3.75Z"/>',
        'truck' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h10.5v8.25H3.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9h3.12l2.88 2.88v3.12h-6"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM17.25 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>',
        'shield' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6.75v5.7c0 4.18 2.86 8.09 6.75 9.8 3.89-1.71 6.75-5.62 6.75-9.8v-5.7L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 12 1.5 1.5 3-3"/>',
        'tag' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 3.75h6l2.25 2.25v6L12 20.25 3.75 12Z"/><circle cx="16.5" cy="7.5" r="1.125"/>',
        'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6-5.3 6-10.13a6 6 0 1 0-12 0C6 15.7 12 21 12 21Z"/><circle cx="12" cy="10.5" r="2.25"/>',
        'credit-card' => '<rect x="3.75" y="5.25" width="16.5" height="13.5" rx="1.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 15h3"/>',
        'package' => '<path stroke-linecap="round" stroke-linejoin="round" d="m12 3.75 7.5 3.75v9L12 20.25 4.5 16.5v-9L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7.5 7.5 3.75 7.5-3.75"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 11.25v9"/>',
        'wallet' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15v10.5h-15z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 9V6.75A2.25 2.25 0 0 1 6.75 4.5h9"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 13.5h3"/>',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 3.87a1.5 1.5 0 0 1 3 0l.22 1.4a1.5 1.5 0 0 0 1.03 1.2l1.34.43a1.5 1.5 0 0 1 .8 2.3l-.82 1.15a1.5 1.5 0 0 0 0 1.74l.82 1.15a1.5 1.5 0 0 1-.8 2.3l-1.34.43a1.5 1.5 0 0 0-1.03 1.2l-.22 1.4a1.5 1.5 0 0 1-3 0l-.22-1.4a1.5 1.5 0 0 0-1.03-1.2l-1.34-.43a1.5 1.5 0 0 1-.8-2.3l.82-1.15a1.5 1.5 0 0 0 0-1.74l-.82-1.15a1.5 1.5 0 0 1 .8-2.3l1.34-.43a1.5 1.5 0 0 0 1.03-1.2l.22-1.4Z"/><circle cx="12" cy="12" r="3"/>',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M4.5 12h15M4.5 17.25h15"/>',
        'filter' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12h9"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 17.25h3"/>',
        'sort' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 5.25v13.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 8.25 3-3 3 3"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75V5.25"/><path stroke-linecap="round" stroke-linejoin="round" d="m13.5 15.75 3 3 3-3"/>',
        'palette' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75a8.25 8.25 0 0 0 0 16.5h1.05a1.95 1.95 0 0 0 0-3.9h-.3a1.2 1.2 0 0 1 0-2.4H15A5.25 5.25 0 0 0 15 3.75h-3Z"/><circle cx="7.5" cy="11.25" r=".75"/><circle cx="9.75" cy="7.5" r=".75"/><circle cx="14.25" cy="7.5" r=".75"/><circle cx="16.5" cy="11.25" r=".75"/>',
        'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" d="m12 3 1.35 4.65L18 9l-4.65 1.35L12 15l-1.35-4.65L6 9l4.65-1.35L12 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 16.5.75 2.25L7.5 19.5l-2.25.75L4.5 22.5l-.75-2.25L1.5 19.5l2.25-.75.75-2.25Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m18 15 .9 2.1 2.1.9-2.1.9-.9 2.1-.9-2.1-2.1-.9 2.1-.9.9-2.1Z"/>',
    ];

    $svg = $icons[$name] ?? $icons['grid'];
@endphp

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.7']) }}>
    {!! $svg !!}
</svg>
