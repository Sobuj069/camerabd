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
        if (Schema::hasTable('website_orders')) {
            Schema::table('website_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('website_orders', 'status')) {
                    $table->string('status', 50)->nullable()->default('processing')->after('order_status')->index();
                }
                if (!Schema::hasColumn('website_orders', 'customer_phone')) {
                    $table->string('customer_phone', 50)->nullable()->after('phone')->index();
                }
                if (!Schema::hasColumn('website_orders', 'customer_address')) {
                    $table->text('customer_address')->nullable()->after('delivery_address');
                }
                if (!Schema::hasColumn('website_orders', 'courier_service')) {
                    $table->string('courier_service', 100)->nullable()->after('delivery_method');
                }
                if (!Schema::hasColumn('website_orders', 'note')) {
                    $table->text('note')->nullable()->after('order_notes');
                }
                if (!Schema::hasColumn('website_orders', 'note_updated_at')) {
                    $table->timestamp('note_updated_at')->nullable()->after('note');
                }
                if (!Schema::hasColumn('website_orders', 'shipping_fee')) {
                    $table->decimal('shipping_fee', 12, 2)->nullable()->default(0.00)->after('shipping_cost');
                }
            });

            // Backfill existing rows
            DB::statement("UPDATE website_orders SET 
                status = LOWER(COALESCE(order_status, 'processing')), 
                customer_phone = phone, 
                customer_address = delivery_address, 
                courier_service = delivery_method, 
                note = order_notes, 
                shipping_fee = shipping_cost 
                WHERE status IS NULL OR status = 'processing'");
        }

        if (Schema::hasTable('website_order_items')) {
            Schema::table('website_order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('website_order_items', 'product_variation_id')) {
                    $table->unsignedBigInteger('product_variation_id')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('website_order_items', 'product_code')) {
                    $table->string('product_code', 100)->nullable()->after('product_barcode');
                }
                if (!Schema::hasColumn('website_order_items', 'subtotal')) {
                    $table->decimal('subtotal', 12, 2)->nullable()->default(0.00)->after('total_price');
                }
            });

            DB::statement("UPDATE website_order_items SET 
                product_code = product_barcode, 
                subtotal = total_price 
                WHERE subtotal IS NULL OR subtotal = 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('website_orders')) {
            Schema::table('website_orders', function (Blueprint $table) {
                $columns = ['status', 'customer_phone', 'customer_address', 'courier_service', 'note', 'note_updated_at', 'shipping_fee'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('website_orders', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('website_order_items')) {
            Schema::table('website_order_items', function (Blueprint $table) {
                $itemCols = ['product_variation_id', 'product_code', 'subtotal'];
                foreach ($itemCols as $col) {
                    if (Schema::hasColumn('website_order_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
