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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'woocommerce_product_id')) {
                $table->unsignedBigInteger('woocommerce_product_id')->nullable()->index()->after('id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'woocommerce_order_id')) {
                $table->unsignedBigInteger('woocommerce_order_id')->nullable()->index()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'woocommerce_product_id')) {
                $table->dropColumn('woocommerce_product_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
                $table->dropColumn('woocommerce_order_id');
            }
        });
    }
};
