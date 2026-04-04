<?php

namespace App\Models;

use App\Support\Enums\OrderStatus;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku_prefix',
        'description',
        'base_price',
        'weight_gram',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->whereNull('product_variant_id')
            ->orderBy('sort_order');
    }

    public function allImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->whereNull('product_variant_id')
            ->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->latest();
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $productImage = $this->preferredProductImage();

        if ($productImage?->image_url) {
            return $productImage->image_url;
        }

        $variantImageUrl = $this->preferredVariantImageUrl();

        if (filled($variantImageUrl)) {
            return $variantImageUrl;
        }

        return $this->placeholderProductImage()?->image_url;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->preferredProductImage()?->image_url;
    }

    public function getLowestDisplayPriceAttribute(): float
    {
        $prices = $this->activeVariantCollection()
            ->map(fn (ProductVariant $variant) => (float) $variant->effectivePrice())
            ->filter(fn (float $price) => $price > 0);

        if ($prices->isEmpty()) {
            return (float) $this->base_price;
        }

        return (float) $prices->min();
    }

    public function getCompareAtPriceAttribute(): ?float
    {
        $variant = $this->featuredVariant();

        if (! $variant || ! $variant->hasDiscount()) {
            return null;
        }

        return (float) $variant->originalPrice();
    }

    public function getDiscountPercentageAttribute(): int
    {
        return (int) ($this->featuredVariant()?->discount_percentage ?? 0);
    }

    public function getSoldCountAttribute(): int
    {
        if (array_key_exists('sold_qty', $this->attributes)) {
            return (int) ($this->attributes['sold_qty'] ?? 0);
        }

        return (int) $this->orderItems()
            ->whereHas('order', fn ($query) => $query->whereIn('order_status', [
                OrderStatus::Paid->value,
                OrderStatus::Processing->value,
                OrderStatus::Shipped->value,
                OrderStatus::Completed->value,
            ]))
            ->sum('qty');
    }

    public function getRatingValueAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return round((float) ($this->attributes['reviews_avg_rating'] ?? 0), 1);
        }

        return round((float) ($this->reviews()->avg('rating') ?? 0), 1);
    }

    public function getReviewCountAttribute(): int
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) ($this->attributes['reviews_count'] ?? 0);
        }

        return (int) $this->reviews()->count();
    }

    public function getBrandLabelAttribute(): string
    {
        if (filled($this->sku_prefix)) {
            $prefix = Str::upper((string) Str::of($this->sku_prefix)->before('-'));

            if (in_array($prefix, ['RDS', 'RS'], true)) {
                return 'Radean';
            }

            return $prefix !== '' ? Str::headline(Str::lower($prefix)) : 'Radean';
        }

        return 'Radean';
    }

    public function getAvailableSizesAttribute(): array
    {
        return $this->activeVariantCollection()
            ->pluck('size')
            ->filter()
            ->unique()
            ->sortBy(fn ($size) => (int) $size)
            ->values()
            ->all();
    }

    public function getAvailableColorsAttribute(): array
    {
        return $this->activeVariantCollection()
            ->pluck('color')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->activeVariantCollection()->sum('stock_qty');
    }

    protected function activeVariantCollection(): Collection
    {
        if ($this->relationLoaded('variants')) {
            $this->variants->loadMissing('images');

            return $this->variants->where('is_active', true)->values();
        }

        return $this->variants()->where('is_active', true)->get();
    }

    protected function featuredVariant(): ?ProductVariant
    {
        return $this->activeVariantCollection()
            ->sortBy(fn (ProductVariant $variant) => (float) $variant->effectivePrice())
            ->first();
    }

    protected function preferredProductImage(): ?ProductImage
    {
        return $this->productImageCollection()
            ->first(fn (ProductImage $image) => filled($image->image_url) && ! $this->isPlaceholderImage($image));
    }

    protected function placeholderProductImage(): ?ProductImage
    {
        return $this->productImageCollection()
            ->first(fn (ProductImage $image) => filled($image->image_url));
    }

    protected function preferredVariantImageUrl(): ?string
    {
        return $this->activeVariantCollection()
            ->map(fn (ProductVariant $variant) => $variant->primary_image_url)
            ->first(fn (?string $imageUrl) => filled($imageUrl));
    }

    protected function productImageCollection(): Collection
    {
        if ($this->relationLoaded('images')) {
            return $this->images;
        }

        return $this->images()->get();
    }

    protected function isPlaceholderImage(ProductImage $image): bool
    {
        $imagePath = Str::lower((string) $image->image_path);

        if ($imagePath === '') {
            return false;
        }

        return str_contains($imagePath, 'default-logo')
            || str_contains($imagePath, 'logo-preview')
            || str_ends_with($imagePath, '/logo.png')
            || str_ends_with($imagePath, '\\logo.png');
    }
}
