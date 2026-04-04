<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Models\User;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('public storefront pages render successfully', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    ProductVariant::factory()->for($product)->create([
        'price_override' => 450000,
        'stock_qty' => 10,
    ]);

    $product->images()->create([
        'image_path' => 'products/demo-image.svg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('xl:grid-cols-4', false)
        ->assertSee('Diskon 50%* hanya di RadeanShoes')
        ->assertSee('Promo khusus untuk pengguna')
        ->assertSee('Pengiriman produk setiap hari')
        ->assertSee('Bantuan dan Panduan')
        ->assertSee('Radean Care')
        ->assertSee('Syarat dan Ketentuan')
        ->assertSee('Kebijakan Privasi')
        ->assertDontSee('Operasional Senin - Sabtu, 09.00 - 20.00 WIB')
        ->assertSee('&copy; 2026, PT. Rafky Dean Textile. All Rights Reserved.', false)
        ->assertSee('aria-label="Keranjang"', false);
    $this->get(route('products.index'))->assertOk();
    $this->get(route('products.show', $product))->assertOk();
});

test('storefront header renders category dropdown with active categories only', function () {
    Storage::fake('public');

    $activeCategoryA = Category::factory()->create([
        'name' => 'Running Harian',
        'is_active' => true,
    ]);
    $activeCategoryB = Category::factory()->create([
        'name' => 'Casual Canvas',
        'is_active' => true,
    ]);
    $inactiveCategory = Category::factory()->create([
        'name' => 'Kategori Nonaktif',
        'is_active' => false,
    ]);
    $previewProductA = Product::factory()->for($activeCategoryA)->create([
        'name' => 'Sprint One',
        'is_active' => true,
    ]);
    $previewProductB = Product::factory()->for($activeCategoryA)->create([
        'name' => 'Urban Two',
        'is_active' => true,
    ]);
    $inactivePreviewProduct = Product::factory()->for($activeCategoryA)->create([
        'name' => 'Hidden Preview',
        'is_active' => false,
    ]);

    Storage::disk('public')->put('products/sprint-one.svg', '<svg></svg>');
    Storage::disk('public')->put('products/sprint-one-alt.svg', '<svg></svg>');
    Storage::disk('public')->put('products/urban-two.svg', '<svg></svg>');
    Storage::disk('public')->put('products/hidden-preview.svg', '<svg></svg>');
    Storage::disk('public')->put('products/logo-preview.svg', '<svg></svg>');

    $previewProductA->images()->create([
        'image_path' => 'products/logo-preview.svg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);
    $previewProductB->images()->create([
        'image_path' => 'products/logo-preview.svg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);
    $inactivePreviewProduct->images()->create([
        'image_path' => 'products/hidden-preview.svg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $previewVariantA = ProductVariant::factory()->for($previewProductA)->create([
        'stock_qty' => 10,
    ]);
    $previewVariantB = ProductVariant::factory()->for($previewProductB)->create([
        'stock_qty' => 8,
    ]);
    $inactivePreviewVariant = ProductVariant::factory()->for($inactivePreviewProduct)->create([
        'stock_qty' => 7,
    ]);

    $previewVariantA->images()->create([
        'product_id' => $previewProductA->id,
        'image_path' => 'products/sprint-one.svg',
        'sort_order' => 0,
        'is_primary' => false,
    ]);
    $previewVariantA->images()->create([
        'product_id' => $previewProductA->id,
        'image_path' => 'products/sprint-one-alt.svg',
        'sort_order' => 1,
        'is_primary' => false,
    ]);
    $previewVariantB->images()->create([
        'product_id' => $previewProductB->id,
        'image_path' => 'products/urban-two.svg',
        'sort_order' => 0,
        'is_primary' => false,
    ]);
    $inactivePreviewVariant->images()->create([
        'product_id' => $inactivePreviewProduct->id,
        'image_path' => 'products/hidden-preview.svg',
        'sort_order' => 0,
        'is_primary' => false,
    ]);

    $homeResponse = $this->get(route('home'));

    $homeResponse
        ->assertOk()
        ->assertSee('Kategori')
        ->assertSee($activeCategoryA->name)
        ->assertSee($activeCategoryB->name)
        ->assertDontSee($inactiveCategory->name)
        ->assertSee($previewProductA->name)
        ->assertSee($previewProductB->name)
        ->assertDontSee($inactivePreviewProduct->name)
        ->assertSee('products?category='.$activeCategoryA->id, false)
        ->assertSee('products?category='.$activeCategoryB->id, false)
        ->assertSee('sprint-one.svg', false)
        ->assertSee('sprint-one-alt.svg', false)
        ->assertSee('urban-two.svg', false)
        ->assertDontSee('hidden-preview.svg', false);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Kategori');
});

test('storefront product cards and product detail prefer variant images over placeholder product images', function () {
    Storage::fake('public');

    $category = Category::factory()->create([
        'name' => 'Formal',
        'is_active' => true,
    ]);
    $product = Product::factory()->for($category)->create([
        'name' => 'Radean Classic Leather',
        'slug' => 'radean-classic-leather',
        'is_active' => true,
    ]);

    Storage::disk('public')->put('products/default-logo.png', 'placeholder');
    Storage::disk('public')->put('products/variants/radean-classic-leather-main.svg', '<svg></svg>');

    $product->images()->create([
        'image_path' => 'products/default-logo.png',
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $variant = ProductVariant::factory()->for($product)->create([
        'size' => '40',
        'color' => 'Hitam',
        'stock_qty' => 10,
        'is_active' => true,
    ]);

    $variant->images()->create([
        'product_id' => $product->id,
        'image_path' => 'products/variants/radean-classic-leather-main.svg',
        'sort_order' => 0,
        'is_primary' => false,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('radean-classic-leather-main.svg', false)
        ->assertDontSee('products/default-logo.png', false);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('radean-classic-leather-main.svg', false)
        ->assertDontSee('products/default-logo.png', false);
});

test('authenticated storefront pages render successfully', function () {
    $customer = User::factory()->create();
    $address = Address::factory()->for($customer)->create(['is_default' => true]);
    $shippingOption = ShippingOption::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    $variant = ProductVariant::factory()->for($product)->create([
        'stock_qty' => 10,
    ]);

    $cart = Cart::factory()->for($customer)->create();
    CartItem::factory()->for($cart)->for($variant, 'productVariant')->create();

    $order = Order::factory()->for($customer)->create([
        'address_id' => $address->id,
        'shipping_courier_name' => $shippingOption->courier_name,
        'shipping_service_name' => $shippingOption->service_name,
        'shipping_etd_text' => $shippingOption->etd_text,
        'order_status' => OrderStatus::Processing,
        'payment_status' => PaymentStatus::Paid,
    ]);

    OrderItem::factory()->for($order)->for($product)->for($variant, 'productVariant')->create();
    Payment::factory()->for($order)->create();

    $this->actingAs($customer)->get(route('cart.index'))->assertOk();
    $this->actingAs($customer)->get(route('checkout.index'))->assertOk();
    $this->actingAs($customer)->get(route('orders.index'))->assertOk();
    $this->actingAs($customer)->get(route('orders.show', $order->order_number))->assertOk();
    $this->actingAs($customer)->get(route('addresses.index'))->assertOk();
    $this->actingAs($customer)->get(route('account.profile.edit'))->assertOk();
});
