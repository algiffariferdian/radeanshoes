@props([
    'product',
    'compact' => false,
    'fullLink' => false,
])

@php
    $productUrl = route('products.show', $product);
    $soldLabel = number_format($product->sold_count, 0, ',', '.').' terjual';
    $sizeLimit = $compact ? 2 : 3;
@endphp

<article class="product-card {{ $fullLink ? 'product-card-clickable' : '' }} flex h-full flex-col">
    @if ($fullLink)
        <a href="{{ $productUrl }}" class="absolute inset-0 z-10" aria-label="Lihat {{ $product->name }}"></a>
    @endif

    <div class="product-media">
        @if ($fullLink)
            @if ($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-200">
            @else
                <div class="flex h-full items-center justify-center text-5xl font-semibold text-stone-400">!</div>
            @endif
        @else
            <a href="{{ $productUrl }}" class="block h-full w-full">
                @if ($product->primary_image_url)
                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-200 hover:scale-[1.02]">
                @else
                    <div class="flex h-full items-center justify-center text-5xl font-semibold text-stone-400">!</div>
                @endif
            </a>
        @endif

        @if ($compact)
            @if ($product->discount_percentage > 0)
                <div class="absolute left-0 top-0 z-[1]">
                    <span class="relative inline-flex bg-[var(--accent-primary)] px-2 py-1 text-[10px] font-bold leading-none text-white">
                        -{{ $product->discount_percentage }}%
                        <span class="absolute -bottom-[6px] left-0 h-0 w-0 border-r-[6px] border-t-[6px] border-r-transparent border-t-[#226d43]"></span>
                    </span>
                </div>
            @endif
        @else
            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                @if ($product->discount_percentage > 0)
                    <span class="badge-discount">-{{ $product->discount_percentage }}%</span>
                @endif
                <span class="badge-neutral">{{ $soldLabel }}</span>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col {{ $compact ? 'gap-3 p-3' : 'gap-4 p-4' }}">
        <div class="{{ $compact ? 'space-y-1.5' : 'space-y-2' }}">
            <div class="flex items-center justify-between gap-2">
                <span class="meta-copy truncate">{{ $product->category->name }}</span>
                @if ($compact)
                    <span class="shrink-0 text-[11px] font-medium text-[var(--text-muted)]">{{ $soldLabel }}</span>
                @endif
            </div>
            @if ($fullLink)
                <p class="{{ $compact ? 'text-sm font-semibold leading-5 text-[var(--text-primary)] line-clamp-2' : 'heading-card line-clamp-2' }}">{{ $product->name }}</p>
            @else
                <a href="{{ $productUrl }}" class="{{ $compact ? 'line-clamp-2 text-sm font-semibold leading-5 text-[var(--text-primary)] hover:text-[var(--accent-primary)]' : 'heading-card line-clamp-2 hover:text-[var(--accent-primary)]' }}">
                    {{ $product->name }}
                </a>
            @endif
            @if ($product->review_count > 0)
                <x-store.rating-stars :rating="$product->rating_value" :reviews="$product->review_count" :size="$compact ? 'h-3.5 w-3.5' : 'h-4 w-4'" :text-class="$compact ? 'text-xs text-stone-600' : 'text-sm text-stone-600'" />
            @else
                <p class="{{ $compact ? 'text-xs text-[var(--text-muted)]' : 'text-sm text-[var(--text-muted)]' }}">Belum ada rating</p>
            @endif
        </div>

        <div class="{{ $compact ? 'space-y-1.5' : 'space-y-2' }}">
            <div class="flex flex-wrap items-center gap-2">
                <span class="{{ $compact ? 'text-base font-bold leading-none text-[var(--text-primary)]' : 'price-current' }}">Rp{{ number_format((float) $product->lowest_display_price, 0, ',', '.') }}</span>
                @if ($product->compare_at_price)
                    <span class="{{ $compact ? 'text-xs text-[var(--text-muted)] line-through' : 'price-strike' }}">Rp{{ number_format((float) $product->compare_at_price, 0, ',', '.') }}</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach (collect($product->available_sizes)->take($sizeLimit) as $size)
                    <span class="{{ $compact ? 'inline-flex rounded-full bg-[#f2f4f3] px-2 py-0.5 text-[10px] font-semibold text-[var(--text-secondary)]' : 'badge-neutral' }}">EU {{ $size }}</span>
                @endforeach
                @if (count($product->available_sizes) > $sizeLimit)
                    <span class="{{ $compact ? 'inline-flex rounded-full bg-[#f2f4f3] px-2 py-0.5 text-[10px] font-semibold text-[var(--text-secondary)]' : 'badge-neutral' }}">+{{ count($product->available_sizes) - $sizeLimit }} ukuran</span>
                @endif
            </div>
        </div>

        @unless ($fullLink)
            <div class="mt-auto flex items-center gap-2 pt-1">
                <a href="{{ $productUrl }}" class="btn-secondary flex-1 px-3 py-2.5 text-center text-sm">Lihat Detail</a>
                <a href="{{ $productUrl }}#purchase" class="btn-primary flex-1 px-3 py-2.5 text-center text-sm">Pilih Produk</a>
            </div>
        @endunless
    </div>
</article>
