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
        if (Schema::hasTable('website_orders') && !Schema::hasColumn('website_orders', 'total_paid')) {
            Schema::table('website_orders', function (Blueprint $table) {
                $table->decimal('total_paid', 12, 2)->nullable()->default(0.00)->after('total_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('website_orders') && Schema::hasColumn('website_orders', 'total_paid')) {
            Schema::table('website_orders', function (Blueprint $table) {
                $table->dropColumn('total_paid');
            });
        }
    }
};
