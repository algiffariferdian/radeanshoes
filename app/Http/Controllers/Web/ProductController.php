<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'images',
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('size'),
        ]);

        $relatedProducts = Product::query()
            ->with(['primaryImage', 'images', 'variants'])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return view('web.products.show', compact('product', 'relatedProducts'));
    }
}
