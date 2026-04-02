<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
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
                ->withCount('products')
                ->orderBy('name')
                ->take(8)
                ->get();
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
