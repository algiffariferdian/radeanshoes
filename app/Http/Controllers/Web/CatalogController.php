<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
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

        $products = Product::query()
            ->with(['category', 'primaryImage', 'images', 'variants'])
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true))
            ->whereHas('variants', fn (Builder $query) => $query->where('is_active', true))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
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
                $query->where(function (Builder $priceQuery) use ($minPrice): void {
                    $priceQuery
                        ->where('base_price', '>=', $minPrice)
                        ->orWhereHas('variants', fn (Builder $variantQuery) => $variantQuery->whereNotNull('price_override')->where('price_override', '>=', $minPrice));
                });
            })
            ->when(! is_null($maxPrice), function (Builder $query) use ($maxPrice): void {
                $query->where(function (Builder $priceQuery) use ($maxPrice): void {
                    $priceQuery
                        ->where('base_price', '<=', $maxPrice)
                        ->orWhereHas('variants', fn (Builder $variantQuery) => $variantQuery->whereNotNull('price_override')->where('price_override', '<=', $maxPrice));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $colors = ProductVariant::query()->where('is_active', true)->distinct()->orderBy('color')->pluck('color');
        $sizes = ProductVariant::query()->where('is_active', true)->distinct()->orderBy('size')->pluck('size');

        return view('web.catalog.index', compact('products', 'categories', 'colors', 'sizes', 'search', 'categoryId', 'color', 'size', 'minPrice', 'maxPrice'));
    }
}
