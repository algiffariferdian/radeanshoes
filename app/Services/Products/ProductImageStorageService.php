<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageStorageService
{
    /**
     * @param  array<int, UploadedFile>  $images
     */
    public function replaceProductCover(Product $product, ?UploadedFile $image): void
    {
        if (! $image) {
            return;
        }

        DB::transaction(function () use ($product, $image): void {
            foreach ($product->images()->get() as $existingImage) {
                Storage::disk('public')->delete($existingImage->image_path);
            }

            $path = $image->store('products', 'public');

            $product->images()->delete();

            $product->images()->create([
                'image_path' => $path,
                'sort_order' => 0,
                'is_primary' => true,
            ]);
        });
    }

    /**
     * @param  array<int, UploadedFile>  $images
     */
    public function storeVariantImages(Product $product, ProductVariant $variant, array $images): void
    {
        if ($images === []) {
            return;
        }

        $sortOrder = (int) ($variant->images()->max('sort_order') ?? -1);

        foreach ($images as $image) {
            $path = $image->store('products', 'public');

            $variant->images()->create([
                'product_id' => $product->id,
                'image_path' => $path,
                'sort_order' => ++$sortOrder,
                'is_primary' => false,
            ]);
        }
    }

    public function deleteImage(ProductImage $image): void
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
    }

    public function deleteAll(Product $product): void
    {
        DB::transaction(function () use ($product): void {
            $product->loadMissing('allImages');

            foreach ($product->allImages as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->allImages()->delete();
        });
    }

    public function deleteVariantImages(ProductVariant $variant): void
    {
        $variant->loadMissing('images');

        foreach ($variant->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $variant->images()->delete();
    }
}
