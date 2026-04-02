<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductVariantRequest;
use App\Http\Requests\Admin\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\Catalog\CatalogIdentityService;
use App\Services\Products\ProductImageStorageService;
use App\Support\Catalog\ProductColorOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function __construct(
        protected CatalogIdentityService $catalogIdentityService,
        protected ProductImageStorageService $productImageStorageService,
    ) {}

    public function index(Product $product): View
    {
        $variants = $product->variants()
            ->withCount('images')
            ->latest()
            ->paginate(15);

        return view('admin.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product): View
    {
        $colorOptions = ProductColorOptions::values();

        return view('admin.variants.create', compact('product', 'colorOptions'));
    }

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $variant = $product->variants()->create([
            'size' => (string) $data['size'],
            'color' => $data['color'],
            'sku' => $this->catalogIdentityService->uniqueVariantSku($product, $data['size'], $data['color']),
            'price_override' => $data['price'],
            'discount_percentage' => (int) ($data['discount_percentage'] ?? 0),
            'stock_qty' => (int) $data['stock_qty'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->productImageStorageService->storeVariantImages($product, $variant, $request->file('images', []));
        $this->syncCachedProductPrice($product);

        return redirect()->route('admin.products.variants.index', $product)->with('status', 'Varian berhasil dibuat.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        $variant = $this->ownedVariant($product, $variant);
        $variant->load('images');
        $colorOptions = ProductColorOptions::values();

        return view('admin.variants.edit', compact('product', 'variant', 'colorOptions'));
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant = $this->ownedVariant($product, $variant);
        $data = $request->validated();
        $variant->update([
            'size' => (string) $data['size'],
            'color' => $data['color'],
            'sku' => $this->catalogIdentityService->uniqueVariantSku($product, $data['size'], $data['color'], $variant),
            'price_override' => $data['price'],
            'discount_percentage' => (int) ($data['discount_percentage'] ?? 0),
            'stock_qty' => (int) $data['stock_qty'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->productImageStorageService->storeVariantImages($product, $variant, $request->file('images', []));
        $this->syncCachedProductPrice($product);

        return redirect()->route('admin.products.variants.index', $product)->with('status', 'Varian berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant = $this->ownedVariant($product, $variant);
        $this->productImageStorageService->deleteVariantImages($variant);
        $variant->delete();
        $this->syncCachedProductPrice($product);

        return redirect()->route('admin.products.variants.index', $product)->with('status', 'Varian berhasil dihapus.');
    }

    public function destroyImage(Product $product, ProductVariant $variant, ProductImage $productImage): RedirectResponse
    {
        $variant = $this->ownedVariant($product, $variant);
        abort_unless($productImage->product_id === $product->id && $productImage->product_variant_id === $variant->id, 404);

        $this->productImageStorageService->deleteImage($productImage);

        return back()->with('status', 'Gambar varian berhasil dihapus.');
    }

    protected function ownedVariant(Product $product, ProductVariant $variant): ProductVariant
    {
        abort_unless($variant->product_id === $product->id, 404);

        return $variant;
    }

    protected function syncCachedProductPrice(Product $product): void
    {
        $lowestPrice = $product->variants()
            ->where('is_active', true)
            ->get()
            ->map(fn (ProductVariant $variant) => (float) $variant->effectivePrice())
            ->filter(fn (float $price) => $price >= 0)
            ->min();

        $product->updateQuietly([
            'base_price' => $lowestPrice ?? 0,
        ]);
    }
}
