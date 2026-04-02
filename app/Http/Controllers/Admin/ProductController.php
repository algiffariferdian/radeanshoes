<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Catalog\CatalogIdentityService;
use App\Services\Products\ProductImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected CatalogIdentityService $catalogIdentityService,
        protected ProductImageStorageService $productImageStorageService,
    ) {}

    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->withCount(['images', 'variants'])
            ->latest()
            ->paginate(12);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->catalogIdentityService->uniqueProductSlug($data['name']);
        $data['sku_prefix'] = $this->catalogIdentityService->uniqueProductSkuPrefix($data['name']);
        $data['base_price'] = 0;
        $data['is_active'] = $request->boolean('is_active');

        $product = Product::create($data);

        $this->productImageStorageService->replaceProductCover($product, $request->file('cover_image'));

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'variants.images']);
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->catalogIdentityService->uniqueProductSlug($data['name'], $product);
        $data['sku_prefix'] = $this->catalogIdentityService->uniqueProductSkuPrefix($data['name'], $product);
        $data['base_price'] = $product->base_price;
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        $this->productImageStorageService->replaceProductCover($product, $request->file('cover_image'));

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productImageStorageService->deleteAll($product);

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dihapus.');
    }

    public function destroyImage(Product $product, ProductImage $productImage): RedirectResponse
    {
        abort_unless($productImage->product_id === $product->id, 404);
        abort_if($productImage->product_variant_id !== null, 404);

        $this->productImageStorageService->deleteImage($productImage);

        return back()->with('status', 'Gambar berhasil dihapus.');
    }
}
