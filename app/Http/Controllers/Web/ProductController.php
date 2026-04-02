<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
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
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(8)
            ->get();

        $reviewHighlights = $product->reviews->take(6);
        $existingReview = null;
        $canReview = false;

        if (auth()->check()) {
            $existingReview = auth()->user()->productReviews()->where('product_id', $product->id)->first();
            $canReview = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->whereIn('order_status', [
                        OrderStatus::Paid->value,
                        OrderStatus::Processing->value,
                        OrderStatus::Shipped->value,
                        OrderStatus::Completed->value,
                    ]))
                ->exists();
        }

        $storeProfile = [
            'name' => 'RadeanShoes Official',
            'location' => 'Jakarta Selatan',
            'response_time' => '< 15 menit',
            'product_count' => Product::query()->where('is_active', true)->count(),
            'rating' => round((float) (ProductReview::query()->avg('rating') ?? 0), 1),
        ];

        return view('web.products.show', compact(
            'product',
            'relatedProducts',
            'reviewHighlights',
            'storeProfile',
            'existingReview',
            'canReview',
        ));
    }
}
