<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
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
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $product = Product::create($data);

        $this->storeUploadedImages($product, $request->file('images', []));

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'variants']);
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        $this->storeUploadedImages($product, $request->file('images', []));
        $this->syncPrimaryImage($product, $request->integer('primary_image_id') ?: null);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dihapus.');
    }

    public function destroyImage(Product $product, ProductImage $productImage): RedirectResponse
    {
        abort_unless($productImage->product_id === $product->id, 404);

        $wasPrimary = $productImage->is_primary;
        Storage::disk('public')->delete($productImage->image_path);
        $productImage->delete();

        if ($wasPrimary) {
            $product->images()->oldest('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Gambar berhasil dihapus.');
    }

    /**
     * @param  array<int, UploadedFile>  $images
     */
    protected function storeUploadedImages(Product $product, array $images): void
    {
        $sortOrder = (int) ($product->images()->max('sort_order') ?? -1);
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');

            $product->images()->create([
                'image_path' => $path,
                'sort_order' => ++$sortOrder,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }
    }

    protected function syncPrimaryImage(Product $product, ?int $primaryImageId): void
    {
        if (! $primaryImageId || ! $product->images()->whereKey($primaryImageId)->exists()) {
            if (! $product->images()->where('is_primary', true)->exists()) {
                $product->images()->oldest('sort_order')->first()?->update(['is_primary' => true]);
            }

            return;
        }

        $product->images()->update(['is_primary' => false]);
        $product->images()->whereKey($primaryImageId)->update(['is_primary' => true]);
    }
}
