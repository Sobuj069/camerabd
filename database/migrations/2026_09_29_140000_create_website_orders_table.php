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
        if (!Schema::hasTable('website_orders')) {
            Schema::create('website_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_no')->unique();
                $table->string('customer_name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('division')->nullable();
                $table->string('city')->nullable();
                $table->text('delivery_address');
                $table->text('order_notes')->nullable();
                $table->string('delivery_method')->default('express_dhaka');
                $table->decimal('shipping_cost', 12, 2)->default(0.00);
                $table->decimal('subtotal', 12, 2)->default(0.00);
                $table->decimal('discount', 12, 2)->default(0.00);
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->string('payment_method')->default('Cash On Delivery');
                $table->string('payment_status')->default('unpaid');
                $table->string('order_status')->default('pending');
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('branch_id')->default(1);
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('website_order_items')) {
            Schema::create('website_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('website_order_id');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_name');
                $table->string('product_barcode')->nullable();
                $table->text('product_image')->nullable();
                $table->decimal('unit_price', 12, 2)->default(0.00);
                $table->integer('quantity')->default(1);
                $table->decimal('total_price', 12, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('website_order_id')->references('id')->on('website_orders')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_order_items');
        Schema::dropIfExists('website_orders');
    }
};
