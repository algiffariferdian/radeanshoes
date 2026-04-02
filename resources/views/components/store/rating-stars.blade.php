@props([
    'rating' => 0,
    'reviews' => null,
    'size' => 'h-4 w-4',
    'textClass' => 'text-sm text-stone-600',
])

@php
    $ratingValue = (float) $rating;
    $fullStars = (int) floor($ratingValue);
    $hasHalf = ($ratingValue - $fullStars) >= 0.5;
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="flex items-center gap-0.5 text-amber-400">
        @for ($index = 0; $index < 5; $index++)
            @php($isFilled = $index < $fullStars || ($hasHalf && $index === $fullStars))
            <x-store.icon
                name="star"
                :class="$size.' '.($isFilled ? 'fill-current text-amber-400' : 'text-stone-300')"
            />
        @endfor
    </div>
    <span class="{{ $textClass }}">
        {{ number_format($ratingValue, 1, ',', '.') }}
        @if (! is_null($reviews))
            <span class="text-stone-400">({{ number_format((int) $reviews, 0, ',', '.') }})</span>
        @endif
    </span>
</div>
