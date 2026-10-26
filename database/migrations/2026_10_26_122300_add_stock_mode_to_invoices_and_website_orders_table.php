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
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'stock_mode')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('stock_mode')->nullable()->default('without_manage')->after('order_status');
            });
        }

        if (Schema::hasTable('website_orders') && !Schema::hasColumn('website_orders', 'stock_mode')) {
            Schema::table('website_orders', function (Blueprint $table) {
                $table->string('stock_mode')->nullable()->default('without_manage')->after('order_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'stock_mode')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('stock_mode');
            });
        }

        if (Schema::hasTable('website_orders') && Schema::hasColumn('website_orders', 'stock_mode')) {
            Schema::table('website_orders', function (Blueprint $table) {
                $table->dropColumn('stock_mode');
            });
        }
    }
};
