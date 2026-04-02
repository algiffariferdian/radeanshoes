<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductVariantRequest;
use App\Http\Requests\Admin\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(Product $product): View
    {
        $variants = $product->variants()->latest()->paginate(15);

        return view('admin.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product): View
    {
        return view('admin.variants.create', compact('product'));
    }

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $product->variants()->create($data);

        return redirect()->route('admin.products.variants.index', $product)->with('status', 'Varian berhasil dibuat.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        $variant = $this->ownedVariant($product, $variant);

        return view('admin.variants.edit', compact('product', 'variant'));
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant = $this->ownedVariant($product, $variant);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $variant->update($data);

        return redirect()->route('admin.products.variants.index', $product)->with('status', 'Varian berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->ownedVariant($product, $variant)->delete();

        return redirect()->route('admin.products.variants.index', $product)->with('status', 'Varian berhasil dihapus.');
    }

    protected function ownedVariant(Product $product, ProductVariant $variant): ProductVariant
    {
        abort_unless($variant->product_id === $product->id, 404);

        return $variant;
    }
}
