<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreProductReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        $orderItem = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($query) => $query
                ->where('user_id', $user->id)
                ->whereIn('order_status', [
                    OrderStatus::Paid->value,
                    OrderStatus::Processing->value,
                    OrderStatus::Shipped->value,
                    OrderStatus::Completed->value,
                ]))
            ->latest('id')
            ->first();

        if (! $orderItem) {
            throw ValidationException::withMessages([
                'rating' => 'Rating hanya bisa diberikan oleh pembeli yang sudah menyelesaikan transaksi.',
            ]);
        }

        ProductReview::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            [
                'order_item_id' => $orderItem->id,
                'rating' => $request->integer('rating'),
                'review' => $request->filled('review') ? trim((string) $request->string('review')) : null,
            ],
        );

        return back()->with('status', 'Ulasan berhasil disimpan.');
    }
}
