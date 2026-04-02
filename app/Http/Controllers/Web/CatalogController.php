<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $categoryId = $request->integer('category');
        $color = trim($request->string('color')->toString());
        $size = trim($request->string('size')->toString());
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $sort = trim($request->string('sort')->toString()) ?: 'terlaris';
        $lowestVariantPriceSubquery = ProductVariant::query()
            ->selectRaw('MIN(price_override - (price_override * discount_percentage / 100))')
            ->whereColumn('product_id', 'products.id')
            ->where('is_active', true);

        $products = Product::query()
            ->select('products.*')
            ->selectSub($lowestVariantPriceSubquery, 'lowest_variant_price')
            ->with([
                'category',
                'primaryImage',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true),
            ])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->withCount('reviews')
            ->withSum([
                'orderItems as sold_qty' => fn ($query) => $query->whereHas('order', fn ($orderQuery) => $orderQuery->whereIn('order_status', [
                    OrderStatus::Paid->value,
                    OrderStatus::Processing->value,
                    OrderStatus::Shipped->value,
                    OrderStatus::Completed->value,
                ])),
            ], 'qty')
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true))
            ->whereHas('variants', fn (Builder $query) => $query->where('is_active', true))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('sku_prefix', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryId, fn (Builder $query) => $query->where('category_id', $categoryId))
            ->when($color !== '', function (Builder $query) use ($color): void {
                $query->whereHas('variants', fn (Builder $variantQuery) => $variantQuery->where('is_active', true)->where('color', $color));
            })
            ->when($size !== '', function (Builder $query) use ($size): void {
                $query->whereHas('variants', fn (Builder $variantQuery) => $variantQuery->where('is_active', true)->where('size', $size));
            })
            ->when(! is_null($minPrice), function (Builder $query) use ($minPrice): void {
                $query->whereHas('variants', function (Builder $variantQuery) use ($minPrice): void {
                    $variantQuery
                        ->where('is_active', true)
                        ->whereRaw('(price_override - (price_override * discount_percentage / 100)) >= ?', [$minPrice]);
                });
            })
            ->when(! is_null($maxPrice), function (Builder $query) use ($maxPrice): void {
                $query->whereHas('variants', function (Builder $variantQuery) use ($maxPrice): void {
                    $variantQuery
                        ->where('is_active', true)
                        ->whereRaw('(price_override - (price_override * discount_percentage / 100)) <= ?', [$maxPrice]);
                });
            });

        $this->applySorting($products, $sort);

        $products = $products
            ->paginate(16)
            ->withQueryString();

        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $colors = ProductVariant::query()->where('is_active', true)->distinct()->orderBy('color')->pluck('color');
        $sizes = ProductVariant::query()->where('is_active', true)->distinct()->orderBy('size')->pluck('size');
        $sortOptions = [
            'terlaris' => 'Terlaris',
            'terbaru' => 'Terbaru',
            'harga_terendah' => 'Harga Terendah',
            'harga_tertinggi' => 'Harga Tertinggi',
            'rating' => 'Rating',
        ];

        return view('web.catalog.index', compact(
            'products',
            'categories',
            'colors',
            'sizes',
            'search',
            'categoryId',
            'color',
            'size',
            'minPrice',
            'maxPrice',
            'sort',
            'sortOptions',
        ));
    }

    protected function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'terbaru' => $query->latest(),
            'harga_terendah' => $query->orderBy('lowest_variant_price')->orderByDesc('id'),
            'harga_tertinggi' => $query->orderByDesc('lowest_variant_price')->orderByDesc('id'),
            'rating' => $query->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count')->orderByDesc('sold_qty'),
            default => $query->orderByDesc('sold_qty')->orderByDesc('id'),
        };
    }
}
