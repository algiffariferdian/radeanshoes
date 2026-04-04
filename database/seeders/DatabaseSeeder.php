<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Models\User;
use App\Models\Voucher;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use App\Support\Enums\UserRole;
use App\Support\Enums\VoucherDiscountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $createdProducts = collect();

        User::query()->firstOrCreate(
            ['email' => 'admin@radeanshoes.test'],
            [
                'name' => 'Radean Admin',
                'phone' => '081234567890',
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        ShippingOption::factory()->count(3)->create();
        $primaryShippingOption = ShippingOption::query()->orderBy('id')->first();
        $createdProducts = $this->seedCatalogProducts();

        $customer = User::query()->firstOrCreate(
            ['email' => 'customer@radeanshoes.test'],
            [
                'name' => 'Test Customer',
                'phone' => '081111111111',
                'role' => UserRole::Customer,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $buyer = User::query()->firstOrCreate(
            ['email' => 'buyer@radeanshoes.test'],
            [
                'name' => 'Sample Buyer',
                'phone' => '082222222222',
                'role' => UserRole::Customer,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $customerAddress = Address::factory()->for($customer)->create([
            'recipient_name' => $customer->name,
            'phone' => $customer->phone,
            'is_default' => true,
        ]);

        $buyerAddress = Address::factory()->for($buyer)->create([
            'recipient_name' => $buyer->name,
            'phone' => $buyer->phone,
            'is_default' => true,
        ]);

        $this->seedBanners();
        $activeVoucher = $this->seedVouchers();

        $showcaseVariants = $createdProducts
            ->take(4)
            ->map(fn (Product $product) => $product->variants()->where('is_active', true)->orderByDesc('discount_percentage')->orderBy('size')->first())
            ->filter()
            ->values();

        if ($primaryShippingOption && $showcaseVariants->count() >= 4) {
            $this->seedCompletedOrder(
                $customer,
                $customerAddress,
                $showcaseVariants->get(0),
                $primaryShippingOption,
                2,
                5,
                'Ukuran pas dan nyaman dipakai harian.',
                $activeVoucher,
                10,
            );

            $this->seedCompletedOrder(
                $buyer,
                $buyerAddress,
                $showcaseVariants->get(1),
                $primaryShippingOption,
                1,
                4,
                'Ringan dipakai jalan dan warna sesuai foto.',
                null,
                8,
            );

            $this->seedCompletedOrder(
                $customer,
                $customerAddress,
                $showcaseVariants->get(2),
                $primaryShippingOption,
                3,
                5,
                'Sol empuk dan enak untuk aktivitas seharian.',
                null,
                6,
            );

            $this->seedCompletedOrder(
                $buyer,
                $buyerAddress,
                $showcaseVariants->get(3),
                $primaryShippingOption,
                1,
                4,
                'Cocok untuk latihan ringan dan terasa stabil.',
                null,
                4,
            );
        }
    }

    protected function seedBanners(): void
    {
        $bannerImages = collect(Storage::disk('public')->files('banners'))
            ->filter(fn (string $path) => filled($path))
            ->values();

        collect([
            [
                'title' => 'Langkah ringan untuk aktivitas harian',
                'subtitle' => 'Pilih sneakers, running, dan casual terbaru dengan ukuran yang siap dipakai.',
                'button_label' => 'Lihat Katalog',
                'link_url' => '/products',
                'accent' => '#0F766E',
            ],
            [
                'title' => 'Promo sepatu pilihan minggu ini',
                'subtitle' => 'Cari varian dengan diskon aktif dan lanjut checkout lebih cepat.',
                'button_label' => 'Lihat Promo',
                'link_url' => '/products?sort=harga_terendah',
                'accent' => '#166534',
            ],
            [
                'title' => 'Ukuran, warna, dan stok lebih jelas',
                'subtitle' => 'Semua varian ditampilkan langsung agar belanja sepatu terasa lebih pasti.',
                'button_label' => 'Belanja Sekarang',
                'link_url' => '/products',
                'accent' => '#1F2937',
            ],
        ])->each(function (array $banner, int $index) use ($bannerImages): void {
            $imagePath = $bannerImages->get($index);

            if (! $imagePath) {
                $imagePath = 'banners/banner-'.($index + 1).'-seed.svg';
                Storage::disk('public')->put($imagePath, $this->seedBannerSvg($banner['title'], $banner['subtitle'], $banner['accent']));
            }

            Banner::query()->create([
                'title' => $banner['title'],
                'subtitle' => $banner['subtitle'],
                'button_label' => $banner['button_label'],
                'link_url' => $banner['link_url'],
                'image_path' => $imagePath,
                'sort_order' => $index,
                'is_active' => true,
            ]);
        });
    }

    protected function seedVouchers(): Voucher
    {
        Voucher::query()->create([
            'code' => 'HEMAT10',
            'name' => 'Diskon 10% Maksimal 75 Ribu',
            'discount_type' => VoucherDiscountType::Percent,
            'discount_value' => 10,
            'min_subtotal' => 300000,
            'max_discount' => 75000,
            'usage_limit' => 200,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(2),
            'is_active' => true,
        ]);

        Voucher::query()->create([
            'code' => 'HEMAT15',
            'name' => 'Diskon 15 Persen',
            'discount_type' => VoucherDiscountType::Percent,
            'discount_value' => 15,
            'min_subtotal' => 500000,
            'max_discount' => 100000,
            'usage_limit' => 150,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(1),
            'is_active' => true,
        ]);

        return Voucher::query()->where('code', 'HEMAT10')->firstOrFail();
    }

    protected function seedCompletedOrder(
        User $user,
        Address $address,
        ProductVariant $variant,
        ShippingOption $shippingOption,
        int $qty,
        int $rating,
        string $review,
        ?Voucher $voucher,
        int $daysAgo,
    ): void {
        $variant->loadMissing('product');

        $unitPrice = (float) $variant->effectivePrice();
        $subtotal = $unitPrice * $qty;
        $shippingCost = (float) $shippingOption->price;
        $discountAmount = $voucher ? (float) $voucher->calculateDiscount($subtotal) : 0;
        $total = max(0, $subtotal + $shippingCost - $discountAmount);
        $placedAt = now()->subDays($daysAgo);

        $order = Order::query()->create([
            'order_number' => 'RDS-SEED-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'address_id' => $address->id,
            'voucher_id' => $voucher?->id,
            'shipping_recipient_name' => $address->recipient_name,
            'shipping_phone' => $address->phone,
            'shipping_address_line' => $address->address_line,
            'shipping_district' => $address->district,
            'shipping_city' => $address->city,
            'shipping_province' => $address->province,
            'shipping_postal_code' => $address->postal_code,
            'shipping_courier_name' => $shippingOption->courier_name,
            'shipping_service_name' => $shippingOption->service_name,
            'shipping_etd_text' => $shippingOption->etd_text,
            'shipping_cost' => number_format($shippingCost, 2, '.', ''),
            'voucher_code' => $voucher?->code,
            'discount_amount' => number_format($discountAmount, 2, '.', ''),
            'subtotal_amount' => number_format($subtotal, 2, '.', ''),
            'total_amount' => number_format($total, 2, '.', ''),
            'order_status' => OrderStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
            'placed_at' => $placedAt,
            'paid_at' => $placedAt->copy()->addHour(),
            'shipped_at' => $placedAt->copy()->addDay(),
            'completed_at' => $placedAt->copy()->addDays(3),
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'product_name_snapshot' => $variant->product->name,
            'variant_size_snapshot' => $variant->size,
            'variant_color_snapshot' => $variant->color,
            'sku_snapshot' => $variant->sku,
            'unit_price' => number_format($unitPrice, 2, '.', ''),
            'qty' => $qty,
            'line_total' => number_format($subtotal, 2, '.', ''),
        ]);

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'midtrans',
            'provider_mode' => 'sandbox',
            'transaction_id' => 'seed-'.Str::lower(Str::random(12)),
            'order_id_provider' => $order->order_number,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'gross_amount' => number_format($total, 2, '.', ''),
            'raw_response_json' => ['seeded' => true],
            'paid_at' => $placedAt->copy()->addHour(),
        ]);

        if ($voucher) {
            $voucher->increment('used_count');
        }

        $variant->decrement('stock_qty', $qty);

        ProductReview::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $variant->product_id,
            ],
            [
                'order_item_id' => $orderItem->id,
                'rating' => $rating,
                'review' => $review,
            ],
        );
    }

    protected function seedBannerSvg(string $title, string $subtitle, string $accent): string
    {
        $safeTitle = e(Str::limit($title, 42, ''));
        $safeSubtitle = e(Str::limit($subtitle, 78, ''));

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="600" viewBox="0 0 1600 600" fill="none">
  <rect width="1600" height="600" fill="#F5F7F6"/>
  <rect x="24" y="24" width="1552" height="552" rx="32" fill="#FFFFFF"/>
  <rect x="24" y="24" width="1552" height="552" rx="32" fill="url(#hero)"/>
  <circle cx="1240" cy="208" r="136" fill="#FFFFFF" fill-opacity="0.12"/>
  <circle cx="1380" cy="352" r="92" fill="#FFFFFF" fill-opacity="0.09"/>
  <rect x="108" y="104" width="170" height="42" rx="21" fill="#FFFFFF" fill-opacity="0.16"/>
  <text x="132" y="132" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="22" font-weight="700">RADEANSHOES</text>
  <text x="108" y="228" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="50" font-weight="700">{$safeTitle}</text>
  <text x="108" y="290" fill="#E5E7EB" font-family="Arial, sans-serif" font-size="24">{$safeSubtitle}</text>
  <path d="M934 344C1018 244 1142 198 1288 198C1360 198 1428 214 1494 248L1450 318C1394 290 1340 276 1282 276C1176 276 1084 314 1014 382L934 344Z" fill="#FFFFFF" fill-opacity="0.18"/>
  <path d="M896 392L1014 382C1084 314 1176 276 1282 276C1340 276 1394 290 1450 318L1492 378L1410 408C1324 438 1232 454 1140 454H944L896 392Z" fill="#FFFFFF" fill-opacity="0.22"/>
  <defs>
    <linearGradient id="hero" x1="24" y1="24" x2="1576" y2="576" gradientUnits="userSpaceOnUse">
      <stop stop-color="{$accent}"/>
      <stop offset="1" stop-color="#111827"/>
    </linearGradient>
  </defs>
</svg>
SVG;
    }

    protected function seedCatalogProducts()
    {
        $rows = collect($this->catalogProductRows());
        $categories = $rows
            ->pluck('category')
            ->unique()
            ->mapWithKeys(function (string $categoryName): array {
                $category = Category::query()->updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    [
                        'name' => $categoryName,
                        'is_active' => true,
                    ],
                );

                return [$categoryName => $category];
            });

        return $rows
            ->groupBy('handle')
            ->map(function ($productRows) use ($categories) {
                $firstRow = $productRows->first();
                $category = $categories->get($firstRow['category']);

                $product = Product::query()->updateOrCreate(
                    ['slug' => $firstRow['handle']],
                    [
                        'category_id' => $category->id,
                        'name' => $firstRow['title'],
                        'sku_prefix' => Str::before($firstRow['sku'], '-') ?: Str::upper(Str::limit(Str::slug($firstRow['title'], ''), 8, '')),
                        'description' => $this->catalogProductDescription($firstRow['title'], $firstRow['category'], $productRows),
                        'base_price' => (float) $productRows->min('price'),
                        'weight_gram' => $this->catalogProductWeight($firstRow['category']),
                        'is_active' => true,
                    ],
                );

                $product->images()->delete();

                $productRows->each(function (array $row) use ($product): void {
                    $variant = ProductVariant::query()->updateOrCreate(
                        ['sku' => $row['sku']],
                        [
                            'product_id' => $product->id,
                            'size' => $row['size'],
                            'color' => $row['color'],
                            'price_override' => (float) $row['price'],
                            'discount_percentage' => $this->catalogDiscountPercentage($row['handle']),
                            'stock_qty' => (int) $row['stock'],
                            'is_active' => true,
                        ],
                    );

                    $variantImagePath = $this->catalogVariantImagePath($row)
                        ?? 'products/variants/'.Str::slug($row['sku']).'.svg';

                    if (! Storage::disk('public')->exists($variantImagePath)) {
                        Storage::disk('public')->put($variantImagePath, $this->seedVariantImageSvg($product, $variant, 1));
                    }

                    $variant->images()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'sort_order' => 0,
                        ],
                        [
                            'image_path' => $variantImagePath,
                            'is_primary' => false,
                        ],
                    );
                });

                return $product->fresh(['variants', 'images']);
            })
            ->values();
    }

    protected function catalogVariantImagePath(array $row): ?string
    {
        $files = collect(Storage::disk('public')->allFiles('products'));
        if ($files->isEmpty()) {
            return null;
        }

        $sku = Str::slug((string) $row['sku']);
        $handle = Str::slug((string) $row['handle']);
        $category = Str::slug((string) $row['category']);
        $size = Str::slug((string) $row['size']);
        $color = Str::slug((string) $row['color']);

        $bestMatch = null;
        $bestScore = -1;

        foreach ($files as $path) {
            $filename = Str::slug(pathinfo($path, PATHINFO_FILENAME));

            $score = 0;
            if ($sku !== '' && str_contains($filename, $sku)) {
                $score += 100;
            }
            if ($handle !== '' && str_contains($filename, $handle)) {
                $score += 30;
            }
            if ($category !== '' && str_contains($filename, $category)) {
                $score += 10;
            }
            if ($size !== '' && str_contains($filename, $size)) {
                $score += 15;
            }
            if ($color !== '' && str_contains($filename, $color)) {
                $score += 15;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $path;
            }
        }

        return $bestScore > 0 ? $bestMatch : null;
    }

    protected function catalogProductDescription(string $title, string $categoryName, $productRows): string
    {
        $sizes = $productRows->pluck('size')->unique()->values()->implode(', ');
        $colors = $productRows->pluck('color')->unique()->values()->implode(', ');

        return "{$title} adalah koleksi {$categoryName} dari RadeanShoes dengan pilihan ukuran {$sizes} dan warna {$colors}. Produk ini disiapkan untuk kebutuhan harian dengan stok aktif per varian.";
    }

    protected function catalogProductWeight(string $categoryName): int
    {
        return match ($categoryName) {
            'Sneakers' => 820,
            'Formal' => 900,
            'Sport' => 780,
            'Casual' => 720,
            'Outdoor' => 1080,
            'Sandal' => 460,
            default => 800,
        };
    }

    protected function catalogDiscountPercentage(string $handle): int
    {
        return [
            'radean-flex-run' => 10,
            'radean-street-pro' => 12,
            'radean-classic-leather' => 8,
            'radean-office-prime' => 10,
            'radean-air-boost' => 15,
            'radean-runner-x' => 12,
            'radean-slip-on-lite' => 8,
            'radean-casual-knit' => 10,
        ][$handle] ?? 0;
    }

    protected function catalogProductRows(): array
    {
        $csv = <<<'CSV'
Handle,Title,Category,Option1 Name,Option1 Value,Option2 Name,Option2 Value,SKU,Price,Stock
radean-flex-run,Radean Flex Run,Sneakers,Ukuran,39,Warna,Hitam,RFR-39-HITAM,350000,10
radean-flex-run,Radean Flex Run,Sneakers,Ukuran,40,Warna,Putih,RFR-40-PUTIH,350000,8
radean-flex-run,Radean Flex Run,Sneakers,Ukuran,41,Warna,Navy,RFR-41-NAVY,360000,6

radean-street-pro,Radean Street Pro,Sneakers,Ukuran,39,Warna,Abu-abu,RSP-39-ABU,400000,7
radean-street-pro,Radean Street Pro,Sneakers,Ukuran,40,Warna,Hitam,RSP-40-HITAM,400000,9
radean-street-pro,Radean Street Pro,Sneakers,Ukuran,41,Warna,Merah,RSP-41-MERAH,420000,5

radean-classic-leather,Radean Classic Leather,Formal,Ukuran,39,Warna,Cokelat,RCL-39-COKLAT,500000,6
radean-classic-leather,Radean Classic Leather,Formal,Ukuran,40,Warna,Hitam,RCL-40-HITAM,500000,8
radean-classic-leather,Radean Classic Leather,Formal,Ukuran,41,Warna,Maroon,RCL-41-MAROON,520000,4

radean-office-prime,Radean Office Prime,Formal,Ukuran,39,Warna,Hitam,ROP-39-HITAM,480000,7
radean-office-prime,Radean Office Prime,Formal,Ukuran,40,Warna,Cokelat,ROP-40-COKLAT,480000,6
radean-office-prime,Radean Office Prime,Formal,Ukuran,41,Warna,Beige,ROP-41-BEIGE,490000,5

radean-air-boost,Radean Air Boost,Sport,Ukuran,39,Warna,Biru,RAB-39-BIRU,450000,10
radean-air-boost,Radean Air Boost,Sport,Ukuran,40,Warna,Hitam,RAB-40-HITAM,450000,9
radean-air-boost,Radean Air Boost,Sport,Ukuran,41,Warna,Hijau,RAB-41-HIJAU,470000,6

radean-runner-x,Radean Runner X,Sport,Ukuran,39,Warna,Navy,RRX-39-NAVY,430000,8
radean-runner-x,Radean Runner X,Sport,Ukuran,40,Warna,Hitam,RRX-40-HITAM,430000,7
radean-runner-x,Radean Runner X,Sport,Ukuran,41,Warna,Merah,RRX-41-MERAH,450000,5

radean-slip-on-lite,Radean Slip On Lite,Casual,Ukuran,39,Warna,Cream,RSL-39-CREAM,300000,10
radean-slip-on-lite,Radean Slip On Lite,Casual,Ukuran,40,Warna,Hitam,RSL-40-HITAM,300000,8
radean-slip-on-lite,Radean Slip On Lite,Casual,Ukuran,41,Warna,Abu-abu,RSL-41-ABU,320000,6

radean-casual-knit,Radean Casual Knit,Casual,Ukuran,39,Warna,Beige,RCK-39-BEIGE,320000,9
radean-casual-knit,Radean Casual Knit,Casual,Ukuran,40,Warna,Olive,RCK-40-OLIVE,320000,7
radean-casual-knit,Radean Casual Knit,Casual,Ukuran,41,Warna,Hitam,RCK-41-HITAM,330000,5

radean-hiking-pro,Radean Hiking Pro,Outdoor,Ukuran,39,Warna,Cokelat,RHP-39-COKLAT,550000,6
radean-hiking-pro,Radean Hiking Pro,Outdoor,Ukuran,40,Warna,Olive,RHP-40-OLIVE,550000,5
radean-hiking-pro,Radean Hiking Pro,Outdoor,Ukuran,41,Warna,Hitam,RHP-41-HITAM,570000,4

radean-trail-master,Radean Trail Master,Outdoor,Ukuran,39,Warna,Hijau,RTM-39-HIJAU,530000,7
radean-trail-master,Radean Trail Master,Outdoor,Ukuran,40,Warna,Cokelat,RTM-40-COKLAT,530000,6
radean-trail-master,Radean Trail Master,Outdoor,Ukuran,41,Warna,Hitam,RTM-41-HITAM,550000,5

radean-sandal-flex,Radean Sandal Flex,Sandal,Ukuran,39,Warna,Hitam,RSF-39-HITAM,200000,12
radean-sandal-flex,Radean Sandal Flex,Sandal,Ukuran,40,Warna,Abu-abu,RSF-40-ABU,200000,10
radean-sandal-flex,Radean Sandal Flex,Sandal,Ukuran,41,Warna,Navy,RSF-41-NAVY,220000,8

radean-sandal-pro,Radean Sandal Pro,Sandal,Ukuran,39,Warna,Hitam,RSPRO-39-HITAM,220000,11
radean-sandal-pro,Radean Sandal Pro,Sandal,Ukuran,40,Warna,Cokelat,RSPRO-40-COKLAT,220000,9
radean-sandal-pro,Radean Sandal Pro,Sandal,Ukuran,41,Warna,Olive,RSPRO-41-OLIVE,230000,7
CSV;

        $lines = collect(preg_split('/\r\n|\r|\n/', trim($csv)))
            ->map(fn (string $line) => trim($line))
            ->filter();

        $headers = collect(str_getcsv((string) $lines->shift()))
            ->map(fn (string $header) => Str::snake(Str::lower($header)));

        return $lines
            ->map(function (string $line) use ($headers): array {
                $values = collect(str_getcsv($line));
                $row = $headers->combine($values)->all();

                return [
                    'handle' => $row['handle'],
                    'title' => $row['title'],
                    'category' => $row['category'],
                    'size' => $row['option1_value'],
                    'color' => $row['option2_value'],
                    'sku' => $row['sku'],
                    'price' => (int) $row['price'],
                    'stock' => (int) $row['stock'],
                ];
            })
            ->values()
            ->all();
    }

    protected function seedProductImageSvg(Product $product, Category $category): string
    {
        $title = e(Str::limit($product->name, 18, ''));
        $categoryName = e(Str::limit($category->name, 18, ''));

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900" fill="none">
  <rect width="1200" height="900" fill="#F5F5F4"/>
  <rect x="72" y="72" width="1056" height="756" rx="48" fill="url(#bg)"/>
  <path d="M240 560C310 430 474 350 645 350C782 350 899 405 970 500L1020 566L936 604L842 562C753 522 649 506 548 516L364 534L240 560Z" fill="#1C1917"/>
  <path d="M282 574L366 552C446 531 530 522 614 526L738 532C793 535 846 549 894 573L960 606L899 676H353L282 574Z" fill="#44403C"/>
  <rect x="286" y="648" width="164" height="58" rx="29" fill="#0F172A"/>
  <rect x="770" y="648" width="164" height="58" rx="29" fill="#0F172A"/>
  <text x="108" y="150" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="28" font-weight="700" letter-spacing="3">RADEANSHOES</text>
  <text x="108" y="742" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="46" font-weight="700">{$title}</text>
  <text x="108" y="790" fill="#E7E5E4" font-family="Arial, sans-serif" font-size="26">{$categoryName}</text>
  <defs>
    <linearGradient id="bg" x1="72" y1="72" x2="1128" y2="828" gradientUnits="userSpaceOnUse">
      <stop stop-color="#F59E0B"/>
      <stop offset="0.5" stop-color="#57534E"/>
      <stop offset="1" stop-color="#0F766E"/>
    </linearGradient>
  </defs>
</svg>
SVG;
    }

    protected function seedVariantImageSvg(Product $product, ProductVariant $variant, int $imageIndex): string
    {
        $title = e(Str::limit($product->name, 16, ''));
        $variantLabel = e('EU '.$variant->size.' - '.$variant->color);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900" fill="none">
  <rect width="1200" height="900" fill="#F5F5F4"/>
  <rect x="72" y="72" width="1056" height="756" rx="48" fill="#FFFFFF"/>
  <rect x="132" y="132" width="936" height="636" rx="36" fill="#E7F3EC"/>
  <path d="M246 566C329 442 485 372 644 372C780 372 892 419 965 502L1010 554L938 594L846 560C760 528 663 516 566 524L366 542L246 566Z" fill="#1F2937"/>
  <path d="M292 588L370 566C447 545 528 536 610 540L730 546C787 549 840 562 890 586L956 618L900 686H360L292 588Z" fill="#4B5563"/>
  <rect x="304" y="654" width="150" height="52" rx="26" fill="#111827"/>
  <rect x="774" y="654" width="150" height="52" rx="26" fill="#111827"/>
  <text x="130" y="170" fill="#111827" font-family="Arial, sans-serif" font-size="26" font-weight="700">RADEANSHOES</text>
  <text x="130" y="724" fill="#111827" font-family="Arial, sans-serif" font-size="42" font-weight="700">{$title}</text>
  <text x="130" y="774" fill="#4B5563" font-family="Arial, sans-serif" font-size="24">{$variantLabel}</text>
  <text x="904" y="170" fill="#4B5563" font-family="Arial, sans-serif" font-size="22" font-weight="700">VIEW {$imageIndex}</text>
</svg>
SVG;
    }
}
