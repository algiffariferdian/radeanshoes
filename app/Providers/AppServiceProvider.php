<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.store', function ($view): void {
            $categories = Category::query()
                ->where('is_active', true)
                ->addSelect([
                    'variants_count' => ProductVariant::query()
                        ->selectRaw('count(*)')
                        ->join('products', 'products.id', '=', 'product_variants.product_id')
                        ->whereColumn('products.category_id', 'categories.id')
                        ->where('products.is_active', true)
                        ->where('product_variants.is_active', true),
                ])
                ->withCount([
                    'products' => fn ($query) => $query->where('is_active', true),
                ])
                ->orderBy('name')
                ->get();

            $previewProductsByCategory = Product::query()
                ->whereIn('category_id', $categories->pluck('id'))
                ->where('is_active', true)
                ->with([
                    'variants' => fn ($query) => $query
                        ->where('is_active', true)
                        ->with('images')
                        ->orderBy('id'),
                ])
                ->latest('id')
                ->get()
                ->map(function (Product $product): Product {
                    $previewImageUrls = $product->variants
                        ->flatMap(fn ($variant) => $variant->image_urls)
                        ->filter()
                        ->unique()
                        ->values();

                    $product->setAttribute('category_preview_image_urls', $previewImageUrls->all());
                    $product->setAttribute('category_preview_image_url', $previewImageUrls->first());

                    return $product;
                })
                ->groupBy('category_id')
                ->map(fn ($products) => $products
                    ->filter(fn ($product) => filled($product->getAttribute('category_preview_image_url')))
                    ->take(4)
                    ->values());

            $categories->each(function (Category $category) use ($previewProductsByCategory): void {
                $category->setRelation('previewProducts', $previewProductsByCategory->get($category->id, collect()));
            });

            $banners = Banner::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->latest('id')
                ->get();

            $cartCount = 0;

            if (Auth::check() && ! Auth::user()->isAdmin()) {
                $cartCount = (int) (Auth::user()
                    ->cart()
                    ->firstOrCreate()
                    ->items()
                    ->sum('qty'));
            }

            $view->with([
                'storefrontNavCategories' => $categories,
                'storefrontBanners' => $banners,
                'storefrontCartCount' => $cartCount,
            ]);
        });
    }
}
