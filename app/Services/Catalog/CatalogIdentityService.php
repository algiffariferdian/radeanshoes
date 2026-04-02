<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CatalogIdentityService
{
    public function uniqueCategorySlug(string $name, ?Category $ignoreCategory = null): string
    {
        $baseSlug = Str::slug($name) ?: 'kategori';

        return $this->uniqueValue($baseSlug, function (string $candidate) use ($ignoreCategory): bool {
            return Category::query()
                ->when($ignoreCategory, fn (Builder $query) => $query->whereKeyNot($ignoreCategory->id))
                ->where('slug', $candidate)
                ->exists();
        });
    }

    public function uniqueProductSlug(string $name, ?Product $ignoreProduct = null): string
    {
        $baseSlug = Str::slug($name) ?: 'produk';

        return $this->uniqueValue($baseSlug, function (string $candidate) use ($ignoreProduct): bool {
            return Product::query()
                ->when($ignoreProduct, fn (Builder $query) => $query->whereKeyNot($ignoreProduct->id))
                ->where('slug', $candidate)
                ->exists();
        });
    }

    public function uniqueProductSkuPrefix(string $name, ?Product $ignoreProduct = null): string
    {
        $seed = Str::upper(Str::replace('-', '', Str::slug($name)));
        $basePrefix = 'RDS-'.Str::limit($seed, 8, '');
        $basePrefix = rtrim($basePrefix, '-');
        $basePrefix = $basePrefix === 'RDS' ? 'RDS-ITEM' : $basePrefix;

        return $this->uniqueValue($basePrefix, function (string $candidate) use ($ignoreProduct): bool {
            return Product::query()
                ->when($ignoreProduct, fn (Builder $query) => $query->whereKeyNot($ignoreProduct->id))
                ->where('sku_prefix', $candidate)
                ->exists();
        });
    }

    public function uniqueVariantSku(Product $product, string|int $size, string $color, ?ProductVariant $ignoreVariant = null): string
    {
        $sizeCode = preg_replace('/\D+/', '', (string) $size) ?: Str::upper((string) $size);
        $colorCode = Str::upper(Str::limit(Str::replace('-', '', Str::slug($color)), 3, '')) ?: 'CLR';
        $baseSku = implode('-', array_filter([
            $product->sku_prefix ?: $this->uniqueProductSkuPrefix($product->name, $product),
            $colorCode,
            $sizeCode,
        ]));

        return $this->uniqueValue($baseSku, function (string $candidate) use ($ignoreVariant): bool {
            return ProductVariant::query()
                ->when($ignoreVariant, fn (Builder $query) => $query->whereKeyNot($ignoreVariant->id))
                ->where('sku', $candidate)
                ->exists();
        });
    }

    protected function uniqueValue(string $baseValue, callable $exists): string
    {
        $candidate = $baseValue;
        $counter = 2;

        while ($exists($candidate)) {
            $candidate = $baseValue.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }
}
