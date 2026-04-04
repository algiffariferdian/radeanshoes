<?php

use App\Models\Banner;
use App\Models\User;
use App\Models\Voucher;
use App\Support\Enums\VoucherDiscountType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can create banner with uploaded image', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.banners.store'), [
            'sort_order' => 1,
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('banner.png', 1920, 720),
        ])
        ->assertRedirect(route('admin.banners.index'));

    $banner = Banner::query()->first();

    expect($banner)->not->toBeNull()
        ->and($banner->sort_order)->toBe(1)
        ->and($banner->is_active)->toBeTrue();

    Storage::disk('public')->assertExists($banner->image_path);
});

test('admin can create voucher', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.vouchers.store'), [
            'code' => 'hemat20',
            'name' => 'Diskon 20 Persen',
            'discount_value' => 20,
            'min_subtotal' => 300000,
            'max_discount' => 100000,
            'usage_limit' => 50,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.vouchers.index'));

    $voucher = Voucher::query()->first();

    expect($voucher)->not->toBeNull()
        ->and($voucher->code)->toBe('HEMAT20')
        ->and($voucher->discount_type)->toBe(VoucherDiscountType::Percent);
});
