<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Enums\OrderStatus;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'primaryImage',
            'images',
            'variants' => fn ($query) => $query->where('is_active', true)->with('images')->orderBy('color')->orderBy('size'),
            'reviews.user',
        ])->loadAvg('reviews as reviews_avg_rating', 'rating')
            ->loadCount('reviews')
            ->loadSum([
            'orderItems as sold_qty' => fn ($query) => $query->whereHas('order', fn ($orderQuery) => $orderQuery->whereIn('order_status', [
                OrderStatus::Paid->value,
                OrderStatus::Processing->value,
                OrderStatus::Shipped->value,
                OrderStatus::Completed->value,
            ])),
        ], 'qty');

        $relatedProducts = Product::query()
            ->with([
                'category',
                'primaryImage',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true)->with('images'),
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
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->whereHas('variants', fn ($query) => $query->where('is_active', true))
            ->whereKeyNot($product->id)
            ->orderByDesc('sold_qty')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $reviewHighlights = $product->reviews->take(6);

        return view('web.products.show', compact(
            'product',
            'relatedProducts',
            'reviewHighlights',
        ));
    }
}
