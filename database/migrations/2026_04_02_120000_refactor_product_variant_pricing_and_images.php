<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percentage')->default(0)->after('price_override');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();
        });

        $productBasePrices = DB::table('products')->pluck('base_price', 'id');

        DB::table('product_variants')
            ->select(['id', 'product_id'])
            ->whereNull('price_override')
            ->get()
            ->each(function (object $variant) use ($productBasePrices): void {
                DB::table('product_variants')
                    ->where('id', $variant->id)
                    ->update([
                        'price_override' => $productBasePrices[$variant->product_id] ?? 0,
                    ]);
            });

        collect([
            'Black' => 'Hitam',
            'White' => 'Putih',
            'Grey' => 'Abu-abu',
            'Gray' => 'Abu-abu',
            'Brown' => 'Cokelat',
            'Red' => 'Merah',
            'Blue' => 'Biru',
            'Green' => 'Hijau',
        ])->each(function (string $newColor, string $oldColor): void {
            DB::table('product_variants')
                ->where('color', $oldColor)
                ->update(['color' => $newColor]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }
};
