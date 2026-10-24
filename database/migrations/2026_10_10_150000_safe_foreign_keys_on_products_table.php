<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop existing cascade foreign key for brand_id
            try {
                $table->dropForeign('products_brand_id_foreign');
            } catch (\Throwable $e) {}
        });

        Schema::table('products', function (Blueprint $table) {
            // Re-add brand_id foreign key with ON DELETE SET NULL to protect products from deletion
            $table->foreign('brand_id', 'products_brand_id_foreign')
                ->references('id')
                ->on('brands')
                ->onDelete('set null');
        });

        Schema::table('products', function (Blueprint $table) {
            // Drop existing cascade foreign key for category_id
            try {
                $table->dropForeign('products_category_id_foreign');
            } catch (\Throwable $e) {}
        });

        Schema::table('products', function (Blueprint $table) {
            // Re-add category_id foreign key with ON DELETE SET NULL to protect products from deletion
            $table->foreign('category_id', 'products_category_id_foreign')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropForeign('products_brand_id_foreign');
            } catch (\Throwable $e) {}
            try {
                $table->dropForeign('products_category_id_foreign');
            } catch (\Throwable $e) {}

            $table->foreign('brand_id', 'products_brand_id_foreign')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->foreign('category_id', 'products_category_id_foreign')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }
};
