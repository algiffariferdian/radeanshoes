<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $baseQuery = Product::query()
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
            ->whereHas('variants', fn (Builder $query) => $query->where('is_active', true));

        $bestSellerProducts = (clone $baseQuery)
            ->orderByDesc('sold_qty')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $newArrivalProducts = (clone $baseQuery)
            ->latest()
            ->take(8)
            ->get();

        $promoProducts = (clone $baseQuery)
            ->whereHas('variants', fn (Builder $query) => $query->where('is_active', true)->where('discount_percentage', '>', 0))
            ->latest()
            ->take(8)
            ->get();

        return view('web.home', compact(
            'bestSellerProducts',
            'newArrivalProducts',
            'promoProducts',
        ));
    }
}
