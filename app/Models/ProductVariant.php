<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'sku',
        'price_override',
        'discount_percentage',
        'stock_qty',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_override' => 'decimal:2',
            'discount_percentage' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function effectivePrice(): string
    {
        $basePrice = (float) $this->originalPrice();
        $discount = max(0, min(100, (int) $this->discount_percentage));
        $effectivePrice = $basePrice - ($basePrice * $discount / 100);

        return number_format($effectivePrice, 2, '.', '');
    }

    public function originalPrice(): string
    {
        return number_format((float) ($this->price_override ?? $this->product->base_price), 2, '.', '');
    }

    public function hasDiscount(): bool
    {
        return (int) $this->discount_percentage > 0;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->images->first()?->image_url;
    }

    public function getImageUrlsAttribute(): array
    {
        /** @var Collection<int, ProductImage> $images */
        $images = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->get();

        return $images
            ->map(fn (ProductImage $image) => $image->image_url)
            ->filter()
            ->values()
            ->all();
    }
}
