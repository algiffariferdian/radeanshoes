@props([
    'product',
    'compact' => false,
])

@php
    $productUrl = route('products.show', $product);
@endphp

<article class="product-card flex h-full flex-col">
    <div class="product-media">
        <a href="{{ $productUrl }}" class="block h-full w-full">
            @if ($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-200 hover:scale-[1.02]">
            @else
                <div class="flex h-full items-center justify-center text-5xl font-semibold text-stone-400">!</div>
            @endif
        </a>

        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            @if ($product->discount_percentage > 0)
                <span class="badge-discount">-{{ $product->discount_percentage }}%</span>
            @endif
            <span class="badge-neutral">{{ number_format($product->sold_count, 0, ',', '.') }} terjual</span>
        </div>
    </div>

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="space-y-2">
            <span class="meta-copy">{{ $product->category->name }}</span>
            <a href="{{ $productUrl }}" class="heading-card line-clamp-2 hover:text-[var(--accent-primary)]">
                {{ $product->name }}
            </a>
            @if ($product->review_count > 0)
                <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count" />
            @else
                <p class="text-sm text-[var(--text-muted)]">Belum ada rating</p>
            @endif
        </div>

        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="price-current">Rp{{ number_format((float) $product->lowest_display_price, 0, ',', '.') }}</span>
                @if ($product->compare_at_price)
                    <span class="price-strike">Rp{{ number_format((float) $product->compare_at_price, 0, ',', '.') }}</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach (collect($product->available_sizes)->take(3) as $size)
                    <span class="badge-neutral">EU {{ $size }}</span>
                @endforeach
                @if (count($product->available_sizes) > 3)
                    <span class="badge-neutral">+{{ count($product->available_sizes) - 3 }} ukuran</span>
                @endif
            </div>
        </div>

        <div class="mt-auto flex items-center gap-2 pt-1">
            <a href="{{ $productUrl }}" class="btn-secondary flex-1 px-3 py-2.5 text-center text-sm">Lihat Detail</a>
            <a href="{{ $productUrl }}#purchase" class="btn-primary flex-1 px-3 py-2.5 text-center text-sm">Pilih Produk</a>
        </div>
    </div>
</article>
