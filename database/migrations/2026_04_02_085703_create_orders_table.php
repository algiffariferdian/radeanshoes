<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->string('shipping_recipient_name', 100);
            $table->string('shipping_phone', 30);
            $table->text('shipping_address_line');
            $table->string('shipping_district', 100)->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100);
            $table->string('shipping_postal_code', 20);
            $table->string('shipping_courier_name', 100);
            $table->string('shipping_service_name', 100);
            $table->string('shipping_etd_text', 50);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('subtotal_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('order_status', 30);
            $table->string('payment_status', 30);
            $table->string('midtrans_snap_token', 255)->nullable();
            $table->string('midtrans_redirect_url', 255)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
